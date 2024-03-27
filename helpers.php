<?php

/**
 * Calculate value depends of priceList
 *
 * @param  Collection $priceList
 * @param  Number $value
 * @return Float
 */
if (!function_exists('icommercepricelist_calculatePriceByPriceList')) {

    function icommercepricelist_calculatePriceByPriceList($priceList, $value)
    {
      $valuePriceList = floatval($value * ($priceList->value / 100));
      if ($priceList->operation_prefix == '-') return $value - $valuePriceList;
      else return $value + $valuePriceList;
    }

}
