<?php

namespace Database\Seeders;

use App\Models\CommonCurrency;
use App\Models\Translation;
use Illuminate\Database\Seeder;

class CommonCurrencySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $path = storage_path('app/dbSourceFiles/commonCurrency.json');
        if (!file_exists($path))
            return;
        $commonCurrencyArray = json_decode(file_get_contents($path), true);
        foreach ($commonCurrencyArray as $key => $currency)
        {
            if(CommonCurrency::where('currency', $key)->first())
                continue;
            CommonCurrency::create([
                'currency' => $key,
                'currency_symbol' => $currency['symbol'],
                'currency_name' => $currency['name']
            ]);
            Translation::create([
                'en' => $currency['name'],
                'pt' => $currency['pt'],
                'es' => $currency['es'],
                'category' => Translation::CATEGORY_COMMON_CURRENCIES
            ]);
        }
    }
}
