<?php
require_once(__DIR__ . "/../data/words.php");

class GameRepository
{
    private DecryptoTest $db;

    function __construct(DecryptoTest $db)
    {
        $this->db = $db;
    }

    public function saveCodes(array $codes): void
    {
        $sql = "INSERT INTO codes (value) VALUES ";
        $values = array();

        foreach ($codes as $code) {
            $values[] = "('". json_encode($code)."')";
        }
        $sql .= implode(',', $values);
        $this->db->DbQuery2($sql);
    }

    public function getWords(): array
    {
        return str_getcsv(french);
    }

    public function getWordsForPlayer($player_id): array
    {
        $sql = "SELECT word_draws.value "
            . "FROM word_draws "
            . "LEFT JOIN teams "
            . "ON teams.id = word_draws.team_id "
            . "LEFT JOIN player "
            . "ON player.team_id = teams.id "
            . "WHERE player.player_id = " . $player_id;
        $words = $this->db->getUniqueValueFromDb2($sql);
        if ($words == null) {
            return ['no word'];
        }
        return json_decode($words);
    }

    public function saveWords(int $teamId, array $words): void
    {
        $sql = "INSERT INTO word_draws ("
            . "team_id, "
            . "value "
            . ") VALUES ("
            . $teamId . ","
            . "'" . json_encode($words) . "'"
            . ")";

        $this->db->dbQuery2($sql);
    }

    public function insertTurn(int $round_number, int $turn_number)
    {
        $sql = "INSERT INTO turns ("
            . "round_number, "
            . "turn_number "
            . ") VALUES ("
            . $round_number . ","
            . $turn_number
            . ")";

        $this->db->dbQuery2($sql);
    }
}
