{OVERALL_GAME_HEADER}

<div id="teamSetupUi">
    <div id="teams" class="teams"></div>
</div>

<div id="electEncryptorUi">
    <span>guess hints</span>
</div>

<div id="giveHintsUi">
    <div class="words-panel">
        <div class="words">
            <span class="word">{WORD1}</span>
            <span class="word">{WORD2}</span>
            <span class="word">{WORD3}</span>
            <span class="word">{WORD4}</span>
        </div>
    </div>
    <div class="hints-panel">
        <div class="hint">
            <label for="hint1">3</label>
            <input id="hint1" type="text" size="50" maxlength="50">
        </div>
        <div class="hint">
            <label for="hint2">4</label>
            <input id="hint2" type="text" size="50" maxlength="50">
        </div>
        <div class="hint">
            <label for="hint3">1</label>
            <input id="hint3"type="text" size="50" maxlength="50">
        </div>
    </div>
    <!--

        <div class="guess-panels">
            <div class="guess-panel">
                <div>1</div>
                <div>2</div>
                <div>3</div>
                <div>4</div>
            </div>
            <div class="guess-panel">
                <div>1</div>
                <div>2</div>
                <div>3</div>
                <div>4</div>
            </div>
            <div class="guess-panel">
                <div>1</div>
                <div>2</div>
                <div>3</div>
                <div>4</div>
            </div>
            <div class="guess-panel">
                <div>1</div>
                <div>2</div>
                <div>3</div>
                <div>4</div>
            </div>
        </div>

        <div class="hint">
            3-4-1
        </div>

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

</script>

{OVERALL_GAME_FOOTER}
