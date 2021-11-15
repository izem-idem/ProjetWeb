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
            <p class='info title'>" . $id . "</p>
            <a href='Gene-ProtPage.html'>" . $id . " informations</a><br>";

            // Display of the validation status
            if ($annotation['validated'] == 0) { /*The annotation is waiting for validation*/
                echo "Waiting for validation";

            } elseif ($annotation['validated'] == 1) { /*The annotation is validated, the commentary is displayed*/
                echo "Status :<p>Validated</p>
                Validator :
                <label for='valid_" . $id . "'></label>
                <input class='info' disabled id='valid_" . $id . "' value='" . $annotation['validator_email'] . "'> <br>
                <br><br>Commentary: <br> ".$annotation['commentary'];

            } else { /*The annotation is rejected, the commentary is displayed*/
                echo "Status :<p>Rejected</p>
                Validator :
                <label for='valid_" . $id . "'></label>
                <input class='info' disabled id='" . $annotation['validator_email'] . "' value='" . $annotation['validator_email'] . "'> <br>
                <br>Commentary: <br> ".$annotation['commentary'];
            }

    //Display of the annotation submitted
    echo "<div class='title'>Annotations</div>
            <!--Input for id_gene-->
            <label for='gene_id_".$id."'>ID of gene</label>
            <input class='info' disabled id='gene_id_".$id."' value='".$annotation['id_gene']."'><br>
            
            <!--Input for gene_biotype-->
            <label for='gene_biotype_".$id."'>Biotype of gene</label>
            <input class='info' disabled id='gene_biotype_".$id."' value='".$annotation['gene_biotype']."'><br>
            
            <!--Input for symbol-->
            <label for='gene_symbol_".$id."'>Gene symbol</label>
            <input class='info' disabled id='gene_symbol_".$id."' value='".$annotation['symbol']."'><br>
            
            <!--Input for transcript_biotype-->
            <label for='prot_biotype_".$id."'> Biotype of protein</label>
            <input class='info' disabled id='prot_biotype_".$id."' value='".$annotation['transcript_biotype']."'><br>
            
            <!--Input for description-->
            <label for='desc_".$id."'>Description of function</label>
            <input class='info' disabled id='desc_".$id."' value='".$annotation['description']."'>
        </div>";
}
disconnect_db(); /*Disconnect from the database*/
?>
