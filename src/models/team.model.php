<?php

class Team
{
    public int $id;
    public string $name;
    public int $orderId;
    public array $playerIds;

    function __construct(int $id, string $name, int $orderId, array $playerIds)
    {
        $this->id = $id;
        $this->name = $name;
        $this->orderId = $orderId;
        $this->playerIds = $playerIds;
    }
}
