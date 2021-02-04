<?php
use Illuminate\Routing\Router;


$locale = LaravelLocalization::setLocale() ?: App::getLocale();


/** @var Router $router */
Route::group(['prefix' => LaravelLocalization::setLocale(),
    'middleware' => ['localize']], function (Router $router) use ($locale) {
    $router->get( trans('icommercepricelist::routes.pricelists.index'), [
        'as' => $locale . '.icommercepricelist.pricelists.index',
        'uses' => 'PriceListController@index',
    ]);
});
