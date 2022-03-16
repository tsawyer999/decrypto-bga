<?php

class Tokens
{
    public int $success;
    public int $fail;

    function __construct()
    {
        $this->success = 0;
        $this->fail = 0;
    }
}