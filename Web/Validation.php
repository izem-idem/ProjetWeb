<?php
/*Author : Camille RABIER*/
/*This page is used by Validator_area.php to validate the annotations submitted
The submitted annotations can be validated by any validator, it does not need to be the one that assigned*/
// Connect to database and find non-assigned transcript
    // Connect to database
include_once 'libphp/db_utils.php';
connect_db();
    // QUERIES
        // Find annotations to validate
$validator = "camrabier@gmail.com";
$tovalidate = "SELECT id_transcript, id_gene, gene_biotype, transcript_biotype, symbol, description, annotator_email FROM website.annotate WHERE annotate.validated=0";
$tovalidate = pg_query_params($db_conn, $tovalidate, array());
$id_transcript_tabs = pg_fetch_all_columns($tovalidate);

        //Update annotate
$update = "UPDATE website.annotate SET validator_email=$1, validated=$2, commentary=$3 WHERE id_transcript=$4";

echo "<div class='tab'>";
foreach ($id_transcript_tabs as $tab){
    if (!isset($_POST["reject_".$tab]) && !isset($_POST["validate_".$tab])){
        echo "<button class='tablinks' onclick='openTab(event,&quot;valid_".$tab."&quot;,true)'>".$tab."</button>";
    }
}
echo "</div>";
while ($annotation = pg_fetch_assoc($tovalidate)) {
    $id = $annotation['id_transcript'];
    if (isset($_POST["validate_" . $id])) {
        $comment = $_POST["comment_" . $id];
        $update_status = pg_query_params($db_conn, $update, array($validator,1,$comment,$id)) or die("Error " . pg_last_error());
        echo "The annotation for ".$id." has been validated<br>";
    }else if (isset($_POST["reject_" . $id])) {
        $comment = $_POST["comment_" . $id];
        $update_status = pg_query_params($db_conn, $update, array($validator,2,$id)) or die("Error " . pg_last_error());
        echo "The annotation for ".$id." has been rejected<br>";
    }else{
        echo "<div class='tabcontent' id=valid_" . $id . ">
            <form action=" . $_SERVER['PHP_SELF'] . " method='POST'>
                <p class='info title'>" . $id . "</p>
                <a href='Gene-ProtPage.html'>" . $id . " informations</a><br>
                Annotator :
                <label for='user" . $id . "'></label>
                <input class='info' disabled id='user" . $id . "' name='user" . $id . "' value='" . $annotation['annotator_email'] . "'><br>
                <label for='gene_id_" . $id . "'>ID of gene</label>
                <input class='info' disabled id='gene_id_" . $id . "' name='gene_" . $id . "' value='" . $annotation['id_gene'] . "'><br>
                <label for='gene_biotype_" . $id . "'>Biotype of gene</label>
                <input class='info' disabled id='gene_biotype_" . $id . "' name='gene_biotype_" . $id . "' value='" . $annotation['gene_biotype'] . "'><br>
                <label for='gene_symbol_" . $id . "'>Gene symbol</label>
                <input class='info' disabled id='gene_symbol_" . $id . "' name='gene_symbol_" . $id . "' value='" . $annotation['symbol'] . "'><br>
                <label for='prot_biotype_" . $id . "'> Biotype of protein</label>
                <input class='info' disabled id='prot_biotype_" . $id . "' name='prot_biotype_" . $id . "' value='" . $annotation['transcript_biotype'] . "'><br>
                <label for='description_" . $id . "'>Description of function</label>
                <input class='info' disabled id='description_" . $id . "' name='description_" . $id . "' value='" . $annotation['description'] . "'><br>
                <label for='comment_" . $id . "'>Comment</label><br>
                <textarea cols='40' rows='5' id='comment_" . $id . "' name='comment_" . $id . "' placeholder='Enter your commentary here...' required maxlength='500'></textarea>
                <br>
                <button class='little_submit_button' type='submit' name='validate_".$id."'> Validate</button>
                <button class='little_submit_button' type='submit' name='reject_".$id."'> Reject</button>
            </form>
        </div>";
    }
}
?>

