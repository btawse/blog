<?php
  session_start();
  unset($_SESSION["logged_in"]);
  unset($_SESSION["user_username"]);
  unset($_SESSION["user_id"]);
  header("Location: index.php?logout_success=true");
  die();
?>
