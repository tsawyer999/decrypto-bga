<?php

require_once(APP_GAMEMODULE_PATH.'module/table/table.game.php');

require_once('models/team.model.php');

require_once('repositories/game.repository.php');
require_once('repositories/player.repository.php');
require_once('repositories/team.repository.php');

require_once('services/game.service.php');
require_once('services/player.service.php');
require_once('services/team.service.php');

class DecryptoTest extends Table
{
    private GameService $gameService;
    private PlayerService $playerService;
    private TeamService $teamService;

    function __construct()
    {
        parent::__construct();

        $teamRepository = new TeamRepository($this);
        $this->teamService = new TeamService($teamRepository);

        $gameRepository = new GameRepository($this);
        $this->gameService = new GameService($gameRepository, $teamRepository);

        $playerRepository = new PlayerRepository($this);
        $this->playerService = new PlayerService($playerRepository);

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
        $sql = "SELECT player_id id, player_score score, team_id team_id FROM player ";
        $result['players'] = self::getCollectionFromDb($sql);

        return $result;
    }

    protected function argPlayerTurn()
    {
        return array(
            'possibleMoves' => self::getPossibleMoves(self::getActivePlayerId())
        );
    }

    function getGameProgression()
    {
        // TODO: compute and return the game progression

        return 0;
    }

    function getCollectionFromDb2($sql)
    {
        return self::getCollectionFromDb($sql);
    }

    function dbQuery2($sql)
    {
        return self::DbQuery($sql);
    }

    function getObjectFromDb2($sql)
    {
        return self::getObjectFromDB($sql);
    }

    function getUniqueValueFromDb2($sql)
    {
        return self::getUniqueValueFromDB($sql);
    }

    function getObjectListFromDd2($sql)
    {
        return self::getObjectListFromDB($sql);
    }

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
        self::dump('name_of_variable', $hints);
    }

    function logMessage($message): void
    {
        self::debug($message);
    }

    function argTeamSetup()
    {
        $result = [];

        $result['players'] = $this->playerService->getPlayers();
        $result['teams'] = $this->teamService->getTeams();

        return $result;
    }

    function argGiveHints()
    {
        $result = [];

        $current_player_id = self::getCurrentPlayerId();

        $result['teams'] = $this->teamService->getTeams();
        $result['words'] = $this->gameService->getWordsForPlayer($current_player_id);
        $result['code'] = [1, 4, 2];

        return $result;
    }

    function stBeginGame()
    {
        $this->logMessage("stBeginGame");
        $sequence_length = 3;
        $param_number_words = 4;

        $this->gameService->startGame($sequence_length, $param_number_words);

        $this->gamestate->nextState('beginTurn');
    }

    function stBeginTurn()
    {
        $this->logMessage("stBeginTurn");
        $this->gameService->moveToNextTurn();

        $this->gamestate->nextState("giveHints");
    }

    function stGiveHints() {
        $playerId = $this->gameService->getPlayerIdForGiveHints();

        self::dump('=== name_of_variable ===', $playerId);

        $this->gamestate->changeActivePlayer($playerId);
    }

    function stGuessHints() {
        $this->gamestate->setAllPlayersMultiactive();
    }

    function stEndTurn()
    {
        if (true)
        {
            $this->gamestate->nextState( "newTurn" );
        } else
        {
            $this->gamestate->nextState( "gameEnd" );
        }
    }

    function zombieTurn($state, $active_player)
    {
        $statename = $state['name'];

        if ($state['type'] === "activeplayer")
        {
            switch ($statename)
            {
                default:
                    $this->gamestate->nextState("zombiePass");
                    break;
            }

            return;
        }

        if ($state['type'] === "multipleactiveplayer")
        {
            // Make sure player is in a non blocking status for role turn
            $this->gamestate->setPlayerNonMultiactive($active_player, '');

            return;
        }

        throw new feException("Zombie mode not supported at this game state: ".$statename);
    }

    function upgradeTableDb($from_version)
    {
    }
}
