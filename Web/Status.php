<?php
/*Author : Camille RABIER*/
/*This page is used by Annotator_area.php to display the status of the submitted annotations by the annotator connected*/

// Connect to database and find annotated transcript status
    // Connect to database
include_once 'libphp/db_utils.php';
connect_db();

// QUERIES
$annotator = "camrabier@gmail.com"; /*TODO modify for the logged one*/

// Find annotations done by the annotator connected
$annotated_query = "SELECT id_transcript, id_gene, gene_biotype, transcript_biotype, symbol, description, commentary, validated, annotator_email, validator_email FROM website.annotate WHERE annotate.annotator_email = $1";

$annotated = pg_query_params($db_conn, $annotated_query, array($annotator)) or die("Error " . pg_last_error());
/*Query to select the infos to echo for all annotation done by the annotator*/

$id_transcript_tabs = pg_fetch_all_columns($annotated);
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
while ($annotation = pg_fetch_assoc($annotated)) {
    $id = $annotation['id_transcript'];

    //Display of the annotations done
    echo "<div class='tabcontent' id=status_" . $id . ">
            <p class='title'>" . $id . "</p>
            <a href='Gene-ProtPage.html'>" . $id . " informations</a><br>";

            // Display of the validation status
            if ($annotation['validated'] == 0) { /*The annotation is waiting for validation*/
                echo "Waiting for validation";

            } elseif ($annotation['validated'] == 1) { /*The annotation is validated, the commentary is displayed*/
                echo "Status :<p>Validated</p>
                Validator :
                <p class='info'>" . $annotation['validator_email'] . "</p>
                <br><br>Commentary: <br> ".$annotation['commentary'];

            } else { /*The annotation is rejected, the commentary is displayed*/
                echo "Status :<p>Rejected</p>
                Validator :
                <p class='info'>" . $annotation['validator_email'] . "</p>
                <br>Commentary: <br> ".$annotation['commentary'];
            }

    //Display of the annotation submitted
    echo "<div class='title'>Annotations</div>
            <!--Input for id_gene-->
            ID of gene : 
            <p class='info'>".$annotation['id_gene']."</p><br>
         
            <!--Input for gene_biotype-->
            Biotype of gene : 
            <p class='info'>".$annotation['gene_biotype']."</p><br>
            
            <!--Input for symbol-->
            Gene symbol : 
            <p class='info'>".$annotation['symbol']."</p><br>
            
            <!--Input for transcript_biotype-->
            Biotype of transcript : 
            <p class='info'>".$annotation['transcript_biotype']."</p><br>
            
            <!--Input for description-->
            Description of function : 
            <p class='info'>".$annotation['description']."</p>
        </div>";
}
disconnect_db(); /*Disconnect from the database*/
?>
