<?php

namespace App\Http\Controllers;

class ProductionStateController extends Controller
{
    public function index(): string
    {
        return file_get_contents(storage_path() . "/data/ProductieStaat.json");
    }

    public function extractPartialValues()
    {

        // Read the contents of the data.json file and parse the JSON data into a PHP array
        $data = json_decode(file_get_contents(storage_path() . "/data/ProductieStaat.json"), true);

        // Create regex pattern to match the "g" values
        $pattern = '/g\d+/i';

        // Extract the "saw" objects
        $sawItems = [];
        foreach ($data as $item) {
            $saw = $item['saw'];
            // Check object has title key under profielkleur
            if (isset($saw['profielkleur']['title'])) {
                $profielkleur = $saw['profielkleur']['title'];
                // initialize empty array at the index of the profielkleur
                $sawItems[$profielkleur] = [];

                // Loop through the saw items
                foreach ($saw as $key => $value) {
                    if ($key === 'profielkleur') {
                        continue;
                    }

                    $amount = $saw[$key]['amount'];
                    $value = $saw[$key]['value'];

                    // Check if the key matches the pattern
                    if (preg_match_all($pattern, $key, $matches)) {
                        $g_numbers = $matches[0];
                        foreach ($g_numbers as $g_number) {
                            $g_number = strtoupper($g_number);
                            $g_object = [
                                'length' => $value,
                                'count' => $amount,
                            ];
                            $sawItems[$profielkleur][$g_number][] = $g_object;
                        }
                    } else {
                        // If the title doesn't match the pattern, skip this "saw" item
                        continue;
                    }
                }
            }
        }

        // Create a new API response with the extracted data
        $response = response()->json([
            $sawItems
        ]);

        // Return the API response
        return $response;
    }
}
