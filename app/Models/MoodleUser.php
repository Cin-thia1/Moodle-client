<?php

namespace App\Models;

class MoodleUser
{
    public int $id;
    public string $username;
    public string $firstname;
    public string $lastname;
    public string $fullname;
    public string $email;
    public string $department;
    public int $firstaccess;
    public int $lastaccess;
    public string $auth;
    public bool $suspended;
    public bool $confirmed;
    public string $lang;
    public string $theme;
    public string $timezone;
    public int $mailformat;
    public int $trackforums;
    public string $description;
    public int $descriptionformat;
    public string $profileimageurlsmall;
    public string $profileimageurl;

    public function __construct(array $userData)
    {
        foreach ($userData as $key => $value) {
            if (property_exists($this, $key)) {
                $this->$key = $value;
            }
        }
    }

    public static function fromArray(array $data): self
    {
        return new self($data);
    }

    public function toArray(): array
    {
        return get_object_vars($this);
    }
}