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

    public function __construct()
    {
        parent::__construct();

        $teamsRepository = new TeamsRepository($this);
        $this->teamsService = new TeamsService($teamsRepository);

        $gamesRepository = new GamesRepository($this);
        $this->gamesService = new GamesService($gamesRepository, $teamsRepository);

        $playersRepository = new PlayersRepository($this);
        $this->playersService = new PlayersService($playersRepository);

        self::initGameStateLabels([]);
    }

    protected function getGameName(): string
    {
        return "decryptotest";
    }

    protected function setupNewGame($players, $options = [])
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

    public function getGameProgression(): int
    {
        return 0;
    }

    public function zombieTurn($state, $active_player): void
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

    public function logMessage(string $message): void
    {
        self::debug($message);
    }

    public function actChangeTeamName(int $teamId, string $teamName): void {
        $this->teamsService->changeTeamName($teamId, $teamName);

        $playerName = $this->getCurrentPlayerName();
        self::notifyAllPlayers('onChangeteamName', "$playerName change team $teamId name to $teamName", array(
            'teamId' => $teamId,
            'teamName' => $teamName
        ));
    }

    public function actCompleteTeamSetup(): void {
        $playerId = $this->getCurrentPlayerId();
        $this->gamestate->setPlayerNonMultiactive($playerId, 'beginGame');
    }

    public function actSwitchTeam(): void {
        $playerId = $this->getCurrentPlayerId();
        $teamId = $this->teamsService->switchTeam($playerId);

        $playerName = $this->getCurrentPlayerName();
        self::notifyAllPlayers('onSwitchTeam', "$playerName switch to team $teamId", array(
            'playerId' => $playerId,
            'playerName' => $playerName,
            'teamId' => $teamId
        ));
    }

    public function actGiveHints($hints): void
    {
        $player_id = self::getCurrentPlayerId();
        $this->gamesService->giveHints($player_id, $hints);

        $this->gamestate->nextState('guessHints');
    }

    public function actChangeGuessSelectorIndex(int $hintIndex, int $selectorIndex): void
    {
        $current_player_id = self::getCurrentPlayerId();

        self::notifyAllPlayers('onChangeGuessSelectorIndex', "SOMETHING HAPPEN", array(
            'hintIndex' => $hintIndex,
            'selectorIndex' => $selectorIndex
        ));
    }

    public function argPlayerTurn(): array
    {
        $result = [];

        $result['possibleMoves'] = self::getPossibleMoves(self::getActivePlayerId());
        return $result;
    }

    public function argTeamSetup(): array
    {
        $result = [];

        $result['players'] = $this->playersService->getPlayers();
        $result['teams'] = $this->teamsService->getTeams();

        return $result;
    }

    public function argGiveHints(): array
    {
        $result = [];
        $current_player_id = self::getCurrentPlayerId();

        $result['teams'] = $this->teamsService->getTeams();
        $result['words'] = $this->gamesService->getWordsForPlayer($current_player_id);
        $result['previousHints'] = $this->gamesService->getPreviousHints();

        if ($current_player_id == $this->gamesService->getPlayerIdForGiveHints())
        {
            $result['code'] = $this->gamesService->newCodeForPlayer($current_player_id);
        }
        else
        {
            $result['code'] = null;
        }

        return $result;
    }

    public function argGuessHints(): array
    {
        $result = [];
        $current_player_id = self::getCurrentPlayerId();

        $result['teams'] = $this->teamsService->getTeams();
        $result['words'] = $this->gamesService->getWordsForPlayer($current_player_id);
        $result['previousHints'] = $this->gamesService->getPreviousHints();
        $result['hints'] = $this->gamesService->getHintsForCurrentTurn();

        return $result;
    }

    public function stBeginGame(): void
    {
        $this->logMessage("stBeginGame");
        $sequence_length = 3;
        $param_number_words = 4;

        $this->gamesService->startGame($sequence_length, $param_number_words);

        $this->gamestate->nextState('beginTurn');
    }

    public function stBeginTurn(): void
    {
        $this->logMessage("stBeginTurn");
        $this->gamesService->moveToNextTurn();

        $playerId = $this->gamesService->getPlayerIdForGiveHints();
        $this->gamestate->changeActivePlayer($playerId);

        $this->gamestate->nextState("giveHints");
    }

    public function stGiveHints(): void
    {
    }

    public function stGuessHints(): void
    {
        $this->gamestate->setAllPlayersMultiactive();
    }

    public function stEndTurn(): void
    {
        if (true)
        {
            $this->gamestate->nextState( "newTurn" );
        } else
        {
            $this->gamestate->nextState( "gameEnd" );
        }
    }

    public function getCollectionFromDb2(string $sql): array
    {
        return self::getCollectionFromDb($sql);
    }

    public function dbQuery2(string $sql)
    {
        return self::DbQuery($sql);
    }

    public function getObjectFromDb2(string $sql)
    {
        return self::getObjectFromDB($sql);
    }

    public function getUniqueValueFromDb2(string $sql)
    {
        return self::getUniqueValueFromDB($sql);
    }

    public function getObjectListFromDd2(string $sql): array
    {
        return self::getObjectListFromDB($sql);
    }
}
