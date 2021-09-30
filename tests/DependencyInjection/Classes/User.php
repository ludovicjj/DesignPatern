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

    /** @var array $info */
    private $info;

    /**
     * @var string|null
     */
    private $other;

    public function __construct(
        string $lastname,
        string $firstname,
        array $info,
        string $other = null
    )
    {
        $this->firstname = $firstname;
        $this->lastname = $lastname;
        $this->birthday = new DateTimeImmutable("now");
        $this->info = $info;
        $this->other = $other;
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

    /**
     * @return string|null
     */
    public function getOther()
    {
        return $this->other;
    }
}