<?php

declare(strict_types=1);

namespace App\Lib\Handlers;

use App\Models\Customer;

use Illuminate\Support\Facades\Log;
use Shopify\Webhooks\Handler;

class CustomerUpdated implements Handler
{
    public function handle(string $topic, string $shop, array $body): void
    {
        $db_customer = Customer::where('customer_id', $body['id'])->first();
        if (!is_null($db_customer)) {
            Log::debug("Customer was updated from $shop - updating customer table.");
            $db_customer->first_name = $body['first_name'];
            $db_customer->last_name = $body['last_name'];
            $db_customer->email = $body['email'];
            $db_customer->num_orders = $body['orders_count'];
            $db_customer->net_sales = $body['total_spent'];
            $db_customer->save();
        }
    }
}
