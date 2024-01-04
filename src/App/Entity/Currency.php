<?php
namespace App\Entity;

use JsonSerializable;

class Currency implements JsonSerializable
{
    private $code;
    private $name;

    public function __construct(string $code, string $name)
    {
        $this->code = $code;
        $this->name = $name;
    }

    public function getCode(): string
    {
        return $this->code;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function jsonSerialize() :array
    {
        return [
            'code'=>$this->code,
            'name' =>$this->name
        ];
    }
}
