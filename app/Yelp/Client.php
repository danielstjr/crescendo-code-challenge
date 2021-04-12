<?php

namespace App\Yelp;

use GuzzleHttp\Client as GuzzleClient;
use GuzzleHttp\Exception\GuzzleException;
use Psr\Http\Message\ResponseInterface;

/**
 * Class Client
 *
 * Simple client to access and retrieve Yelp Information
 *
 * @package App\Yelp
 */
class Client
{
    /** @var GuzzleClient Guzzle client instantiated with a Yelp bearer token */
    private $client;

    /** @var array Header information for every request */
    private $headers;

    /**
     * Client constructor.
     * @param string $apiKey
     */
    public function __construct(string $apiKey)
    {
        $this->client = new GuzzleClient(['base_uri' => 'https://api.yelp.com/v3/']);
        $this->headers = [
            'Authorization' => 'Bearer ' . $apiKey,
            'Accept' => 'application/json',
        ];
    }

    public function getBusinessById(string $businessId): ?Business
    {
        $response = $this->makeGetRequest("businesses/{$businessId}");

        return $response === null ? null : $this->makeBusiness(json_decode($response->getBody()->getContents(), true));
    }

    /**
     * Get a business ID by a lat/long pair, sorted by "Yelp Sort" (according to their documentation)
     *
     * @param float $latitude
     * @param float $longitude
     * @return Business|null
     */
    public function getBusinessByLatLong(float $latitude, float $longitude): ?Business
    {
        $response = $this->makeGetRequest('businesses/search', ['query' => ['latitude' => $latitude, 'longitude' => $longitude]]);

        return $response === null ? null : $this->getFirstBusinessFromSearch($response);
    }

    /**
     * Get the first business ID by a string location, sorted by "Yelp Sort" (according to their documentation)
     *
     * @param string $location
     * @return Business|null
     */
    public function getBusinessByLocation(string $location): ?Business
    {
        $response = $this->makeGetRequest('businesses/search', ['query' => ['location' => $location]]);

        return $response === null ? null : $this->getFirstBusinessFromSearch($response);
    }

    /**
     * Get the reviews for the given business ID, or use the Jack Stack BBQ ID
     *
     * @param string $businessId Business ID to get reviews for
     * @return array
     */
    public function getReviews(string $businessId): array
    {
        $response = $this->makeGetRequest("businesses/{$businessId}/reviews");

        return $response === null ? [] : json_decode($response->getBody()->getContents(), true)['reviews'];
    }

    /**
     * Get first business from business search endpoint and return its ID, or null if no businesses are returned
     *
     * @param ResponseInterface $response
     * @return Business|null
     */
    private function getFirstBusinessFromSearch(ResponseInterface $response): ?Business
    {
        $businesses = json_decode($response->getBody()->getContents(), true)['businesses'];

        return empty($businesses) ? null : $this->makeBusiness(head($businesses));
    }

    /**
     * Create a business from Yelp API response data
     *
     * @param array $businessData
     * @return Business
     */
    private function makeBusiness(array $businessData): Business
    {
        $location = $businessData['location'];

        if (array_key_exists('display_address', $location)) {
            $address = implode(' ', $location['display_address']);
        } else {
            $address = "{$location['address1']} {$location['address2']} {$location['address3']} {$location['city']}, {$location['state']} {$location['zip_code']}";
        }

        return new Business(
            $address,
            $businessData['id'],
            $businessData['coordinates']['latitude'],
            $businessData['coordinates']['longitude']
        );
    }

    /**
     * Make the request thread safe so it doesn't crash the request if it fails to make the request to Yelp
     *
     * @param string $uri
     * @param array|null $additionalOptions
     * @return ResponseInterface|null
     */
    private function makeGetRequest(string $uri, array $additionalOptions = null): ?ResponseInterface
    {
        logger()->debug(json_encode(array_merge(['headers' => $this->headers], $additionalOptions ?? [])));
        try {
            $response = $this->client->get($uri, array_merge(['headers' => $this->headers], $additionalOptions ?? []));

            if ($response->getStatusCode() !== 200) {
                logger()->debug("Status code: {$response->getStatusCode()}");
                $response = null;
            }
        } catch (GuzzleException $exception) {
            logger()->debug($exception);
            $response = null;
        }

        return $response;
    }
}
