<?php

declare(strict_types=1);

namespace App\Lib\Handlers;

use App\Models\Customer;

use Illuminate\Support\Facades\Log;
use Shopify\Webhooks\Handler;

class CustomerCreated implements Handler
{
    public function handle(string $topic, string $shop, array $body): void
    {
        Log::debug("Customer was created from $shop - updating customer table.");
        
        $new_customer = new Customer;
        $new_customer->customer_id = $body['id'];
        $new_customer->first_name = $body['first_name'];
        $new_customer->last_name = $body['last_name'];
        $new_customer->email = $body['email'];
        $new_customer->num_orders = $body['orders_count'];
        $new_customer->net_sales = $body['total_spent'];
        $new_customer->save();
    }
}
