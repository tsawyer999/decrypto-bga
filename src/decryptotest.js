/**
 *------
 * BGA framework: © Gregory Isabelli <gisabelli@boardgamearena.com> & Emmanuel Colin <ecolin@boardgamearena.com>
 * DecryptoTest implementation : © <Your name here> <Your email address here>
 *
 * This code has been produced on the BGA studio platform for use on http://boardgamearena.com.
 * See http://en.boardgamearena.com/#!doc/Studio for more information.
 * -----
 *
 * decryptotest.js
 *
 * DecryptoTest user interface script
 *
 * In this file, you are describing the logic of your user interface, in Javascript language.
 *
 */

define(
    [
        "dojo","dojo/_base/declare",
        "ebg/core/gamegui",
        "ebg/counter"
    ],
    function (dojo, declare) {
        return declare("bgagame.decryptotest", ebg.core.gamegui, {
            constructor: function () {
                console.log('decryptotest constructor');

                // Here, you can init the global variables of your user interface
                // Example:
                // this.myGlobalValue = 0;

            },

            /*
            setup:

            This method must set up the game user interface according to current game situation specified
            in parameters.

            The method is called each time the game interface is displayed to a player, ie:
            _ when the game starts
            _ when a player refreshes the game page (F5)

            "gamedatas" argument contains all datas retrieved by your "getAllDatas" PHP method.
            */

            setup: function (gamedatas) {
                console.log("Starting game setup", gamedatas);

                for (const teamId of Object.keys(gamedatas.teams)) {
                    const team = gamedatas.teams[teamId];
                    const teamBlock = this.format_block('jstpl_team', {
                        id: team.id,
                        name: team.name
                    });
                    dojo.place(teamBlock, 'teams');
                    dojo.connect(document.getElementById(`changeTeamName${team.id}Button`), 'onclick', this.subscribeChangeTeamNameClick(team.id));

                    for (const playerId of team.playerIds) {
                        const player = gamedatas.players[playerId];
                        if (player) {
                            const teamMemberBlock = this.format_block('jstpl_team_member', {
                                id: player.id,
                                name: player.name
                            });
                            dojo.place(teamMemberBlock, `teamMembers${team.id}`);
                        } else {
                            console.error(`player with id ${playerId} not found in`, gamedatas.players)
                        }
                    }
                }

                // Setup game notifications to handle (see "setupNotifications" method below)
                this.setupNotifications();

                console.log("Ending game setup");
            },


            ///////////////////////////////////////////////////
            //// Game & client states

            // onEnteringState: this method is called each time we are entering into a new game state.
            //                  You can use this method to perform some user interface changes at this moment.
            //
            onEnteringState: function ( stateName, args ) {
                console.log('Entering state: ' + stateName, args);

                switch ( stateName ) {
                    case 'teamSetup':
                        dojo.style('teamSetupUi', 'display', 'flex');
                        dojo.style('electEncryptorUi', 'display', 'none');
                        dojo.style('giveHintsUi', 'display', 'none');
                        dojo.style('guessHintsUi', 'display', 'none');

                        this.addActionButton('switchBtn', _("SwitchTeam"), 'onSwitchTeamClick');
                        this.addActionButton('readyBtn', _("Ready"), 'onClickCompleteTeamSetupButton');
                        break;

                    case 'electEncryptor':
                        dojo.style('teamSetupUi', 'display', 'none');
                        dojo.style('electEncryptorUi', 'display', 'flex');
                        dojo.style('giveHintsUi', 'display', 'none');
                        dojo.style('guessHintsUi', 'display', 'none');

                        this.addActionButton('switchBtn', _("SwitchTeam"), 'onSwitchTeamClick');
                        this.addActionButton('readyBtn', _("Ready"), 'onClickCompleteTeamSetupButton');
                        break;

                    case 'giveHints':
                        dojo.style('teamSetupUi', 'display', 'none');
                        dojo.style('electEncryptorUi', 'display', 'none');
                        dojo.style('giveHintsUi', 'display', 'flex');
                        dojo.style('guessHintsUi', 'display', 'none');

                        this.addActionButton('giveHintsBtn', _("Give hints"), 'onGiveHintsClick');
                        break;

                    case 'guessHints':
                        dojo.style('teamSetupUi', 'display', 'none');
                        dojo.style('electEncryptorUi', 'display', 'none');
                        dojo.style('giveHintsUi', 'display', 'none');
                        dojo.style('guessHintsUi', 'display', 'flex');

                        break;
                    default:
                        console.error(`state [${stateName}] is not managed`)
                }
            },

            // onLeavingState: this method is called each time we are leaving a game state.
            //                 You can use this method to perform some user interface changes at this moment.
            //
            onLeavingState: function ( stateName ) {
                console.log('Leaving state: '+stateName);

                switch ( stateName ) {
                    /* Example:

                case 'myGameState':

                    // Hide the HTML block we are displaying only during this game state
                    dojo.style( 'my_html_block_id', 'display', 'none' );

                    break;
                   */


                    case 'dummmy':
                        break;
                }
            },

            // onUpdateActionButtons: in this method you can manage "action buttons" that are displayed in the
            //                        action status bar (ie: the HTML links in the status bar).
            //
            onUpdateActionButtons: function ( stateName, args ) {
                console.log('onUpdateActionButtons: '+stateName);

                if ( this.isCurrentPlayerActive() ) {
                    switch ( stateName ) {
                        /*
                                 Example:

                                 case 'myGameState':

                                    // Add 3 action buttons in the action status bar:

                                    this.addActionButton( 'button_1_id', _('Button 1 label'), 'onMyMethodToCall1' );
                                    this.addActionButton( 'button_2_id', _('Button 2 label'), 'onMyMethodToCall2' );
                                    this.addActionButton( 'button_3_id', _('Button 3 label'), 'onMyMethodToCall3' );
                                    break;
                        */
                    }
                }
            },

            onClickCompleteTeamSetupButton: function () {
                console.log('onClickCompleteTeamSetupButton');
                if (!this.checkAction('completeTeamSetup', true)) {
                    return;
                }
                this.doAction("completeTeamSetup", {});
            },

            subscribeChangeTeamNameClick: function (teamId) {
                const fn = this.onChangeTeamNameClick.bind(this);
                return function () {
                    fn(teamId);
                };
            },

            onChangeTeamNameClick: function (teamId) {
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

            onSwitchTeamClick: function () {
                console.log('onSwitchTeamClick');
                if (!this.checkAction('switchTeam', true)) {
                    return;
                }
                this.doAction("switchTeam", {});
            },

            onGiveHintsClick: function() {
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
            },

            ///////////////////////////////////////////////////
            //// Utility methods

            doAction(actionName, payLoad) {
                this.ajaxcall(`/${this.game_name}/${this.game_name}/${actionName}.html`, payLoad,
                    this,
                    function (result) {},
                    function (is_error) {}
                );
            },
            /*

            Here, you can defines some utility methods that you can use everywhere in your javascript
            script.

            */


            ///////////////////////////////////////////////////
            //// Player's action

            /*

            Here, you are defining methods to handle player's action (ex: results of mouse click on
            game objects).

            Most of the time, these methods:
            _ check the action is possible at this game state.
            _ make a call to the game server

            */

            /* Example:

        onMyMethodToCall1: function( evt )
        {
            console.log( 'onMyMethodToCall1' );

            // Preventing default browser reaction
            dojo.stopEvent( evt );

            // Check that this action is possible (see "possibleactions" in states.inc.php)
            if( ! this.checkAction( 'myAction' ) )
            {   return; }

            this.ajaxcall( "/decryptotest/decryptotest/myAction.html", {
                                                                    lock: true,
                                                                    myArgument1: arg1,
                                                                    myArgument2: arg2,
                                                                    ...
                                                                 },
                         this, function( result ) {

                            // What to do after the server call if it succeeded
                            // (most of the time: nothing)

                         }, function( is_error) {

                            // What to do after the server call in anyway (success or failure)
                            // (most of the time: nothing)

                         } );
        },

            */


            ///////////////////////////////////////////////////
            //// Reaction to cometD notifications

            setupNotifications: function () {
                dojo.subscribe('changeTeamName', this, "notif_changeteamName");
                dojo.subscribe('completeTeamSetup', this, "notif_completeTeamSetup");
                dojo.subscribe('switchTeam', this, "notif_switchTeam");
            },

            notif_changeteamName: function (notification) {
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

            notif_completeTeamSetup: function (notification) {
                console.log('onCompleteTeamSetup', notification);
            },

            notif_switchTeam: function (notification) {
                console.log('onSwitchTeam', notification);
                const sourceId = `teamMember${notification.args.playerId}`;
                const targetId = `teamMembers${notification.args.teamId}`;

                this.slideToObject(sourceId, targetId).play();
            }
        });
    }
);
