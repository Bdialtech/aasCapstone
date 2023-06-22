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
    $conn->close();

    if (!$isAdmin) {
        header("Location: ../permissionDenied.php");
        exit();
    }
?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <link rel="stylesheet" href="../css/barebones.css">
        <title>PSTCC Comic Bookstore - Create New Blog Entry</title>
        <script>
            function validateForm() {
                let title = document.forms["blogEntry"]["title"].value.trim();
                let text = document.forms["blogEntry"]["content"].value.trim();
                if (title == "") {
                    alert("The post must have a title.");
                    return false;
                }
                if (text == "") {
                    alert("The post must have content.");
                    return false;
                }
            } 
    </script>
    </head>
    <body>
        <?php include $_SERVER['DOCUMENT_ROOT'].'/comicbookstore/templates/loginHeader.php';?>
        <form name="blogEntry" action="/comicbookstore/blog/upload.php" method="post" onsubmit="return validateForm()" class="blogForm">
            Title <br><textarea name="title" rows="1"></textarea><br>
            Text <br><textarea name="content" rows="20"></textarea><br>
            <br><br><input type="submit" value="Submit" style="width: 33%;">
        </form>
    </body>
</html>