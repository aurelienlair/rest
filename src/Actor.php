<?php

namespace Cast;

use Psr\Http\Message\ServerRequestInterface;
use Hashids\Hashids;

class Actor
{
    private $firstName;
    private $lastName;
    private $country;
    private $id;

    private function __construct($firstName, $lastName, $country, $id=null)
    {
        if (empty($id)) {
            $this->id = md5(uniqid(rand(), true));
        } else {
            $this->id = $id;
        }
        $this->firstName = $firstName;
        $this->lastName = $lastName;
        $this->country = $country;
    }

    public static function fromRequest(ServerRequestInterface $request)
    {
        $firstName = $request->getParsedBody()['firstname'];
        $lastName = $request->getParsedBody()['lastname'];
        $country = $request->getParsedBody()['country'];

        return new self($firstName, $lastName, $country);
    }

    public static function fromArray(array $parameters)
    {
        return new self(...array_values($parameters));
    }

    public function export()
    {
        return [
            'id' => $this->id,
            'firstname' => $this->firstName,
            'lastname' => $this->lastName,
            'country' => $this->country
        ];
    }
}
