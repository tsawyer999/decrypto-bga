<?php
require_once(__DIR__ . "/../data/words.php");

class CodeRepository
{
    private DecryptoTest $db;

    function __construct(DecryptoTest $db)
    {
        $this->db = $db;
    }

    public function saveCodes(array $codes): void
    {
        $sql = "INSERT INTO code (code_value) VALUES ";
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
        $sql = "SELECT word_draw.word_draw_value "
            . "FROM word_draw "
            . "LEFT JOIN teams "
            . "ON team.team_id = word_draw.word_draw_team_id "
            . "LEFT JOIN player "
            . "ON player.team_id = teams.team_id "
            . "WHERE player.player_id = " . $player_id;
        $words = $this->db->getUniqueValueFromDb2($sql);
        if ($words == null) {
            return ['no word'];
        }
        return json_decode($words);
    }

    public function saveWords(int $teamId, array $words): void
    {
        $sql = "INSERT INTO word_draw ("
            . "word_draw_team_id, "
            . "word_draw_value "
            . ") VALUES ("
            . $teamId . ","
            . "'" . json_encode($words) . "'"
            . ")";

        $this->db->dbQuery2($sql);
    }
}
