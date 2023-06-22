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

    $page = 0;
    if (empty($_GET)){
        $page = 1;
    } else {
        $page = (int)$_GET['page'];
    }
    $sqlNumOfEntries = "SELECT entryID FROM blog";
    $numOfEntries = $conn->query($sqlNumOfEntries);
    $pagePrev = $page - 1;
    $pageNext = $page + 1;
    $pageLast = ceil($numOfEntries->num_rows / 5);
    if ($pageLast == 0) $pageLast = 1;
?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <link rel="stylesheet" href="../css/barebones.css">
        <title>PSTCC Comic Bookstore - Blog</title>
    </head>
    <body>
        <?php include $_SERVER['DOCUMENT_ROOT'].'/comicbookstore/templates/loginHeader.php';?>
        <div class="nav">
            <button <?php if ($page == 1) {echo("disabled");} ?> type="button" onclick="window.location.href='view.php?page=1';">First <<</button>
            <button <?php if ($page == 1) {echo("disabled");} ?> type="button" onclick="window.location.href='view.php?page=<?php echo((int)$pagePrev); ?>';">Previous 5 <</button>
            <button <?php if ($page == $pageLast) {echo("disabled");}?> type="button" onclick="window.location.href='view.php?page=<?php echo((int)$pageNext); ?>';">Next 5 ></button>
            <button <?php if ($page == $pageLast) {echo("disabled");}?> type="button" onclick="window.location.href='view.php?page=<?php echo((int)$pageLast); ?>';">Last >></button>
        </div>
        <div class="content">
            <?php
                $offset = -5 + (5 * $page);

                $sqlPull = "SELECT title, author, datePosted, content FROM blog ORDER BY datePosted DESC LIMIT 5 OFFSET $offset";
                $pullResult = $conn->query($sqlPull);

                if ($pullResult->num_rows > 0) {
                    while($row = $pullResult->fetch_assoc()) {
                        echo "<div class=\"blogEntry\">";
                        echo "<h2>".$row["title"]."</h2><h3>".$row["author"]." - ".$row["datePosted"]."</h3><p>".$row["content"]."</p>";
                        if ($isAdmin) {
                            echo "<button type=\"button\" onclick=\"window.location.href='newEntry.php'\">Edit</button>";
                            echo "<button type=\"button\" onclick=\"window.location.href='newEntry.php'\">Mark as Invisible</button>";
                        }
                        echo "</div>";
                    }
                } else {
                    echo "<h4 class=\"message\">Nothing here yet!</h4>";
                }
                $conn->close();
            ?>
        </div>
        <div class="nav">
            <button <?php if ($page == 1) {echo("disabled");} ?> type="button" onclick="window.location.href='view.php?page=1';">First <<</button>
            <button <?php if ($page == 1) {echo("disabled");} ?> type="button" onclick="window.location.href='view.php?page=<?php echo((int)$pagePrev); ?>';">Previous 5 <</button>
            <button <?php if ($page == $pageLast) {echo("disabled");}?> type="button" onclick="window.location.href='view.php?page=<?php echo((int)$pageNext); ?>';">Next 5 ></button>
            <button <?php if ($page == $pageLast) {echo("disabled");}?> type="button" onclick="window.location.href='view.php?page=<?php echo((int)$pageLast); ?>';">Last >></button>
        </div>
    </body>
</html>