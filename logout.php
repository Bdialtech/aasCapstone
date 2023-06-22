<?php
    $servername = "localhost";
    $username = "root";
    $password = "";
    $dbname = "comic";

    $conn = new mysqli($servername, $username, $password, $dbname);

    $tokenLife = "12:00:00";
    $sqlCheckToken = "SELECT username, isAdmin, TIMESTAMPDIFF(SECOND, CURRENT_TIMESTAMP, ADDTIME(loginTokenCreated, ?)) AS diff FROM users WHERE loginToken=?";
    $stmt= $conn->prepare($sqlCheckToken);
    $stmt->bind_param("ss", $tokenLife, $_COOKIE['token']);
    $stmt->execute();
    $checkTokenResult = $stmt->get_result();

    $sqlUpdateToken = "UPDATE users SET loginToken = NULL, loginTokenCreated = NULL WHERE username=?";
    $stmt= $conn->prepare($sqlUpdateToken);
    $stmt->bind_param("s", $user);
    $stmt->execute();

    setcookie("user", "", time() - 3600);
    setcookie("token", "", time() - 3600);

    $loggedIn = FALSE;
    $isAdmin = FALSE;

    $conn->close();
?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <link rel="stylesheet" href="css/barebones.css">
        <title>PSTCC Comic Bookstore - Logout</title>
    </head>
    <body>
        <?php include $_SERVER['DOCUMENT_ROOT'].'/comicbookstore/templates/loginHeader.php';?>
        <div class="message">
            <h1>You have been successfully logged out.</h1>
        </div>
    </body>
</html>