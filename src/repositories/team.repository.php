<?php

require_once(__DIR__ . "/../models/team.model.php");

class TeamRepository
{
    private $db;

    function __construct(DecryptoTest $db)
    {
        $this->db = $db;
    }

    function saveTeam(Team $team): void
    {
        $sql = "INSERT INTO team "
            . "("
            . "team_name, "
            . "team_order_id, "
            . "team_words "
            . ") VALUES ("
            . "'" . $team->name . "',"
            . $team->orderId . ","
            . "'" . json_encode($team->words) . "'"
            . ")";

        $this->db->dbQuery2($sql);
    }

    function getTeams(): array
    {
        $sql = "SELECT "
            . "team.team_id id, "
            . "team.team_name name, "
            . "team.team_order_id order_id, "
            . "GROUP_CONCAT(player.player_id) as player_ids "
            . "FROM team "
            . "LEFT JOIN player "
            . "ON player.player_team_id = team.team_id "
            . "GROUP BY team.team_id";
        $teams = $this->db->getObjectListFromDd2($sql);

        return $this->convertTeamsRecordToModels($teams);
    }

    private function convertTeamsRecordToModels(array $teamList): array
    {
        $teams = [];
        foreach ($teamList as $t)
        {
            $playerIds = explode(',', $t['player_ids']);
            $team = new Team($t['id'], $t['name'], $t['order_id'], $playerIds);
            array_push($teams, $team);
        }

        return $teams;
    }

    function changeTeamName(int $teamId, string $teamName): void {
        $sql = "UPDATE team SET team_name='$teamName' WHERE team_id='$teamId'";
        $this->db->dbQuery2($sql);
    }

    function switchTeam(int $playerId): int {
        $sql = "SELECT player_team_id FROM player WHERE player_id='$playerId'";
        $teamId = $this->db->getUniqueValueFromDb2($sql);
        $teamId = $teamId == 1 ? 2 : 1;

        $sql = "UPDATE player SET player_team_id='$teamId' WHERE player_id='$playerId'";
        $this->db->dbQuery2($sql);

        return $teamId;
    }

    function getWords(): array
    {
        return str_getcsv(french);
    }

    function updateWords(int $teamId, array $words): void
    {
        $sql = "UPDATE team SET "
            . "team_words = '" . json_encode($words) . "' "
            . "WHERE team_id = " . $teamId;

        $this->db->dbQuery2($sql);
    }
}
