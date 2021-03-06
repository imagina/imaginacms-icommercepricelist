<?php

use Illuminate\Routing\Router;

$router->group(['prefix' => '/icommercepricelist/v3'/*,'middleware' => ['auth:api']*/], function (Router $router) {
//======  PRICE LISTS
    require('ApiRoutes/priceListRoutes.php');

    //======  PRODUCT LISTS
    require('ApiRoutes/productListRoutes.php');
});
