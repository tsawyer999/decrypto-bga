<?php

class Turn
{
    public int $id;
    public int $round_number;
    public int $turn_number;

    function __construct(int $round_number, int $turn_number)
    {
        $this->round_number = $round_number;
        $this->turn_number = $turn_number;
    }
}
