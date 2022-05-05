<?php

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

        $this->game->actChangeTeamName($teamId, $teamName);
        self::ajaxResponse();
    }

    public function completeTeamSetup()
    {
        self::setAjaxMode();
        $this->game->actCompleteTeamSetup();
        self::ajaxResponse();
    }

    public function switchTeam()
    {
        self::setAjaxMode();
        $this->game->actSwitchTeam();
        self::ajaxResponse();
    }

    public function giveHints()
    {
        self::setAjaxMode();
        $hints = self::getArg( "hints", AT_json, true );
        $this->game->actGiveHints($hints);
        self::ajaxResponse();
    }

    public function changeGuessSelectorIndex()
    {
        self::setAjaxMode();
        $hintIndex = self::getArg( "hintIndex", AT_posint, true );
        $selectorIndex = self::getArg( "selectorIndex", AT_posint, true );

        $this->game->actChangeGuessSelectorIndex($hintIndex, $selectorIndex);
        self::ajaxResponse();
    }
}
