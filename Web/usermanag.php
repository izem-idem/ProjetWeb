<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title> User management </title>
    <link rel="stylesheet" type="text/css" href="website.css">
</head>
<body>
  <header>
      <h1>CALI</h1>
  </header>

  <div class="topnav">
      <a href="menu.html">Home</a>
      <a href="AnnotatorArea.html"> Annotator area</a>
      <a href="ValidatorArea.html"> Validator area</a>
      <a class="active" href="usermanag.html"> User management</a> <!--Page active-->
      <a href="Add_genome.html"> Add genome</a>
      <button type="button" class="LogOut" onclick="window.location.href = 'LoginPage.html'">Log out </button>
  </div>
  <div>
    <?php
    include_once 'libphp/db_utils.php';
    connect_db ();


    $q_user_name = "SELECT email FROM website.users";

    $res_user_name = pg_query($db_conn, $q_user_name) or die (pg_last_error());
    $res_user_array = pg_fetch_all_columns($res_user_name);

    ?>

    <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="POST">

    <?php
    foreach ($res_user_array as $id_user_name){
      echo "<select name='sel".$id_user_name."'>
        <option value='Reader'> Reader</option>
        <option value='Annotator'> Annotator</option>
        <option value='Validator'> Validator</option>
        </select>
        <button class='little_submit_button' type='submit', name = 'submit".$id_user_name."'> Change role</button>";
      }



      if (isset($_POST["submit".$id_user_name])) {

        if ($_POST["sel".$id_user_name] == 'Reader'){
          $alter_role = "UPDATE website.users SET status = 'Reader' WHERE users.email = ".$id_user_name;
        }
        else if ($_POST["sel".$id_user_name] == 'Annotator') {
          $alter_role = "UPDATE website.users SET status = 'Annotator' WHERE users.email = ".$id_user_name;
        }
        else if ($_POST["sel".$id_user_name] == 'Validator') {
          $alter_role = "UPDATE website.users SET status = 'Validator' WHERE users.email = ".$id_user_name;
        }

      }
        echo "<button class='little_submit_button' type='submit', name = 'del".$id_user_name."'> Delete user</button>";
        if ($_POST["del".$id_user_name]) {
          $del_user = "DELETE FROM website.users
          WHERE email = $id_user_name";
        }


    ?>
  </form>

    <?php

    echo "</form>";
    while ($line = pg_fetch_array($res_user_name, null, PGSQL_ASSOC)) {
    echo "\t<tr>\n";
    foreach ($line as $col_value) {
    echo "\t\t<td>$col_value</td>\n";
    }
    echo "\t</tr>\n";
    }
    echo "</table>\n";
    pg_free_result($res_user_name);

    disconnect_db();
    ?>
  </div>

</body>
</html>
