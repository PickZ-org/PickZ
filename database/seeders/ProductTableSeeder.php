<?php
namespace Database\Seeders;
use Illuminate\Database\Seeder;
use App\Models\Product;

class ProductTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        if ( Product::count() < 50 ) {
            $faker = \Faker\Factory::create();
            \Bezhanov\Faker\ProviderCollectionHelper::addAllProvidersTo($faker);

            for ( $i = 0; $i <= 10; $i++ ) {
                $product = Product::create( [
                    'name' => $faker->productName,
                    'description' => $faker->sentence( 10 ),
                    'ean' => $faker->ean13,
                    'sku'=> $faker->isbn13,
                    'barcode' => $faker->ean13,
                    'owner_contact_id' => \App\Models\Contact::all()->random()->id
                ]);

                // Product meta

                $width = array(
                    'key' => 'width',
                    'value' => $faker->randomDigit
                );

                $height = array(
                    'key' => 'height',
                    'value' => $faker->randomDigit
                );

                $weight = array(
                    'key' => 'weight',
                    'value' => $faker->randomDigit
                );

                $hazardous = array(
                    'key' => 'hazardous',
                    'value' => $faker->boolean
                );

                $product->meta()->create($width);
                $product->meta()->create($height);
                $product->meta()->create($weight);
                $product->meta()->create($hazardous);

                // Product UOM

                $product->productUoms()->create([
                    'name' => 'each',
                    'base' => true,
                    'default' => true,
                    'breakable' => false,
                    'price_unit' => $faker->randomFloat(2, 1, 1000),
                    'price_period' => $faker->randomFloat(2, 1, 1000),
                ]);

                $product->productUoms()->create([
                    'name' => 'case',
                    'base' => false,
                    'default' => false,
                    'breakable' => true,
                    'quantity' => 6,
                    'price_unit' => $faker->randomFloat(2, 1, 1000),
                    'price_period' => $faker->randomFloat(2, 1, 1000),
                ]);

            }
        }
    }
}
