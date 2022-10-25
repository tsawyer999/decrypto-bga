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
                selectorIndex: selectorIndex
            });
        },
        getCode(code) {
            return that.format_block('jstpl_code', {
                code: code.join('-')
            });
        },
        getWordColumn(id, word) {
            return that.format_block('jstpl_word_column', {
                id: id,
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
        getPreviousHint(hint) {
            return that.format_block('jstpl_previous_hint', {
                hint: hint
            });
        }
    };
};

const layout = function(that, dojo, templates) {
    return {
        displayWords(words) {
            for (const [index, word] of words.entries()) {
                const wordBlock = templates.getWordColumn(index, word);
                dojo.place(wordBlock, 'wordsSection');
            }
        },
        displayPreviousHints(hints) {
            console.log('displayPreviousHints');
            for (let i=0; i<hints.length; i++) {
                for (const [index, hint] of hints[i].entries()) {
                    const previousHintBlock = templates.getPreviousHint(hint);
                    dojo.place(previousHintBlock, `previousHints${i}`);
                }
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
        displayGuessHints(hints, wordsCount, onGuessHintSelectorClick) {
            let hintIndex=0;
            for (const hint of hints) {
                const guessHintBlockId = `guessSelector${hintIndex}`;
                const guessHintBlock = templates.getGuessHint(hintIndex, hint);
                dojo.place(guessHintBlock, 'guessHintsUi');

                for (let selectorIndex=1; selectorIndex<=wordsCount; selectorIndex++) {
                    const selectorBlock = templates.getGuessSelector(hintIndex, selectorIndex);
                    dojo.place(selectorBlock, guessHintBlockId);

                    const id = `guess_selector_item_${hintIndex}_${selectorIndex}`;
                    const button = document.getElementById(id);
                    dojo.connect(button, 'onclick', onGuessHintSelectorClick(hintIndex, selectorIndex));
                }

                hintIndex++;
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
        displayTeamsSetup(teams, players, onChangeTeamNameClick) {
            for (const teamId of Object.keys(teams)) {
                const team = teams[teamId];
                const teamBlock = templates.getTeam(team);
                dojo.place(teamBlock, 'teams');

                const button = document.getElementById(`changeTeamName${team.id}Button`);
                dojo.connect(button, 'onclick', onChangeTeamNameClick(team.id));

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

const states = {
    teamSetup: function(that, dojo, layout) {
        return {
            entering(args) {
                dojo.style('teamSetupUi', 'display', 'flex');
                dojo.style('boardUi', 'display', 'none');
                dojo.style('giveHintsUi', 'display', 'none');
                dojo.style('guessHintsUi', 'display', 'none');

                that.addActionButton('switchBtn', _("SwitchTeam"), this.onSwitchTeamClick);
                that.addActionButton('readyBtn', _("Ready"), this.onClickCompleteTeamSetupButton);

                layout.displayTeamsSetup(args.teams, args.players, this.subscribeChangeTeamNameClick.bind(this));
            },
            leaving(stateName) {
                console.log(`leaving stateName: ${stateName}`);
            },
            updateActionButtons(stateName, args) {
                console.log(`updateActionButtons stateName: ${stateName} args: ${args}`);
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
            subscribeChangeTeamNameClick(teamId) {
                const fn = this.onChangeTeamNameClick.bind(this);
                return function () {
                    fn(teamId);
                };
            },
            onChangeTeamNameClick(teamId) {
                console.log('onChangeTeamNameClick', teamId);
                if (!that.checkAction('changeTeamName', true)) {
                    return;
                }

                const textboxId = `teamName${teamId}`;
                const teamNameTextbox = document.getElementById(textboxId);
                if (teamNameTextbox) {
                    const teamName = teamNameTextbox.value;
                    teamNameTextbox.value = '';

                    that.doAction("changeTeamName", {
                        teamId : teamId,
                        name: teamName
                    });
                } else {
                    throw `textbox with id [${textboxId}] not found`;
                }
            },
        };
    },
    giveHints: function(that, dojo, layout) {
        return {
            entering(args) {
                dojo.style('teamSetupUi', 'display', 'none');
                dojo.style('boardUi', 'display', 'flex');
                dojo.style('giveHintsUi', 'display', 'flex');
                dojo.style('guessHintsUi', 'display', 'none');

                const teams = args.teams;
                const words = args.words;
                const code = args.code;
                const previousHints = args.previousHints;

                layout.displayWords(words);
                layout.displayPreviousHints(previousHints);
                layout.displayTokens(teams);
                
                if (!!code) {
                    layout.displayCodeCard(code);
                    layout.displayGiveHints(code);
                }

                that.addActionButton('giveHintsBtn', _("Give hints"), this.onGiveHintsClick);
            },
            leaving(stateName) {
                console.log(`leaving stateName: ${stateName}`);
            },
            updateActionButtons(stateName, args) {
                console.log(`updateActionButtons stateName: ${stateName} args: ${args}`);
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
    },
    guessHints: function(that, dojo, layout) {
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
                layout.displayGuessHints(hints, words.length, this.onGuessHintSelectorClick.bind());
            },
            leaving(stateName) {
                console.log(`leaving stateName: ${stateName}`);
            },
            updateActionButtons(stateName, args) {
                console.log(`updateActionButtons stateName: ${stateName} args: ${args}`);
            },
            onGuessHintSelectorClick(hintIndex, selectorIndex) {
                return function() {
                    console.log(`click hintIndex=${hintIndex} selectorIndex=${selectorIndex}`);
                    that.doAction('changeGuessSelectorIndex', {
                        hintIndex,
                        selectorIndex
                    });
                }
            }
        };
    }
}

const events = {
    onChangeteamName: function(notification) {
        console.log('onChangeteamName', notification);
        const teamId = notification.args.teamId;
        const teamName = notification.args.teamName;

        const labelId = `teamNameLabel${teamId}`;
        const teamNameLabel = document.getElementById(labelId);
        if (teamNameLabel) {
            teamNameLabel.innerText = teamName;
        } else {
            throw `label with id [${labelId}] not found`;
        }
    },
    onSwitchTeam: function (notification) {
        console.log('onSwitchTeam', notification);
        const player = document.getElementById(`teamMember${notification.args.playerId}`);
        const team = document.getElementById(`teamMembers${notification.args.teamId}`);

        team.appendChild(player);
    },
    onChangeGuessSelectorIndex: function (notification) {
        console.log('onChangeGuessSelectorIndex', notification);

        const hintIndex = notification.args.hintIndex;
        const selectorIndex = notification.args.selectorIndex;

        let items = document.querySelectorAll(`#guessSelector${hintIndex} > .selected`);
        for (const item of items) {
            item.classList.remove('selected');
        }
        items = document.querySelectorAll(`.guess-selector-item.selected[data-selector-index="${selectorIndex}"]`);
        for (const item of items) {
            item.classList.remove('selected');
        }

        const currentItem = document.querySelector(`#guess_selector_item_${hintIndex}_${selectorIndex}`);
        currentItem.classList.add('selected');
    }
}

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

                this.states = {};
                for (const state in states) {
                    this.states[state] = states[state](this, dojo, l);
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
            doAction(actionName, payLoad) {
                this.ajaxcall(`/${this.game_name}/${this.game_name}/${actionName}.html`, payLoad,
                    this,
                    function (result) {},
                    function (is_error) {}
                );
            },
            setupNotifications() {
                for (const event in events) {
                    console.log(`setupNotifications event=${event}`);
                    dojo.subscribe(event, this, events[event].bind(this));
                }
            }
        });
    }
);
