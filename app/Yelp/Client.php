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

    /** @var string ID of Jack Stack BBQ, the restaurant I've selected for this coding challenge */
    private const BUSINESS_ID = 'ieI6wjZZXti4x5bZ3DEyXg';

    /**
     * Client constructor.
     * @param string $apiKey
     */
    public function __construct(string $apiKey)
    {
        $this->client = new GuzzleClient(['base_uri' => 'https://api.yelp.com/v3/']);
        $this->headers =  [
            'Authorization' => 'Bearer ' . $apiKey,
            'Accept'        => 'application/json',
        ];
    }

    /**
     * Get the reviews for the given business ID, or use the Jack Stack BBQ ID
     *
     * @param string|null $businessId
     * @return ResponseInterface
     * @throws GuzzleException
     */
    public function getReviews($businessId = null)
    {
        $businessId = $businessId ?? self::BUSINESS_ID;

        return $this->client->get("businesses/{$businessId}/reviews", ['headers' => $this->headers]);
    }
}
