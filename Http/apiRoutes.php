<?php

use Illuminate\Routing\Router;

$locale = \LaravelLocalization::setLocale() ?: \App::getLocale();
Route::prefix('/icommercepricelist/v3')->middleware('auth:api')->group(function (Router $router) use($locale) {
  //======  PRICE LISTS
  $router->apiCrud([
    'module' => 'icommercepricelist',
    'prefix' => 'price-lists',
    'permission' => 'icommercepricelist.pricelists',
    'controller' => 'PriceListApiController',
    'permission' => 'icommercepricelist.pricelists',
    'middleware' => [
      'create' => ['auth:api', 'auth-can:icommercepricelist.pricelists.create'],
      'update' => ['auth:api', 'auth-can:icommercepricelist.pricelists.edit'],
      'delete' => ['auth:api', 'auth-can:icommercepricelist.pricelists.destroy'],
      // 'restore' => []
    ]
  ]);

  //======  PRODUCT LISTS
  $router->apiCrud([
    'module' => 'icommercepricelist',
    'prefix' => 'product-lists',
    'permission' => 'icommercepricelist.productlist',
    'controller' => 'ProductListApiController',
    'permission' => 'icommercepricelist.productlist',
    'middleware' => [
      'create' => ['auth:api', 'auth-can:icommercepricelist.productlist.create'],
      'update' => ['auth:api', 'auth-can:icommercepricelist.productlist.edit'],
      'delete' => ['auth:api', 'auth-can:icommercepricelist.productlist.destroy'],
      // 'restore' => []
    ],
    'customRoutes' => [ // Include custom routes if needed
      [
        'method' => 'post',
        'path' => '/sync',
        'uses' => 'syncProductsList',
        //'middleware' => []
      ]
    ]
  ]);
});
