<?php

use App\Address;
use App\User;
use Grimzy\LaravelMysqlSpatial\Types\Point;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $address = Address::firstOrCreate([
            'city' => 'Wrocław',
            'street' => 'Pl Powstańców Śląskich',
        ], [
            'coordinates' => new Point(51.09089993, 17.0154598),
        ]);
        $user = User::firstOrCreate([
            'email' => 'user@bezinteresowni.pl',
        ], [
            'password' => Hash::make('useruser'),
            'first_name' => 'przykładowy',
            'last_name' => 'użytkownik',
            'date_of_birth' => new \DateTime('23-11-1969'),
            'phone_number' => phone('123555789'),
            'addresses_id' => $address->id,
        ]);
        $user->api_token = 'asdf';
        $user->save();
    }
}
