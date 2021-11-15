<!--To create paginations used : https://www.the-art-of-web.com/php/pagination/-->

<?php
/*Author : Camille RABIER*/
/*This page is user by Validator_Area.php to assign non assigned transcripts to an annotator*/

//Library required:
require_once 'libphp/db_utils.php';
require_once 'libphp/Pagination.php';



// Determine on which page the user is and how much results are wanted per page
/*It is used by the query to find non annotated transcripts, to limit the number of transcript shown*/
if (isset($_GET['page']) && !empty($_GET['page'])){ /*if page number is defined in url*/
    $currentPage = (int) strip_tags($_GET['page']);
} else{ /*no page number is defined*/
    $currentPage = 1;
}
$perpage = 20; /*Number of results per page*/
$begin = ($currentPage - 1) * $perpage; /*How much results have already been displayed on the preceding pages*/


// Connect to database
connect_db(); /*Connect to database*/

// QUERIES
    // Find non annotated transcript
$non_annotated_query = "SELECT * FROM website.transcript WHERE annotation = 0 LIMIT $1 OFFSET $2";
/*Annotation = 0 for no annotator assigned
With LIMIT only 20 (defined by $perpage) results of the query will be shown
With OFFSET, if the user is not on the first page, all results that are on the preceding pages will not be shown*/
$non_annotated = pg_query_params($db_conn, $non_annotated_query, array($perpage, $begin)) or die("Error " . pg_last_error());

    // Find number of results, to know how much pages are needed
$nr_results_query = "SELECT count(id_transcript) FROM website.transcript WHERE annotation = 0";
$nr_results_finding = pg_query($db_conn,$nr_results_query) or die("Error " . pg_last_error());
$nr_results = pg_fetch_all_columns($nr_results_finding)[0]; /*Number of results*/

    // Find annotators (= admin + validator + annotator)
$annotator_query = "SELECT email FROM website.users WHERE status in ('Admin','Validator','Annotator')";
/*The admin and validator can also annotate*/
$annotator_results = pg_query($db_conn,$annotator_query) or die("Error " . pg_last_error());

$annotators = pg_fetch_all_columns($annotator_results); /*List of annotators, by defaut pg_fetch_all_colums takes the first column*/

    // Assign transcript to user
$assignment_query = "UPDATE website.transcript SET annotation=2, annotator_email=$1 WHERE Id_transcript = $2";


//ADD PAGINATION
create_pagination($nr_results,$perpage,$currentPage);

// DISPLAY AND ASSIGNMENT OF TRANSCRIPTS
while ($transcript = pg_fetch_assoc($non_annotated)){
    $id=$transcript['id_transcript'];

    // Updating of database to assign transcript to annotator
    if (isset($_POST["submit_".$id])) { /*A transcript has been assigned to an annotator*/
        $id_transcript = $id;
        $annotator = $_POST["Role_".$id]; /*Annotator to which the transcript has been assigned*/
        $assignment_update = pg_query_params($db_conn,$assignment_query,array($annotator,$id_transcript)) or die("Error " . pg_last_error());

        /*Send mail to annotator to let him know about the assignment*/
        $subject = "New transcript assignment";
        $message = " Hello, \n you have been assigned a new transcript to annotate. Its ID is : $id \n You can go annotate in the Annotator Area. \n Have a good day, \n The CALI team.";
        mail($annotator,$subject,$message,"From: camrabier@gmail.com");

        echo "The transcript ".$id." has been assigned to ".$annotator;

    //Display of non-annotated transcripts
    } else { /*If no assignment has been done*/
        /*Name of transcript*/
        echo "<label for='id_transcript1'></label>
        <input class='info title' disabled id='id_transcript1' value=". $id.">
        
        <!--Link to known information about the transcript-->
        <a href='Gene-ProtPage.html'> Temporary page for transcript 1</a><br>
        
        <!--List of annotator-->
        <label for='Role_".$id."'></label>
        <select id='Role_".$id."' name='Role_".$id."'>";
        foreach ($annotators as $annotator){ /*List of annotators as drop-down list*/
            echo "<option value='".$annotator."'>" .$annotator."</option>";
        }
        echo "</select>

        <!--Assign transcript to annotator selected in drop down-->
        <button name='submit_".$id."' class='little_submit_button' type='submit'> Affect transcript</button>
        <br>
        
        <!--Display of sequences-->
        <div class='double'>
            Nucleotidic sequence <br>
            <p>".$transcript['sequence_nt']."</p><br>
        </div>
        <div class='double'>
            Proteic sequence <br>
            <p>".$transcript['sequence_p']."</p><br>
        </div>";
    }
}
disconnect_db(); /*disconnect from the database*/
?>
