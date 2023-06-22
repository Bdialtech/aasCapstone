<?php
    echo "<div class=\"loginHeader\">";
        if ($loggedIn) { 
            echo "Logged in as ".$_COOKIE["user"]."     ";
            echo "<button type=\"button\" onclick=\"window.location.href='/comicbookstore/logout.php'\">Log Out</button>";
        } else {
            echo "<button type=\"button\" onclick=\"window.location.href='/comicbookstore/login.php'\">Log In</button>";
        }
        echo "<button type=\"button\" onclick=\"window.location.href='/comicbookstore/index.php'\">Home</button>";
        echo "<button type=\"button\" onclick=\"window.location.href='/comicbookstore/blog/view.php'\">Blog</button>";
        if ($isAdmin) {
            echo "<button type=\"button\" onclick=\"window.location.href='/comicbookstore/blog/newEntry.php'\">Make Blog Post</button>";
        }
    echo "</div><br>";
?>