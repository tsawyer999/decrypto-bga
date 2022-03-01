<?php
$machinestates = array(
    1 => array(
        "name" => "gameSetup",
        "description" => "",
        "type" => "manager",
        "action" => "stGameSetup",
        "transitions" => array("teamSetup" => 10)
    ),
    10 => array(
        "name" => "teamSetup",
        "description" => clienttranslate('${actplayer} must choose a team'),
        "descriptionmyturn" => clienttranslate('${you} must choose a team!'),
        "type" => "multipleactiveplayer",
        "args" => "argTeamSetup",
        "possibleactions" => array( "changeTeamName", "completeTeamSetup", "switchTeam" ),
        "transitions" => array( "beginGame" => 20 )
    ),
    20 => array(
        "name" => "beginGame",
        "description" => "",
        "type" => "game",
        "action" => "stBeginGame",
        "transitions" => array( "beginTurn" => 30 )
    ),
    30 => array(
        "name" => "beginTurn",
        "description" => "",
        "type" => "game",
        "action" => "stBeginTurn",
        "transitions" => array( "giveHints" => 40 )
    ),
    40 => array(
        "name" => "giveHints",
        "description" => clienttranslate('${actplayer} must supply hints'),
        "descriptionmyturn" => clienttranslate('${you} must supply hints'),
        "type" => "multipleactiveplayer",
        "possibleactions" => array( "giveHints" ),
        "action" => "stGiveHints",
        "transitions" => array( "guessHints" => 50 )
    ),
    50 => array(
        "name" => "guessHints",
        "description" => clienttranslate('${actplayer} must guess hints'),
        "descriptionmyturn" => clienttranslate('${you} must guess hints!'),
        "type" => "multipleactiveplayer",
        "possibleactions" => array( "guessHints" ),
        "action" => "stGuessHints",
        "transitions" => array( "endTurn" => 60 )
    ),
    60 => array(
        "name" => "endTurn",
        "description" => "",
        "type" => "game",
        "action" => "stEndTurn",
        "transitions" => array( "beginTurn" => 30, "gameEnd" => 99 )
    ),
    99 => array(
        "name" => "gameEnd",
        "description" => clienttranslate("End of game"),
        "type" => "manager",
        "action" => "stGameEnd",
        "args" => "argGameEnd"
    )
);



