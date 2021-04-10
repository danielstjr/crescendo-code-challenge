<?php

namespace App\Http\Controllers;

use App\Yelp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Http\JsonResponse;

class YelpController extends Controller
{
    /** @var Client Class that contains API calls to the Yelp v3 API */
    private $client;

    /**
     * YelpController constructor.
     * @param Client $client
     */
    public function __construct(Client $client)
    {
        $this->client = $client;
    }

    /**
     * Map the contents of the Yelp API's bussiness reviews endpoint into a json object
     *
     * @return JsonResponse
     */
    public function reviews(): JsonResponse
    {
        try {
            $response = $this->client->getReviews();
        } catch (GuzzleException $exception) {
            $response = null;
        }

        if (!$response || $response->getStatusCode() !== 200) {
            return response()->json('Something went wrong retrieving the yelp data!', 500);
        }

        $rawReviews = json_decode($response->getBody()->getContents())->reviews;

        $formattedReviews = [];
        foreach($rawReviews as $review) {
            $formattedReviews[] = [
                'name' => $review->user->name,
                'profile_image_url' => $review->user->image_url,
                'location' => $this->client::BUSINESS_ADDRESS,
                'rating' => $review->rating,
                'content' => $review->text,
            ];
        }

        return response()->json($formattedReviews, 200);
    }
}
