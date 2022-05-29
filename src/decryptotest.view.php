<?php
require_once(APP_BASE_PATH."view/common/game.view.php");
require_once("models/dictionary-random-picker.php");

class view_decryptotest_decryptotest extends game_view
{
    function getGameName(): string {
        return "decryptotest";
    }

    function build_page( $viewArgs )
    {
    }
}


