<?php

namespace Modules\Icommercepricelist\Http\Requests;

use Modules\Core\Internationalisation\BaseFormRequest;
use Modules\Ihelpers\Rules\UniqueSlugRule;

class SyncProductListRequest extends BaseFormRequest
{
  public function rules()
  {
    return [
      'product_id' => 'required',
      'price_list_id' => 'required'
    ];
  }

  public function translationRules()
  {
    return [];
  }

  public function authorize()
  {
    return true;
  }

  public function messages()
  {
    return [
      'product_id.required' => trans('icommerce::common.messages.field required'),
      'price_list_id.required' => trans('icommerce::common.messages.field required'),
    ];
  }

  public function translationMessages()
  {
    return [];
  }
}
