<?php

require_once(APP_GAMEMODULE_PATH.'module/table/table.game.php');

require_once('models/team.model.php');

require_once('repositories/games.repository.php');
require_once('repositories/players.repository.php');
require_once('repositories/teams.repository.php');

require_once('services/games.service.php');
require_once('services/players.service.php');
require_once('services/teams.service.php');

class DecryptoTest extends Table
{
    private GamesService $gamesService;
    private PlayersService $playersService;
    private TeamsService $teamsService;

    function __construct()
    {
        parent::__construct();

        $teamsRepository = new TeamsRepository($this);
        $this->teamsService = new TeamsService($teamsRepository);

        $gamesRepository = new GamesRepository($this);
        $this->gamesService = new GamesService($gamesRepository, $teamsRepository);

        $playersRepository = new PlayersRepository($this);
        $this->playersService = new PlayersService($playersRepository);

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
            $this->teamsService->createTeam();
        }

        $gameinfos = self::getGameinfos();
        $default_colors = $gameinfos['player_colors'];
        $this->playersService->savePlayers($players, $param_number_team, $default_colors);

        self::reattributeColorsBasedOnPreferences($players, $gameinfos['player_colors']);
        self::reloadPlayersBasicInfos();

        $this->gamestate->setAllPlayersMultiactive();
    }

    protected function getAllDatas()
    {
        $result = [];

        $result['players'] = $this->playersService->getPlayers();

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
        return 0;
    }

    function changeTeamName(int $teamId, string $teamName) {
        $this->teamsService->changeTeamName($teamId, $teamName);

        $playerName = $this->getCurrentPlayerName();
        self::notifyAllPlayers('onChangeteamName', "$playerName change team $teamId name to $teamName", array(
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
        $teamId = $this->teamsService->switchTeam($playerId);

        $playerName = $this->getCurrentPlayerName();
        self::notifyAllPlayers('onSwitchTeam', "$playerName switch to team $teamId", array(
            'playerId' => $playerId,
            'playerName' => $playerName,
            'teamId' => $teamId
        ));
    }

    function giveHints($hints)
    {
        $player_id = self::getCurrentPlayerId();
        $this->gamesService->giveHints($player_id, $hints);

        $this->gamestate->nextState('guessHints');
    }

    function changeGuessSelectorIndex(int $hintIndex, int $selectorIndex)
    {
        $current_player_id = self::getCurrentPlayerId();

        self::notifyAllPlayers('onChangeGuessSelectorIndex', "SOMETHING HAPPEN", array(
            'hintIndex' => $hintIndex,
            'selectorIndex' => $selectorIndex
        ));
    }

    function logMessage(string $message): void
    {
        self::debug($message);
    }

    function argTeamSetup()
    {
        $result = [];

        $result['players'] = $this->playersService->getPlayers();
        $result['teams'] = $this->teamsService->getTeams();

        return $result;
    }

    function argGiveHints()
    {
        $result = [];
        $current_player_id = self::getCurrentPlayerId();

        $result['teams'] = $this->teamsService->getTeams();
        $result['words'] = $this->gamesService->getWordsForPlayer($current_player_id);
        $result['code'] = [1, 4, 2];

        return $result;
    }

    function argGuessHints()
    {
        $result = [];
        $current_player_id = self::getCurrentPlayerId();

        $result['teams'] = $this->teamsService->getTeams();
        $result['words'] = $this->gamesService->getWordsForPlayer($current_player_id);
        $result['hints'] = $this->gamesService->getHintsForCurrentTurn();

        return $result;
    }

    function stBeginGame()
    {
        $this->logMessage("stBeginGame");
        $sequence_length = 3;
        $param_number_words = 4;

        $this->gamesService->startGame($sequence_length, $param_number_words);

        $this->gamestate->nextState('beginTurn');
    }

    function stBeginTurn()
    {
        $this->logMessage("stBeginTurn");
        $this->gamesService->moveToNextTurn();

        $playerId = $this->gamesService->getPlayerIdForGiveHints();
        $this->gamestate->changeActivePlayer($playerId);

        $this->gamestate->nextState("giveHints");
    }

    function stGiveHints() {
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

    function getCollectionFromDb2(string $sql)
    {
        return self::getCollectionFromDb($sql);
    }

    function dbQuery2(string $sql)
    {
        return self::DbQuery($sql);
    }

    function getObjectFromDb2(string $sql)
    {
        return self::getObjectFromDB($sql);
    }

    function getUniqueValueFromDb2(string $sql)
    {
        return self::getUniqueValueFromDB($sql);
    }

    function getObjectListFromDd2(string $sql)
    {
        return self::getObjectListFromDB($sql);
    }
}
