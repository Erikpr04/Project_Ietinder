<?php
    if (isset($_COOKIE['user_id'])) {
        echo "sendLog('User {$_COOKIE['user_id']} logged in, redirecting him to discover from index.').then(() => { window.location.href = 'discover.php'; });";
    } else {
        echo "sendLog('User not logged in, redirecting him to login from index.').then(() => { window.location.href = 'login.php'; });";
    }

    require_once './rsc/log.php';

    if (isset($_COOKIE['user_id'])) {
        createLog(action: "Usuario con id: " . $_COOKIE['user_id'] . "logged, redirigiendo a discover desde index");
        header('Location: ./discover.php');
    }
    else {
        createLog(action: "Usuario no loggeado, redirigiendo a logging desde index");
        header('Location: ./login.php');
    }