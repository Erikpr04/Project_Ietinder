<?php
if (isset($_COOKIE['user_id'])) {
    header("Location: discover.php");
    exit();
} else {
    header("Location: login.php");
    exit();
}