<?php

namespace JanWennrich\BoardGameGeekApi\User;

final readonly class User
{
    public function __construct(
        private int $id,
        private string $name,
        private ?string $firstName,
        private ?string $lastName,
        private ?string $avatarLink,
        private ?int $yearRegistered,
        private ?string $lastLogin,
        private ?string $stateOrProvince,
        private ?string $country,
        private ?string $webAddress,
        private ?string $xboxAccount,
        private ?string $wiiAccount,
        private ?string $psnAccount,
        private ?string $battleNetAccount,
        private ?string $steamAccount,
        private ?int $marketRating,
        private ?int $tradeRating,
        private ?UserBuddies $userBuddies,
        private ?UserGuilds $userGuilds,
        private ?UserRanking $top,
        private ?UserRanking $hot,
    ) {}

    public function getId(): int
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getFirstName(): ?string
    {
        return $this->firstName;
    }

    public function getLastName(): ?string
    {
        return $this->lastName;
    }

    public function getAvatarLink(): ?string
    {
        return $this->avatarLink;
    }

    public function getYearRegistered(): ?int
    {
        return $this->yearRegistered;
    }

    public function getLastLogin(): ?string
    {
        return $this->lastLogin;
    }

    public function getStateOrProvince(): ?string
    {
        return $this->stateOrProvince;
    }

    public function getCountry(): ?string
    {
        return $this->country;
    }

    public function getWebAddress(): ?string
    {
        return $this->webAddress;
    }

    public function getXboxAccount(): ?string
    {
        return $this->xboxAccount;
    }

    public function getWiiAccount(): ?string
    {
        return $this->wiiAccount;
    }

    public function getPsnAccount(): ?string
    {
        return $this->psnAccount;
    }

    public function getBattleNetAccount(): ?string
    {
        return $this->battleNetAccount;
    }

    public function getSteamAccount(): ?string
    {
        return $this->steamAccount;
    }

    public function getMarketRating(): ?int
    {
        return $this->marketRating;
    }

    public function getTradeRating(): ?int
    {
        return $this->tradeRating;
    }

    public function getBuddies(): ?UserBuddies
    {
        return $this->userBuddies;
    }

    public function getGuilds(): ?UserGuilds
    {
        return $this->userGuilds;
    }

    public function getTop(): ?UserRanking
    {
        return $this->top;
    }

    public function getHot(): ?UserRanking
    {
        return $this->hot;
    }
}
