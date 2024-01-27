<?php function navbar ($logged_in){
    //TODO: fix profile image
    ?>
    <?php if($logged_in):?>
        <?php include "profile-modal.php" ?>
        <?php profileModal();?>
    <?php else:?>
        <?php include "login-modal.php" ?>
        <?php loginModal();?>
    <?php endif;?>
    <header id="navbar">
        <a id="wordmark" href="index.php"><h1>MusicGraph</h1></a>
        <div>
            <?php if($logged_in):?>
                <button id="profile-picture" title="profile" onclick="showProfileModal()"></button>
            <?php else:?>
                <button id="navbar_login" onclick="showLoginSignupModal()">log in</button>
            <?php endif;?>
        </div>
    </header>
    <?php
}

function playlistNavbar ($logged_in, $owner, $playlist, $temp, $liked){?>
    <?php if($logged_in):?>
        <?php include "profile-modal.php" ?>
        <?php profileModal();?>
    <?php else:?>
        <?php include "login-modal.php" ?>
        <?php loginModal();?>
    <?php endif;?>
    <header id="navbar">
        <a id="wordmark" href="index.php"><h1>MusicGraph</h1></a>
        <div>
        <?php if($owner):?>
            <input id="playlist-name" placeholder="Playlist Name" type="text" title="playlist-name" value="<?=$playlist["name"]?>"/>
            <label id="playlist-privacy" class="switch" title="make-private">
                <input id="visibility" type="checkbox" <?php if ($playlist["public"] === 'f') echo "checked"?>><span class="slider"></span>
            </label>
        <?php else:?>
            <span id="playlist-name"><?=$playlist["name"]?></span>
        <?php endif;?>
        </div>
        <div>
            <?php if($logged_in):?>
                <button id="fork-button" title="fork">
                    <img src="images/share.svg" alt=""/><span>Fork</span>
                </button>
                <?php if(!$owner):?>
                    <button id="like-button" <?php if ($liked) echo "class='liked'"?> title="like">★</button>
                <?php endif;?>
                <button id="profile-picture" title="profile" onclick="showProfileModal()"></button>
            <?php else:?>
                <button id="navbar_login" onclick="showLoginSignupModal()">log in</button>
            <?php endif;?>
        </div>
    </header>
    <?php
}