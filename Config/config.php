<?php

return [
  'name' => 'Icommercepricelist',
  /*
   |--------------------------------------------------------------------------
   | Configuration by google sheet template id
   |--------------------------------------------------------------------------
   */
  'synchronizable' => [
    'entities' => [
      'icommercepricelist_syncProductlist' => [
        'base_template_id' => '1OnBxmLWhdaQavewwAf-UlnvaWN6rI-seCnCrPOagnI0',
        'apiRoute' => '/icommercepricelist/v3/product-lists',
        "supportedActions" =>  ["import", "export"],
        'sheetName' => 'Icommerce ProductList',
        'include' => 'priceList',
        'customColumns' => true,
        'dependencies' => [
          'icommercepricelist_syncPricelists' => [
            'apiRoute' => '/icommercepricelist/v3/price-lists',
            'sheetName' => 'Icommerce PriceList',
            'columns' => [
              'id' => 'ID',
              'name' => 'NOMBRE',
              'operationPrefix' => "OPERACION",
              'value' => 'VALOR'
            ]
          ],
        ]
      ],
    ]
  ],
];
