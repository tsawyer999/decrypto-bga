<?php

class Hint
{
    public int $id;
    public int $turn_id;
    public int $player_id;
    public array $value;

    function __construct()
    {
        $this->id = 0;
        $this->turn_id = 0;
        $this->player_id = 0;
        $this->value = [];
    }
}
