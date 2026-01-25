<?php

namespace JanWennrich\BoardGameGeekApi\Guild;

final readonly class GuildLocation
{
    public function __construct(
        private string $addr1,
        private string $addr2,
        private string $city,
        private string $stateOrProvince,
        private string $postalCode,
        private string $country,
    ) {}

    public function getAddr1(): string
    {
        return $this->addr1;
    }

    public function getAddr2(): string
    {
        return $this->addr2;
    }

    public function getCity(): string
    {
        return $this->city;
    }

    public function getStateOrProvince(): string
    {
        return $this->stateOrProvince;
    }

    public function getPostalCode(): string
    {
        return $this->postalCode;
    }

    public function getCountry(): string
    {
        return $this->country;
    }
}
