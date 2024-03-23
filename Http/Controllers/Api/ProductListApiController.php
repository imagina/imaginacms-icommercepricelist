<?php

namespace Modules\Icommercepricelist\Http\Controllers\Api;

// Requests & Response
use Modules\Icommerce\Entities\Product;
use Modules\Icommercepricelist\Entities\PriceList;
use Modules\Icommercepricelist\Http\Requests\ProductListRequest;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

// Base Api
use Modules\Ihelpers\Http\Controllers\Api\BaseApiController;

// Transformers
use Modules\Icommercepricelist\Transformers\ProductListTransformer;

// Repositories
use Modules\Icommercepricelist\Repositories\ProductListRepository;
use Modules\Icommercepricelist\Http\Requests\UpdatePriceListRequest;

class ProductListApiController extends BaseApiController
{
    private $productList;

    public function __construct(ProductListRepository $productList)
    {
        $this->productList = $productList;
    }

    /**
     * Display a listing of the resource.
     * @return Response
     */
    public function index(Request $request)
    {
        try {
            //Request to Repository
            $productLists = $this->productList->getItemsBy($this->getParamsRequest($request));

            //Response
            $response = ['data' => ProductListTransformer::collection($productLists)];
            //If request pagination add meta-page
            $request->page ? $response['meta'] = ['page' => $this->pageTransformer($productLists)] : false;

        } catch (\Exception $e) {
            //Message Error
            $status = 500;
            $response = [
                'errors' => $e->getMessage()
            ];
        }
        return response()->json($response, $status ?? 200);
    }

    /** SHOW
     * @param Request $request
     *  URL GET:
     *  &fields = type string
     *  &include = type string
     */
    public function show($criteria, Request $request)
    {

        try {
            //Get Parameters from URL.
            $params = $this->getParamsRequest($request);

            //Request to Repository
            $criteria = $this->productList->getItem($criteria, $params);

            //Break if no found item
            if (!$criteria) throw new \Exception('Item not found', 404);

            //Response
            $response = ["data" => new ProductListTransformer($criteria)];

            //If request pagination add meta-page
            $params->page ? $response["meta"] = ["page" => $this->pageTransformer($criteria)] : false;

        } catch (\Exception $e) {
            $status = $this->getStatusError($e->getCode());
            $response = ["errors" => $e->getMessage()];
        }
        return response()->json($response ?? ["data" => "Request successful"], $status ?? 200);

    }

    /**
     * Show the form for creating a new resource.
     * @return Response
     */
    public function create(Request $request)
    {
        \DB::beginTransaction();
        try {
            $data = $request->input('attributes') ?? [];//Get data

            //Validate Request
            $this->validateRequestApi(new ProductListRequest($data));

            //Create item
            $entity = $this->productList->create($data);

            //Response
            $response = ["data" => new ProductListTransformer($entity)];
            \DB::commit(); //Commit to Data Base
        } catch (\Exception $e) {
            \Log::error($e);
            \DB::rollback();//Rollback to Data Base
            $status = $this->getStatusError($e->getCode());
            $response = ["errors" => $e->getMessage()];
        }
        //Return response
        return response()->json($response ?? ["data" => "Request successful"], $status ?? 200);
    }

    /**
     * Update the specified resource in storage.
     * @param  Request $request
     * @return Response
     */
    public function update($criteria, Request $request)
    {

        \DB::beginTransaction();
        try {
            $params = $this->getParamsRequest($request);
            $data = $request->input('attributes');

            //Validate Request
            $this->validateRequestApi(new UpdatePriceListRequest($data));

            //Update data
            $category = $this->productList->updateBy($criteria, $data,$params);

            //Response
            $response = ['data' => 'Item Updated'];
            \DB::commit(); //Commit to Data Base
        } catch (\Exception $e) {
            \DB::rollback();//Rollback to Data Base
            $status = $this->getStatusError($e->getCode());
            $response = ["errors" => $e->getMessage()];
        }
        return response()->json($response, $status ?? 200);

    }

    /**
     * Remove the specified resource from storage.
     * @return Response
     */
    public function delete($criteria, Request $request)
    {

        \DB::beginTransaction();
        try {
            //Get params
            $params = $this->getParamsRequest($request);

            //Delete data
            $this->productList->deleteBy($criteria, $params);

            //Response
            $response = ['data' => ''];
            \DB::commit(); //Commit to Data Base
        } catch (\Exception $e) {
            \DB::rollback();//Rollback to Data Base
            $status = $this->getStatusError($e->getCode());
            $response = ["errors" => $e->getMessage()];
        }
        return response()->json($response, $status ?? 200);

    }

    public function syncProductsList(Request $request)
    {
      $msg = "";
      \DB::beginTransaction(); //DB Transaction
      try {
        $attributes = $request->input('attributes') ?? [];//Get data

        if(!isset($attributes["product_id"]) || !isset($attributes["price_list_id"]))
          return response()->json(["errors" => "Miss fields: ProductId or PriceList"], 404);

        $value = $attributes["price"] ?? 0;

        //Get Product
        $product = Product::Where('id', $attributes["product_id"])->first();

        //Return if product not found
        if(!isset($product)) return response()->json(["errors" => "Producto no encontrado"], 404);


        //Get priceList
        $priceList = PriceList::where('id', $attributes["price_list_id"])->first();

        //Return if priceList not found
        if(!isset($priceList)) return response()->json(["errors" => "Lista de precio no encontrada"], 404);

        //Make percentage opertion
        if($priceList->criteria !== 'fixed') {
          $price = $product->price;
          $valuePriceList = ($price * ($priceList->value / 100));
          if ($priceList->operation_prefix == '-') $value = $price - $valuePriceList;
          else $value = $price + $valuePriceList;

        }

        $data = [
          'product_id' => $product->id,
          'price_list_id' => $priceList->id,
          'price' => $value
        ];

        $checkProduct = null;

        // Update Product List
        if(is_null($attributes["id"])) {
          $criteria = $product->id;

          $params = [
            "filter" => [
              "field" => "product_id",
              'price_list_id' => $priceList->id,
            ]
          ];

          // Verify if exist productList
          $checkProduct = $this->productList->getItem($criteria, $params);

          //Create the product List
          if(!isset($checkProduct)) $msg = $this->productList->create($data);
          else $attributes["id"] = $checkProduct->id;

        }

        //Update the product List
        if(isset($attributes["id"])) $msg = $this->productList->updateBy($criteria, $data,$params);

        //Response
        $response = ["data" => $msg];
        \DB::commit();//Commit to DataBase
      } catch (\Exception $e) {
        \DB::rollback();//Rollback to Data Base
        \Log::error($e->getMessage(), $e->getFile(), $e->getLine());
        $status = $this->getStatusError($e->getCode());
        $response = ["errors" => $e->getMessage()];
      }

      return response()->json($response ?? ["data" => "Request successful"], $status ?? 200);
    }
}
