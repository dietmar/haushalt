<?php
require_once('program/Config.php');
require_once('program/Haushalt.php');

session_name('haushalt');
session_start();

if(!isset($_SESSION['okay'])) {
   if($_POST['thepassword'] == HAUSHALT_PASSWORD) {
      $_SESSION['okay'] = true;
   }
   else {
      echo '<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
      "http://www.w3.org/TR/html4/loose.dtd">
      <html>
      <head>
      <title>Haushalt</title>
      </head>
      <body>
      <form action="index.php" method="post">
      <input type="text" id="username" value="haushalt1190" style="display: none;">
      <input type="password" id="pwdfield" name="thepassword" size="30">
      <button type="submit">submit</button>
      </form>';
      if($_POST['thepassword']) {
          echo '<div>no.</div>';
      }
      echo '<script type="text/javascript">
      document.getElementById("pwdfield").focus();
      </script>
      </body>
      </html>';
      die();
   }
}
$h = new Haushalt();
$h->start();

?>
