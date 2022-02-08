<?php

class CodeRepository
{
    private $db;

    function __construct($db)
    {
        $this->db = $db;
    }
}
