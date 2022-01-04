<?php

class Team
{
    public $id;
    public $name;
    public $playerIds;

    function __construct($id, $name, $playerIds)
    {
        $this->id = $id;
        $this->name = $name;
        $this->playerIds = $playerIds;
    }
}
