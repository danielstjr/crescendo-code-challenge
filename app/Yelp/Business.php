<?php

namespace App\Yelp;

/**
 * Class Business
 *
 * Simple data object for holding all data from the Yelp business search API
 *
 * @package App\Yelp
 */
class Business
{
    /** @var string Business Address associated with the Yelp Search API */
    private $address;

    /** @var string Business ID associated with a single business in the Yelp Search API */
    private $id;

    /** @var float Latitude for a Yelp Business */
    private $latitude;

    /** @var float Longitude for a Yelp Business */
    private $longitude;

    /**
     * Business constructor.
     *
     * @param string $address
     * @param string $id
     * @param float|null $latitude
     * @param float|null $longitude
     */
    public function __construct(string $address, string $id, float $latitude = null, float $longitude = null)
    {
        $this->address = $address;
        $this->id = $id;
        $this->latitude = $latitude;
        $this->longitude = $longitude;
    }

    /**
     * @return string
     */
    public function getAddress(): string
    {
        return $this->address;
    }

    /**
     * @return string
     */
    public function getId(): string
    {
        return $this->id;
    }

    /**
     * @return float|null
     */
    public function getLatitude(): ?float
    {
        return $this->latitude;
    }

    /**
     * @return float|null
     */
    public function getLongitude(): ?float
    {
        return $this->longitude;
    }
}
