<?php

class Code
{
    public int $id;
    public array $value;

    function __construct(int $id, int $value)
    {
        $this->id = $id;
        $this->value = $value;
    }
}
