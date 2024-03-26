<?php

namespace Modules\Icommercepricelist\Http\Controllers\Api;

// Requests & Response
use Modules\Icommercepricelist\Entities\ProductList;
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
use Modules\Icommercepricelist\Http\Requests\SyncProductListRequest;

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

        // Validate if exist ProductId and PriceListId
        $this->validateRequestApi(new SyncProductListRequest($attributes));

        //If Get a id, get
        $criteria = $attributes["id"]
          ? ['id' => $attributes["id"]]
          : ['product_id' => $attributes["product_id"], 'price_list_id' => $attributes["price_list_id"]];

        try {
          //Update or Create the Relation
          $msg = ProductList::updateOrCreate(
            $criteria,
            [
              'product_id' => $attributes["product_id"],
              'price_list_id' => $attributes["price_list_id"],
              'price' => $attributes["price"] ?? 0
            ]
          );
        } catch (\Exception $e) {
          // Get the SQL error message
          $msg = $e->getMessage();
          $status = $this->getStatusError($e->getCode());

          // Check if the error message contains the string "SQLSTATE"
          if (strpos($msg, "SQLSTATE") !== false) {
            // Extract the column name causing the error
            preg_match("/CONSTRAINT `[^`]+` FOREIGN KEY \(`([^`]+)`\) REFERENCES/", $msg, $matches);
            $columnName = $matches[1] ?? 'unknown';
            // Return only the name of the failed field
            $msg = ("Failed to find: $columnName");
          }
          $response = ["errors" => $msg];
        }
        //Response
        $response = $response ?? ["data" => $msg];
        \DB::commit();//Commit to DataBase
      } catch (\Exception $e) {
        \DB::rollback();//Rollback to Data Base
        \Log::error("File: ". $e->getFile() ."Line: ". $e->getLine() ."Message: ". $e->getMessage());
        $status = $this->getStatusError($e->getCode());
        $response = ["errors" => $e->getMessage()];
      }

      return response()->json($response ?? ["data" => "Request successful"], $status ?? 200);
    }
}
