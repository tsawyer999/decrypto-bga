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

            setup: function ( gamedatas ) {
                console.log("Starting game setup");

                for (const teamId of Object.keys(gamedatas.teams)) {
                    const team = gamedatas.teams[teamId];
                    const teamBlock = this.format_block('jstpl_team', {
                        id: team.id,
                        name: team.name
                    });
                    dojo.place(teamBlock, 'teams');
                    dojo.connect(document.getElementById(`changeTeamName${team.id}Button`), 'onclick', this.subscribeChangeTeamNameClick(team.id));

                    const memberIds = (team.members || '').trim().split(',');
                    for (const memberId of memberIds) {
                        const player = gamedatas.players[memberId];
                        if (player) {
                            const teamMemberBlock = this.format_block('jstpl_team_member', {
                                id: player.id,
                                name: player.name
                            });
                            dojo.place(teamMemberBlock, `teamMembers${team.id}`);
                        } else {
                            console.error(`player with id ${memberId} not found in`, gamedatas.players)
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
                console.log('Entering state: ' + stateName);

                switch ( stateName ) {
                    case 'teamSetup':
                        dojo.style('phase1', 'display', 'flex');
                        dojo.style('phase2', 'display', 'none');

                        this.addActionButton('Switch', _("SwitchTeam"), 'onSwitchTeamClick');
                        this.addActionButton('Ready', _("Ready"), 'onClickCompleteTeamSetupButton');
                        break;
                    default:
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
                if (this.checkAction('completeTeamSetup', true)) {
                    this.ajaxcall("/" + this.game_name + "/" + this.game_name + "/completeTeamSetup.html", {
                        lock : true
                    }, this, function (result) {
                    }, function (is_error) {
                    });
                }
            },
            subscribeChangeTeamNameClick: function (teamId) {
                const fn = this.onChangeTeamNameClick.bind(this);
                return function () {
                    fn(teamId);
                };
            },
            onChangeTeamNameClick: function (teamId) {
                console.log('onChangeTeamNameClick', teamId);
                const textboxId = `teamName${teamId}`;
                const teamNameTextbox = document.getElementById(textboxId);
                if (teamNameTextbox) {
                    const teamName = teamNameTextbox.value;
                    teamNameTextbox.value = '';
                    if (this.checkAction('completeTeamSetup', true)) {
                        this.ajaxcall("/" + this.game_name + "/" + this.game_name + "/changeTeamName.html", {
                            teamId : teamId,
                            name: teamName
                        }, this, function (result) {
                        }, function (is_error) {
                        });
                    }
                } else {
                    throw `textbox with id [${textboxId}] not found`;
                }
            },

            onSwitchTeamClick: function () {
                console.log('onSwitchTeamClick');
                if (this.checkAction('completeTeamSetup', true)) {
                    this.ajaxcall("/" + this.game_name + "/" + this.game_name + "/switchTeam.html", {
                        lock : true
                    }, this, function (result) {
                    }, function (is_error) {
                    });
                }
            },

            ///////////////////////////////////////////////////
            //// Utility methods

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

            /*
            setupNotifications:

            In this method, you associate each of your game notifications with your local method to handle it.

            Note: game notification names correspond to "notifyAllPlayers" and "notifyPlayer" calls in
                  your decryptotest.game.php file.

            */
            setupNotifications: function () {
                dojo.subscribe('changeTeamName', this, "notif_changeteamName");
                dojo.subscribe('completeTeamSetup', this, "notif_completeTeamSetup");
                dojo.subscribe('switchTeam', this, "notif_switchTeam");
            },

            // TODO: from this point and below, you can write your game notifications handling methods
            notif_changeteamName: function (notification) {
                console.log('onChangeteamName', notification);
                const labelId = `teamNameLabel${notification.args.teamId}`;
                const teamNameLabel = document.getElementById(labelId);
                if (teamNameLabel) {
                    teamNameLabel.innerText = notification.args.teamName;
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
