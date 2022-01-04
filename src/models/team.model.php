<?php

class Team
{
    public $id;
    public $name;
    public $playerIds;

    function __construct() {
        $this->playerIds = [];
    }
}