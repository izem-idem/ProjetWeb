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
$update_status = "UPDATE website.annotate SET validator_email=$1, validated=$2, commentary=$3 WHERE id_transcript=$4";

// Find annotators (= admin + validator + annotator)
$annotator_query = "SELECT email FROM website.users WHERE status in ('Admin','Validator','Annotator')";
$annotator_results = pg_query($db_conn, $annotator_query);
//Array of annotators
$annotators = pg_fetch_all_columns($annotator_results); /*List of annotators, by defaut pg_fetch_all_colums takes the first column*/

// Assign transcript to new user if rejection
$assignment_query = "UPDATE website.transcript SET annotator_email=$1 WHERE Id_transcript = $2";

//For each annotation to validate a tablink is created
echo "<div class='tab'>";
foreach ($id_transcript_tabs as $tab) {
    if (!isset($_POST["reject_" . $tab]) && !isset($_POST["validate_" . $tab])) {
        echo "<button class='tablinks' onclick='openTab(event,&quot;valid_" . $tab . "&quot;,true)'>" . $tab . "</button>";
    }
}
echo "</div>";

//For each annotation a div is created, that can accessed through the tablinks
while ($annotation = pg_fetch_assoc($tovalidate)) {
    $id = $annotation['id_transcript'];
    //If the validator has validated or rejected the annotation
    if (isset($_POST["validate_" . $id])) {/*The annotation has been validated*/
        $comment = $_POST["comment_" . $id];
        /*Update status of the annotation to validated and also add the validator email and the commentary*/
        $update_status = pg_query_params($db_conn, $update_status, array($validator, 1, $comment, $id)) or die("Error " . pg_last_error());

        /*Send mail to annotator to let him know about the assignment*/
        $subject = "You annotation for $id has been validated";
        $message = " Hello, \n your annotation for the transcript $id has been reviewed. You can go look at the commentary on your annotator area.\n Have a good day, \n The CALI team.";
        mail($annotation['annotator_email'], $subject, $message, "From: camrabier@gmail.com");

        echo "The annotation for " . $id . " has been validated<br>"; /*Message to confirm the validation*/

    } else if (isset($_POST["reject_" . $id])) { /*The annotation has been rejected, the annotator can be changed*/
        $comment = filter_var($_POST["comment_" . $id], FILTER_SANITIZE_STRING);
        $new_annotator = $_POST["Role_" . $id];

        /*Update status of the annotation to rejected and also add the validator email and the commentary*/
        $update_status = pg_query_params($db_conn, $update_status, array($validator, 2, $comment, $id)) or die("Error " . pg_last_error());

        /*Update the annotator email in transcript*/
        $assignment_update = pg_query_params($db_conn, $assignment_query, array($new_annotator, $id));

        /*Send mail to annotator to let him know about the assignment*/
        $subject = "You annotation for $id has been rejected";
        if ($new_annotator == $annotation['annotator_email']) {
            $assignment_info = "The transcript has been reassigned to you. You can submit a new annotation on annotator area.";
        } else {
            $assignment_info = " The transcript has been reassigned to a new person.";
        }
        $message = " Hello, \n your annotation for the transcript $id has been reviewed. $assignment_info \nYou can go look at the commentary on your annotator area.\n Have a good day, \n The CALI team.";
        mail($annotation['annotator_email'], $subject, $message, "From: camrabier@gmail.com");
        echo "The annotation for " . $id . " has been rejected, the annotation has been reassigned to :" . $new_annotator . "<br>"; /*Message to confirm the rejection*/

    } else { /*The annotation have not been validated or rejected*/
        echo "<div class='tabcontent' id=valid_" . $id . ">
                <form action=" . $_SERVER['PHP_SELF'] . " method='POST'> <!--When the validation and rejection button is clicked the form redirects to the same page-->
                    <p class='title'>" . $id . "</p>
                    <a href='../Web_izem/Gene-ProtPage.php?id=$id'>" . $id . " informations</a><br>
                    
                    <!--Annotation information-->
                    <table class='spaced_table'>
                        <tr>
                            <td>Annotator : </td>
                            <td><p class='info'>".$annotation['annotator_email']."</p></td>
                        </tr>
                        <tr>                          
                            <td>ID of gene : </td>
                            <td><p class='info'>" . $annotation['id_gene'] . "</p></td>
                        </tr>
                        <tr>
                            <td>Biotype of gene : </td>
                            <td><p class='info'>" . $annotation['gene_biotype'] . "</p></td>
                        </tr>
                        <tr>
                            <td>Gene symbol : </td>
                            <td><p class='info'>" . $annotation['symbol'] . "</p></td>
                        </tr>
                        <tr>
                            <td>Biotype of transcript : </td>
                            <td><p class='info'>" . $annotation['transcript_biotype'] . "</p></td>
                        </tr>
                        <tr>
                            <td>Description of function : </td>
                            <td><p class='info'>" . $annotation['description'] . "</p></td>
                        </tr>
                    </table>
                    <!--Validation commentary-->
                    <label for='comment_" . $id . "'>Comment</label><br>
                    <textarea cols='40' rows='5' id='comment_" . $id . "' name='comment_" . $id . "' placeholder='Enter your commentary here...' required maxlength='500'></textarea><br>
                    
                    <!--Validation/rejection buttons-->
                    <button class='little_submit_button' type='submit' name='validate_" . $id . "'> Validate</button><br>
                    
                    <label for='Role_" . $id . "'></label> <select id='Role_" . $id . "' name='Role_" . $id . "'>";/*if the validator rejects, he can change the annotator*/
        foreach ($annotators as $new_annotator) { /*List of annotators as drop-down list*/
            echo "<option value='" . $new_annotator . "'>" . $new_annotator . "</option>";
        }
        echo "</select>
                    <button class='little_submit_button' type='submit' name='reject_" . $id . "'> Reject</button> <!--Rejects the annotation and assign a new annotator or the same-->
                </form>
            </div>";
    }
}
disconnect_db(); /*Disconnect from database*/
?>

