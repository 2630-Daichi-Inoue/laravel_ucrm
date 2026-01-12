<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Item;
use App\Models\Customer;
use App\Models\Purchase;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            UserSeeder::class,
            ItemSeeder::class,
        ]);

        Customer::factory(1000)->create();

        $items = Item::all();

        Purchase::factory(100)->create()
        ->each(function(Purchase $purchase) use ($items) {
            $purchase->items()->attach(
                $items->random(rand(1, 3))->pluck('id')->toArray(),
                [ 'quantity' => rand(1,5) ]
            );
        });

        User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
        ]);
    }
}
