<?php

declare(strict_types=1);

namespace App\Lib\Handlers;

use App\Models\Product;
use App\Models\Variant;

use Illuminate\Support\Facades\Log;
use Shopify\Webhooks\Handler;

class ProductDeleted implements Handler
{
    public function handle(string $topic, string $shop, array $body): void
    {
        Log::debug("Product was deleted from $shop - updating product table.");

        Variant::where('parent_id', $body['id'])->delete();
        Product::where('product_id', $body['id'])->delete();

    }
}
