<?php

require_once(__DIR__ . "/../repositories/player.repository.php");

class PlayerService
{
    private $playerRepository;

    function __construct(PlayerRepository $playerRepository)
    {
        $this->playerRepository = $playerRepository;
    }

    function savePlayers(array $players, int $param_number_team, array $default_colors)
    {
        $this->playerRepository->savePlayers($players, $param_number_team, $default_colors);
    }
}
