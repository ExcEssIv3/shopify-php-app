<?php

declare(strict_types=1);

namespace App\Lib\Handlers;

use App\Models\Product;
use App\Models\Variant;

use Illuminate\Support\Facades\Log;
use Shopify\Webhooks\Handler;

class ProductCreated implements Handler
{
    public function handle(string $topic, string $shop, array $body): void
    {
        Log::debug("Product was created from $shop - updating product table.");
        // Log::debug("PRODUCT BODY: " . var_export($body, true));

        $new_product = new Product;
        $new_product->product_id = $body['id'];
        $new_product->title = $body['title'];
        $new_product->vendor = $body['vendor'];
        $new_product->type = $body['product_type'];
        $new_product->price = $body['variants'][0]['price'];
        $new_product->has_only_default_variant = !(sizeof($body['variants']) > 1);
        $new_product->save();

        foreach($body['variants'] as $variant) {
            // $db_variant = Variant::where('variant_id', $variant['id'])->first();
            // if (!is_null($db_variant)) {
            //     $db_variant->title = $variant['title'];
            //     $db_variant->vendor = $body['vendor'];
            //     $db_variant->type = $body['product_type'];
            //     $db_variant->price = $variant['price'];
            //     $db_variant->save();
            // } else {
                $new_variant = new Variant;
                $new_variant->variant_id = $variant['id'];
                $new_variant->parent_id = $variant['product_id'];
                $new_variant->title = $variant['title'];
                $new_variant->vendor = $body['vendor'];
                $new_variant->type = $body['product_type'];
                $new_variant->price = $variant['price'];
                $new_variant->save();
            // }
        }
    }
}
