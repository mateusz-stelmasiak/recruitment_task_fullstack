<?php

namespace Integration\Client;

use App\Client\NbpApiClient;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class NbpApClientTest extends KernelTestCase
{
    function testNbpClientReturnsCorrectDataForKnownData()
    {
        self::bootKernel();
        $apiClient = self::$container->get('App\Client\NbpApiClient');
        //not a test assertion, just helps IDE know what class it is
        assert($apiClient instanceof NbpApiClient);

        $data = $apiClient->fetchCurrencyData('EUR', '2023-12-28');

        // Data from https://api.nbp.pl/api/exchangerates/rates/A/EUR/2023-12-28?format=json
        //{"table":"A","currency":"euro","code":"EUR","rates":[{"no":"250/A/NBP/2023","effectiveDate":"2023-12-28","mid":4.3392}]}
        $expectedData = [
            'name' => "euro",
            'nbpRate' => 4.3392
        ];
        self::assertEquals($expectedData,$data,"Data from NbpClient doesn't match actual data");
    }
}
