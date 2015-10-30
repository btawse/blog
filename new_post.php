<?php
  session_start();

  // Redirect to login unless logged in
  if (!$_SESSION["user_username"] && !$_SESSION["user_id"]) {
    header("Location: login.php?login_prompt=true");
    die();
  }

  // Initialise variables
  $title = $body = "";
  $title_error = $body_error = "";

  // If method is POST then try and save blog post
  if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // Assign variables
    $title = $_POST["title"];
    $body = $_POST["body"];
    $user_id = $_SESSION["user_id"];

    // If title and body are present then proceed
    if (!empty($title) && !empty($body)) {

      // Connect to DB
      $connection = new mysqli("127.0.0.1", "root", "", "blog");
      if ($connection->connect_error) {
        die("Connection failed: " . $connection->connect_error);
      }

      // Try and save post - store outcome
      $query = "INSERT INTO posts (user_id, title, body) VALUES ('$user_id', '$title', '$body')";
      $post_success = $connection->query($query);
      if ($post_success) {
        // If post succeeded redirect to index - otherwise remain on this page to see error message
        header("Location: index.php?post_success=true");
        die();
      }

    }
    else {
      // Form validation failed
      if (empty($title)) {
        $title_error = "Please enter a title";
      }
      if (empty($body)) {
        $body_error = "Please enter some body text";
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
    <title>New post</title>
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
          <h1 class = "title">New post</h1>
          <?php
            if (isset($post_success) && !$post_success) {
              echo "<div class = \"alert alert-error\">" .
                    "<p>There was an error trying to save your post. Please contact the site administrator.</p>" .
                  "</div>";
            }
          ?>
          <form action = "new_post.php" method = "post" class = "form-horizontal">
            <div class = "form-group <?php if (!empty($title_error)) { echo 'has-error'; } ?>">
              <div class = "col-sm-2">
                <label class = "control-label">Title</label>
              </div>
              <div class = "col-sm-8">
                <input type = "text" name = "title" class = "form-control" value = "<?php echo $title; ?>">
                <?php if (!empty($title_error)) { echo "<span class = \"help-block\">" . $title_error . "</span>"; } ?>
              </div>
            </div>
            <div class = "form-group <?php if (!empty($body_error)) { echo 'has-error'; } ?>">
              <div class = "col-sm-2">
                <label class = "control-label">Body</label>
              </div>
              <div class = "col-sm-8">
                <textarea name = "body" cols = "100" rows = "13" class = "form-control"><?php echo $body; ?></textarea>
                <?php if (!empty($body_error)) { echo "<span class = \"help-block\">" . $body_error . "</span>"; } ?>
              </div>
            </div>
            <div class = "form-group">
              <div class = "col-sm-offset-2 col-sm-8">
                <input type = "submit" value = "Post" class = "btn btn-primary">
              </div>
            </div>
          </form>
        </div>
      </div>
    </div>
  </body>
</html>
