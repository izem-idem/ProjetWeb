<?php
session_start();
if (isset($_SESSION['Email'])){
if ($_SESSION['Status']!='Admin'){
header("HTTP/1.0 404 Not Found");
echo "<h1>404 Not Found</h1>";
echo "The page that you have requested is not accessible for you.";
echo "<a href='search_page.php'>Go back to search page</a>";
exit();
}
}else {
header("Location: LoginPage.php");
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title> User management </title>
    <link rel="stylesheet" type="text/css" href="website.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css"> <!--CSS for log out button-->
</head>
<body>
<header>
    <h1>CALI</h1>
</header>

<div class="topnav">
    <?php require_once 'libphp/Menu.php';
    echo Menu($_SESSION['Status'],"usermanag.php")?>
</div>
<div class="center">
    <div class="container">
        <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="POST">
            <table class="spaced_table">
                <?php
                include_once 'libphp/db_utils.php';
                connect_db();


                $q_name_status = "SELECT email, status, lastconnection FROM website.users WHERE status != 'Admin' AND Access = TRUE";


                $res_name_status = pg_query($db_conn, $q_name_status) or die (pg_last_error());
                $res_user_array = pg_fetch_all_columns($res_name_status);
                $res_last_co = pg_fetch_all_columns($res_name_status, 2); // récupère date de dernière connexion


                while ($id = pg_fetch_assoc($res_name_status)) {
                    $id_email = $id['email']; // adresse mail
                    $id_user_name = explode(".", $id_email)[0]; // id_user_name : tout ce qu'il y a avant '.com' ou '.fr' de l'adresse mail
                    $id_last_co = explode(".",$id['lastconnection'])[0]; // date de dernière connection

                    if (isset($_POST["del" . $id_user_name])) { // si on supprime un utilisateur
                        // celui-ci perd l'accès au site (mais n'est pas supprimé de la base de données)
                        $del_user = "UPDATE website.users
                                SET Access = FALSE
                                WHERE email = '$id_email'";

                        $res_delete_role = pg_query($db_conn, $del_user) or die(pg_last_error());

                    } else {
                        if (isset($_POST["submit" . $id_user_name])) { // Change le rôle d'un utilisateur
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
                        echo "<td><button class='little_submit_button' type='submit' name = 'del" . $id_user_name . "'> Delete user</button></td>";
                        echo '<td>' . $id_last_co . '</td></tr>';

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
