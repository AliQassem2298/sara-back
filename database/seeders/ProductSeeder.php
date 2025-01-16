<?php

namespace Database\Seeders;

use App\Models\Product;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $product1 = Product::create([
            'product_name' => 'vdfdcb ',
            'description' => 'fbejhdfb',
            'price' => '15.8',
            'quantity'=>21
        ]);

      

        $product1->image()->create([
            'image_url' => 'images/XxeprRBvS8QzfRXCMLimtb36wq5wSpnHkEegLXWp.jpg'
        ]);

        $product2 = Product::create([
            'product_name' => 'jnhdcb ',
            'description' => 'dbhnjd jdh',
            'price' => '12.8',
            'quantity'=>20
        ]);


        $product2->image()->create([
            'image_url' => 'images/XxeprRBvS8QzfRXCMLimtb36wq5wSpnHkEegLXWp.jpg'
        ]);

        $product3 = Product::create([
           'product_name' => 'cdbkkd ',
            'description' => 'ncfdjf dfbj',
            'price' => '20.0',
            'quantity'=>22
        ]);

        $product3->image()->create([
            'image_url' => 'images/XxeprRBvS8QzfRXCMLimtb36wq5wSpnHkEegLXWp.jpg'
        ]);


         $product4 = Product::create([
            'product_name' => 'qwe ',
             'description' => 'ncfdjf dfbj',
             'price' => '20.0',
             'quantity'=>22
         ]);
 
         $product4->image()->create([
             'image_url' => 'images/XxeprRBvS8QzfRXCMLimtb36wq5wSpnHkEegLXWp.jpg'
         ]);



         $product5 = Product::create([
            'product_name' => 'asd ',
             'description' => 'ncfdjf dfbj',
             'price' => '20.0',
             'quantity'=>22
         ]);
 
         $product5->image()->create([
             'image_url' => 'images/XxeprRBvS8QzfRXCMLimtb36wq5wSpnHkEegLXWp.jpg'
         ]);





         $product6 = Product::create([
            'product_name' => 'zxc ',
             'description' => 'ncfdjf dfbj',
             'price' => '20.0',
             'quantity'=>22
         ]);
 
         $product6->image()->create([
             'image_url' => 'images/XxeprRBvS8QzfRXCMLimtb36wq5wSpnHkEegLXWp.jpg'
         ]); 
         
         $product7 = Product::create([
            'product_name' => 'cdbkwkd ',
             'description' => 'ncfdjf dfbj',
             'price' => '20.0',
             'quantity'=>22
         ]);
 
         $product7->image()->create([
             'image_url' => 'images/XxeprRBvS8QzfRXCMLimtb36wq5wSpnHkEegLXWp.jpg'
         ]);



         $product8 = Product::create([
            'product_name' => 'cdbkakd ',
             'description' => 'ncfdjf dfbj',
             'price' => '20.0',
             'quantity'=>22
         ]);
 
         $product8->image()->create([
             'image_url' => 'images/XxeprRBvS8QzfRXCMLimtb36wq5wSpnHkEegLXWp.jpg'
         ]);



    }
}
