<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <script src="./js/utils.js"></script>
</head>
<body>
    <script>
        <?php
        if (isset($_COOKIE['user_id'])) {
            echo "sendLog('User {$_COOKIE['user_id']} logged in, redirecting him to discover from index.').then(() => { window.location.href = 'discover.php'; });";
        } else {
            echo "sendLog('User not logged in, redirecting him to login from index.').then(() => { window.location.href = 'login.php'; });";
        }
        ?>
    </script>
</body>
</html>