<?php

namespace Unit\Service;
use PHPUnit\Framework\TestCase;
use App\Service\CurrencyExchangeService;
use App\Client\NbpApiClient;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

class CurrencyExchangeServiceTest extends TestCase
{
    private $nbpApiClientMock;
    private $currencyExchangeService;

    protected function setUp(): void
    {
        // mock up currencies config
        $supportedCurrencies = [
            'EUR' => [
                'buy_margin' => 0.05,
                'sell_margin' => 0.07,
            ],
            'USD' => [
                'buy_margin' => 0.05,
                'sell_margin' => 0.07,
            ]
        ];
        // Mock up nbp API Client
        $this->nbpApiClientMock = $this->createMock(NbpApiClient::class);

        // Mock up parameters bag
        $parameterBagMock = $this->createMock(ParameterBagInterface::class);
        $parameterBagMock->method('get')
            ->with('app.supportedCurrencies')
            ->willReturn($supportedCurrencies);

        $this->currencyExchangeService = new CurrencyExchangeService($parameterBagMock, $this->nbpApiClientMock);
    }
    public function testBuyingRateCalculation()
    {
        $currency = "EUR";
        $nbpRate = 4.5123;
        $buyMargin = $this->currencyExchangeService->getSupportedCurrencies()[$currency]['buy_margin'];
        $expectedBuyingRate = $nbpRate - $buyMargin;

        $this->nbpApiClientMock->method('fetchCurrencyData')
            ->willReturn(['name' => 'Euro', 'nbpRate' => $nbpRate]);


        $result = $this->currencyExchangeService->getRatesByDate('2023-02-03');
        $this->assertEquals($expectedBuyingRate, $result[0]->getBuyingRate());
    }


    public function testSellingRateCalculation()
    {
        $currency = "EUR";
        $nbpRate = 4.2134;
        $sellMargin = $this->currencyExchangeService->getSupportedCurrencies()[$currency]['sell_margin'];
        $expectedSellingRate = $nbpRate + $sellMargin;

        $this->nbpApiClientMock->method('fetchCurrencyData')
            ->willReturn(['name' => 'Euro', 'nbpRate' => $nbpRate]);


        $result = $this->currencyExchangeService->getRatesByDate('2023-02-03');
        $this->assertEquals($expectedSellingRate, $result[0]->getSellingRate());
    }


}

