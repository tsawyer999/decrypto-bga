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
        "transitions" => array( "newRound" => 30 )
    ),
    30 => array(
        "name" => "newRound",
        "description" => clienttranslate('${actplayer} must guess for team1'),
        "descriptionmyturn" => clienttranslate('${you} must guess for team1!'),
        "type" => "activeplayer",
        "possibleactions" => array( "guess" ),
        "action" => "stNewRound",
        "transitions" => array( "playCard" => 20, "pass" => 20 )
    ),
    99 => array(
        "name" => "gameEnd",
        "description" => clienttranslate("End of game"),
        "type" => "manager",
        "action" => "stGameEnd",
        "args" => "argGameEnd"
    )
);



