<?php
$machinestates = array(
    1 => array(
        "name" => "gameSetup",
        "description" => "",
        "type" => "manager",
        "action" => "stGameSetup",
        "transitions" => array("" => 20)
    ),
    20 => array(
        "name" => "teamSetup",
        "description" => clienttranslate('${actplayer} must choose a team'),
        "descriptionmyturn" => clienttranslate('${you} must choose a team!'),
        "type" => "multipleactiveplayer",
        "possibleactions" => array( "changeTeamName", "completeTeamSetup", "switchTeam" ),
        "transitions" => array( "electEncryptor" => 30 )
    ),
    //
    // I N I T I A L I Z E   R O U N D
    //
    // TURN
    //      Encryptor
    //          turn_id | player_id
    //
    //      ROUND
    //          GiveHints
    //          GuessHints
    // insert turn data
    //
    // replace stElectEncryptor by stEnableEncryptor
    // update flow to go back at initializeRound
    //
    30 => array(
        "name" => "electEncryptor",
        "description" => "",
        "type" => "game",
        "action" => "stElectEncryptor",
        "transitions" => array( "giveHints" => 40 )
    ),
    40 => array(
        "name" => "giveHints",
        "description" => clienttranslate('${actplayer} must supply hints'),
        "descriptionmyturn" => clienttranslate('${you} must supply hints'),
        "type" => "activeplayer",
        "possibleactions" => array( "giveHints" ),
        "transitions" => array( "guessHints" => 50 )
    ),
    50 => array(
        "name" => "guessHints",
        "description" => clienttranslate('${actplayer} must guess hints'),
        "descriptionmyturn" => clienttranslate('${you} must guess hints!'),
        "type" => "multipleactiveplayer",
        "possibleactions" => array( "guessHints" ),
        "action" => "stGuessHints",
        "transitions" => array( "electEncryptor" => 30, "gameEnd" => 99 )
    ),
    99 => array(
        "name" => "gameEnd",
        "description" => clienttranslate("End of game"),
        "type" => "manager",
        "action" => "stGameEnd",
        "args" => "argGameEnd"
    )
);



