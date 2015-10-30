<?php
  session_start();

  if($_SESSION["logged_in"] && $_GET["post_id"]) {

    // Connect to DB
    $connection = new mysqli("127.0.0.1", "root", "", "blog");
    if ($connection->connect_error) {
      die("Connection failed: " . $connection->connect_error);
    }

    // Retrieve post from DB so we can check user ID
    $post_id = $_GET["post_id"];
    $query = "SELECT * FROM posts WHERE id='$post_id'";
    $result = $connection->query($query);
    $row = $result->fetch_assoc();

    if ($row["user_id"] == $_SESSION["user_id"]) {
      // If user is authorised then delete post and redirect to index
      $query = "DELETE FROM posts WHERE id='$post_id'";
      if ($connection->query($query)) {
        header("Location: index.php?delete_success=true");
        die();
      } else {
        header("Location: index.php?delete_success=false");
        die();
      }
    } else {
      // Otherwise redirect to index with error message
      header("Location: index.php?unauthorised_deletion=true");
      die();
    }

  }

?>
