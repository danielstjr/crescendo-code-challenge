<?php

namespace App\Http\Controllers;

use App\Yelp\Business;
use App\Yelp\Client;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

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
    public function reviews(Request $request): JsonResponse
    {
        $businessId = $request->query('id') ?: null;
        $latitude = $request->query('latitude') ?: null;
        $longitude = $request->query('longitude') ?: null;
        $location = $request->query('location') ?: null;

        // Prefer a business ID, then lat/long pairs, then a string location (address), then default to Jack Stack BBQ
        if ($businessId) {
            $business = $this->client->getBusinessById($businessId);
            $nullBusinessMessage = 'No business found under the given business ID';
        } elseif ($latitude || $longitude) {
            if (!$latitude) {
                return response()->json('Latitude is required when longitude is passed in', 422);
            } elseif (!$longitude) {
                return response()->json('Longitude is required when latitude is passed in', 422);
            }

            $business = $this->client->getBusinessByLatLong((float) $latitude, (float) $longitude);
            $nullBusinessMessage = 'No businesses found within a 40000 meter radius at those latitude and longitude coordinates';
        } elseif ($location) {
            $business = $this->client->getBusinessByLocation($location);
            $nullBusinessMessage = 'No businesses found at the given location';
        } else {
            // Default to Jack Stack BBQ in Overland Park, KS, if no query params are passed in to filter data
            $business = new Business(
                '9520 Metcalf Ave Overland Park, KS 66212',
                'ieI6wjZZXti4x5bZ3DEyXg',
                38.95591,
                -94.66847
            );
        }

        if ($business === null && isset($nullBusinessMessage)) {
            return response()->json($nullBusinessMessage, 422);
        }

        $rawReviews = $this->client->getReviews($business->getId());

        $formattedReviews = [];
        foreach($rawReviews as $review) {
            $formattedReviews[] = [
                'name' => $review['user']['name'],
                'profile_image_url' => $review['user']['image_url'],
                'location' => $business->getAddress(),
                'rating' => $review['rating'],
                'content' => $review['text'],
            ];
        }

        return response()->json($formattedReviews, 200);
    }
}
