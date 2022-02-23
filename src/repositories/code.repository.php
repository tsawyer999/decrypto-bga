<?php

class CodeRepository
{
    private $db;

    function __construct(DecryptoTest $db)
    {
        $this->db = $db;
    }

    function newWord(int $teamId, int $position, string $value): void
    {
        $sql = "INSERT INTO team "
            . "("
            . "team_name, "
            . "team_order_id "
            . ") VALUES ("
            . "'" . $name . "',"
            . "0"
            . ")";

        $this->db->dbQuery2($sql);
    }
}
