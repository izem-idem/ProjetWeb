<?php
/*Author : Camille RABIER*/
/*This page is used by Annotator_area.php to display the status of the submitted annotations by the annotator connected*/

// Connect to database and find annotated transcript status
    // Connect to database
include_once 'libphp/db_utils.php';
connect_db();

// QUERIES
$annotator = $_SESSION['Email'];

// Find annotations done by the annotator connected
$annotated_query = "SELECT id_transcript, id_gene, gene_biotype, transcript_biotype, symbol, description, commentary, validated, annotator_email, validator_email FROM website.annotate WHERE annotate.annotator_email = $1";

$annotated_results = pg_query_params($db_conn, $annotated_query, array($annotator)) or die("Error " . pg_last_error());
/*Query to select the infos to echo for all annotation done by the annotator*/

$id_transcript_tabs = pg_fetch_all_columns($annotated_results);
/*Get all the id of transcript to annotate*/

//DISPLAY OF SUBMITTED ANNOTATION
//For each annotation a tablink is created
echo "<div class='tab'>";
foreach ($id_transcript_tabs as $tab) {
//    If use '' or "" instead of &quot;, the name is in lowercase for some unknown reasons
    echo "<button class='tablinks' onclick='openTab(event,&quot;status_" . $tab . "&quot;,true)'>" . $tab . "</button>";
}
echo "</div>";

//For each annotation a div is created, that can accessed through the tablinks
while ($annotation = pg_fetch_assoc($annotated_results)) {
    $id = $annotation['id_transcript'];

    //Display of the annotations done
    /*Header*/
    echo "<div class='tabcontent' id=status_" . $id . ">
            <p class='title'>" . $id . "</p>
            <a href='../Web_izem/Gene-ProtPage.php?id=$id'>" . $id . " informations</a><br>";

            // Display of the validation status
            if ($annotation['validated'] == 0) { /*The annotation is waiting for validation*/
                echo "<table class='spaced_table'>
                        <tr><td class='double'>Status :</td><td class='double'><p class='info'>Waiting for validation</p></td></tr></table>";

            } elseif ($annotation['validated'] == 1) { /*The annotation is validated, the commentary is displayed*/
                echo "<table class='spaced_table'>
                        <tr><td class='double'>Status :</td><td class='double'><p class='info'>Validated</p></td></tr>
                        <tr><td class='double'>Validator : </td><td class='double'><p class='info'>" . $annotation['validator_email'] . "</p></td></tr>
                        <tr><td class='double'>Commentary:</td><td class='double'><p class='info'>". $annotation['commentary']."</p></td></tr></table>";

            } else { /*The annotation is rejected, the commentary is displayed*/
                echo "<table class='spaced_table'>
                        <tr><td class='double'>Status :</td><td class='double'><p class='info'>Rejected</p></td></tr>
                        <tr><td class='double'>Validator : </td><td class='double'><p class='info'>" . $annotation['validator_email'] . "</p></td></tr>
                        <tr><td class='double'>Commentary:</td><td class='double'><p class='info'>". $annotation['commentary']."</p></td></tr></table>";
            }

    /*Fields of annotation*/
    echo "<div class='title'>Annotations</div>
            <table class='spaced_table'>
                    <tr>                          
                        <td class='double'>ID of gene : </td>
                        <td class='double'><p class='info'>" . $annotation['id_gene'] . "</p></td>
                    </tr>
                    <tr>
                        <td class='double'>Biotype of gene : </td>
                        <td class='double'><p class='info'>" . $annotation['gene_biotype'] . "</p></td>
                    </tr>
                    <tr>
                        <td class='double'>Gene symbol : </td>
                        <td class='double'><p class='info'>" . $annotation['symbol'] . "</p></td>
                    </tr>
                    <tr>
                        <td class='double'>Biotype of transcript : </td>
                        <td class='double'><p class='info'>" . $annotation['transcript_biotype'] . "</p></td>
                    </tr>
                    <tr>
                        <td class='double'>Description of function : </td>
                        <td class='double'><p class='info'>" . $annotation['description'] . "</p></td>
                    </tr>
                </table>         
        </div>";
}
disconnect_db(); /*Disconnect from the database*/
?>
