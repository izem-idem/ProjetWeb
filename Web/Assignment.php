<!--To create paginations used : https://www.the-art-of-web.com/php/pagination/-->

<?php
// Determine on which page the user is and how much results are wanted per page
if (isset($_GET['page']) && !empty($_GET['page'])){ /*if page number is defined in url*/
    $currentPage = (int) strip_tags($_GET['page']);
} else{ /*no page number is defined*/
    $currentPage = 1;
}
$perpage = 20; /*Number of results per page*/
$begin = ($currentPage - 1) * $perpage; /*How much results have already been displayed*/


// Connect to database and find non-assigned transcript
    // Connect to database
include_once 'libphp/db_utils.php';
connect_db();
    // QUERIES
        // Find non annotated transcript
$non_annotated_query = "SELECT * FROM website.transcript WHERE annotation = 0 LIMIT $1 OFFSET $2";
$non_annotated_finding = pg_query_params($db_conn, $non_annotated_query, array($perpage, $begin));

        // Find number of results, to know how much pages are needed
$nr_results_query = "SELECT count(id_transcript) FROM website.transcript WHERE annotation = 0";
$nr_results_finding = pg_query($db_conn,$nr_results_query);
$nr_results = pg_fetch_all_columns($nr_results_finding)[0]; /*Number of results*/

        // Find annotators (= admin + validator + annotator)
$annotator_query = "SELECT email FROM website.users WHERE status in ('Admin','Validator','Annotator')";
$annotator_finding = pg_query($db_conn,$annotator_query);
            //Array of annotators
$annotators = array();
while ($annotator= pg_fetch_array($annotator_finding)){
    $annotators[]=$annotator;
}
        // Assign transcript to user
$assignment_query = "UPDATE website.transcript SET annotation=2, annotator_email=$1 WHERE Id_transcript = $2";

// Display of the non-assigned transcript
while ($transcript = pg_fetch_assoc($non_annotated_finding)){
    //Names of fields to fetch with POST, without them, the fields can't be distinguished
    $name_submit="submit_".$transcript['id_transcript'];
    $name_select="Role_".$transcript['id_transcript'];
    if (isset($_POST[$name_submit])) {
        $id_transcript = $transcript['id_transcript'];
        $user = $_POST[$name_select];
        $assignment_update = pg_query_params($db_conn,$assignment_query,array($user,$id_transcript));
    } else {
        echo "<label for='id_transcript1'></label>
        <input class='info title' disabled id='id_transcript1' value=". $transcript['id_transcript'].">
        <a href='Gene-ProtPage.html'> Temporary page for transcript 1</a>
        <br>
        <label for='".$name_select."'></label>
        <select id='".$name_select."' name='".$name_select."'>";
        foreach ($annotators as $user){ /*List of annotators as drop-down list*/
            echo "<option value='".$user['email']."'>" .$user['email']."</option>";
        }
        echo "</select>
        <button name='submit_".$transcript['id_transcript']."' class='little_submit_button' type='submit'> Affect transcript</button>
        <br>
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

// build array containing links to all pages
$tmp = [];
for($p=1, $i=0; $i < $nr_results; $p++, $i += $perpage) {
    if($currentPage == $p) {
        // current page shown as bold, no link
        $tmp[] = "<b>".$p."</b>";
    } else {
        $tmp[] = "<a href=\"?page=".$p."\">".$p."</a>";
    }
}

// thin out the links (optional)
for($i = count($tmp) - 3; $i > 1; $i--) {
    if(abs($currentPage - $i - 1) > 2) {
        unset($tmp[$i]);
    }
}

// display page navigation if data covers more than one page
if(count($tmp) > 1) {
    echo "<p>";

    if($currentPage > 1) {
        // display 'Prev' link
        echo "<a href=\"?page=" . ($currentPage - 1) . "\">&laquo; Prev</a> | ";
    } else {
        echo "Page ";
    }

    $lastlink = 0;
    foreach($tmp as $i => $link) {
        if($i > $lastlink + 1) {
            echo " ... "; // where one or more links have been omitted
        } elseif($i) {
            echo " | ";
        }
        echo $link;
        $lastlink = $i;
    }

    if($currentPage <= $lastlink) {
        // display 'Next' link
        echo " | <a href=\"?page=" . ($currentPage + 1) . "\">Next &raquo;</a>";
    }

    echo "</p>\n\n";
}
disconnect_db();
?>
