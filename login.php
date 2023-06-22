<?php
    $servername = "localhost";
    $username = "root";
    $password = "";
    $dbname = "comic";

    $conn = new mysqli($servername, $username, $password, $dbname);

    $tokenLife = "12:00:00";
    $sqlCheckToken = "SELECT username, TIMESTAMPDIFF(SECOND, CURRENT_TIMESTAMP, ADDTIME(loginTokenCreated, ?)) AS diff FROM users WHERE loginToken=?";
    $stmt= $conn->prepare($sqlCheckToken);
    $stmt->bind_param("ss", $tokenLife, $_COOKIE['token']);
    $stmt->execute();
    $checkTokenResult = $stmt->get_result();

    $loggedIn = FALSE;
    $isAdmin = FALSE;
    if ($checkTokenResult !== false && $checkTokenResult->num_rows > 0) {
        $data = $checkTokenResult->fetch_assoc();
        if ((int)$data['diff'] > 0) {
            $loggedIn = TRUE;
            if ($data['isAdmin'] == 1) $isAdmin = TRUE;
        } else {
            $sqlDeleteToken = "UPDATE users SET loginToken=NULL, loginTokenCreated=NULL WHERE loginToken=?";
            $stmt= $conn->prepare($sqlDeleteToken);
            $stmt->bind_param("s", $_COOKIE['token']);
            $stmt->execute();
        }
    }
    $conn->close();
?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <link rel="stylesheet" href="css/barebones.css">
        <title>PSTCC Comic Bookstore - Log In</title>
    </head>
    <body>
        <?php include $_SERVER['DOCUMENT_ROOT'].'/comicbookstore/templates/loginHeader.php';?>
        <div class="loginPrompt">
        <h1 style="text-align: center;">Log In</h1>
            <form action="loginResult.php" method="post">
                <label for="user">Username</label><br>
                <input type="text" id="user" name="user"><br>
                <label for="pass">Password</label><br>
                <input type="password" id="pass" name="pass"><br><br>
                <input type="submit" value="Log In">
            </form><br><br>
        </div>
    </body>
</html>