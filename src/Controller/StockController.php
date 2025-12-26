<?php

namespace Note\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

use Note\Service\StockService;
use Symfony\Component\Validator\Constraints\Json;

class StockController extends AbstractController
{
    const STOCK_CHECK_PERIOD = "Daily";

    private StockService $stockService;

    // Inject StockService into the Controller via dependency injection
    public function __construct(StockService $stockService)
    {
        $this->stockService = $stockService;
    }

    #[Route('/api/stocks/{symbol}/{format}', name: 'stock_check', defaults: ['format' => 'json'], methods: ['GET'])]
    public function stockCheck(string $symbol, string $format, Request $request): Response
    {
        // Gather parameters
        $source = $request->query->get('source') ?? StockService::STOCK_ALPAC_MARKETS;

        error_log("Update the stock status from source `$source`");
        if (StockService::STOCK_ALPAC_MARKETS == $source) {
            $stockJson = $this->stockService->getAlpacaMarketsStockPrice($source, $symbol, $format);
        } else {
            // Finnhub as default
            $stockJson = $this->stockService->getFinnhubStockPrice($source, $symbol, $format);
        }

        if (strtolower($format) == "html") {
            // Render the Twig template
            $htmlContent = $this->renderView('note/stock.html.twig', [
                'stockInfo' => $stockJson
            ]);

            return new JsonResponse([
                'stockInfo' => [
                    "headers" => [],
                    "content" => $htmlContent
                ]
            ]);
        }

        // Default: JSON response with structured data
        return new JsonResponse([
            'stockInfo' => $stockJson,
        ]);
    }
}
