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
        'base_template_id' => '1OOyW4ySI7RJMdS9fgcJWOfnPpi41FRFq2WmMhcOQonE',
        'apiRoute' => '/icommercepricelist/v3/product-lists',
        "supportedActions" =>  ["import", "export"],
        'sheetName' => 'Icommerce ProductList',
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
