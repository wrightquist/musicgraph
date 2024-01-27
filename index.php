<?php session_start()
//todo: images for link cards
//todo: replace form buttons with js buttons
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="styles/styles.css"/>
    <link rel="stylesheet" href="styles/homepage.css"/>
    <title>MusicGraph</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="author" content="Wright Quist">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.7/jquery.js"></script>
    <script src="https://malsup.github.io/jquery.form.js"></script>
    <!--
    urls:
        homepage: https://cs4640.cs.virginia.edu/wru3rm/musicgraph/
        user playlists: https://cs4640.cs.virginia.edu/wru3rm/musicgraph/my-playlists.php
    -->
</head>
<body>
<?php
if (gethostname() === "cs4640") {
    include "/students/wru3rm/students/wru3rm/src/templates/navbar.php";
} else {
    include "src/templates/navbar.php";
} ?>
<?php navbar(isset($_SESSION["id"])) ?>
<div id="banner">
    <img src="images/graph.png" alt="">
    <div id="banner-text">
        <h1>Beyond Linear Playlists</h1>
        <p>Link songs into networks to create playlists that branch, split, re-converge, and cycle — make meaning by
            making connections</p>
        <div>
            <?php if (isset($_SESSION["id"])): ?>
                <form action="my-playlists.php" method="post">
                    <button type="submit">Create a New Playlist Now</button>
                </form>
            <?php else: ?>
                <button onclick="showLoginSignupModal(); showSignup();">Sign Up Now</button>
            <?php endif; ?>
        </div>
    </div>
</div>
<div id="cards">
    <a class="card" href="#">Overview and Tutorials</a>
    <a class="card" href="#">Share Playlists With Friends</a>
    <a class="card" href="#">Browse Playlists</a>
    <a class="card" href="my-playlists.php">My Playlists</a>
</div>
</body>
</html>
