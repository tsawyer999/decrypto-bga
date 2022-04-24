const templates = function(that) {
    return {
        getTokens(team) {
            return that.format_block('jstpl_tokens', {
                teamId: team.id,
                teamName: team.name
            });
        },
        getSuccessToken() {
            return that.format_block('jstpl_token_success', {})
        },
        getFailToken() {
            return that.format_block('jstpl_token_fail', {})
        },
        getGiveHint(index, code) {
            return that.format_block('jstpl_give_hint', {
                id: index,
                code: code
            });
        },
        getGuessHint(hintIndex, hint) {
            return that.format_block('jstpl_guess_hint', {
                hintIndex: hintIndex,
                hint: hint
            });
        },
        getGuessSelector(hintIndex, selectorIndex) {
            return that.format_block('jstpl_guess_selector_item', {
                hintIndex: hintIndex,
                selectorIndex: selectorIndex,
                label: selectorIndex + 1
            });
        },
        getCode(code) {
            return that.format_block('jstpl_code', {
                code: code.join('-')
            });
        },
        getWord(word) {
            return that.format_block('jstpl_word', {
                word: word
            });
        },
        getTeam(team) {
            return that.format_block('jstpl_team', {
                id: team.id,
                name: team.name
            });
        },
        getTeamMember(player) {
            return that.format_block('jstpl_team_member', {
                id: player.id,
                name: player.name
            });
        },
    };
};

const layout = function(that, dojo, templates) {
    return {
        displayWords(words) {
            for (const word of words) {
                const wordBlock = templates.getWord(word);
                dojo.place(wordBlock, 'wordsSection');
            }
        },
        displayCodeCard(code) {
            const codeBlock = templates.getCode(code);
            dojo.place(codeBlock, 'codeSection');
        },
        displayGiveHints(code) {
            let i=0;
            for (const c of code) {
                const giveHintBlock = templates.getGiveHint(i, c);
                dojo.place(giveHintBlock, 'giveHintsUi');
                i++;
            }
        },
        displayGuessHints(hints, wordsCount) {
            let hintIndex=0;
            for (const hint of hints) {
                const guessHintBlockId = `guessSelector${hintIndex}`;
                const guessHintBlock = templates.getGuessHint(hintIndex, hint);
                dojo.place(guessHintBlock, 'guessHintsUi');
                hintIndex++;

                for (let j=0; j<wordsCount; j++) {
                    const selectorBlock = templates.getGuessSelector(hintIndex, j);
                    dojo.place(selectorBlock, guessHintBlockId);
                }
            }
        },
        displayTokens(teams) {
            const successTokenBlock = templates.getSuccessToken();
            const failTokenBlock = templates.getFailToken();

            for (const team of teams) {
                const tokensBlock = templates.getTokens(team);
                dojo.place(tokensBlock, 'tokensSection');

                const placeId = `tokens${team.id}`;
                for (let i=0; i<team.tokens.success; i++) {
                    dojo.place(successTokenBlock, placeId);
                }
                for (let i=0; i<team.tokens.fail; i++) {
                    dojo.place(failTokenBlock, placeId);
                }
            }
        },
        displayTeamsSetup(teams, players) {
            for (const teamId of Object.keys(teams)) {
                const team = teams[teamId];
                const teamBlock = templates.getTeam(team);
                dojo.place(teamBlock, 'teams');
                dojo.connect(document.getElementById(`changeTeamName${team.id}Button`), 'onclick', that.subscribeChangeTeamNameClick(team.id));

                this.displayPlayersByTeams(team, players);
            }
        },
        displayPlayersByTeams(team, players) {
            for (const playerId of team.playerIds) {
                const player = players[playerId];
                if (player) {
                    const teamMemberBlock = templates.getTeamMember(player);
                    dojo.place(teamMemberBlock, `teamMembers${team.id}`);
                } else {
                    console.error(`player with id ${playerId} not found in`, players)
                }
            }
        }
    }
};

const teamSetup = function(that, dojo, layout) {
    return {
        entering(args) {
            dojo.style('teamSetupUi', 'display', 'flex');
            dojo.style('boardUi', 'display', 'none');
            dojo.style('giveHintsUi', 'display', 'none');
            dojo.style('guessHintsUi', 'display', 'none');

            that.addActionButton('switchBtn', _("SwitchTeam"), this.onSwitchTeamClick);
            that.addActionButton('readyBtn', _("Ready"), this.onClickCompleteTeamSetupButton);

            layout.displayTeamsSetup(args.teams, args.players);
        },
        leaving(stateName) {
        },
        updateActionButtons(stateName, args) {
        },
        onSwitchTeamClick() {
            console.log('onSwitchTeamClick');
            if (!that.checkAction('switchTeam', true)) {
                return;
            }
            that.doAction("switchTeam", {});
        },
        onClickCompleteTeamSetupButton() {
            if (!that.checkAction('completeTeamSetup', true)) {
                return;
            }
            that.doAction("completeTeamSetup", {});
        },
    };
};

const giveHints = function(that, dojo, layout) {
    return {
        entering(args) {
            dojo.style('teamSetupUi', 'display', 'none');
            dojo.style('boardUi', 'display', 'flex');
            dojo.style('giveHintsUi', 'display', 'flex');
            dojo.style('guessHintsUi', 'display', 'none');

            const teams = args.teams;
            const words = args.words;
            const code = args.code;

            layout.displayWords(words);
            layout.displayTokens(teams);
            layout.displayCodeCard(code);
            layout.displayGiveHints(code);

            that.addActionButton('giveHintsBtn', _("Give hints"), this.onGiveHintsClick);
        },
        leaving(stateName) {
        },
        updateActionButtons(stateName, args) {
        },
        onGiveHintsClick() {
            console.log('onGiveHintsClick');
            if (!that.checkAction('giveHints', true)) {
                return;
            }
            const inputs = document.querySelectorAll(".hint input");

            let hints = [];
            for (const input of inputs) {
                hints.push(input.value);
            }
            const payload = {hints: JSON.stringify(hints)};
            console.log({payload});
            this.doAction("giveHints", payload);
        }
    };
};

const guessHints = function(that, dojo, layout) {
    return {
        entering(args) {
            dojo.style('teamSetupUi', 'display', 'none');
            dojo.style('boardUi', 'display', 'flex');
            dojo.style('giveHintsUi', 'display', 'none');
            dojo.style('guessHintsUi', 'display', 'flex');

            const teams = args.teams;
            const words = args.words;
            const hints = args.hints;

            layout.displayWords(words);
            layout.displayTokens(teams);
            layout.displayGuessHints(hints, words.length);
        },
        leaving(stateName) {
        },
        updateActionButtons(stateName, args) {
        },
    };
};

const notif_changeteamName = function(notification) {
    const teamId = notification.args.teamId;
    const teamName = notification.args.teamName;

    const labelId = `teamNameLabel${teamId}`;
    const teamNameLabel = document.getElementById(labelId);
    if (teamNameLabel) {
        teamNameLabel.innerText = teamName;
    } else {
        throw `label with id [${labelId}] not found`;
    }
};

const notif_completeTeamSetup = function(notification) {
    console.log('onCompleteTeamSetup', notification);
};

const notif_switchTeam = function (notification) {
    console.log('onSwitchTeam', notification);
    const player = document.getElementById(`teamMember${notification.args.playerId}`);
    const team = document.getElementById(`teamMembers${notification.args.teamId}`);

    team.appendChild(player);
};

define(
    [
        "dojo",
        "dojo/_base/declare",
        "ebg/core/gamegui",
        "ebg/counter"
    ],
    function (dojo, declare) {
        return declare("bgagame.decryptotest", ebg.core.gamegui, {
            that: this,
            constructor() {
                const t = templates(this);
                const l = layout(this, dojo, t);

                this.states = {
                    teamSetup: teamSetup(this, dojo, l),
                    giveHints: giveHints(this, dojo, l),
                    guessHints: guessHints(this, dojo, l)
                }
            },
            setup(gamedatas) {
                console.log("Starting game setup", gamedatas);
                this.setupNotifications();
                console.log("Ending game setup");
            },
            onEnteringState(stateName, args) {
                console.log("Entering state [" + stateName + "]", args);
                if (this.states[stateName]) {
                    this.states[stateName].entering(args.args);
                } else {
                    console.error(`entering state [${stateName}] is not managed`)
                }
            },
            onLeavingState(stateName) {
                console.log("Leaving state [" + stateName + "]");
                if (this.states[stateName]) {
                    this.states[stateName].leaving(stateName);
                } else {
                    console.error(`leaving state [${stateName}] is not managed`)
                }
            },
            onUpdateActionButtons(stateName, args) {
                console.log('onUpdateActionButtons: '+stateName);

                if ( this.isCurrentPlayerActive() ) {
                    console.log('onUpdateActionButtons.isCurrentPlayerActive');
                    if (this.states[stateName]) {
                        this.states[stateName].updateActionButtons(stateName, args);
                    } else {
                        console.error(`leaving state [${stateName}] is not managed`)
                    }
                }
            },
            subscribeChangeTeamNameClick(teamId) {
                const fn = this.onChangeTeamNameClick.bind(this);
                return function () {
                    fn(teamId);
                };
            },
            onChangeTeamNameClick(teamId) {
                console.log('onChangeTeamNameClick', teamId);
                if (!this.checkAction('changeTeamName', true)) {
                    return;
                }

                const textboxId = `teamName${teamId}`;
                const teamNameTextbox = document.getElementById(textboxId);
                if (teamNameTextbox) {
                    const teamName = teamNameTextbox.value;
                    teamNameTextbox.value = '';

                    this.doAction("changeTeamName", {
                        teamId : teamId,
                        name: teamName
                    });
                } else {
                    throw `textbox with id [${textboxId}] not found`;
                }
            },
            doAction(actionName, payLoad) {
                this.ajaxcall(`/${this.game_name}/${this.game_name}/${actionName}.html`, payLoad,
                    this,
                    function (result) {},
                    function (is_error) {}
                );
            },
            setupNotifications() {
                dojo.subscribe('changeTeamName', this, notif_changeteamName);
                dojo.subscribe('completeTeamSetup', this, notif_completeTeamSetup);
                dojo.subscribe('switchTeam', this, notif_switchTeam);
            }
        });
    }
);
