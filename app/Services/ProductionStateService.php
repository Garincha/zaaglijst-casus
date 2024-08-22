<?php

namespace App\Services;

class ProductionStateService
{
    private SawItemExtractorService $sawItemExtractorService;

    public function __construct(SawItemExtractorService $sawItemExtractorService)
    {
        $this->sawItemExtractorService = $sawItemExtractorService;
    }

    public function getProductionStateData(): string|false
    {
        return file_get_contents(storage_path() . "/data/ProductieStaat.json");
    }

    /**
     * Extracts and groups saw items by profile color.
     *
     * @return array<int|string, array<int, array<string, int>>>
     */
    public function extractSawItemsGroupedByProfileColor(): array
    {
        $jsonData = $this->getProductionStateData();

        if ($jsonData === false) {
            // Handle the error - for example, throw an exception or return an empty array
            throw new \RuntimeException('Failed to retrieve production state data.');
        }

        $data = json_decode($jsonData, true);

        // Ensure that json_decode succeeded
        if ($data === null) {
            throw new \RuntimeException('Failed to decode JSON data.');
        }

        return $this->sawItemExtractorService->groupSawItemsByProfileColor($data);
    }
}
