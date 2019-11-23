<?php

use App\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    const CATEGORIES = [
        ['name' => 'Pomoc z zakupami', 'slug' => 'pomoc-z-zakupami'],
        ['name' => 'Pomoc z zwierzęciem', 'slug' => 'pomoc-z-zwierzeciem'],
        ['name' => 'Transport', 'slug' => 'transport'],
    ];

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        foreach (self::CATEGORIES as ['name' => $name, 'slug' => $slug]) {
            Category::firstOrCreate(['name' => $name], ['slug' => $slug]);
        }
    }
}
