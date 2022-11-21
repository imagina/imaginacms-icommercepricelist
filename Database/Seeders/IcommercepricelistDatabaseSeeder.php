<?php

namespace Modules\Icommercepricelist\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;

class IcommercepricelistDatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Model::unguard();

        $this->call(IcommercepricelistModuleTableSeeder::class);
        // $this->call("OthersTableSeeder");
    }
}
