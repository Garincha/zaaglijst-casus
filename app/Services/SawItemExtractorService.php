<?php

namespace App\Services;

class SawItemExtractorService
{
    private string $pattern = '/g\d+/i';

    /**
     * Groups saw items by profile color.
     *
     * @param array<int, array<string, mixed>> $data
     * @return array<int|string, array<int, array<string, int>>>
     */
    public function groupSawItemsByProfileColor(array $data): array
    {
        $sawItems = [];

        foreach ($data as $item) {
            $saw = $item['saw'];
            if (isset($saw['profielkleur']['title'])) {
                $profielkleur = $saw['profielkleur']['title'];
                if (!isset($sawItems[$profielkleur])) {
                    $sawItems[$profielkleur] = [];
                }
                $sawItems[$profielkleur] = array_merge($sawItems[$profielkleur], $this->processSawItems($saw));
            }
        }

        return $sawItems;
    }


    /**
     * Processes saw items and returns an array grouped by G-numbers.
     *
     * @param array<string, array<string, mixed>> $saw
     * @return array<int, array<string, int>>
     */

    private function processSawItems(array $saw): array
    {
        $sawItems = [];

        foreach ($saw as $key => $value) {
            if ($key === 'profielkleur') {
                continue;
            }

            if ($this->isValidKey($key)) {
                $gNumbers = $this->extractGNumbers($key);
                foreach ($gNumbers as $gNumber) {
                    $sawItems[] = [
                        'length' => $value['value'],
                        'count' => $value['amount'],
                    ];
                }
            }
        }

        return $sawItems;
    }


    private function isValidKey(string $key): bool
    {
        return preg_match($this->pattern, $key) === 1;
    }


    /**
     * Extracts G numbers from the key.
     *
     * @return array<string>
     */
    private function extractGNumbers(string $key): array
    {
        preg_match_all($this->pattern, $key, $matches);
        return array_map('strtoupper', $matches[0]);
    }

}
