{OVERALL_GAME_HEADER}

<div id="teamSetupUi">
    <div id="teams"></div>
</div>

<div id="boardUi">
    <div id="wordsSection"></div>
    <div id="codeSection">
        <div id="codeStack"></div>
    </div>
    <div id="tokensSection"></div>
</div>

<div id="giveHintsUi">
</div>

<div id="guessHintsUi">
</div>

<script type="text/javascript">

    let jstpl_team = '<div class="team">'
        + '<span id="teamNameLabel${id}">${name}</span>'
        + '<input id="teamName${id}" type="text" maxlength="50">'
        + '<button id="changeTeamName${id}Button" class="action-button bgabutton bgabutton_blue">change</button>'
        + '<div id="teamMembers${id}"></div>'
        + '</div>';

    let jstpl_team_member = '<div id="teamMember${id}" class="team-member">${name}</div>';

    let jstpl_word = '<div class="word"><span>${word}</span>';

    let jstpl_give_hint = '<div class="hint">'
        + '<label for="hint${id}">${code}</label>'
        + '<input id="hint${id}" type="text" size="30" maxlength="30">'
        + '</div>'

    let jstpl_guess_hint = '<div class="guess">'
        + '<div class="guess-label">${hint}</div>'
        + '<div id="guessSelector${hintIndex}" class="guess-selector"></div>'
        + '</div>'

    let jstpl_guess_selector_item = '<div id="guess_selector_item_${hintIndex}_${selectorIndex}" class="guess-selector-item">${selectorIndex}</div>';

    let jstpl_tokens = '<div>'
        + '<div>${teamName}</div>'
        + '<div id="tokens${teamId}" class="tokens"></div>'
        + '</div>'

    let jstpl_token_success = '<div class="token token-success"></div>';

    let jstpl_token_fail = '<div class="token token-fail"></div>';

    let jstpl_code = '<div id="code"><div>${code}</div></div>';

</script>

{OVERALL_GAME_FOOTER}
