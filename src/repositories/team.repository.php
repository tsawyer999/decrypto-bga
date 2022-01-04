<?php

class TeamRepository
{
    private $db;

    function __construct($db) {
        $this->db = $db;
    }

    function getTeams() {
        $sql = "SELECT "
            . "team.team_id id, "
            . "team.team_name name, "
            . "GROUP_CONCAT(player.player_id) as members "
            . "FROM team "
            . "LEFT JOIN player "
            . "ON player.player_team_id = team.team_id "
            . "GROUP BY team.team_id";
        $teams = $this->db->getCollectionFromDb2($sql);

        return $teams;
    }

    function changeTeamName($teamId, $teamName) {
        $sql = "UPDATE team SET team_name='$teamName' WHERE team_id='$teamId'";
        $this->db->dbQuery2($sql);
    }

    function switchTeam($playerId) {
        $sql = "SELECT player_team_id FROM player WHERE player_id='$playerId'";
        $teamId = $this->db->getUniqueValueFromDb2($sql);
        $teamId = $teamId == 1 ? 2 : 1;

        $sql = "UPDATE player SET player_team_id='$teamId' WHERE player_id='$playerId'";
        $this->db->dbQuery2($sql);

        return $teamId;
    }
}