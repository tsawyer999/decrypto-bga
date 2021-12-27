<?php
/**
 *------
 * BGA framework: © Gregory Isabelli <gisabelli@boardgamearena.com> & Emmanuel Colin <ecolin@boardgamearena.com>
 * DecryptoTest implementation : © <Your name here> <Your email address here>
 *
 * This code has been produced on the BGA studio platform for use on https://boardgamearena.com.
 * See http://en.doc.boardgamearena.com/Studio for more information.
 * -----
 *
 * decryptotest.action.php
 *
 * DecryptoTest main action entry point
 *
 *
 * In this file, you are describing all the methods that can be called from your
 * user interface logic (javascript).
 *
 * If you define a method "myAction" here, then you can call it from your javascript code with:
 * this.ajaxcall( "/decryptotest/decryptotest/myAction.html", ...)
 *
 */

class action_decryptotest extends APP_GameAction
{
    // Constructor: please do not modify
    public function __default()
    {
        if (self::isArg('notifwindow')) {
            $this->view = "common_notifwindow";
            $this->viewArgs['table'] = self::getArg("table", AT_posint, true);
        } else {
            $this->view = "decryptotest_decryptotest";
            self::trace("Complete reinitialization of board game");
        }
    }

    public function changeTeamName()
    {
        self::setAjaxMode();
        $teamId = self::getArg( "teamId", AT_posint, true );
        $teamName = self::getArg( "name", AT_alphanum_dash, true );

        $this->game->changeTeamName($teamId, $teamName);
        self::ajaxResponse();
    }

    public function completeTeamSetup()
    {
        self::setAjaxMode();
        $this->game->completeTeamSetup();
        self::ajaxResponse();
    }

    public function switchTeam()
    {
        self::setAjaxMode();
        $this->game->switchTeam();
        self::ajaxResponse();
    }

    public function giveHints()
    {
        self::setAjaxMode();
//        $hints = self::getArg( "hints", AT_json, true );
        $hints = "";
        $this->game->giveHints($hints);
        self::ajaxResponse();
    }

    /*

    Example:

    public function myAction()
    {
        self::setAjaxMode();

        // Retrieve arguments
        // Note: these arguments correspond to what has been sent through the javascript "ajaxcall" method
        $arg1 = self::getArg( "myArgument1", AT_posint, true );
        $arg2 = self::getArg( "myArgument2", AT_posint, true );

        // Then, call the appropriate method in your game logic, like "playCard" or "myAction"
        $this->game->myAction( $arg1, $arg2 );

        self::ajaxResponse( );
    }

    */
}
