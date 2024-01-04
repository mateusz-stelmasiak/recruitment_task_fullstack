<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\Currency;
use App\Entity\ExchangeRate;
use App\Exception\InvalidDateException;
use App\Service\CurrencyExchangeService;
use DateTime;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class ExchangeRatesController extends AbstractController
{
    private $currencyExchangeService;


    public function __construct(CurrencyExchangeService $currencyExchangeService)
    {
        $this->currencyExchangeService = $currencyExchangeService;

    }

    public function getRatesByDate(string $date): JsonResponse
    {
        try {
            $this->validateDate($date);
            $rates = $this->currencyExchangeService->getRatesByDate($date);
            return new JsonResponse($rates, Response::HTTP_OK);
        } catch (InvalidDateException $e) {
            return new JsonResponse(['error' => $e->getMessage()], Response::HTTP_BAD_REQUEST);
        } catch (\Exception $e) {
            return new JsonResponse(['error' => $e->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * @throws InvalidDateException
     */
    private function validateDate($date)
    {
        try {
            $dateObj = new DateTime($date);
        } catch (Exception $e) {
            throw new InvalidDateException("Given argument is not a date.");
        }

        // Check if the date format is YYYY-MM-DD
        if ($dateObj->format('Y-m-d') !== $date) {
            throw new InvalidDateException("The date must be in YYYY-MM-DD format.");
        }
    }


}
