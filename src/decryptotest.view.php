<?php
require_once( APP_BASE_PATH."view/common/game.view.php" );
require_once("modules/dictionary-random-picker.php");
require_once("words.php");

class view_decryptotest_decryptotest extends game_view
{
    function getGameName() {
        return "decryptotest";
    }

    function build_page( $viewArgs )
    {
        // Get players & players number
        $players = $this->game->loadPlayersBasicInfos();
        $players_nbr = count( $players );

        $rounds = $this->getRounds();
        $rounds_nbr = count($rounds);
        $this->tpl['ROUNDS_NBR'] = $rounds_nbr;

        $teams = [
            new Team('team white'),
            new Team('team black')
        ];

        $words = str_getcsv(french);
        $dictionary = new DictionaryRandomPicker($words);

        $this->tpl['WORD1'] = $dictionary->pick();
        $this->tpl['WORD2'] = $dictionary->pick();
        $this->tpl['WORD3'] = $dictionary->pick();
        $this->tpl['WORD4'] = $dictionary->pick();

        /*
                $template1 = '';
                $this->page->begin_block($template1, "team");
                foreach ($teams as $team) {
                    $this->page->insert_block("team", [
                        'NAME' => $team->name
                    ]);
                }

                $template2 = self::getGameName() . "_" . self::getGameName();
                $this->page->begin_block($template2, "round");
                for ($round=0; $round<$rounds_nbr; $round++) {
                    for ($hint=0; $hint<3; $hint++) {
                        $this->page->insert_block("round", [
                            "HINT" => $rounds[$round]["hints"][$hint]
                        ]);
                    }
                }*/
        /*********** Place your code below:  ************/
        /*
                $template2 = "decryptotest_decryptotest";

                $directions = array( 'S', 'W', 'N', 'E' );

                // this will inflate our player block with actual players data
                $this->page->begin_block($template2, "player");
                foreach ( $players as $player_id => $info ) {
                    $dir = array_shift($directions);
                    $this->page->insert_block("player", array ("PLAYER_ID" => $player_id,
                        "PLAYER_NAME" => $players [$player_id] ['player_name'],
                        "PLAYER_COLOR" => $players [$player_id] ['player_color'],
                        "DIR" => $dir ));
                }
                // this will make our My Hand text translatable
                $this->tpl['MY_HAND'] = self::_("My hand");
        */
        /*********** Do not change anything below this line  ************/
    }

    function getRounds() {
        return [
            [
                "hints" => ["word1a", "word1b", "word1c"],
                "guess_sequence" => [1, 3, 2],
                "correct_sequence" => [1, 3, 2]
            ],
            [
                "hints" => ["word2a", "word2b", "word2c"],
                "guess_sequence" => [3, 1, 4],
                "correct_sequence" => [3, 1, 4]
            ]
        ];
    }
}


