<?php

namespace App\Http\Controllers;

use App\Services\ProductionStateService;
use Illuminate\Http\JsonResponse;

class ProductionStateController extends Controller
{
    private ProductionStateService $productionStateService;

    public function __construct(ProductionStateService $productionStateService)
    {
        $this->productionStateService = $productionStateService;
    }

    public function index(): string | false
    {
        return $this->productionStateService->getProductionStateData();
    }

    public function getSawData(): JsonResponse
    {
        $sawItems = $this->productionStateService->extractSawItemsGroupedByProfileColor();

        return response()->json($sawItems);
    }
}
