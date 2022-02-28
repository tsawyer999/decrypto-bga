<?php

require_once(APP_GAMEMODULE_PATH.'module/table/table.game.php');

require_once('models/team.model.php');

require_once('repositories/code.repository.php');
require_once('repositories/player.repository.php');
require_once('repositories/team.repository.php');

require_once('services/code.service.php');
require_once('services/player.service.php');
require_once('services/team.service.php');

class DecryptoTest extends Table
{
    private $codeService;
    private $playerService;
    private $teamService;

    function __construct()
    {
        parent::__construct();

        $codeRepository = new CodeRepository($this);
        $this->codeService = new CodeService($codeRepository);

        $playerRepository = new PlayerRepository($this);
        $this->playerService = new PlayerService($playerRepository);

        $teamRepository = new TeamRepository($this);
        $this->teamService = new TeamService($teamRepository);

        self::initGameStateLabels(array());
    }

    protected function getGameName()
    {
        return "decryptotest";
    }

    protected function setupNewGame($players, $options = array())
    {
        $param_number_team = 2;
        for ($i = 1; $i <= $param_number_team; $i++)
        {
            $this->teamService->createTeam();
        }

        $gameinfos = self::getGameinfos();
        $default_colors = $gameinfos['player_colors'];
        $this->playerService->savePlayers($players, $param_number_team, $default_colors);

        self::reattributeColorsBasedOnPreferences($players, $gameinfos['player_colors']);
        self::reloadPlayersBasicInfos();

        $this->gamestate->setAllPlayersMultiactive();
    }

    protected function getAllDatas()
    {
        $result = array();

        // Get information about players
        // Note: you can retrieve some extra field you added for "player" table in "dbmodel.sql" if you need it.
        $sql = "SELECT player_id id, player_score score, player_team_id team_id FROM player ";
        $result['players'] = self::getCollectionFromDb($sql);

        // TODO: Gather all information about current game situation (visible by player $current_player_id).
        $current_player_id = self::getCurrentPlayerId();    // !! We must only return informations visible by this player !!

//        $result['words'] = $this->codeService->getWordsForPlayer($current_player_id);
        $result['teams'] = $this->teamService->getTeams();

        return $result;
    }

    protected function argPlayerTurn()
    {
        return array(
            'possibleMoves' => self::getPossibleMoves(self::getActivePlayerId())
        );
    }

    /*
        getGameProgression:

        Compute and return the current game progression.
        The number returned must be an integer beween 0 (=the game just started) and
        100 (= the game is finished or almost finished).

        This method is called each time we are in a game state with the "updateGameProgression" property set to true
        (see states.inc.php)
    */
    function getGameProgression()
    {
        // TODO: compute and return the game progression

        return 0;
    }


//////////////////////////////////////////////////////////////////////////////
//////////// Utility functions
////////////

    function getCollectionFromDb2($sql)
    {
        return self::getCollectionFromDb($sql);
    }

    function dbQuery2($sql)
    {
        return self::DbQuery($sql);
    }

    function getUniqueValueFromDb2($sql)
    {
        return self::getUniqueValueFromDB($sql);
    }

    function getObjectListFromDd2($sql)
    {
        return self::getObjectListFromDB($sql);
    }

//////////////////////////////////////////////////////////////////////////////
//////////// Player actions
////////////

    /*
        Each time a player is doing some game action, one of the methods below is called.
        (note: each method below must match an input method in decryptotest.action.php)
    */

    function changeTeamName($teamId, $teamName) {
        $this->teamService->changeTeamName($teamId, $teamName);

        $playerName = $this->getCurrentPlayerName();
        self::notifyAllPlayers('changeTeamName', "$playerName change team $teamId name to $teamName", array(
            'teamId' => $teamId,
            'teamName' => $teamName
        ));
    }

    function completeTeamSetup() {
        $playerId = $this->getCurrentPlayerId();
        $this->gamestate->setPlayerNonMultiactive($playerId, 'beginGame');
    }

    function switchTeam() {
        $playerId = $this->getCurrentPlayerId();
        $teamId = $this->teamService->switchTeam($playerId);

        $playerName = $this->getCurrentPlayerName();
        self::notifyAllPlayers('switchTeam', "$playerName switch to team $teamId", array(
            'playerId' => $playerId,
            'playerName' => $playerName,
            'teamId' => $teamId
        ));
    }

    function giveHints($hints)
    {
    }

//////////////////////////////////////////////////////////////////////////////
//////////// Game state arguments
////////////

    /*
        Here, you can create methods defined as "game state arguments" (see "args" property in states.inc.php).
        These methods function is to return some additional information that is specific to the current
        game state.
    */

    /*

    Example for game state "MyGameState":

    function argMyGameState()
    {
        // Get some values from the current game situation in database...

        // return values:
        return array(
            'variable1' => $value1,
            'variable2' => $value2,
            ...
        );
    }
    */

//////////////////////////////////////////////////////////////////////////////
//////////// Game state actions
////////////

    /*
        Here, you can create methods defined as "game state actions" (see "action" property in states.inc.php).
        The action method of state X is called everytime the current game state is set to X.
    */

    /*

    Example for game state "MyGameState":

    function stMyGameState()
    {
        // Do some stuff ...

        // (very often) go to another gamestate
        $this->gamestate->nextState( 'some_gamestate_transition' );
    }
    */

    function stBeginGame()
    {
//        $players = self::loadPlayersBasicInfos();
//        $playerId = key($players);
//        $this->gamestate->changeActivePlayer($playerId);

        $param_number_words = 4;
        $this->teamService->setWordsForAllTeams($param_number_words);

        $this->gamestate->nextState( 'beginTurn' );
    }

    function stBeginTurn()
    {
        $this->gamestate->nextState( "giveHints" );
    }

    function stGiveHints() {
        $this->gamestate->setAllPlayersMultiactive();
    }

    function stGuessHints() {
        $this->gamestate->setAllPlayersMultiactive();
    }

    function stEndTurn()
    {
        if (true) {
            $this->gamestate->nextState( "newTurn" );
        } else {
            $this->gamestate->nextState( "gameEnd" );
        }
//        $this->gamestate->setAllPlayersMultiactive();
    }

//////////////////////////////////////////////////////////////////////////////
//////////// Zombie
////////////

    /*
        zombieTurn:

        This method is called each time it is the turn of a player who has quit the game (= "zombie" player).
        You can do whatever you want in order to make sure the turn of this player ends appropriately
        (ex: pass).

        Important: your zombie code will be called when the player leaves the game. This action is triggered
        from the main site and propagated to the gameserver from a server, not from a browser.
        As a consequence, there is no current player associated to this action. In your zombieTurn function,
        you must _never_ use getCurrentPlayerId() or getCurrentPlayerName(), otherwise it will fail with a "Not logged" error message.
    */

    function zombieTurn($state, $active_player)
    {
        $statename = $state['name'];

        if ($state['type'] === "activeplayer") {
            switch ($statename) {
                default:
                    $this->gamestate->nextState("zombiePass");
                    break;
            }

            return;
        }

        if ($state['type'] === "multipleactiveplayer") {
            // Make sure player is in a non blocking status for role turn
            $this->gamestate->setPlayerNonMultiactive($active_player, '');

            return;
        }

        throw new feException("Zombie mode not supported at this game state: ".$statename);
    }

///////////////////////////////////////////////////////////////////////////////////:
////////// DB upgrade
//////////

    /*
        upgradeTableDb:

        You don't have to care about this until your game has been published on BGA.
        Once your game is on BGA, this method is called everytime the system detects a game running with your old
        Database scheme.
        In this case, if you change your Database scheme, you just have to apply the needed changes in order to
        update the game database and allow the game to continue to run with your new version.

    */

    function upgradeTableDb($from_version)
    {
        // $from_version is the current version of this game database, in numerical form.
        // For example, if the game was running with a release of your game named "140430-1345",
        // $from_version is equal to 1404301345

        // Example:
//        if( $from_version <= 1404301345 )
//        {
//            // ! important ! Use DBPREFIX_<table_name> for all tables
//
//            $sql = "ALTER TABLE DBPREFIX_xxxxxxx ....";
//            self::applyDbUpgradeToAllDB( $sql );
//        }
//        if( $from_version <= 1405061421 )
//        {
//            // ! important ! Use DBPREFIX_<table_name> for all tables
//
//            $sql = "CREATE TABLE DBPREFIX_xxxxxxx ....";
//            self::applyDbUpgradeToAllDB( $sql );
//        }
//        // Please add your future database scheme changes here
//
//
    }
}
