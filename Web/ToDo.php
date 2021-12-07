<?php
/*Author : Camille RABIER*/
/*This page is used by Annotator_area.php to get all the annotations to do by the logged annotator and update the DB with the annotation
If the annotator has doubts on how to fill the fields, he can hover his mouse over the question marks to have more information
All the fields, but gene symbol are required. Once all the fields required are filled, he can click on submit*/
/*Source for extra info : https://codepen.io/EasyBoarder/pen/Lkzzjy*/


// Connect to database
require_once 'libphp/db_utils.php'; /*Functions to connect and disconnect the database*/
connect_db();

// QUERIES
$annotator = $_SESSION['Email']; /*Annotator connected TODO modify for logged one*/

// Find annotations to do for annotator connected. The query select all the transcript that have been assigned to the user
// excluding waiting for validation (annotation = 0) or already validated (validated =1) annotation.
$to_annotate_query = "SELECT id_transcript,sequence_nt, sequence_p, annotation FROM website.transcript WHERE transcript.annotator_email = $1 AND annotation != 0 
                        EXCEPT SELECT transcript.id_transcript,sequence_nt, sequence_p,annotation FROM website.transcript, website.annotate  
                                WHERE annotate.id_transcript=transcript.id_transcript AND validated IN (0,1)";
/*annotation=2 for the annotations has been assigned but no transcript has been done*/

$to_annotate = pg_query_params($db_conn, $to_annotate_query, array($annotator)) or die("Error " . pg_last_error());
/*Query to select the infos to echo for all transcripts assigned to the annotator*/

$id_transcript_tabs = pg_fetch_all_columns($to_annotate);
/*Get all the id of transcript to annotate*/


// Insert annotations in DB and update transcript status
$annotation_query = "INSERT INTO website.annotate(id_transcript, id_gene, gene_biotype, transcript_biotype, symbol, description, validated, date_annotation, annotator_email) VALUES ($1,$2,$3,$4,$5,$6,0,'now',$7)";
/*The status of validated is updated to 0 for "Waiting for validation" and the annotation date is the current time, all other fields are given with $_POST variables*/

$update_status_query = "UPDATE website.transcript SET annotation=1 WHERE id_transcript=$1";
/*The status of annotation of the transcript is updated to 1 for "Annotated (even if no validated)"*/



//DISPLAY AND SUBMITTING OF ANNOTATION
/*For all transcript a link is generated*/
echo "<div class='tab'>";
foreach ($id_transcript_tabs as $tab) {
    if (!isset($_POST["submit_" . $tab])) { /*If an annotation has been submitted and will be treated on this chargement, no tab for this transcript should be displayed*/
         /*   If use '' or "" instead of &quot;, the name is in lowercase for some unknown reasons*/
        echo "<button class='tablinks' onclick='openTab(event,&quot;todo_" . $tab . "&quot;,true)'>" . $tab . "</button>";
    }
}
echo "</div>";

// For each transcript a div is created, that can be accesed through tablinks
while ($transcript = pg_fetch_assoc($to_annotate)) {/*while there exists rows of the query result not treated*/
    $id = $transcript['id_transcript'];

    //Updating of database for new annotated transcrips
    if (isset($_POST["submit_" . $id])) { /*Find if the submit has been clicked for this transcript*/
        /*Get all the annotation entered*/
        /*By filtering the input, we verify that no harmul characters like script injections are given as input*/
        $gene_id = $comment = filter_var($_POST["gene_id_" . $id],FILTER_SANITIZE_STRING);
        $gene_biotype = filter_var($_POST["gene_biotype_" . $id],FILTER_SANITIZE_STRING);
        $gene_symbol = filter_var($_POST["gene_symbol_" . $id],FILTER_SANITIZE_STRING); /*Can be empty*/
        $prot_biotype = filter_var($_POST["prot_biotype_" . $id],FILTER_SANITIZE_STRING);
        $description = filter_var($_POST["description_" . $id],FILTER_SANITIZE_STRING);

        if (empty($gene_symbol)){ /*if no annotation for gene_symbol was not given, no value will be inserted for gene_symbol in the DB*/
            $annotation = pg_query_params($db_conn, $annotation_query, array($id, $gene_id, $gene_biotype, $prot_biotype, null, $description, $annotator)) or die("Error " . pg_last_error());
        } else{
            $annotation = pg_query_params($db_conn, $annotation_query, array($id, $gene_id, $gene_biotype, $prot_biotype, $gene_symbol, $description, $annotator)) or die("Error " . pg_last_error());
        }
    $update_status = pg_query_params($db_conn, $update_status_query, array($id)) or die("Error " . pg_last_error()); /*Status of annotation is updated to "annotation exists"*/
        echo "Your annotation for " . $id . " has been submitted";

    //Display of the annotations areas for all transcript
    } else {/*If no annotation has been submitted the annotations fields are displayed*/
        echo "<div class='tabcontent' id=todo_" . $id . ">     
            <form action=" . $_SERVER['PHP_SELF'] . " method='POST'>
                <!--This form redirects to itself when the user submits his annotation-->
                
            <!--Informations on transcript-->
                <p class='title'>" . $id . "</p> <!-- Name of transcript-->
                <a href='../Web_izem/Gene-ProtPage.php?$id'>" . $id . " informations</a><br> 
                    <!--Page with known informations on transcript-->
                ";
        if ($transcript['annotation']==1){
            echo "<a href='Annotation_history.php?id=$id'>$id annotations history</a><br>";
        }
        echo"<!--Sequences for quick access-->
                <div class='double'>
                    Nucleotidic sequence: <br>
                    <p class='info'>" . $transcript['sequence_nt'] . "</p><br>
                </div>
                <div class='double'>
                    Proteic sequence: <br>
                    <p class='info'>" . $transcript['sequence_p'] . "</p><br>
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
disconnect_db(); /*Disconnect from database*/
?>