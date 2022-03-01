{OVERALL_GAME_HEADER}

<div id="teamSetupUi">
    <div id="teams"></div>
</div>

<div id="boardUi">
    <div id="words"></div>
    <div id="score"></div>
</div>

<div id="giveHintsUi">
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

    let jstpl_word = '<div class="word"><span>${word}<span></span>';

    let jstpl_hint = '<div class="hint">'
        + '<label for="${id}">3</label>'
        + '<input id="${id}" type="text" size="50" maxlength="50">'
        + '</div>'

    let jstpl_score = '<div>'
        + '<div>${teamName}</div>'
        + '<div id="score${teamId}">'
        + '<div class="token token-success"></div>'
        + '<div class="token token-success"></div>'
        + '<div class="token token-fail"></div>'
        + '<div class="token token-fail"></div>'
        + '</div>'
        + '</div>'

</script>

{OVERALL_GAME_FOOTER}
