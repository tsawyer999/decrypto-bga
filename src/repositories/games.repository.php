<?php
require_once(__DIR__ . "/../data/words.php");
require_once(__DIR__ . "/../models/code.model.php");
require_once(__DIR__ . "/../models/turn.model.php");

class GamesRepository
{
    private DecryptoTest $db;

    function __construct(DecryptoTest $db)
    {
        $this->db = $db;
    }

    public function saveCodes(array $codes): void
    {
        $sql = "INSERT INTO codes (id, value) VALUES ";
        $values = array();
        $id = 0;
        foreach ($codes as $code) {
            $id++;
            $values[] = "(". $id .",'". json_encode($code)."')";
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

    public function saveTurn(Turn $turn): void
    {
        $sql = "INSERT INTO turns ("
            . "round_number, "
            . "turn_number "
            . ") VALUES ("
            . $turn->round_number . ","
            . $turn->turn_number
            . ")";

        $this->db->dbQuery2($sql);
    }

    public function getCurrentTurn(): Turn
    {
        $sql = "SELECT "
            . "turns.id, "
            . "turns.round_number, "
            . "turns.turn_number "
            . "FROM turns "
            . "ORDER BY "
            . "turns.round_number DESC, "
            . "turns.turn_number DESC "
            . "LIMIT 1";

        $turn = $this->db->getObjectFromDb2($sql);
        return $this->convertTurnRecordToModel($turn);
    }

    private function convertTurnRecordToModel(array $t): Turn
    {
        $turn = new Turn($t['round_number'], $t['turn_number']);
        $turn->id = $t['id'];

        return $turn;
    }

    public function saveHints(Hint $hint)
    {
        $sql = "INSERT INTO hints ("
            . "turn_id,"
            . "player_id,"
            . "value"
            . ") VALUES ("
            . $hint->turn_id . ","
            . $hint->player_id . ","
            . "'" . json_encode($hint->value) . "'"
            . ")";

        $this->db->dbQuery2($sql);
    }

    public function getHints(int $turnId): array
    {
        $sql = "SELECT hints.value "
            . "FROM hints "
            . "WHERE hints.turn_id=" . $turnId;

        $value = $this->db->getUniqueValueFromDb2($sql);
        return json_decode($value);
    }

    public function getAvailableCodes(int $player_id): array
    {
        $sql = "SELECT "
            . "codes.id, "
            . "codes.value "
            . "FROM codes "
            . "LEFT JOIN "
            . "(SELECT code_draws.code_id, player.team_id "
            . "FROM code_draws "
            . "INNER JOIN player "
            . "ON player.player_id = code_draws.player_id "
            . "WHERE player.team_id = (SELECT team_id FROM player WHERE player_id = " . $player_id . ")) AS code_draws_teams "
            . "ON codes.id = code_draws_teams.code_id "
            . "WHERE code_id IS NULL";

        $codes = $this->db->getCollectionFromDb2($sql);

        return $this->convertCodesRecordToModel($codes);
    }

    private function convertCodesRecordToModel(array $codeList): array
    {
        $codes = [];
        foreach ($codeList as $t)
        {
            $code = $this->convertCodeRecordToModel($t);
            array_push($codes, $code);
        }

        return $codes;
    }

    private function convertCodeRecordToModel(array $t): Code
    {
        $this->db->logMessage("code_id:" . $t['id'] . " value:" . $t['value']);
        return new Code($t['id'], json_decode($t['value']));
    }

    public function saveDrawCode($turnId, $codeId, $playerId): void
    {
        $sql = "INSERT INTO code_draws ("
            . "turn_id,"
            . "code_id,"
            . "player_id"
            . ") VALUES ("
            . $turnId . ","
            . $codeId . ","
            . $playerId
            . ")";

        $this->db->dbQuery2($sql);
    }
}
