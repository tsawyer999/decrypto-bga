<?php

/**
 *------
 * BGA framework: © Gregory Isabelli <gisabelli@boardgamearena.com> & Emmanuel Colin <ecolin@boardgamearena.com>
 * DecryptoTest implementation : © <sebastien derrico> <sderrico@hollox.net>
 *
 * This code has been produced on the BGA studio platform for use on http://boardgamearena.com.
 * See http://en.boardgamearena.com/#!doc/Studio for more information.
 * -----
 *
 * gameoptions.inc.php
 *
 * DecryptoTest game options description
 *
 * In this file, you can define your game options (= game variants).
 *
 * Note: If your game has no variant, you don't have to modify this file.
 *
 * Note²: All options defined in this file should have a corresponding "game state labels"
 *        with the same ID (see "initGameStateLabels" in decryptotest.game.php)
 *
 * !! It is not a good idea to modify this file when a game is running !!
 *
 */

$game_options = array(
    100 => array(
        'name' => totranslate('How team are matches'),
        'values' => array(

            // A simple value for this option:
            1 => array( 'name' => totranslate('randomize') ),

            // A simple value for this option.
            // If this value is chosen, the value of "tmdisplay" is displayed in the game lobby
            2 => array( 'name' => totranslate('people decide') ),
        ),
        'default' => 1
    )
);


