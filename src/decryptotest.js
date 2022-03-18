const templates = function(format_block) {
    return {
        getTokens(team) {
            return format_block('jstpl_tokens', {
                teamId: team.id,
                teamName: team.name
            });
        },
        getSuccessToken() {
            return format_block('jstpl_token_success', {})
        },
        getFailToken() {
            return format_block('jstpl_token_fail', {})
        },
        getGiveHint(index, code) {
            return format_block('jstpl_give_hint', {
                id: index,
                code: code
            });
        },
        getCode(code) {
            return format_block('jstpl_code', {
                code: code.join('-')
            });
        },
        getWord(word) {
            return format_block('jstpl_word', {
                word: word
            });
        },
        getTeam(team) {
            return format_block('jstpl_team', {
                id: team.id,
                name: team.name
            });
        },
        getTeamMember(player) {
            return format_block('jstpl_team_member', {
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
            dojo.place(codeBlock, 'code');
        },
        displayGiveHints(code) {
            let i=0;
            for (const c of code) {
                const giveHintBlock = templates.getGiveHint(i, c);
                dojo.place(giveHintBlock, 'giveHintsUi');
                i++;
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
                dojo.connect(document.getElementById(`changeTeamName${team.id}Button`), 'onclick', () => onChangeTeamNameClick(team.id));

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

            layout.displayTeamsSetup(args.teams, args.players, this.onChangeTeamNameClick);
        },
        leaving() {
        },
        updateActionButtons() {
        },
        onSwitchTeamClick() {
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
        onChangeTeamNameClick(teamId) {
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
        leaving() {
        },
        updateActionButtons() {
        },
        onGiveHintsClick() {
            console.log('onGiveHintsClick');
            if (!this.checkAction('giveHints', true)) {
                return;
            }
            const inputs = document.querySelectorAll(".hint input");
            console.log(inputs);
            let hints = [];
            for (const input of inputs) {
                hints.push({ id: input.id, value: input.value });
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
        },
        leaving() {
        },
        updateActionButtons() {
        },
    };
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
                const t = templates(this.format_block);
                const l = layout(this, dojo, t);

                this.states = {
                    teamSetup: teamSetup(this, dojo, l),
                    giveHints: giveHints(this, dojo, l),
                    guessHints: guessHints(this, dojo, l)
                }

                this.notifications = {
                    changeTeamName: this.notif_changeteamName,
                    completeTeamSetup: this.notif_completeTeamSetup,
                    switchTeam: this.notif_switchTeam
                }
            },

            setup(gamedatas) {
                console.log("Starting game setup", gamedatas);
                this.setupNotifications();
                console.log("Ending game setup");
            },

            onEnteringState(stateName, args) {
                console.log("Entering state [" + stateName + "]");
                if (this.states[stateName]) {
                    this.states[stateName].entering(args.args);
                } else {
                    console.error(`entering state [${stateName}] is not managed`)
                }
            },

            onLeavingState(stateName) {
                console.log("Leaving state [" + stateName + "]");
                if (this.states[stateName]) {
                    this.states[stateName].leaving();
                } else {
                    console.error(`leaving state [${stateName}] is not managed`)
                }
            },

            onUpdateActionButtons(stateName, args) {
                console.log("Update action buttons with state [" + stateName + "]");
                if (this.states[stateName]) {
                    this.states[stateName].updateActionButtons();
                } else {
                    console.error(`update action buttons with state [${stateName}] is not managed`)
                }
            },

            doAction(actionName, payLoad) {
                this.ajaxcall(`/${this.game_name}/${this.game_name}/${actionName}.html`, payLoad,
                    this,
                    function (result) {
                        console.log(`action [${actionName}] success with result ${result}`);
                    },
                    function (error) {
                        console.log(`action [${actionName}] fails with error ${error}`);
                    }
                );
            },

            setupNotifications() {
                for (let notificationName in this.notifications) {
                    dojo.subscribe(notificationName, this, this.notifications[notificationName]);
                }
            },

            notif_changeteamName(notification) {
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

            notif_completeTeamSetup(notification) {
                console.log('onCompleteTeamSetup', notification);
            },

            notif_switchTeam(notification) {
                console.log('onSwitchTeam', notification);
                const sourceId = `teamMember${notification.args.playerId}`;
                const targetId = `teamMembers${notification.args.teamId}`;

                this.slideToObject(sourceId, targetId).play();
            }
        });
    }
);
