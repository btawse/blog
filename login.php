<?php

  // Initialise variables
  $username = $password = "";
  $username_error = $password_error = "";

  // If method is POST then try and login
  if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // Assign variables
    $username = $_POST["username"];
    $password = $_POST["password"];

    // If username and password are present then proceed
    if (!empty($username) && !empty($password)) {

      // Connect to DB
      $connection = new mysqli("127.0.0.1", "root", "", "blog");
      if ($connection->connect_error) {
        die("Connection failed: " . $connection->connect_error);
      }

      // Retrieve user by name
      $query = "SELECT * FROM users WHERE `username` = '$username'";
      $result = $connection->query($query);
      // If user exists then check password and log them in
      if ($result && $result->num_rows) {
        $user = $result->fetch_assoc();
        if (password_verify($password, $user["hashed_password"])) {
          session_start();
          $_SESSION["logged_in"] = true;
          $_SESSION["user_id"] = $user["id"];
          $_SESSION["user_username"] = $user["username"];
          header("Location: index.php?login_success=true");
          die();
        }
        else {
          $password_error = "Sorry, that password is incorrect";
        }
      }
      else {

      }

    }
    else {
      // Form validation failed
      if (empty($username)) {
        $username_error = "Please enter your username";
      }
      if (empty($password)) {
        $password_error = "Please enter your password";
      }
    }

  }

?>
<!DOCTYPE html>
<html>
  <head>
    <meta name="viewport" content="width=device-width">
    <link rel = "stylesheet" type = "text/css" href = "css/bootstrap.min.css">
    <link rel = "stylesheet" type = "text/css" href = "css/site.css">
    <script type = "text/javascript" src = "js/jquery-1.11.3.min.js"></script>
    <script type = "text/javascript" src = "js/bootstrap.min.js"></script>
    <title>Login</title>
  </head>
  <body>
    <div class = "container">
      <!-- Navbar -->
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
      <!-- End of navbar -->
      <div class = "row">
        <div class = "col-sm-12">
          <h1 class = "title">Login</h1>
          <?php
            if ($_GET["registration_success"] == "true") {
              echo "<div class = \"alert alert-success\">" .
                    "<p>Registration successful. You can now log in.</p>" .
                  "</div>";
            }
            if ($_GET["login_prompt"] == "true") {
              echo "<div class = \"alert alert-info\">" .
                    "<p>You must login to write a new post.</p>" .
                  "</div>";
            }
          ?>
          <!-- Login form -->
          <form action = "login.php" method = "post" class = "form-horizontal">
            <div class = "form-group <?php if (!empty($username_error)) { echo 'has-error'; } ?>">
              <div class = "col-sm-2">
                <label class = "control-label">Username</label>
              </div>
              <div class = "col-sm-4">
                <input type = "text" name = "username" class = "form-control" value = "<?php echo $username; ?>">
                <?php if (!empty($username_error)) { echo "<span class = \"help-block\">" . $username_error . "</span>"; } ?>
              </div>
            </div>
            <div class = "form-group <?php if (!empty($password_error)) { echo 'has-error'; } ?>">
              <div class = "col-sm-2">
                <label class = "control-label">Password</label>
              </div>
              <div class = "col-sm-4">
                <input type = "password" name = "password" class = "form-control">
                <?php if (!empty($password_error)) { echo "<span class = \"help-block\">" . $password_error . "</span>"; } ?>
              </div>
            </div>
            <div class = "form-group">
              <div class = "col-sm-offset-2 col-sm-4">
                <input type = "submit" value = "Login" class = "btn btn-primary">
              </div>
            </div>
            <div class = "form-group">
              <div class = "col-sm-offset-2 col-sm-4">
                <a href = "register.php">Not registered?</a>
              </div>
            </div>
          </form>
          <!-- End of login form -->
        </div>
      </div>
    </div>
  </body>
</html>
