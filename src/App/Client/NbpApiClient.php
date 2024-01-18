<?php

namespace App\Client;

use App\Exception\InvalidDateException;
use App\Exception\NoDataException;
use DateTime;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\DecodingExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Component\HttpClient\HttpClient;
use Exception;

class NbpApiClient
{
    private const API_BASE_URL = 'https://api.nbp.pl/api/';
    private $client;
    public $earliestDateAvailable;


    public function __construct(string $earliestDateAvailable, HttpClientInterface $client)
    {
        $this->client = $client;
        $this->earliestDateAvailable = new DateTime($earliestDateAvailable);
    }

    /**
     * @throws NoDataException
     * @throws RedirectionExceptionInterface
     * @throws DecodingExceptionInterface
     * @throws ClientExceptionInterface
     * @throws TransportExceptionInterface
     * @throws ServerExceptionInterface
     * @throws InvalidDateException
     */
    public function fetchCurrencyData(string $currencyCode, string $date): array
    {
        $this->validateDate($date);
        $data = $this->callNbpApi($currencyCode, $date);

        if (!isset($data)) {
            throw new NoDataException("No data retrived for {$currencyCode} on {$date}");
        }

        if (!isset($data['currency'])) {
            throw new NoDataException("Name not found for {$currencyCode} on {$date}");
        }

        if (!isset($data['rates']) || !isset($data['rates'][0]) || !isset($data['rates'][0]['mid'])) {
            throw new NoDataException("NBP rate data not found for {$currencyCode} on {$date}");
        }


        return [
            'name' => $data['currency'],
            'nbpRate' => $data['rates'][0]['mid']
        ];
    }


    /**
     * @throws TransportExceptionInterface
     * @throws ServerExceptionInterface
     * @throws NoDataException
     * @throws RedirectionExceptionInterface
     * @throws DecodingExceptionInterface
     * @throws ClientExceptionInterface
     */
    private function callNbpApi(string $currencyCode, string $date): array
    {
        $endpoint = "exchangerates/rates/A/{$currencyCode}/{$date}?format=json";

        $response = $this->client->request(Request::METHOD_GET, self::API_BASE_URL . $endpoint);
        $statusCode = $response->getStatusCode();

        // Handle no data
        if ($statusCode == 404) {
            throw new NoDataException("No data found for currency {$currencyCode} on {$date}");
        }
        if ($statusCode == 200) {
            return $response->toArray();
        }

        throw new Exception("Unexpected error for currency {$currencyCode} on {$date}");
    }


    /**
     * @throws InvalidDateException
     */
    private function validateDate(string $date): void
    {
        try {
            $dateObj = new DateTime($date);
        } catch (Exception $e) {
            throw new InvalidDateException("Given argument is not a date.");
        }

        // The date must be in YYYY-MM-DD format.
        if ($dateObj->format('Y-m-d') !== $date) {
            throw new InvalidDateException("The date must be in YYYY-MM-DD format.");
        }

        // The date must be after cutoff.
        if ($dateObj < $this->earliestDateAvailable) {
            throw new InvalidDateException(
                "Can't choose a date before {$this->earliestDateAvailable->format('Y-m-d')}.");
        }

        // The date must not be from the future.
        $today = new DateTime();
        if ($dateObj > $today) {
            throw new InvalidDateException("Can't choose a date from the future.");
        }
    }

}
