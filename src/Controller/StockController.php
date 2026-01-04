<?php

namespace  NoteReact\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

use Psr\Log\LoggerInterface;  // 需要导入这个接口
use NoteReact\Util\LoggerTrait;

use NoteReact\Service\StockService;
use Symfony\Component\Validator\Constraints\Json;

class StockController extends AbstractController
{
    const STOCK_CHECK_PERIOD = "Daily";

    use LoggerTrait;

    private StockService $stockService;

    // Inject StockService into the Controller via dependency injection
    public function __construct(StockService $stockService, LoggerInterface $logger)
    {
        $this->stockService = $stockService;
        $this->setLogger($logger);
    }

    #[Route('/api/stocks/{symbol}/{format}', name: 'stock_check', defaults: ['format' => 'json'], methods: ['GET'])]
    public function stockCheck(string $symbol, string $format, Request $request): Response
    {
        $this->info("$$$$$$$$$$ Loading `$symbol` stock with `$format`...");
        // Gather parameters
        $source = $request->query->get('source') ?? StockService::STOCK_ALPAC_MARKETS;
        $this->info("$$$$$$$$$$ Stock source: `$source`");

        try {
            if (StockService::STOCK_ALPAC_MARKETS == $source) {
                $stockJson = $this->stockService->getAlpacaMarketsStockPrice($source, $symbol, $format);
            } else {
                $stockJson = $this->stockService->getFinnhubStockPrice($source, $symbol, $format);
            }
        } catch (\Throwable $e) {
            return new JsonResponse([
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ], 500);
        }

        $this->info("$$$$$$$$$$ Stock json: " . json_encode($stockJson));

        // Default: JSON response with structured data
        return new JsonResponse([
            'stockInfo' => $stockJson,
        ]);
    }
}
