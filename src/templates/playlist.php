<?php function playlist($logged_in, $owner, $playlist, $temp, $liked)
{
    //TODO: undo/redo
    //TODO: info popup
    //TODO: share popup
    //TODO: not logged in banner
    //TODO: fork button style
    ?>
    <!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <link rel="stylesheet" href="styles/styles.css"/>
        <link rel="stylesheet" href="styles/playlist.css"/>
        <title><?= $playlist["name"] ?></title>
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta name="author" content="Wright Quist">
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.7/jquery.js"></script>
        <script src="https://malsup.github.io/jquery.form.js"></script>
        <script src="scripts/nodes.js"></script>
        <script src="scripts/player.js"></script>
        <script src="scripts/script.js"></script>
    </head>
    <body>
    <?php include "navbar.php" ?>
    <?php playlistNavbar($logged_in, $owner, $playlist, $temp, $liked) ?>
    <div class="node-area">
        <div id="lines"></div>
        <div id="nodes"></div>
        <div id="node-controls">
            <?php if ($owner): ?>
                <div id="undo-redo">
                    <button id="undo" title="undo"></button>
                    <button id="redo" title="redo"></button>
                </div>
            <?php endif ?>
            <button id="info" title="info">i</button>
            <div id="shortcuts_outer" style="display: none">
                <div id="shortcuts" class="modal-card" style="display: inline-block">
                    <h3>Node Controls</h3>
                    <table>
                        <tr><td>Shift+L</td><td>link 2 selected nodes</td></tr>
                        <tr><td>Shift+U</td><td>unlink 2 selected nodes</td></tr>
                        <tr><td>Delete</td><td>delete selected nodes</td></tr>
                        <tr><td>Click</td><td>select/deselect node</td></tr>
                        <tr><td>Shift+Click</td><td>add/remove node from selection</td></tr>
                        <tr><td>Double Click</td><td>play node</td></tr>
                        <tr><td>Enter</td><td>set current selection as play path</td></tr>
                    </table>
                </div>
            </div>
            <?php if ($owner): ?>
                <button id="create-node" title="create node"></button>
            <?php endif ?>
        </div>
        <div id="new-node-modal" class="modal-outer" style="display: none">
            <div class="modal-card">
                <label for="song-url">Youtube Song Link</label>
                <span id="new-node-error" class="error" style="display: none">Invalid Youtube Url</span>
                <input id="song-url" type="url">
                <div>
                <button id="new-node-cancel">Cancel</button>
                <button id="new-node-button">Add Song</button>
                </div>
            </div>
        </div>
    </div>
    <div id="bottom-bar">
        <div id="song-info-small">
            <div id="song-info">
                <img id="thumbnail" src="images/gradient.png" alt="song thumbnail"/>
                <div>
                    <span id="title">--</span>
                    <span id="artist">--</span>
                </div>
            </div>
            <button id="play-small" title="play"></button>
        </div>
        <div id="controls">
            <div id="player"></div>
            <div id="top">
                <button disabled class="skip" id="prev-song" title="previous song"></button>
                <button disabled id="play" class="paused" title="play"></button>
                <button disabled class="skip" id="next-song" title="next-song"></button>
            </div>
            <div id="bottom">
                <span class="time-text" id="time-elapsed">-:--</span>
                <div>
                    <input type="range" min="1" max="100" value="50" id="time" title="song play time">
                </div>
                <span class="time-text" id="song-length">-:--</span>
            </div>
        </div>
        <div id="volume">
            <button id="volume-button" title="volume"></button>
            <div>
                <input type="range" min="1" max="100" value="100" id="volume-slider" title="volume">
            </div>
        </div>
    </div>
    </body>
    </html>
    <?php
}