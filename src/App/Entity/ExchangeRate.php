<?php
// src/Entity/ExchangeRate.php

namespace App\Entity;

use DateTime;
use JsonSerializable;

class ExchangeRate implements JsonSerializable
{
    private $currency;
    private $nbpRate;
    private $buyingRate;
    private $sellingRate;

    public function __construct(Currency $currency, ?float $nbpRate, ?float $buyingRate, ?float $sellingRate)
    {
        $this->currency = $currency;
        $this->nbpRate = $nbpRate;
        $this->buyingRate = $buyingRate;
        $this->sellingRate = $sellingRate;
    }

    public function jsonSerialize(): array
    {
        return [
            $this->currency->getCode() =>[
                'name' =>$this->currency->getName(),
                'nbp' => $this->getNbpRate(),
                'buy'=>$this->getBuyingRate(),
                'sell'=>$this->getSellingRate()
            ]
        ];
    }

    public function getCurrency(): Currency
    {
        return $this->currency;
    }

    public function getNbpRate(): ?float
    {
        return $this->nbpRate;
    }

    public function getBuyingRate(): ?float
    {
        return $this->buyingRate;
    }

    public function getSellingRate(): ?float
    {
        return $this->sellingRate;
    }
}
