<?php

use PHPUnit\Framework\TestCase;

require_once("models.php");
require_once("words.php");

class models extends TestCase
{
    public function test1(): void
    {
        $this->assertEquals(
            1+1,
            2
        );
    }
/*
    public function test2(): void {
        $words = str_getcsv(french);
        $dictionary = new DictionaryRandomPicker($words);
        $result = $dictionary->pick();
        $this->assertEquals(
            "hoho",
            $result
        );
    }*/
}
