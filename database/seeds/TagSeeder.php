<?php

use App\Tag;
use Illuminate\Database\Seeder;

class TagSeeder extends Seeder
{

    const TAGS = [
        ['name' => 'Osoba w podeszłym wieku', 'slug' => 'osoba-w-podeszlym-wieku'],
        ['name' => 'Osoba z niepełnosprawnością ruchową', 'slug' => 'osoba-z-niepelnosprawnocia-ruchowa'],
        ['name' => 'Osoba poruszająca się na wózku', 'slug' => 'osoba-poruszajaca-sie-na-wozku'],
        ['name' => 'Osoba poruszająca się o kulach', 'slug' => 'osoba-poruszajaca-sie-o-kulach'],
        ['name' => 'Osoba niewidoma', 'slug' => 'osoba-niewidoma'],
        ['name' => 'Osoba niedowidząca', 'slug' => 'osoba-niedowidzaca'],
        ['name' => 'Osoba niskiego wzrostu', 'slug' => 'osoba-niskiego-wzrostu'],
        ['name' => 'Osoba niedosłysząca', 'slug' => 'osoba-niedoslyszaca'],
        ['name' => 'Osoba niesłysząca', 'slug' => 'osoba-nieslyszaca'],
        ['name' => 'Osoba z niepełnosprawnością intelektualną', 'slug' => 'osoba-z-niepelnosprawnoscia-intelektualna'],
        ['name' => 'Osoba z zaburzeniem psychicznym', 'slug' => 'osoba-z-zaburzeniem-psychicznym'],
        ['name' => 'Inne', 'slug' => 'inne'],
    ];

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        foreach (self::TAGS as ['name' => $name, 'slug' => $slug]) {
            Tag::firstOrCreate(['name' => $name], ['slug' => $slug]);
        }
    }
}
