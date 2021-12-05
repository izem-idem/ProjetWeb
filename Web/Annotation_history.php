<?php
if(isset($_GET['id'])){
    $id = $_GET['id'];
} else{ //https://stackoverflow.com/questions/20300789/show-404-error-page-from-php-file-without-redirecting/20300839
    header("HTTP/1.0 404 Not Found");
    echo "<h1>404 Not Found</h1>";
    echo "The page that you have requested could not be found.";
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="author" content="Camille RABIER">
    <meta name="description" content="History of all annotation done for a transcript">
    <title>History of annotations</title>
    <link href="website.css" rel="stylesheet" type="text/css">
<!--    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">-->
<!--    <script src="Tabs.js" type="text/javascript"></script>-->

</head>
<body>
<!--HEADER-->
<header>
    <h1>CALI</h1>
</header>

<!--MENU-->
<div class="topnav">
    <a href="menu.html">Home</a>
    <a href="AnnotatorArea.php"> Annotator area</a>
    <a href="ValidatorArea.php"> Validator area</a>
    <a href="usermanag.php"> User management</a>
    <a href="Add_genome.php"> Add genome</a>
    <button class="fa fa-sign-out LogOut" onclick="window.location.href = 'LoginPage.html'" type="button">Log out</button>
</div>

<!--PAGE CONTENT-->
<h2> History of annotation </h2>
<div class="center">
    <table class='spaced_table'>
        <thead>
        <tr>
            <td>Date</td>
            <td>ID</td>
            <td>Annotation</td>
            <td>Validation</td>
        </tr>
        </thead>
        <tbody>
    <?php
    require_once 'libphp/db_utils.php';
    connect_db();
    $query = "SELECT id, id_transcript, id_gene, gene_biotype, transcript_biotype, symbol, description, commentary, validated, date_annotation FROM website.annotate WHERE id_transcript=$1";
    $result = pg_query_params($db_conn, $query,array($id)) or die("Error " . pg_last_error());
    while ($transcript = pg_fetch_assoc($result)){
        echo "
                <tr>
                    <td><p class='info'>".explode(".",$transcript['date_annotation'])[0]."</p> </td>
                    <td><p class='info'> ".$transcript['id_transcript']."</p><br>
                    </td>
                    <td>Gene ID :<br><p class='info'>  ".$transcript['id_gene']."</p><br>
                    Transcript biotype :<br><p class='info'> ".$transcript['transcript_biotype']."</p><br>
                    Gene biotype : <br><p class='info'> ".$transcript['gene_biotype']."</p><br>
                    Function : <br><p class='info'> ".$transcript['description']."</p><br>
                    Symbol : <br><p class='info'> ".$transcript['symbol']."</p></td>
                    <td> Status: <br>";
                        if($transcript['validated']==1){
                            echo "<p class='info'>Validated</p><br>";
                        } else if ($transcript['validated']==2){
                            echo "<p class='info'>Rejected</p><br>";
                        } else {
                            echo "<p class='info'>Waiting for validation</p><br>";
                        }
                    echo "Commentary :<br> <p class='info'>".$transcript['commentary']."</p><br></td>
                </tr>";
    }
    ?>
        </tbody></table>
</div>
<footer class="footer_2">
    <a href="Contact.php">Contact</a><br>
    <p>© CALI 2021</p>
</footer>
</body>
</html>
