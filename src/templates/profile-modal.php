<?php function profileModal()
{
    //todo: finish styles
    //todo: better forms
    ?>
    <div style="display: none" id="profile-modal" onclick="hideProfileModal()">
        <div class="modal-card">
            <button onclick="hideProfileModal()">&times;</button>
            <ul>
                <li><a href="my-playlists.php">my playlists</a></li>
                <li>
                    <form action="login.php?action=logout" method="post">
                        <button type="submit">log out</button>
                    </form>
                </li>
            </ul>
        </div>
    </div>
    <script>
        $(".modal-card").on("click", (e)=> {
            e.stopPropagation();
        });

        function showProfileModal() {
            $("#profile-modal").show();
        }

        function hideProfileModal() {
            $("#profile-modal").hide();
        }
    </script>
    <?php
}
