CREATE TABLE IF NOT EXISTS teams (
    id INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
    name VARCHAR(50) NOT NULL,
    order_id INT(10) NOT NULL,
    PRIMARY KEY (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;

CREATE TABLE IF NOT EXISTS word_draws (
    id INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
    value JSON NOT NULL,
    team_id INT(10) UNSIGNED NOT NULL,
    PRIMARY KEY (id),
    FOREIGN KEY (team_id) REFERENCES teams (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;

CREATE TABLE IF NOT EXISTS turns
(
    id INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
    round_number INT(10) UNSIGNED NOT NULL,
    turn_number INT(10) UNSIGNED NOT NULL,
    PRIMARY KEY (id),
    CONSTRAINT uc_round_turn_number UNIQUE (round_number, turn_number)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;

CREATE TABLE IF NOT EXISTS codes
(
    id INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
    value JSON NOT NULL,
    PRIMARY KEY (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;

/*
CREATE TABLE IF NOT EXISTS hint
(
    hint_id INT(10) UNSIGNED NOT NULL,
    hint_code_id INT(10) UNSIGNED NOT NULL,
    hint_value VARCHAR(50) NOT NULL,
    hint_player_id INT(10) UNSIGNED NOT NULL,
    PRIMARY KEY (hint_id),
    FOREIGN KEY (hint_code_id) REFERENCES code (code_id),
    FOREIGN KEY (hint_player_id) REFERENCES player (player_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;

CREATE TABLE IF NOT EXISTS guess
(
    guess_round_id INT(10) UNSIGNED NOT NULL,
    guess_team_id INT(10) UNSIGNED NOT NULL,
    guess_code_position INT(10) UNSIGNED NOT NULL,
    guess_code_value INT(10) UNSIGNED NOT NULL,
    PRIMARY KEY (guess_round_id, guess_team_id, guess_code_position)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;

CREATE TABLE IF NOT EXISTS token
(
    token_turn_id INT(10) UNSIGNED NOT NULL,
    token_team_id INT(10) UNSIGNED NOT NULL,
    token_type_id INT(10) UNSIGNED NOT NULL,
    PRIMARY KEY (token_turn_id, token_team_id, token_type_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;
*/
ALTER TABLE `player` ADD `team_id` INT(10) UNSIGNED NOT NULL;
ALTER TABLE `player` ADD CONSTRAINT fk_player_team_id FOREIGN KEY (team_id) REFERENCES teams (id);
