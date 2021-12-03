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


    $q_name_status = "SELECT email, status FROM website.users WHERE status != 'Admin'";


    $res_name_status = pg_query($db_conn, $q_name_status) or die (pg_last_error());
    $res_user_array = pg_fetch_all_columns($res_name_status);
    $res_status_user = pg_fetch_all_columns($res_name_status, 1);


    ?>
    <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="POST">

    <?php

    echo '<table>';
    while ($id = pg_fetch_assoc($res_name_status))
     {
       $id_user_name = $id['email'];
       $id_status = $id['status'];
      echo '<tr> <td> '.$id_user_name .'</td>';
      echo '<td>' .$id_status. '</td>';
      echo "<td> <select name='sel".$id_user_name."'>
        <option value='Reader'> Reader</option>
        <option value='Annotator'> Annotator</option>
        <option value='Validator'> Validator</option>
        </select>
        <button class='little_submit_button' type='submit', name = 'submit".$id_user_name."'> Change role</button> </td>";
      echo "<td><button class='little_submit_button' type='submit', name = 'del".$id_user_name."'> Delete user</button>";





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
            echo $alter_role;
            $res_alter_role = pg_query($db_conn, $alter_role) or die(pg_last_error());
      }

      if (isset($_POST["del".$id_user_name])) {

        $del_user = "DELETE FROM website.users
        WHERE email = $id_user_name";
        echo $del_user;
        $res_delete_role = pg_query($db_conn, $del_user) or die(pg_last_error());
      }

    }





    disconnect_db();
    ?>
  </form>






  </div>

</body>
</html>
