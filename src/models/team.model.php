<?php

class Team
{
    public $id;
    public $name;
    public $orderId;
    public $playerIds;
    public $words;

    function __construct(int $id, string $name, int $orderId, array $playerIds)
    {
        $this->id = $id;
        $this->name = $name;
        $this->orderId = $orderId;
        $this->playerIds = $playerIds;
        $this->words = [];
    }
}
