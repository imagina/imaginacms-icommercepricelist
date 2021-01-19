<?php

namespace Modules\Icommercepricelist\Providers;

use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

use Modules\Icommercepricelist\Events\Handlers\UpdatePriceProductLists;
use Modules\Icommercepricelist\Events\Handlers\RefreshProductPriceLists;
use Modules\Icommercepricelist\Events\ProductListWasCreated;
use Modules\Icommercepricelist\Events\ProductWasCreated;
use Modules\Icommercepricelist\Events\ProductWasUpdated;

class EventServiceProvider extends ServiceProvider
{
    protected $listen = [
        ProductListWasCreated::class => [
            UpdatePriceProductLists::class,
        ],
        ProductWasCreated::class => [
            RefreshProductPriceLists::class,
        ],
        ProductWasUpdated::class => [
            RefreshProductPriceLists::class,
        ],

    ];
}
