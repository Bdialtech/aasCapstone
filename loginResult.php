<?php
    $servername = "localhost";
    $username = "root";
    $password = "";
    $dbname = "comic";

    $conn = new mysqli($servername, $username, $password, $dbname);

    $user = $_POST['user'];
    $encodedPassword = sha1($_POST['pass']);

    // Test to see if the login is a success
    // SELECT query runs WHERE user and password try to match. If zero results, login unsuccessful
    $sqlTestLogin = "SELECT userid, isAdmin FROM users WHERE username=? AND encPass='$encodedPassword'";
    $stmt= $conn->prepare($sqlTestLogin);
    $stmt->bind_param("s", $user);
    $stmt->execute();
    $testResult = $stmt->get_result();

    $isAdmin = FALSE;
    $loggedIn = FALSE;
    $success = FALSE;
    if ($testResult !== false && $testResult->num_rows > 0) {
        $success = TRUE;
        $loggedIn = TRUE;

        $tempData = $testResult->fetch_assoc();
        if ($tempData["isAdmin"] == 1) {
            $isAdmin = TRUE;
        }

        // Generate a unique login token
        $newToken = "";
        for ($i = 0; $i < 16; $i++) {
            $newToken = $newToken . (string)rand(0,9);
        }
        $newToken = sha1($newToken);

        $sqlUpdateToken = "UPDATE users SET loginToken = ?, loginTokenCreated = CURRENT_TIMESTAMP WHERE username=?";
        $stmt= $conn->prepare($sqlUpdateToken);
        $stmt->bind_param("ss", $newToken, $user);
        $stmt->execute();

        // Set a login cookie with token
        setcookie("user", $user);
        $_COOKIE['user'] = $user;
        setcookie("token", $newToken);
    }

    $conn->close();
?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <link rel="stylesheet" href="css/barebones.css">
        <title>PSTCC Comic Bookstore</title>
    </head>
    <body>
        <?php include $_SERVER['DOCUMENT_ROOT'].'/comicbookstore/templates/loginHeader.php';?>
        <div class="message">
        <?php if ($success) {
            echo "<h1>Login Successful</h1><br>";
        } else {
            echo "<h2>That combination of username and password does not exist.</h2>";
        }?>
        </div>
    </body>
</html>