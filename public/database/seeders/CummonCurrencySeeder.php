<?php

namespace Database\Seeders;

use App\Models\CummonCurrency;
use Illuminate\Database\Seeder;

class CummonCurrencySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $path = storage_path('app/dbSourceFiles/cummonCurrency.json');

        if (!file_exists($path))
            return;

        $cummonCurrencyArray = json_decode(file_get_contents($path), true);

        foreach ($cummonCurrencyArray as $key => $currency)
        {
            CummonCurrency::create([
                'currency' => $key,
                'currency_symbol' => $currency['symbol'],
                'currency_name' => $currency['name']
            ]);
        }
    }
}
