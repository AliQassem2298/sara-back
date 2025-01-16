<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Store;

class StoreSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {

        $store1 = Store::create([
            'store_name' => 'asdd ',
            'store_type' => 'restaurant'
        ]);

        $location = $store1->location()->create([
            'address' => 'Syria',
        ]);

        $store1->image()->create([
            'image_url' => 'images/upI6Q5AKyeXycZaFFPIkupLkpWoSQyRqCK8TwnzQ.jpg'
        ]);

        $store2 = Store::create([
            'store_name' => 'fghjk ',
            'store_type' => 'Clothes'
        ]);
        $location = $store2->location()->create([
            'address' => 'Syria',
        ]);

        $store2->image()->create([
            'image_url' => 'images/KWQqSffL5CEDKogJva5WIbZZSbMchyWeUeLJobco.webp'
        ]);

        $store3 = Store::create([
            'store_name' => 'ertyuimn ',
            'store_type' => 'patisserie'
        ]);

        $location = $store3->location()->create([
            'address' => 'Syria',
        ]);
        $store3->image()->create([
            'image_url' => 'images/59ruxpH5xoMMjdYkqV0benG7gIRFq3s3oQOazckG.webp'
        ]);
      
    }
}
