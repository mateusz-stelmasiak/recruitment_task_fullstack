<?php

namespace App\Service;

use App\Client\NbpApiClient;
use App\Entity\Currency;
use App\Entity\ExchangeRate;
use App\Exception\InvalidDateException;
use App\Exception\NoDataException;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

class CurrencyExchangeService
{
    private $supportedCurrencies;
    private $nbpApiClient;

    public function __construct(ParameterBagInterface $params, NbpApiClient $nbpApiClient)
    {
        $this->supportedCurrencies = $params->get('app.supportedCurrencies');
        $this->nbpApiClient = $nbpApiClient;
    }


    /**
     * @throws InvalidDateException
     */
    public function getRatesByDate(string $date): array
    {
        $exchangeRates = [];

        foreach ($this->supportedCurrencies as $code => $currencyConfig) {
            try {
                $currencyData = $this->nbpApiClient->fetchCurrencyData($code, $date);
                $currencyName = $currencyData['name'];
                $currencyNbpRate = $currencyData['nbpRate'];
                $currency = new Currency($code, $currencyName);
                $buyingRate = $this->calculateBuyingRate($currencyNbpRate, $currencyConfig['buy_margin']);
                $sellingRate = $this->calculateSellingRate($currencyNbpRate, $currencyConfig['sell_margin']);
                $exchangeRate = new ExchangeRate($currency, $currencyNbpRate, $buyingRate, $sellingRate);
                $exchangeRates[] = $exchangeRate;
            }
            //If no data found for one of the currencies, add a placeholder and try for others
            catch (NoDataException $e) {
                $exchangeRate = new ExchangeRate(new Currency($code, ""), null, null, null);
                $exchangeRates[] = $exchangeRate;
                continue;
            }
        }

        return $exchangeRates;
    }

    private function calculateBuyingRate(?float $nbpRate, ?float $margin): ?float
    {
        if (!$margin) return null;

        // Calculate the number of decimal places in nbpRate
        // and round the calculated buying rate to that
        $decimalPlaces = $this->getDecimalPlaces($nbpRate);
        return round($nbpRate - $margin, $decimalPlaces);
    }

    private function calculateSellingRate(?float $nbpRate, ?float $margin): ?float
    {
        if (!$margin) return null;

        // Calculate the number of decimal places in nbpRate
        // and round the calculated selling rate to that
        $decimalPlaces = $this->getDecimalPlaces($nbpRate);
        return round($nbpRate + $margin, $decimalPlaces);
    }

    private function getDecimalPlaces($value): int
    {
        if (!is_float($value)) {
            return 0;
        }

        $fraction = (string)($value - (int)$value);
        // Remove leading "0."
        $fraction = substr($fraction, 2);

        return strlen($fraction);
    }

    public function getSupportedCurrencies(){
        return $this->supportedCurrencies;
    }

}