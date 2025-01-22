<?php
namespace Database\Seeders;
use Illuminate\Database\Seeder;
use App\Models\Contact;

class ContactTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        if ( Contact::count() < 10 ) {
            $faker = \Faker\Factory::create();
            \Bezhanov\Faker\ProviderCollectionHelper::addAllProvidersTo($faker);

            for ( $i = 1; $i <= 10; $i++ ) {
                Contact::firstOrCreate( [
                    'name' => $faker->company,
                    'address1' => $faker->streetAddress,
                    'postalcode' => $faker->postcode,
                    'city' => $faker->city,
                    'state' => $faker->state,
                    'country' => $faker->countryCode,
                    'phone' => $faker->phoneNumber,
                    'email' => $faker->email
                ]);
            }
        }

    }
}
