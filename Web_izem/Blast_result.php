<?php

# $ID: blastphp.php, v 1.0 2017/02/21 21:02:21 Ashok Kumar T. $
#
# ===========================================================================
#
# This code is for example purposes only.
#
# Please refer to https://ncbi.github.io/blast-cloud/dev/api.html
# for a complete list of allowed parameters.
#
# Please do not submit or retrieve more than one request every two seconds.
#
# Results will be kept at NCBI for 24 hours. For best batch performance,
# we recommend that you submit requests after 2000 EST (0100 GMT) and
# retrieve results before 0500 EST (1000 GMT).
#
# ===========================================================================
#
# return codes:
#     0 - success
#     1 - invalid arguments
#     2 - no hits found
#     3 - rid expired
#     4 - search failed
#     5 - unknown error
#
# ===========================================================================


// Build the request

###Get the parameters.

$querie=$_GET['seq'];

$database=$_GET['db'];

$program=$_GET['type'];

$acc=$_GET['acc'];


if ($querie == "Nucleic"){ # if it's nucleic I have to give to BLAST the entire sequence

    require_once 'web/libphp/db_utils.php'; 
    connect_db();
    $query_1 = "SELECT Sequence_nt FROM website.transcript WHERE Id_transcript =$1"; 
    $result_1 = pg_query_params($db_conn, $query_1,array($acc)) or die("Error " . pg_last_error());
    $line = pg_fetch_row($result_1);
    
    $encoded_query=$line[0];
    disconnect_db();
    
    $data = array('CMD' => 'Put', 'PROGRAM' => $program, 'DATABASE' => $database, 'QUERY' => $encoded_query);
    $options = array(
  'http' => array(
    'header'  => "Content-type: application/x-www-form-urlencoded\r\n",
    'method'  => 'POST',
    'content' => http_build_query($data)
            )
    );
    $context  = stream_context_create($options);
}
else{ #if it's proteic then I only need to give him the accession number

    $data = array('CMD' => 'Put', 'PROGRAM' => $program, 'DATABASE' => $database, 'QUERY' => $acc);
    $options = array(
      'http' => array(
        'header'  => "Content-type: application/x-www-form-urlencoded\r\n",
        'method'  => 'POST',
        'content' => http_build_query($data)
      )
    );
    $context  = stream_context_create($options);
}


// Get the response from BLAST
$result = file_get_contents("https://blast.ncbi.nlm.nih.gov/blast/Blast.cgi", false, $context);

// Parse out the request ID
preg_match("/^.*RID = .*\$/m", $result, $ridm);
$rid = implode("\n", $ridm);
$rid = preg_replace('/\s+/', '', $rid);
$rid = str_replace("RID=", "", $rid);

// Parse out the estimated time to completion
preg_match("/^.*RTOE = .*\$/m", $result, $rtoem);
$rtoe = implode("\n", $rtoem);
$rtoe = preg_replace('/\s+/', '', $rtoe);
$rtoe = str_replace("RTOE=", "", $rtoe);

// Maximum execution time of webserver (optional)
//ini_set('max_execution_time', $rtoe+60);

//converting string to long (sleep() expects a long)
$rtoe = $rtoe + 0;

// Wait for search to complete
sleep($rtoe);

// Poll for results
while(true) {
  sleep(10);

  $opts = array(
  	'http' => array(
      'method' => 'GET'
  	)
  );
  $contxt = stream_context_create($opts);
  $reslt = file_get_contents("https://blast.ncbi.nlm.nih.gov/blast/Blast.cgi?CMD=Get&FORMAT_OBJECT=SearchInfo&RID=$rid", false, $contxt);

  if(preg_match('/Status=WAITING/', $reslt)) {
  	//print "Searching...\n";
    continue;
  }

  if(preg_match('/Status=FAILED/', $reslt)) {
    print "Search $rid failed, please report to blast-help\@ncbi.nlm.nih.gov.\n";
    exit(4);
  }

  if(preg_match('/Status=UNKNOWN/', $reslt)) {
    print "Search $rid expired.\n";
    exit(3);
  }

  if(preg_match('/Status=READY/', $reslt)) {
    if(preg_match('/ThereAreHits=yes/', $reslt)) {
      //print "Search complete, retrieving results...\n";
      break;
  	} else {
      print "No hits found.\n";
      exit(2);
  	}
  }

  // If we get here, something unexpected happened.
  exit(5);
} // End poll loop

// Retrieve and display results
$opt = array(
  'http' => array(
  	'method' => 'GET'
  )
);
$content = stream_context_create($opt);
$output = file_get_contents("https://blast.ncbi.nlm.nih.gov/blast/Blast.cgi?CMD=Get&FORMAT_TYPE=Text&RID=$rid", false, $content);
print $output;
?>




