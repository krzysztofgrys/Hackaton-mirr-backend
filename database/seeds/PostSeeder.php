<?php

use App\Address;
use App\Category;
use App\Post;
use App\Tag;
use App\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class PostSeeder extends Seeder
{
    const COORDS = [
        ['Rynek', 51.1089776, 17.0326689],
        ['Rondo', 51.09089993781738, 17.01545984696043],
        ['Psiepole', 51.16114399898575, 17.07210810135496],
        ['Olesnica', 51.20891778121325, 17.394831490026835],
        ['Legnica', 51.20375543513386, 16.16573603104242],

    ];

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        foreach (self::COORDS as [$city, $lat, $lng]) {
            $address = Address::firstOrCreate([
                'city' => $city,
                'street' => 'Pl Powstańców Śląskich',
            ], [
                'coordinates' => new \Grimzy\LaravelMysqlSpatial\Types\Point($lat, $lng),
            ]);

            $post = Post::firstOrCreate([
                'title' => 'Potrzebna pomoc w ' . $city,
            ], [
                'description' => 'Potrzebna pomoc w ' . $city,
                'end_at' => Carbon::parse('20-12-2019'),
                'name' => 'Janina Kowalska',
                'phone_number' => phone('123456789', 'pl'),
                'email' => Str::lower('email@' . $city . '.com'),
                'user_id' => User::all()->random()->id,
                'category_id' => Category::all()->random()->id,
                'address_id' => $address->id
            ]);
            $post->tags()->attach(Tag::all()->random());
        }
    }
}
