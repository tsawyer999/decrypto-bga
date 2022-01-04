{OVERALL_GAME_HEADER}

<div id="teamSetupUi">
    <div id="teams" class="teams"></div>
</div>

<div id="electEncryptorUi">
    <span>guess hints</span>
</div>

<div id="giveHintsUi">
    <div class="words-panel">
        <div class="words"></div>
    </div>
    <div class="hints-panel"></div>

    <!--
        <div class="teams-panel">
            <div class="team-panel">
                <h1>white team</h1>
                <div class="tokens">
                    <div class="token token-success"></div>
                    <div class="token token-success"></div>
                    <div class="token token-fail"></div>
                    <div class="token token-fail"></div>
                </div>
            </div>
            <div class="team-panel">
                <h1>black team</h1>
                <div class="tokens">
                    <div class="token token-success"></div>
                    <div class="token token-success"></div>
                    <div class="token token-fail"></div>
                    <div class="token token-fail"></div>
                </div>
            </div>
        </div>

        -->
</div>

<div id="guessHintsUi">
    <span>guess hints</span>
</div>

<script type="text/javascript">

    let jstpl_team = '<div class="team">'
        + '<span id="teamNameLabel${id}">${name}</span>'
        + '<input id="teamName${id}" type="text" maxlength="50">'
        + '<button id="changeTeamName${id}Button" class="action-button bgabutton bgabutton_blue">change</button>'
        + '<div id="teamMembers${id}"></div>'
        + '</div>';

    let jstpl_team_member = '<div id="teamMember${id}" class="team-member">${name}</div>';

    let jstpl_word = '<span class="word">${word}</span>';

    let jstpl_hint = '<div class="hint">'
        + '<label for="${id}">3</label>'
        + '<input id="${id}" type="text" size="50" maxlength="50">'
        + '</div>'

</script>

{OVERALL_GAME_FOOTER}
