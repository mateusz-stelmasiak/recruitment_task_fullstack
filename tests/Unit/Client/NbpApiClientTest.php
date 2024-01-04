<?php

use PHPUnit\Framework\TestCase;
use App\Client\NbpApiClient;
use Symfony\Component\HttpClient\MockHttpClient;
use Symfony\Component\HttpClient\Response\MockResponse;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use App\Exception\NoDataException;
use App\Exception\InvalidDateException;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

class NbpApiClientTest extends TestCase
{
    private $httpClientMock;
    private $nbpApiClient;
    private $parameterBagMock;


    protected function setUp(): void
    {
        $this->httpClientMock = $this->createMock(HttpClientInterface::class);
        $this->parameterBagMock = $this->createMock(ParameterBagInterface::class);
        $this->parameterBagMock->method('get')
            ->with('app.earliest_date_available')
            ->willReturn('2023-07-08');

        $this->nbpApiClient = new NbpApiClient($this->parameterBagMock, $this->httpClientMock);
    }

    public function testValidateDateForDateBeforeCutoff()
    {
        $this->expectException(InvalidDateException::class);
        // Get cutoff date from parameter, subtract couple days from it

        $cutoffDate = clone $this->nbpApiClient->earliestDateAvailable;
        $invalidDate = $cutoffDate->modify('-3 day')->format('Y-m-d');

        $this->nbpApiClient->fetchCurrencyData('EUR', $invalidDate);
    }

    public function testValidateDateForFutureDate()
    {
        $this->expectException(InvalidDateException::class);
        $today = new DateTime();
        $futureDate = $today->modify('+2 day')->format('Y-m-d');
        $this->nbpApiClient->fetchCurrencyData('EUR', $futureDate);
    }

    public function testValidateDateForInvalidDateFormat()
    {
        $this->expectException(InvalidDateException::class);
        $this->nbpApiClient->fetchCurrencyData('EUR', 'invalid-date');
    }

    public function testFetchCurrencyDataNoDataException()
    {
        $this->expectException(NoDataException::class);

        $mockResponse = new MockResponse('', ['http_code' => 404]);
        $this->httpClientMock->method('request')->willReturn($mockResponse);

        $this->nbpApiClient->fetchCurrencyData('EUR', $this->getValidDate());
    }

    public function testFetchCurrencyDataSuccessful()
    {
        $mockResponse = new MockResponse(json_encode(['currency' => 'EUR', 'rates' => [['mid' => 4.50]]]));
        $mockHttpClient = new MockHttpClient($mockResponse);
        $this->nbpApiClient = new NbpApiClient($this->parameterBagMock, $mockHttpClient);


        $result = $this->nbpApiClient->fetchCurrencyData('EUR', $this->getValidDate());
        $this->assertEquals(['name' => 'EUR', 'nbpRate' => 4.50], $result);
    }

    public function testFetchCurrencyDataInvalidDataStructure()
    {
        $this->expectException(NoDataException::class);

        $mockResponse = new MockResponse(json_encode([]));
        $mockHttpClient = new MockHttpClient($mockResponse);
        $this->nbpApiClient = new NbpApiClient($this->parameterBagMock, $mockHttpClient);

        $this->nbpApiClient->fetchCurrencyData('EUR', $this->getValidDate());
    }

    private function getValidDate(): string
    {
        $cutoffDate = clone $this->nbpApiClient->earliestDateAvailable;
        return $cutoffDate->modify('+5 day')->format('Y-m-d');
    }
}

