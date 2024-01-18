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
    private $mockEarliestDateAvailable;


    protected function setUp(): void
    {
        $this->httpClientMock = $this->createStub(HttpClientInterface::class);
        $this->mockEarliestDateAvailable = "2023-07-08";
        $this->nbpApiClient = new NbpApiClient($this->mockEarliestDateAvailable, $this->httpClientMock);
    }

    public function wrongDateProvider(): Generator
    {
        // Date before the cutoff
        $cutoffDate = new DateTime("2023-07-08");
        $beforeCutoff = $cutoffDate->modify('-3 day')->format('Y-m-d');
        yield 'date before the cutoff' => [$beforeCutoff];

        // Future date
        $today = new DateTime();
        $futureDate = $today->modify('+2 day')->format('Y-m-d');
        yield 'future date' => [$futureDate];

        // Invalid date format
        yield 'invalid date format'=>["12-12-2023"];

        // Not a date
        yield 'string that\'s not a date'=>["not-a-date"];

        // Not a string
        yield 'not a string'=>[9];
    }

    /**
     * @dataProvider wrongDateProvider
     */
    public function testValidateDateForInvalidDate($invalidDate)
    {
        $this->expectException(InvalidDateException::class);
        $this->nbpApiClient->fetchCurrencyData('EUR', $invalidDate);
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
        $this->nbpApiClient = new NbpApiClient($this->mockEarliestDateAvailable, $mockHttpClient);


        $result = $this->nbpApiClient->fetchCurrencyData('EUR', $this->getValidDate());
        $this->assertEquals(['name' => 'EUR', 'nbpRate' => 4.50], $result);
    }

    public function testFetchCurrencyDataInvalidDataStructure()
    {
        $this->expectException(NoDataException::class);

        $mockResponse = new MockResponse(json_encode([]));
        $mockHttpClient = new MockHttpClient($mockResponse);
        $this->nbpApiClient = new NbpApiClient($this->mockEarliestDateAvailable, $mockHttpClient);

        $this->nbpApiClient->fetchCurrencyData('EUR', $this->getValidDate());
    }

    private function getValidDate(): string
    {
        $cutoffDate = clone $this->nbpApiClient->earliestDateAvailable;
        return $cutoffDate->modify('+5 day')->format('Y-m-d');
    }
}

