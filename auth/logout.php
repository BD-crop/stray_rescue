<?php
session_start();
if (isset($_COOKIE[session_name()])) {

    $_SESSION = [];

    if (ini_get("session.use_cookies")) {
        $params = session_get_cookie_params();
        setcookie(
            session_name(),
            '',
            time() - 42000,
            $params["path"],
            $params["domain"],
            $params["secure"],
            $params["httponly"]
        );
    }

    session_destroy();
}

echo "<h2>logout successful</h2>
        <h4>login to avail stray_rescue again</h4>";

