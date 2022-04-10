<?php

require_once(__DIR__ . "/../models/team.model.php");

class TeamRepository
{
    private DecryptoTest $db;

    function __construct(DecryptoTest $db)
    {
        $this->db = $db;
    }

    public function saveTeam(Team $team): void
    {
        $sql = "INSERT INTO teams "
            . "("
            . "name, "
            . "order_id "
            . ") VALUES ("
            . "'" . $team->name . "',"
            . $team->orderId
            . ")";

        $this->db->dbQuery2($sql);
    }

    public function getTeams(): array
    {
        $sql = "SELECT "
            . "teams.id, "
            . "teams.name, "
            . "teams.order_id, "
            . "GROUP_CONCAT(player.player_id) AS player_ids, "
            . "1 AS token_success, "
            . "1 AS token_fail "
            . "FROM teams "
            . "LEFT JOIN player "
            . "ON player.team_id = teams.id "
            . "GROUP BY teams.id";
        $teams = $this->db->getObjectListFromDd2($sql);

        return $this->convertTeamsRecordToModels($teams);
    }

    private function convertTeamsRecordToModels(array $teamList): array
    {
        $teams = [];
        foreach ($teamList as $t)
        {
            $team = $this->convertTeamsRecordToModel($t);
            array_push($teams, $team);
        }

        return $teams;
    }

    private function convertTeamsRecordToModel($t): Team
    {
        $playerIds = explode(',', $t['player_ids']);
        $team = new Team($t['id'], $t['name'], $t['order_id'], $playerIds);
        $team->tokens->success = $t['token_success'];
        $team->tokens->fail = $t['token_fail'];

        return $team;
    }

    public function changeTeamName(int $teamId, string $teamName): void {
        $sql = "UPDATE teams SET name='$teamName' WHERE id='$teamId'";
        $this->db->dbQuery2($sql);
    }

    public function switchTeam(int $playerId): int {
        $sql = "SELECT team_id FROM player WHERE player_id='$playerId'";
        $teamId = $this->db->getUniqueValueFromDb2($sql);
        $teamId = $teamId == 1 ? 2 : 1;

        $sql = "UPDATE player SET team_id='$teamId' WHERE player_id='$playerId'";
        $this->db->dbQuery2($sql);

        return $teamId;
    }
}
