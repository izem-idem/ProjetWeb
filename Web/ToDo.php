<?php
/*Author : Camille RABIER*/
/*This page is used by Annotator_area.php to get all the annotations to do by the logged annotator*/
// Connect to database and find non-assigned transcript
    // Connect to database
include_once 'libphp/db_utils.php';
connect_db();

// QUERIES
    // Find annotations to do for annotator connected
$annotator = "camrabier@gmail.com"; /*Annotator connected*/

    /*Query to select the infos to echo for all transcripts assigned to the annotator*/
$to_annotate = "SELECT id_transcript,sequence_nt, sequence_p FROM website.transcript WHERE transcript.annotator_email = $1 AND annotation=2";
$to_annotate = pg_query_params($db_conn, $to_annotate, array($annotator));

    /*Get all the id of transcript to annotate*/
$id_transcript_tabs = pg_fetch_all_columns($to_annotate);

    // Insert annotations in DB
    /*Insert annotations in annotate*/
$annotation = "INSERT INTO website.annotate(id_transcript, id_gene, gene_biotype, transcript_biotype, symbol, description, validated, date_annotation, annotator_email) VALUES ($1,$2,$3,$4,$5,$6,0,'now',$7)";
/*The status of validated is updated to 0 for "Waiting for validation" and the annotation date is the current time, all other fields are given with $_POST variables*/
    /*Update transcript annotation status*/
$update_status = "UPDATE website.transcript SET annotation=1 WHERE id_transcript=$1";
/*The status of annotation of the transcript is updated to 1 for "Annotated (even if no validated)"*/


//Display of the transcript tabs
echo "<div class='tab'>";
/*For all transcript a link is generated*/
foreach ($id_transcript_tabs as $tab) {
    if (!isset($_POST["submit_" . $tab])) { /*If an annotation has been submitted and will be treated on this chargement, no tab for this transcript should be displayed*/
        echo "<button class='tablinks' onclick='openTab(event,&quot;todo_" . $tab . "&quot;,true)'>" . $tab . "</button>";
    }
}
echo "</div>";

//Display of the annotations areas for all transcript
while ($transcript = pg_fetch_assoc($to_annotate)) {/*while there exists rows of the query result not treated*/
    $id = $transcript['id_transcript'];

    if (isset($_POST["submit_" . $id])) { /*Find if the submit has been clicked for this transcript*/
        /*Get all the annotation entered*/
        $gene_id = $_POST["gene_id_" . $id];
        $gene_biotype = $_POST["gene_biotype_" . $id];
        $gene_symbol = $_POST["gene_symbol_" . $id]; /*Can be empty*/
        $prot_biotype = $_POST["prot_biotype_" . $id];
        $description = $_POST["description_" . $id];
        if (empty($gene_symbol)){ /*if no annotation for gene_symbol was not given, no value will be inserted for gene_symbol in the DB*/
            $annotations = pg_query_params($db_conn, $annotation, array($id, $gene_id, $gene_biotype, $prot_biotype, null, $description, $annotator)) or die("Error " . pg_last_error());
        } else{
            $annotations = pg_query_params($db_conn, $annotation, array($id, $gene_id, $gene_biotype, $prot_biotype, $gene_symbol, $description, $annotator)) or die("Error " . pg_last_error());
        }
        $update_status = pg_query_params($db_conn, $update_status, array($id)) or die("Error " . pg_last_error()); /*Status of annotation is updated to "annotation exists"*/
        echo "Your annotation for " . $id . " has been submitted";
    } else {/*If no annotation has been submitted the annotations fields are displayed*/
        echo "<div class='tabcontent' id=todo_" . $id . ">     
            <form action=" . $_SERVER['PHP_SELF'] . " method='POST'>
                <!--This form redirects to itself when the user submits his annotation-->
                
            <!--Informations on transcript-->
                <p class='info title'>" . $id . "</p> <!-- Name of transcript-->
                <a href='Gene-ProtPage.html'>" . $id . " informations</a><br> 
                    <!--Page with known informations on transcript-->
                
                <!--Sequences for quick access-->
                <div class='double'>
                    Nucleotidic sequence: <br>
                    <p>" . $transcript['sequence_nt'] . "</p><br>
                </div>
                <div class='double'>
                    Proteic sequence: <br>
                    <p>" . $transcript['sequence_p'] . "</p><br>
                </div>
                <br>
                
            <!--Annotations fields : for all inputs, the maxlength corresponds to the maximal string accepted by postgres--> 
                <!--Input for id_gene-->
                <label for='gene_id_" . $id . "'>ID of gene</label>
                <input id='gene_id_" . $id . "' name='gene_id_" . $id . "' 
                    maxlength='50' required 
                    placeholder='Enter the id of the gene'><br>
                    
                <!--Input for gene_biotype-->
                <label for='gene_biotype_" . $id . "'>Biotype of gene</label>
                <div class='info_span'><!--Button to have extra informations on what to enter-->
                    <i class='fa fa-question-circle'></i>
                    <span class='extra-info'>
                        Generally it is protein-coding
                    </span>
                </div>
                <input id='gene_biotype_" . $id . "' name='gene_biotype_" . $id . "' 
                    maxlength='50' required
                    placeholder='Enter the gene biotype'><br>    
                
                <!--Input for symbol-->
                <label for='gene_symbol_" . $id . "'>Gene symbol</label>
                <div class='info_span'><!--Button to have extra informations on what to enter-->
                    <i class='fa fa-question-circle'></i>
                    <span class='extra-info'>
                        It is the abbreviation of function name, if it is an hypothetical protein, you can ignore this field
                    </span>
                </div>
                <input id='gene_symbol_" . $id . "' name='gene_symbol_" . $id . "' 
                    maxlength='20'
                    placeholder='Enter the gene symbol'><br>
                 
                 <!--Input for transcript_biotype-->
                <label for='prot_biotype_" . $id . "'> Biotype of protein</label>
                <div class='info_span'><!--Button to have extra informations on what to enter-->
                    <i class='fa fa-question-circle'></i>
                    <span class='extra-info'>
                        Generally it is protein-coding
                    </span>
                </div>
                <input id='prot_biotype_" . $id . "' name='prot_biotype_" . $id . "' 
                    maxlength='50' required
                    placeholder='Enter the transcript biotype'><br>
                    
                <!--Input for description-->
                <label for='description_" . $id . "'>Description of function</label>
                <input id='description_" . $id . "' name='description_" . $id . "' 
                    maxlength='200' required
                    placeholder='Enter the function of the transcript'><br>
                
                <!--SUBMIT BUTTON FOR ANNOTATION-->
                <button name= 'submit_" . $id . "'class='little_submit_button' type='submit'> Submit</button>
            </form>
        </div>";
    }
}

?>