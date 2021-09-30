<?php


namespace Tests\DependencyInjection\Classes;

use DateTimeImmutable;

class User
{
    /** @var string $firstname */
    private $firstname;

    /** @var string $lastname */
    private $lastname;

    /** @var DateTimeImmutable $birthday */
    private $birthday;

    private $info;

    public function __construct(
        string $lastname,
        string $firstname,
        $birthday,
        array $info
    )
    {
        $this->firstname = $firstname;
        $this->lastname = $lastname;
        $this->birthday = $birthday;
        $this->info = $info;
    }

    public function getFirstname(): string
    {
        return $this->firstname;
    }

    public function getLastname(): string
    {
        return $this->lastname;
    }

    public function getBirthday(): DateTimeImmutable
    {
        return $this->birthday;
    }

    public function getInfo(): array
    {
        return $this->info;
    }
}