<?php

namespace Modules\Icommercepricelist\Transformers;

use Illuminate\Http\Resources\Json\JsonResource;
use Modules\Icurrency\Support\Facades\Currency;
use Modules\Core\Icrud\Transformers\CrudResource;

class ProductListTransformer extends CrudResource
{
  public function modelAttributes($request)
  {
    return [];
  }
}
