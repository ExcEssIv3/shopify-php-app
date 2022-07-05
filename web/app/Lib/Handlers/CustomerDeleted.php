<?php

declare(strict_types=1);

namespace App\Lib\Handlers;

use App\Models\Customer;

use Illuminate\Support\Facades\Log;
use Shopify\Webhooks\Handler;

class CustomerDeleted implements Handler
{
    public function handle(string $topic, string $shop, array $body): void
    {
        Log::debug("Customer was deleted from $shop - updating customer table.");
        Customer::where('customer_id', $body['id'])->delete();

    }
}
