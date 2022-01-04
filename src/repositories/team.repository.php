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
}