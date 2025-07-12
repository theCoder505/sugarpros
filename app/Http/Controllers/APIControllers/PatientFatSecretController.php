<?php

namespace App\Http\Controllers\APIControllers;

use App\Http\Controllers\Controller;
use App\Models\Settings;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class PatientFatSecretController extends Controller
{
    private $consumerKey;
    private $consumerSecret;
    private $baseUrl = 'https://platform.fatsecret.com/rest/server.api';

    public function __construct()
    {
        $this->consumerKey = Settings::where('id', 1)->value('FATSECRET_KEY');
        $this->consumerSecret = Settings::where('id', 1)->value('FATSECRET_SECRET');
    }

    public function FatSecret()
    {
        try {
            $initialFoods = $this->searchFoods('chicken soup', 6);
            
            return response()->json([
                'type' => 'success',
                'data' => [
                    'foods' => $initialFoods
                ]
            ], 200);
        } catch (\Exception $e) {
            Log::error('FatSecret Error: ' . $e->getMessage());
            return response()->json([
                'type' => 'error',
                'message' => 'Failed to fetch initial food data'
            ], 500);
        }
    }

    public function getFoods(Request $request)
    {
        try {
            $searchTerm = $request->input('search', 'chicken soup');
            $foods = $this->searchFoods($searchTerm);
            
            return response()->json([
                'type' => 'success',
                'data' => [
                    'foods' => $foods,
                    'search_term' => $searchTerm,
                    'count' => count($foods)
                ]
            ], 200);
        } catch (\Exception $e) {
            Log::error('FatSecret Search Error: ' . $e->getMessage());
            return response()->json([
                'type' => 'error',
                'message' => 'Failed to search foods'
            ], 500);
        }
    }

    public function getFoodDetails($foodId)
    {
        try {
            $params = [
                'method' => 'food.get',
                'food_id' => $foodId,
                'format' => 'json'
            ];

            $response = $this->makeApiRequest($params);
            
            if ($response && isset($response['food'])) {
                $formattedFood = $this->formatFoodData($response);
                
                return response()->json([
                    'type' => 'success',
                    'data' => [
                        'food' => $formattedFood
                    ]
                ], 200);
            }

            return response()->json([
                'type' => 'error',
                'message' => 'Food not found'
            ], 404);

        } catch (\Exception $e) {
            Log::error('FatSecret API Error: ' . $e->getMessage());
            return response()->json([
                'type' => 'error',
                'message' => 'Error fetching food details'
            ], 500);
        }
    }

    public function getBreakfastFoods(Request $request)
    {
        $breakfastTerms = ['breakfast', 'cereal', 'oatmeal', 'pancake', 'eggs', 'toast'];
        $randomTerm = $breakfastTerms[array_rand($breakfastTerms)];
        
        return $this->getFoods($request->merge(['search' => $randomTerm]));
    }

    public function getLunchFoods(Request $request)
    {
        $lunchTerms = ['lunch', 'sandwich', 'salad', 'soup', 'pasta', 'burger'];
        $randomTerm = $lunchTerms[array_rand($lunchTerms)];
        
        return $this->getFoods($request->merge(['search' => $randomTerm]));
    }

    public function getDinnerFoods(Request $request)
    {
        $dinnerTerms = ['dinner', 'chicken', 'beef', 'fish', 'rice', 'vegetables', 'steak'];
        $randomTerm = $dinnerTerms[array_rand($dinnerTerms)];
        
        return $this->getFoods($request->merge(['search' => $randomTerm]));
    }

    // ============ PRIVATE HELPER METHODS ============ //

    private function searchFoods($searchTerm, $limit = 20)
    {
        try {
            $params = [
                'method' => 'foods.search',
                'search_expression' => $searchTerm,
                'max_results' => $limit,
                'format' => 'json'
            ];

            $response = $this->makeApiRequest($params);
            
            if ($response && isset($response['foods']['food'])) {
                $foods = $response['foods']['food'];
                
                // If single food result, wrap in array
                if (isset($foods['food_id'])) {
                    $foods = [$foods];
                }
                
                // Get detailed nutrition info for each food
                $detailedFoods = [];
                foreach ($foods as $food) {
                    $detailResponse = $this->getFoodNutrition($food['food_id']);
                    if ($detailResponse) {
                        $detailedFoods[] = $this->formatFoodData($detailResponse);
                    }
                }
                
                return $detailedFoods;
            }

            return [];

        } catch (\Exception $e) {
            Log::error('FatSecret Search Error: ' . $e->getMessage());
            return [];
        }
    }

    private function getFoodNutrition($foodId)
    {
        try {
            $params = [
                'method' => 'food.get',
                'food_id' => $foodId,
                'format' => 'json'
            ];

            return $this->makeApiRequest($params);

        } catch (\Exception $e) {
            Log::error('FatSecret Nutrition Error: ' . $e->getMessage());
            return null;
        }
    }

    private function formatFoodData($response)
    {
        if (!isset($response['food'])) {
            return null;
        }

        $food = $response['food'];
        $servings = $food['servings']['serving'] ?? [];
        
        // Get the first serving for display
        $serving = is_array($servings) && isset($servings[0]) ? $servings[0] : $servings;
        
        return [
            'id' => $food['food_id'],
            'name' => $food['food_name'],
            'brand' => $food['brand_name'] ?? '',
            'calories' => $serving['calories'] ?? 0,
            'protein' => $serving['protein'] ?? 0,
            'carbs' => $serving['carbohydrate'] ?? 0,
            'fat' => $serving['fat'] ?? 0,
            'fiber' => $serving['fiber'] ?? 0,
            'sugar' => $serving['sugar'] ?? 0,
            'sodium' => $serving['sodium'] ?? 0,
            'serving_description' => $serving['serving_description'] ?? '',
            'food_url' => $food['food_url'] ?? '',
            'health_score' => $this->calculateHealthScore($serving),
            'servings' => is_array($servings) ? $servings : [$servings]
        ];
    }

    private function calculateHealthScore($serving)
    {
        $calories = (float)($serving['calories'] ?? 0);
        $protein = (float)($serving['protein'] ?? 0);
        $fiber = (float)($serving['fiber'] ?? 0);
        $sugar = (float)($serving['sugar'] ?? 0);
        $sodium = (float)($serving['sodium'] ?? 0);
        
        $score = 50; // Base score
        
        if ($protein > 10) $score += 20;
        elseif ($protein > 5) $score += 10;
        
        if ($fiber > 5) $score += 15;
        elseif ($fiber > 2) $score += 8;
        
        if ($sugar > 20) $score -= 20;
        elseif ($sugar > 10) $score -= 10;
        
        if ($sodium > 800) $score -= 15;
        elseif ($sodium > 400) $score -= 8;
        
        if ($calories > 500) $score -= 10;
        
        return max(0, min(100, $score));
    }

    private function makeApiRequest($params)
    {
        $params['oauth_consumer_key'] = $this->consumerKey;
        $params['oauth_signature_method'] = 'HMAC-SHA1';
        $params['oauth_timestamp'] = time();
        $params['oauth_nonce'] = uniqid();
        $params['oauth_version'] = '1.0';

        $signature = $this->generateOAuthSignature('GET', $this->baseUrl, $params);
        $params['oauth_signature'] = $signature;

        $response = Http::timeout(30)->get($this->baseUrl, $params);

        if ($response->successful()) {
            return $response->json();
        }

        throw new \Exception('API request failed: ' . $response->status() . ' - ' . $response->body());
    }

    private function generateOAuthSignature($method, $url, $params)
    {
        ksort($params);
        
        $paramString = '';
        foreach ($params as $key => $value) {
            $paramString .= $key . '=' . rawurlencode($value) . '&';
        }
        $paramString = rtrim($paramString, '&');
        
        $signatureBaseString = strtoupper($method) . '&' . 
                              rawurlencode($url) . '&' . 
                              rawurlencode($paramString);
        
        $signingKey = rawurlencode($this->consumerSecret) . '&';
        
        return base64_encode(hash_hmac('sha1', $signatureBaseString, $signingKey, true));
    }
}