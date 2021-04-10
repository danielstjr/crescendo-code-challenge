<?php

namespace App\Http\Controllers;

use App\Yelp\Client;

class YelpController extends Controller
{
    private $client;

    public function __construct(Client $client)
    {
        $this->client = $client;
    }

    public function reviews()
    {
        return $this->client->getReviews();
    }
}
