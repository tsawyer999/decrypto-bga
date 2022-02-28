CREATE TABLE IF NOT EXISTS team (
    team_id INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
    team_name VARCHAR(50) NOT NULL,
    team_order_id INT(10) NOT NULL,
    team_words JSON NULL,
    PRIMARY KEY (team_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;

CREATE TABLE IF NOT EXISTS word
(
    word_id INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
    word_team_id INT(10) UNSIGNED NOT NULL,
    word_position INT(10) UNSIGNED NOT NULL,
    word_value VARCHAR(50) NOT NULL,
    PRIMARY KEY (word_id),
    FOREIGN KEY (word_team_id) REFERENCES team (team_id),
    CONSTRAINT uc_team_id_position UNIQUE (word_team_id, word_position)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;

CREATE TABLE IF NOT EXISTS turn
(
    turn_id INT(10) UNSIGNED NOT NULL,
    turn_round_id INT(10) UNSIGNED NOT NULL,
    PRIMARY KEY (turn_id, turn_round_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;

CREATE TABLE IF NOT EXISTS code
(
    code_id INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
    code_turn_id INT(10) UNSIGNED NOT NULL,
    code_word_id INT(10) UNSIGNED NOT NULL,
    code_position INT(10) UNSIGNED NOT NULL,
    PRIMARY KEY (code_id),
    FOREIGN KEY (code_turn_id) REFERENCES turn (turn_id),
    FOREIGN KEY (code_word_id) REFERENCES word (word_id),
    CONSTRAINT uc_turn_id_position_word_id UNIQUE (code_turn_id, code_word_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;

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

ALTER TABLE `player` ADD `player_team_id` INT(10) UNSIGNED NOT NULL;
ALTER TABLE `player` ADD CONSTRAINT fk_player_team_id FOREIGN KEY (player_team_id) REFERENCES team (team_id);
