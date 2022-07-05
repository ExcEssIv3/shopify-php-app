<?php

declare(strict_types=1);

namespace App\Lib\Handlers;

use App\Models\Product;
use App\Models\Variant;

use Illuminate\Support\Facades\Log;
use Shopify\Webhooks\Handler;

class ProductUpdated implements Handler
{
    public function handle(string $topic, string $shop, array $body): void
    {
        $db_product = Product::where('product_id', $body['id'])->first();
        if (!is_null($db_product)) {
            Log::debug("Product was updated from $shop - updating product table.");
            $db_product = Product::where('product_id', $body['id'])->first();
            $db_product->title = $body['title'];
            $db_product->vendor = $body['vendor'];
            $db_product->type = $body['product_type'];
            $db_product->price = $body['variants'][0]['price'];
            $db_product->has_only_default_variant = !(sizeof($body['variants']) > 1);
            $db_product->save();

            foreach($body['variants'] as $variant) {
                $db_variant = Variant::where('variant_id', $variant['id'])->first();
                if (!is_null($db_variant)) {
                    $db_variant->title = $variant['title'];
                    $db_variant->vendor = $body['vendor'];
                    $db_variant->type = $body['product_type'];
                    $db_variant->price = $variant['price'];
                    $db_variant->save();
                } else {
                    $new_variant = new Variant;
                    $new_variant->variant_id = $variant['id'];
                    $new_variant->parent_id = $variant['product_id'];
                    $new_variant->title = $variant['title'];
                    $new_variant->vendor = $body['vendor'];
                    $new_variant->type = $body['product_type'];
                    $new_variant->price = $variant['price'];
                    $new_variant->save();
                }
            }
        }
    }
}
