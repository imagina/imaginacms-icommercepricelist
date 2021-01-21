<?php

namespace Modules\Icommercepricelist\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Modules\Icommercepricelist\Repositories\PriceListRepository;
use Modules\Ihelpers\Http\Controllers\Api\BaseApiController;

class PriceListController extends BaseApiController
{
    public $priceList;
    public function __construct(PriceListRepository $priceList)
    {
        $this->priceList = $priceList;
    }

    /**
     * Display a listing of the resource.
     * @return Response
     */
    public function index(Request $request)
    {
        $params = $this->getParamsRequest($request, ['include' => ['products']]);

        $priceLists = $this->priceList->getItemsBy($params);
        return view('icommercepricelist::frontend.index', compact('priceLists'));
    }
}
