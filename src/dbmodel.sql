
-- ------
-- BGA framework: © Gregory Isabelli <gisabelli@boardgamearena.com> & Emmanuel Colin <ecolin@boardgamearena.com>
-- DecryptoTest implementation : © <Sébastien D'Errico> <sebastien@hollox.net>
--
-- This code has been produced on the BGA studio platform for use on http://boardgamearena.com.
-- See http://en.boardgamearena.com/#!doc/Studio for more information.
-- -----

-- dbmodel.sql

-- This is the file where you are describing the database schema of your game
-- Basically, you just have to export from PhpMyAdmin your table structure and copy/paste
-- this export here.
-- Note that the database itself and the standard tables ("global", "stats", "gamelog" and "player") are
-- already created and must not be created here

-- Note: The database schema is created from this file when the game starts. If you modify this file,
--       you have to restart a game to see your changes in database.

-- ----------------------------------------------------
-- T E A M
--
CREATE TABLE IF NOT EXISTS `team` (
    `team_id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
    `team_name` VARCHAR(50) NOT NULL,
    PRIMARY KEY (`team_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;

INSERT INTO `team` ( team_id, team_name )
VALUES ( 1, 'team1' ),
       ( 2, 'team2' );

-- ----------------------------------------------------
-- W O R D
--
CREATE TABLE IF NOT EXISTS `word`
(
    `word_team_id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
    `word_position` INT(10) UNSIGNED NOT NULL,
    `word_value` VARCHAR(50) NOT NULL,
    PRIMARY KEY (`word_team_id`, `word_position`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;

-- ----------------------------------------------------
-- S E Q U E N C E
--
CREATE TABLE IF NOT EXISTS `sequence`
(
    `sequence_round_id` INT(10) UNSIGNED NOT NULL,
    `sequence_position` INT(10) UNSIGNED NOT NULL,
    `sequence_value` VARCHAR(50) NOT NULL,
    PRIMARY KEY (`sequence_round_id`, `sequence_position`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;

-- ----------------------------------------------------
-- G U E S S
--
CREATE TABLE IF NOT EXISTS `guess`
(
    `guess_round_id` INT(10) UNSIGNED NOT NULL,
    `guess_team_id` INT(10) UNSIGNED NOT NULL,
    `guess_sequence_position` INT(10) UNSIGNED NOT NULL,
    `guess_sequence_value` INT(10) UNSIGNED NOT NULL,
    PRIMARY KEY (`guess_round_id`, `guess_team_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;

-- ----------------------------------------------------
-- T O K E N
--
CREATE TABLE IF NOT EXISTS `token`
(
    `token_round_id` INT(10) UNSIGNED NOT NULL,
    `token_team_id` INT(10) UNSIGNED NOT NULL,
    `token_type_id` INT(10) UNSIGNED NOT NULL,
    PRIMARY KEY (`token_round_id`, `token_team_id`, `token_type_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;

-- ----------------------------------------------------
-- P L A Y E R
--
ALTER TABLE `player` ADD `player_team_id` INT(10) UNSIGNED NOT NULL;
ALTER TABLE `player` ADD CONSTRAINT fk_player_team_id FOREIGN KEY (player_team_id) REFERENCES team (team_id);
