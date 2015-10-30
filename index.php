<?php
  session_start();
?>
<!DOCTYPE html>
<html>
  <head>
    <meta name="viewport" content="width=device-width">
    <link rel = "stylesheet" type = "text/css" href = "css/bootstrap.min.css">
    <link rel = "stylesheet" type = "text/css" href = "css/site.css">
    <script type = "text/javascript" src = "js/jquery-1.11.3.min.js"></script>
    <script type = "text/javascript" src = "js/bootstrap.min.js"></script>
    <title>Index</title>
  </head>
  <body>
    <div class = "container">
      <nav class="navbar navbar-default">
        <div class="container-fluid">
          <div class="navbar-header">
            <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1" aria-expanded="false">
              <span class="sr-only">Toggle navigation</span>
              <span class="icon-bar"></span>
              <span class="icon-bar"></span>
              <span class="icon-bar"></span>
            </button>
            <a class="navbar-brand" href="index.php">Blog</a>
          </div>
          <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
            <ul class="nav navbar-nav navbar-right">
              <?php
                if ($_SESSION["user_username"]) {
                  // Dropdown menu
                  echo "<li class=\"dropdown\">" .
                        "<a href=\"#\" class=\"dropdown-toggle\" data-toggle=\"dropdown\" role=\"button\" aria-haspopup=\"true\" aria-expanded=\"false\">" .
                          $_SESSION["user_username"] .
                          "<span class=\"caret\"></span>" .
                        "</a>" .
                        "<ul class=\"dropdown-menu\">" .
                          "<li><a href=\"new_post.php\">New post</a></li>" .
                          "<li role=\"separator\" class=\"divider\"></li>" .
                          "<li><a href=\"logout.php\">Logout</a></li>" .
                        "</ul>" .
                      "</li>";
                } else {
                  echo "<li><a href = \"login.php\">Login</a></li>";
                }
              ?>
            </ul>
          </div>
        </div>
      </nav>
      <div class = "row">
        <div class = "col-sm-12">
          <?php

            // --- Information messages ---
            if ($_GET["post_success"] == "true") {
              echo "<div class = \"alert alert-success\">" .
                    "<p>Post successful.</p>" .
                  "</div>";
            } elseif ($_GET["login_success"] == "true") {
              echo "<div class = \"alert alert-success\">" .
                    "<p>Welcome back, <strong>" . $_SESSION["user_username"] . "</strong>!</p>" .
                  "</div>";
            } else if ($_GET["logout_success"] == "true") {
              echo "<div class = \"alert alert-success\">" .
                    "<p>Logged out successfully.</p>" .
                  "</div>";
            } else if ($_GET["delete_success"] == "true") {
              echo "<div class = \"alert alert-success\">" .
                    "<p>Post successfully deleted.</p>" .
                  "</div>";
            } else if ($_GET["delete_success"] == "false") {
              echo "<div class = \"alert alert-danger\">" .
                    "<p>Error deleting post. Please contact your system administrator.</p>" .
                  "</div>";
            } else if ($_GET["unauthorised_deletion"] == "true") {
              echo "<div class = \"alert alert-danger\">" .
                    "<p>You are not authorised to delete that post.</p>" .
                  "</div>";
            }

            // --- Write posts to page ---
            // Connect to database
            $connection = new mysqli("127.0.0.1", "root", "", "blog");
            if ($connection->connect_error) {
              die("Connection failed: " . $connection->connect_error);
            }
            // Retrieve posts, most recent first
            $query = "SELECT * FROM posts ORDER BY id DESC";
            $result = $connection->query($query);

            // Loop through posts
            if ($result->num_rows > 0) {
              while($row = $result->fetch_assoc()) {
                // Get author name
                $query = "SELECT * FROM users WHERE id='" . $row["user_id"] . "'";
                $user_result = $connection->query($query);
                $user_row = $user_result->fetch_assoc();
                // Write post to page
                echo "<h1>" . $row["title"] . "</h1>";
                echo "<p>" . $row["body"] . "</p>";
                echo "<p class = \"help-block\">" .
                      "Written by <strong>" . $user_row["username"] . "</strong>" .
                      " at " . date("d/m/y h:i A", strtotime($row["created_at"]));
                if ($_SESSION["logged_in"] && $_SESSION["user_id"] == $row["user_id"]) {
                  echo " | <a href = \"delete.php?post_id=" . $row["id"] .
                      "\" onclick = \"return confirm('Are you sure you want to delete " .
                      "this post? This action cannot be undone.')\"" .
                      " class = \"btn btn-danger btn-xs\">Delete post</a>";
                }
                echo  "</p>";
                echo "<hr>";
              }
            } else {
              echo "<p>There are no posts yet. Get writing!</p>";
            }

          ?>
        </div>
      </div>
    </div>
  </body>
</html>
