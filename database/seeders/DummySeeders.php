<?php

namespace Database\Seeders;

use App\Enums\UserRole;
use App\Models\categories;
use App\Models\products;
use App\Models\User;
use Database\Factories\CategoryFactory;
use Database\Factories\ProductFactory;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DummySeeders extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        /*// Create categories
        $categories = CategoryFactory::new()->count(5)->create();

        // Create products
        for ($i = 1; $i <= 20; $i++) {
            $product = ProductFactory::new()->create();
            $product->categories()->attach($categories->random());
        }*/

        
         // Create categories
        // $categories = CategoryFactory::new()->count(5)->create();

         // Create products
       //  for ($i = 1; $i <= 20; $i++) {

        // $product = ProductFactory::new()->create();

         /*$randomCategory = $categories->random();

         $product->categories()->associate($randomCategory);*/

         //$product->categories()->associate($categories->random());

        // $product->save();
         

         // Create categories
        $categories = CategoryFactory::new()->count(5)->create();

        // Create products
        for ($i = 1; $i <= 20; $i++) {
        $product = ProductFactory::new()->make(); // Use make instead of create
        $randomCategory = $categories->random();
        $product->categories()->associate($randomCategory);
        $product->save();
        }

        // admin
        $user = new User();
        $user->name = 'admin';
        $user->email = 'admin@example.com';
        $user->password = bcrypt('password123'); // set a password for the admin user
        $user->role = UserRole::ADMIN; // set the role to admin
        $user->save();

        
    }
}
