<?php

use App\Address;
use App\User;
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
            'zip_code' => '53-030',
            'street' => 'Pl Powstańców Śląskich',
            'house_number' => '7',
        ], [
            'coordinates' => new \Grimzy\LaravelMysqlSpatial\Types\Point(51.21, 17.2),
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
