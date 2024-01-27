<?php function myPlaylists($my_playlists, $liked_playlists)
{
    //TODO: fix divider
    //TODO: renaming for owned playlists
    ?>
    <!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <link rel="stylesheet" href="styles/styles.css"/>
        <link rel="stylesheet" href="styles/my-playlists.css"/>
        <title>My Playlists</title>
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta name="author" content="Wright Quist">
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.7/jquery.js"></script>
    </head>
    <body>
    <?php include "navbar.php" ?>
    <?php navbar(true) ?>
    <div id="content">
        <h1>My Playlists</h1>
        <button class="playlist-tab" id="my-playlists-tab" onclick="showMyPlaylists()">my playlists</button><button class="playlist-tab" id="liked-playlists-tab" onclick="showLikedPlaylists()">liked playlists</button>
        <div id="playlists">
            <div id="my-playlists">
                <?php foreach ($my_playlists as $playlist): ?>
                    <div class="playlist">
                        <div>
                            <div>
                                <a href="playlist.php?id=<?= $playlist["id"] ?>"><h2><?= $playlist["name"] ?></h2></a>
                                <label class="playlist-privacy switch" title="make-private">
                                    <input type="checkbox" <?php if ($playlist["public"] === "f") echo "checked" ?> onclick="togglePrivacy(<?= $playlist["id"] ?>, this)"><span
                                            class="slider"></span>
                                </label>
                            </div>
                            <button title="delete" onclick="deletePlaylist(<?= $playlist["id"] ?>, this)">🗑</button>
                        </div>
                        <div>
                            <div>
                                <span title="likes">★ <?= $playlist["likes"] ?></span>
                                <button title="fork" onclick="forkPlaylist(<?= $playlist["id"] ?>)">⑂ <?= $playlist["forks"] ?></button>
                            </div>
                            <span><?= $playlist["date_created"] ?></span>
                        </div>
                    </div>
                    <div class="divider"></div>
                <?php endforeach; ?>
                <?php if (empty($my_playlists)): ?>
                    <div class="playlist">
                        <span><?= "you have no playlists" ?></span>
                    </div>
                <?php endif; ?>
            </div>
            <div id="liked-playlists" style="display: none">
                <?php foreach ($liked_playlists as $playlist): ?>
                    <div class="playlist">
                        <div>
                            <div>
                                <a href="playlist.php?id=<?= $playlist["id"] ?>"><h2><?= $playlist["name"] ?></h2></a>
                                <span>by: <?= $playlist["username"] ?></span>
                            </div>
                        </div>
                        <div>
                            <div>
                                <button title="unlike" onclick="unlikePlaylist(<?= $playlist["id"] ?>, this)">★ <?= $playlist["likes"] ?></button>
                                <button title="fork" onclick="forkPlaylist(<?= $playlist["id"] ?>)">⑂ <?= $playlist["forks"] ?></button>
                            </div>
                            <span><?= $playlist["date_created"] ?></span>
                        </div>
                    </div>
                    <div class="divider"></div>
                <?php endforeach; ?>
                <?php if (empty($liked_playlists)): ?>
                    <div class="playlist">
                        <span><?= "you have no liked playlists" ?></span>
                    </div>
                <?php endif; ?>
            </div>
        </div>
        <button id="new-playlist" title="new playlist" onclick="createPlaylist()"></button>
    </div>
    <script>
        const likedPlaylistsTab = $("#liked-playlists-tab");
        const myPlaylistsTab = $("#my-playlists-tab");
        const likedPlaylists = $("#liked-playlists");
        const myPlaylists = $("#my-playlists");

        likedPlaylistsTab.css("opacity", "80%");

        function showMyPlaylists() {
            myPlaylists.show();
            likedPlaylists.hide();
            likedPlaylistsTab.css("opacity", "80%");
            myPlaylistsTab.css("opacity", "100%");
        }

        function showLikedPlaylists() {
            myPlaylists.hide();
            likedPlaylists.show();
            myPlaylistsTab.css("opacity", "80%");
            likedPlaylistsTab.css("opacity", "100%");
        }

        function deletePlaylist(id, ele){
            $.post(`playlist.php?id=${id}&action=delete`);
            $(ele).parents(".playlist").remove();
            $(ele).parents(".playlist").next.remove();
        }

        function unlikePlaylist(id, ele){
            $.post(`playlist.php?id=${id}&action=unlike`);
            $(ele).parents(".playlist").remove();
            $(ele).parents(".playlist").next.remove();
        }

        function forkPlaylist(id){
            $.post(`playlist.php?id=${id}&action=fork`).done((data) => {
                location = data.redirect;
            });
        }

        function togglePrivacy(id, toggle){
            const checked =  toggle.checked?"false":"true";
            $.post(window.location.href+"&action=change_visibility", {public: checked});
        }

        function createPlaylist(){
            const form = $(`<form method='POST' action='my-playlists.php'><input type='hidden'/></form>`);
            form.appendTo("body").submit();
            form.remove();
        }

    </script>
    </body>
    </html>
    <?php
}