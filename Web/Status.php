<?php
/*Author : Camille RABIER*/
/*This page is used by Annotator_area.php to get show the status of the submitted annotations by the annotator connected*/
// Connect to database and find non-assigned transcript
    // Connect to database
include_once 'libphp/db_utils.php';
connect_db();
// QUERIES
// Find annotations to do for annotator connected
$annotator = "camrabier@gmail.com";
$annotated = "SELECT id_transcript, id_gene, gene_biotype, transcript_biotype, symbol, description, commentary, validated, annotator_email, validator_email FROM website.annotate WHERE annotate.annotator_email = $1";
$annotated = pg_query_params($db_conn, $annotated, array($annotator));
$id_transcript_tabs = pg_fetch_all_columns($annotated);

echo "<div class='tab'>";
foreach ($id_transcript_tabs as $tab) {
//    If use '' or "" instead of &quot;, the name is in lowercase for some unknown reasons
    echo "<button class='tablinks' onclick='openTab(event,&quot;status_" . $tab . "&quot;,true)'>" . $tab . "</button>";
}
echo "</div>";
while ($annotation = pg_fetch_assoc($annotated)) {
    $id = $annotation['id_transcript'];
    echo "<div class='tabcontent' id=status_" . $id . ">
            <p class='info title'>" . $id . "</p>
            <a href='Gene-ProtPage.html'>" . $id . " informations</a><br>";
            if ($annotation['validated'] == 0) {
                echo "Waiting for validation";
            } elseif ($annotation['validated'] == 1) {
                echo "Status :<p>Validated</p>
                Validator :
                <label for='valid_" . $id . "'></label>
                <input class='info' disabled id='valid_" . $id . "' value='" . $annotation['validator_email'] . "'> <br>
                <br><br>Commentary: <br> ".$annotation['commentary'];
            } else {
                echo "Status :<p>Rejected</p>
                Validator :
                <label for='valid_" . $id . "'></label>
                <input class='info' disabled id='" . $annotation['validator_email'] . "' value='" . $annotation['validator_email'] . "'> <br>
                <br>Commentary: <br> ".$annotation['commentary'];
            }
echo "<div class='title'>Annotations</div>
            <br>
            <label for='gene_id_".$id."'>ID of gene</label>
            <input class='info' disabled id='gene_id_".$id."' value='".$annotation['id_gene']."'><br>
            <label for='gene_biotype_".$id."'>Biotype of gene</label>
            <input class='info' disabled id='gene_biotype_".$id."' value='".$annotation['gene_biotype']."'><br>
            <label for='gene_symbol_".$id."'>Gene symbol</label>
            <input class='info' disabled id='gene_symbol_".$id."' value='".$annotation['symbol']."'><br>
            <label for='prot_biotype_".$id."'> Biotype of protein</label>
            <input class='info' disabled id='prot_biotype_".$id."' value='".$annotation['transcript_biotype']."'><br>
            <label for='desc_".$id."'>Description of function</label>
            <input class='info' disabled id='desc_".$id."' value='".$annotation['description']."'>
        </div>";
}

?>
