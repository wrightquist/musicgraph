<?php function loginModal($tempPlaylist = false)
{
    //TODO: finalize styles
    //TODO: better forms
    ?>
    <div style="display: none" id="login-signup-modal" onclick="hideLoginSignupModal()">
        <div class="modal-card">
            <button onclick="hideLoginSignupModal()">&times;</button>
            <div id="login-modal">
                <h2>Log In</h2>
                <form id="login-form" action="login.php?action=login" method="post">
                    <span class="error" style="display: none" id="login-error"></span>

                    <label for="login-username">username:</label><br>
                    <input type="text" id="login-username" name="username" required><br>
                    <span class="error" style="display: none" id="login-username-error"></span>
                    <br>
                    <br>
                    <label for="login-password">password:</label><br>
                    <input type="password" id="login-password" name="password" required><br>
                    <span class="error" style="display: none" id="login-password-error"></span>
                    <br>
                    <br>
                    <button class="modal-submit" type="submit">Log In</button>
                </form>
                <br>
                <span>or <button onclick="showSignup()">Sign Up</button></span>
            </div>
            <div id="signup-modal" style="display: none">
                <h2>Sign Up</h2>
                <form id="signup-form" action="login.php?action=signup" method="post">
                    <span class="error" style="display: none" id="signup-error"></span>

                    <label for="signup-username">username:</label><br>
                    <input type="text" id="signup-username" name="username" required><br>
                    <span class="error" style="display: none" id="signup-username-error"></span>
                    <br>
                    <br>
                    <label for="signup-password">password:</label><br>
                    <input type="password" id="signup-password" name="password" required><br>
                    <span class="error" style="display: none" id="signup-password-error"></span>
                    <br>
                    <br>
                    <label for="signup-password2">confirm password:</label><br>
                    <input type="password" id="signup-password2" name="password2" required><br>
                    <span class="error" style="display: none" id="signup-password2-error"></span>
                    <br>
                    <br>
                    <button class="modal-submit" type="submit">Sign Up</button>
                </form>
                <br>
                <span>or <button onclick="showLogin()">Log In</button></span>
            </div>
        </div>
    </div>
    <script>
        $('#login-form').ajaxForm({
            dataType: 'json',
            success: logIn
        });

        $('#signup-form').ajaxForm({
            dataType: 'json',
            success: signUp
        });

        function logIn(data) {
            let loginError = $("#login-error");
            let loginUsernameError = $("#login-username-error");
            let loginPasswordError = $("#login-password-error");
            if (data.success === false) {
                if ('all' in data.errors) {
                    loginError.text(data.errors.all);
                    loginError.show();
                } else loginError.hide();
                if ('username' in data.errors) {
                    loginUsernameError.text(data.errors.username);
                    loginUsernameError.show();
                } else loginUsernameError.hide();
                if ('password' in data.errors) {
                    loginPasswordError.text(data.errors.password);
                    loginPasswordError.show();
                } else loginPasswordError.hide();
            } else {
                <?php if ($tempPlaylist):?>
                //@TODO: save the temp playlist
                <?php endif;?>
                location.reload();
            }
        }

        function signUp(data) {
            console.log("logged");
            let signupError = $("#signup-error");
            let signupUsernameError = $("#signup-username-error");
            let signupPasswordError = $("#signup-password-error");
            let signupPassword2Error = $("#signup-password2-error");
            if (data.success === false) {
                if ('all' in data.errors) {
                    signupError.text(data.errors.all);
                    signupError.show();
                } else signupError.hide();
                if ('username' in data.errors) {
                    signupUsernameError.text(data.errors.username);
                    signupUsernameError.show();
                } else signupUsernameError.hide();
                if ('password' in data.errors) {
                    signupPasswordError.text(data.errors.password);
                    signupPasswordError.show();
                } else signupPasswordError.hide();
                if ('password2' in data.errors) {
                    signupPassword2Error.text(data.errors.password2);
                    signupPassword2Error.show();
                } else signupPassword2Error.hide();
            } else {
                <?php if ($tempPlaylist):?>
                //@TODO: save the temp playlist
                <?php endif;?>
                location.reload();
            }
        }

        $(".modal-card").click(function (event) {
            event.stopPropagation();
        });

        function showLoginSignupModal() {
            $("#login-signup-modal").show();
        }

        function hideLoginSignupModal() {
            $("#login-signup-modal").hide();
        }

        function showLogin() {
            $("#signup-modal").hide();
            $("#login-modal").show();
        }

        function showSignup() {
            $("#login-modal").hide();
            $("#signup-modal").show();
        }
    </script>
    <?php
}