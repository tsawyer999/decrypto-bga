<?php

require_once(__DIR__ . "/../repositories/players.repository.php");

class PlayersService
{
    private PlayersRepository $playersRepository;

    function __construct(PlayersRepository $playerRepository)
    {
        $this->playersRepository = $playerRepository;
    }

    function savePlayers(array $players, int $param_number_team, array $default_colors)
    {
        $this->playersRepository->savePlayers($players, $param_number_team, $default_colors);
    }

    public function getPlayers(): array
    {
        return $this->playersRepository->getPlayers();
    }
}
