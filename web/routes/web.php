<?php

use App\Models\Customer;
use App\Models\Product;
use App\Models\Variant;

use App\Lib\EnsureBilling;
use App\Models\Session;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Route;
use Shopify\Auth\OAuth;
use Shopify\Auth\Session as AuthSession;
use Shopify\Clients\HttpHeaders;
use Shopify\Clients\Rest;
use Shopify\Context;
use Shopify\Utils;
use Shopify\Webhooks\Registry;
use Shopify\Webhooks\Topics;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::fallback(function (Request $request) {
    $shop = $request->query('shop') ? Utils::sanitizeShopDomain($request->query('shop')) : null;
    $appInstalled = Session::where('shop', $shop)->exists();
    if ($appInstalled) {
        if (env('APP_ENV') === 'production') {
            return file_get_contents(public_path('index.html'));
        } else {
            return file_get_contents(base_path('frontend/index.html'));
        }
    }
    return redirect("/api/auth?shop=$shop");
});

Route::get('/api/auth/toplevel', function (Request $request, Response $response) {
    // $shop = Utils::sanitizeShopDomain($request->query('shop'));
    $shop = 'dckaptraining.myshopify.com';

    $response = new Response(view('top_level', [
        'apiKey' => Context::$API_KEY,
        'shop' => $shop,
        'hostName' => Context::$HOST_NAME,
    ]));

    $response->withCookie(cookie()->forever('shopify_top_level_oauth', '', null, null, true, true, false, 'strict'));

    // Log::debug('TOPLEVEL RESPONSE: ' . var_export($response, true));

    return $response;
});

Route::get('/api/auth', function (Request $request) {
    // $shop = Utils::sanitizeShopDomain($request->query('shop'));
    $shop = 'dckaptraining.myshopify.com';

    // Log::debug("AUTH REQUEST: " . var_export($request, true));

    if (!$request->hasCookie('shopify_top_level_oauth')) {
        return redirect("/api/auth/toplevel?shop=$shop");
    }

    $installUrl = OAuth::begin(
        $shop,
        '/api/auth/callback',
        true,
        ['App\Lib\CookieHandler', 'saveShopifyCookie'],
    );

    Log::debug('INSTALL URL: ' . $installUrl);

    return redirect($installUrl);
});

Route::get('/api/auth/callback', function (Request $request) {
    Log::debug('CALLBACK 1');
    $session = OAuth::callback(
        $request->cookie(),
        $request->query(),
        ['App\Lib\CookieHandler', 'saveShopifyCookie'],
    );

    Log::debug('CALLBACK 2');

    $host = $request->query('host');
    // $shop = Utils::sanitizeShopDomain($request->query('shop'));
    $shop = 'dckaptraining.myshopify.com';

    $response = Registry::register('/api/webhooks', Topics::APP_UNINSTALLED, $shop, $session->getAccessToken());
    if ($response->isSuccess()) {
        Log::debug("Registered APP_UNINSTALLED webhook for shop $shop");
    } else {
        Log::error(
            "Failed to register APP_UNINSTALLED webhook for shop $shop with response body: " .
                print_r($response->getBody(), true)
        );
    }

    // customer webhooks

    $response = Registry::register('/api/webhooks', Topics::CUSTOMERS_CREATE, $shop, $session->getAccessToken());
    if ($response->isSuccess()) {
        Log::debug("Registered CUSTOMERS_CREATE webhook for shop $shop");
    } else {
        Log::error(
            "Failed to register CUSTOMERS_CREATE webhook for shop $shop with response body: " .
                print_r($response->getBody(), true)
        );
    }

    $response = Registry::register('/api/webhooks', Topics::CUSTOMERS_UPDATE, $shop, $session->getAccessToken());
    if ($response->isSuccess()) {
        Log::debug("Registered CUSTOMERS_UPDATE webhook for shop $shop");
    } else {
        Log::error(
            "Failed to register CUSTOMERS_UPDATE webhook for shop $shop with response body: " .
                print_r($response->getBody(), true)
        );
    }

    $response = Registry::register('/api/webhooks', Topics::CUSTOMERS_DELETE, $shop, $session->getAccessToken());
    if ($response->isSuccess()) {
        Log::debug("Registered CUSTOMERS_DELETE webhook for shop $shop");
    } else {
        Log::error(
            "Failed to register CUSTOMERS_DELETE webhook for shop $shop with response body: " .
                print_r($response->getBody(), true)
        );
    }

    // product webhooks

    $response = Registry::register('/api/webhooks', Topics::PRODUCTS_CREATE, $shop, $session->getAccessToken());
    if ($response->isSuccess()) {
        Log::debug("Registered PRODUCTS_CREATE webhook for shop $shop");
    } else {
        Log::error(
            "Failed to register PRODUCTS_CREATE webhook for shop $shop with response body: " .
                print_r($response->getBody(), true)
        );
    }
    
    $response = Registry::register('/api/webhooks', Topics::PRODUCTS_UPDATE, $shop, $session->getAccessToken());
    if ($response->isSuccess()) {
        Log::debug("Registered PRODUCTS_UPDATE webhook for shop $shop");
    } else {
        Log::error(
            "Failed to register PRODUCTS_UPDATE webhook for shop $shop with response body: " .
                print_r($response->getBody(), true)
        );
    }

    $response = Registry::register('/api/webhooks', Topics::PRODUCTS_DELETE, $shop, $session->getAccessToken());
    if ($response->isSuccess()) {
        Log::debug("Registered PRODUCTS_DELETE webhook for shop $shop");
    } else {
        Log::error(
            "Failed to register PRODUCTS_DELETE webhook for shop $shop with response body: " .
                print_r($response->getBody(), true)
        );
    }



    $redirectUrl = "?" . http_build_query(['host' => $host, 'shop' => $shop]);
    if (Config::get('shopify.billing.required')) {
        list($hasPayment, $confirmationUrl) = EnsureBilling::check($session, Config::get('shopify.billing'));

        if (!$hasPayment) {
            $redirectUrl = $confirmationUrl;
        }
    }

    return redirect($redirectUrl);
});

Route::post('/api/graphql', function (Request $request) {
    $response = Utils::graphqlProxy($request->header(), $request->cookie(), $request->getContent());

    $xHeaders = array_filter(
        $response->getHeaders(),
        function ($key) {
            return str_starts_with($key, 'X') || str_starts_with($key, 'x');
        },
        ARRAY_FILTER_USE_KEY
    );

    return response($response->getDecodedBody(), $response->getStatusCode())->withHeaders($xHeaders);
})->middleware('shopify.auth:online');

Route::get('/api/products-count', function (Request $request) {
    /** @var AuthSession */
    $session = $request->get('shopifySession'); // Provided by the shopify.auth middleware, guaranteed to be active

    $client = new Rest($session->getShop(), $session->getAccessToken());
    $result = $client->get('products/count');

    return response($result->getDecodedBody());
})->middleware('shopify.auth:online');

Route::post('/api/webhooks', function (Request $request) {
    try {
        $topic = $request->header(HttpHeaders::X_SHOPIFY_TOPIC, '');

        $response = Registry::process($request->header(), $request->getContent());

        if (!$response->isSuccess()) {
            Log::error("Failed to process '$topic' webhook: {$response->getErrorMessage()}");
            return response()->json(['message' => "Failed to process '$topic' webhook"], 500);
        }
    } catch (\Exception $e) {
        Log::error("Got an exception when handling '$topic' webhook: {$e->getMessage()}");
        return response()->json(['message' => "Got an exception when handling '$topic' webhook"], 500);
    }
});

// my routes

Route::get('/api/customers', function (Request $request) {
    return Customer::orderBy('created_at', 'asc')->get();
})->middleware('shopify.auth:online');

Route::get('/api/products', function (Request $request) {
    return Product::orderBy('created_at', 'asc')->get();
})->middleware('shopify.auth:online');

Route::get('/api/product/{id}', function(Request $request, $id) {
    return Variant::where('parent_id', $id)->get();
    // return "ID RESPONSE: $id";
})->middleware('shopify.auth:online');

Route::get('/api/customers/update', function(Request $request) {
    /** @var AuthSession */
    $session = $request->get('shopifySession');

    $client = new Rest($session->getShop(), $session->getAccessToken());
    $customers = $client->get('/admin/api/2022-04/customers.json')->getDecodedBody();

    if (gettype($customers) == 'array') {
        $customers = (array) $customers;
        foreach($customers['customers'] as $customer) {
            $db_customer = Customer::where('customer_id', $customer['id'])->first();
            if (!is_null($db_customer)) {
                $db_customer->first_name = $customer['first_name'];
                $db_customer->last_name = $customer['last_name'];
                $db_customer->email = $customer['email'];
                $db_customer->num_orders = $customer['orders_count'];
                $db_customer->net_sales = $customer['total_spent'];
                $db_customer->save();
            } else {
                $new_customer = new Customer;
                $new_customer->customer_id = $customer['id'];
                $new_customer->first_name = $customer['first_name'];
                $new_customer->last_name = $customer['last_name'];
                $new_customer->email = $customer['email'];
                $new_customer->num_orders = $customer['orders_count'];
                $new_customer->net_sales = $customer['total_spent'];
                $new_customer->save();
            }
        }

        return response('customer data updated', 201);
    } else if (is_null($customers)) {
        return response('not found', 400);
    } else {
        Log::error("customers is a string: {$customers}");
        return response('customers is a string', 500);
}

    // Log::debug('CUSTOMERS PRINT: ' . var_export($customers, true));

})->middleware('shopify.auth:online');

Route::get('/api/products/update', function(Request $request) {
    /** @var AuthSession */
    $session = $request->get('shopifySession');

    $client = new Rest($session->getShop(), $session->getAccessToken());
    $products = $client->get('products')->getDecodedBody();

    if (gettype($products) == 'array') {
        $products = (array) $products;
        foreach($products['products'] as $product) {
            $db_product = Product::where('product_id', $product['id'])->first();
            if (!is_null($db_product)) {
                $db_product->title = $product['title'];
                $db_product->vendor = $product['vendor'];
                $db_product->type = $product['product_type'];
                $db_product->price = $product['variants'][0]['price'];
                $db_product->has_only_default_variant = !(sizeof($product['variants']) > 1);
                $db_product->save();
            } else {
                $new_product = new Product;
                $new_product->product_id = $product['id'];
                $new_product->title = $product['title'];
                $new_product->vendor = $product['vendor'];
                $new_product->type = $product['product_type'];
                $new_product->price = $product['variants'][0]['price'];
                $new_product->has_only_default_variant = !(sizeof($product['variants']) > 1);
                $new_product->save();
            }

            foreach($product['variants'] as $variant) {
                $db_variant = Variant::where('variant_id', $variant['id'])->first();
                if (!is_null($db_variant)) {
                    $db_variant->title = $variant['title'];
                    $db_variant->vendor = $product['vendor'];
                    $db_variant->type = $product['product_type'];
                    $db_variant->price = $variant['price'];
                    $db_variant->save();
                } else {
                    $new_variant = new Variant;
                    $new_variant->variant_id = $variant['id'];
                    $new_variant->parent_id = $variant['product_id'];
                    $new_variant->title = $variant['title'];
                    $new_variant->vendor = $product['vendor'];
                    $new_variant->type = $product['product_type'];
                    $new_variant->price = $variant['price'];
                    $new_variant->save();
                }
            }
        }

        return response('products data updated', 201);
    } else if (is_null($products)) {
        return response('not found', 400);
    } else {
        Log::error("products is a string: {$products}");
        return response('products is a string', 500);
    }

})->middleware('shopify.auth:online');
