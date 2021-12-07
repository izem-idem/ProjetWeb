<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title> User management </title>
    <link rel="stylesheet" type="text/css" href="website.css">
</head>
<style>
    table{
        width: 100%;
    }
    tr>td{
        padding-bottom: 1em;
    }
</style>
<body>
<header>
    <h1>CALI</h1>
</header>

<div class="topnav">
    <a href="menu.html">Home</a>
    <a href="AnnotatorArea.php"> Annotator area</a>
    <a href="ValidatorArea.php"> Validator area</a>
    <a class="active" href="usermanag.php"> User management</a> <!--Page active-->
    <a href="Add_genome.php"> Add genome</a>
    <button type="button" class="LogOut" onclick="window.location.href = 'LoginPage.html'">Log out</button>
</div>
<div class="center">
    <div class="container">
        <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="POST">
            <table>
                <?php
                include_once 'libphp/db_utils.php';
                connect_db();


                $q_name_status = "SELECT email, status FROM website.users WHERE status != 'Admin' AND Access = TRUE";


                $res_name_status = pg_query($db_conn, $q_name_status) or die (pg_last_error());
                $res_user_array = pg_fetch_all_columns($res_name_status);
                $res_status_user = pg_fetch_all_columns($res_name_status, 1);


                while ($id = pg_fetch_assoc($res_name_status)) {
                    $id_email = $id['email'];
                    $id_user_name = explode(".", $id_email)[0];

                    if (isset($_POST["del" . $id_user_name])) {

                        $del_user = "UPDATE website.users
                                SET Access = FALSE
                                WHERE email = '$id_email'";
                        echo $del_user;
                        $res_delete_role = pg_query($db_conn, $del_user) or die(pg_last_error());
                    } else {
                        if (isset($_POST["submit" . $id_user_name])) {
                            if ($_POST["sel" . $id_user_name] == 'Reader') {
                                $alter_role = "UPDATE website.users SET status = 'Reader' WHERE users.email = '$id_email'";
                            } else if ($_POST["sel" . $id_user_name] == 'Annotator') {
                                $alter_role = "UPDATE website.users SET status = 'Annotator' WHERE users.email = '$id_email'";
                            } else if ($_POST["sel" . $id_user_name] == 'Validator') {
                                $alter_role = "UPDATE website.users SET status = 'Validator' WHERE users.email = '$id_email'";
                            }


                            $res_alter_role = pg_query($db_conn, $alter_role) or die(pg_last_error());
                            $id_status = $_POST["sel" . $id_user_name];
                        } else {
                            $id_status = $id["status"];
                        }
                        echo '<tr>
                                <td class="title"> ' . $id_email . '</td>';
                        echo '<td>' . $id_status . '</td>';
                        echo "<td> <select name='sel" . $id_user_name . "'>
                                <option value='Reader'> Reader</option>
                                <option value='Annotator'> Annotator</option>
                                <option value='Validator'> Validator</option>
                                </select>
                                <button class='little_submit_button' type='submit' name = 'submit" . $id_user_name . "'> Change role</button> </td>";
                        echo "<td><button class='little_submit_button' type='submit' name = 'del" . $id_user_name . "'> Delete user</button></td></tr>";

                    }
                }


                disconnect_db();
                ?>
            </table>
        </form>
    </div>
</div>

</body>
</html>
