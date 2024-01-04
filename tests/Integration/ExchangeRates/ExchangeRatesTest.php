<?php
namespace Integration\ExchangeRates;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;

class ExchangeRatesTest extends WebTestCase
{
    public function testGetRatesByDateForInvalidDate()
    {
        $client = static::createClient();
        $client->request('GET', '/api/exchange-rates/invalid-date');
        $response = $client->getResponse();
        $this->assertEquals(Response::HTTP_BAD_REQUEST,$response->getStatusCode());
        $this->assertJson($response->getContent());

        $responseData = json_decode($response->getContent(), true);
        $this->assertArrayHasKey('error', $responseData);
        $this->assertNotEmpty($responseData['error']);
        $this->assertEquals("Given argument is not a date.",$responseData['error']);
    }

    public function testGetRatesByDateForWrongDateFormat()
    {
        $client = static::createClient();
        $client->request('GET', '/api/exchange-rates/20-12-2013');
        $response = $client->getResponse();
        $this->assertEquals(Response::HTTP_BAD_REQUEST,$response->getStatusCode());
        $this->assertJson($response->getContent());

        $responseData = json_decode($response->getContent(), true);
        $this->assertArrayHasKey('error', $responseData);
        $this->assertNotEmpty($responseData['error']);
        $this->assertEquals("The date must be in YYYY-MM-DD format.",$responseData['error']);
    }

    public function testGetRatesByDateForValidDate()
    {
        $client = static::createClient();
        $client->request('GET', '/api/exchange-rates/2023-02-02');
        $response = $client->getResponse();
        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode());
        $this->assertJson($response->getContent());
    }

}
