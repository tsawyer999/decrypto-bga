<?php

class PlayersRepository
{
    private DecryptoTest $db;

    function __construct(DecryptoTest $db)
    {
        $this->db = $db;
    }

    public function savePlayers(array $players, int $param_number_team, array $default_colors)
    {
        // Create players
        // Note: if you added some extra field on "player" table in the database (dbmodel.sql), you can initialize it there.
        $sql = "INSERT INTO player ("
            . "player_id,"
            . "player_color,"
            . "player_canal,"
            . "player_name,"
            . "player_avatar,"
            . "team_id "
            . ") VALUES ";

        $values = array();
        $index = 0;
        foreach ($players as $player_id => $player) {
            $color = array_shift($default_colors);
            $index++;
            $team_id = $index % $param_number_team + 1;
            $values[] = "('"
                . $player_id
                . "','$color','"
                . $player['player_canal']
                . "','" . addslashes($player['player_name'])
                . "','" . addslashes($player['player_avatar'])
                . "'," . $team_id
                . ")";
        }
        $sql .= implode(',', $values);
        $this->db->DbQuery2($sql);
    }

    public function getPlayers(): array
    {
        $sql = "SELECT "
            . "player_id id,"
            . "player_name name,"
            . "player_score score,"
            . "team_id "
            . "FROM player";

        return $this->db->getCollectionFromDb2($sql);
    }
}
