<?php

use App\Exception\InvalidDateException;
use PHPUnit\Framework\TestCase;
use App\Controller\ExchangeRatesController;
use App\Service\CurrencyExchangeService;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class ExchangeRatesControllerTest extends TestCase
{
    private $currencyExchangeServiceMock;
    private $controller;

    protected function setUp(): void
    {
        $this->currencyExchangeServiceMock = $this->createMock(CurrencyExchangeService::class);
        $this->controller = new ExchangeRatesController($this->currencyExchangeServiceMock);
    }

    public function testValidateDateNotADate()
    {
        $response = $this->controller->getRatesByDate('not-a-date');
        $this->assertEquals(Response::HTTP_BAD_REQUEST, $response->getStatusCode());
    }

    public function testValidateDateWrongFormat()
    {
        $response = $this->controller->getRatesByDate('01-01-2023'); // Wrong format
        $this->assertEquals(Response::HTTP_BAD_REQUEST, $response->getStatusCode());
    }

    public function testValidateDateOk()
    {
        $validDate = '2023-01-01';
        $this->currencyExchangeServiceMock->method('getRatesByDate')
            ->with($validDate)
            ->willReturn([]);

        $response = $this->controller->getRatesByDate($validDate);
        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode());
    }

}