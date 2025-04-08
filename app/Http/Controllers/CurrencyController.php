<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Cache;

class CurrencyController extends Controller
{
    public function getRates()
    {
        // Kurları 1 saat (3600 saniye) boyunca cache'le
        $currencyData = Cache::remember('tcmb_currencies', 3600, function () {
            $url = 'https://www.tcmb.gov.tr/kurlar/today.xml';
            $xml = simplexml_load_file($url);
            if ($xml === false) {
                throw new \Exception("TCMB verileri alınamadı.");
            }
            
            // XPath ile USD ve EUR öğelerini çek
            $usdNode = $xml->xpath("//Currency[@Kod='USD']");
            $eurNode = $xml->xpath("//Currency[@Kod='EUR']");

            if (!$usdNode || !$eurNode) {
                throw new \Exception("Gerekli döviz kurları bulunamadı.");
            }

            $usdCurrency = $usdNode[0];
            $eurCurrency = $eurNode[0];

            // Virgülü noktaya çevirip float'a dönüştür
            $usdRate = (float) str_replace(',', '.', (string)$usdCurrency->ForexBuying);
            $eurRate = (float) str_replace(',', '.', (string)$eurCurrency->ForexBuying);

            return [
                'USD' => $usdRate,
                'EUR' => $eurRate,
            ];
        });

        return $currencyData;
    }
}
