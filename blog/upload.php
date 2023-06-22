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

    if (!$isAdmin) {
        header("Location: /comicbookstore/permissionDenied.php");
        exit();
    }
    if (empty($_POST)) {
        header("Location: newEntry.php");
        exit();
    }

    $sqlGetName = "SELECT firstName, lastName FROM users WHERE loginToken=?";
    $stmt= $conn->prepare($sqlGetName);
    $stmt->bind_param("s", $_COOKIE['token']);
    $stmt->execute();
    $nameData = $stmt->get_result()->fetch_assoc();
    $fullName = (string)$nameData['firstName']." ".(string)$nameData['lastName'];

    $postSuccess = FALSE;
    if (!empty($_POST)) {
        $entry = $conn->prepare("INSERT INTO blog VALUES ('', ?, ?, CURRENT_TIMESTAMP, ?, NULL, '')");
        $entry->bind_param("sss", $_POST["title"], $fullName, $_POST["content"]);
        if ($entry->execute()) {
            $postSuccess = TRUE;
        }
    }

    $conn->close();
?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <link rel="stylesheet" href="/comicbookstore/css/barebones.css">
        <title>PSTCC Comic Bookstore - Upload</title>
    </head>
    <body>
        <?php include $_SERVER['DOCUMENT_ROOT'].'/comicbookstore/templates/loginHeader.php';?>
        <div class="message">
        <?php 
            if ($postSuccess) {
                echo "<h1>Entry successfully posted.</h1>";
            } else {
                echo "<h1>Entry failed to post.</h1>";
            }
        ?>
        </div>
    </body>
</html>