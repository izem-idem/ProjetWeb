<!--Launch this page from your browser, it will add the new coli genome-->
<!--This script was created, specifically for user that can not or do not want to change their php.ini-->
<!--WARNING : you can change $PATH if the file is not in a Data directory in the parent directory -->
<?php
//Required libraries
include_once "libphp/db_utils.php"; /*DB connection functions*/
include_once 'libphp/Functions_ph8.php'; /*There are php8 functions used, for older php version, the functions are replaced*/

//Connect to database
connect_db();

//Specie and strain names for new coli
$species = "Escherichia Coli";
$strain = "";

// Files path
$PATH = '../Data/';
$genome_file = $PATH . "new_coli.fa";
$cds_file = $PATH . "new_coli_cds.fa";
$pep_file = $PATH . "new_coli_pep.fa";

//PREPARE SQL COMMANDS
//Insertion in genome table
$sql_genome = pg_prepare($db_conn, "insert_genome", "INSERT INTO website.genome (Id_genome,Species, Strain, Sequence,Size_genome) VALUES ($1,$2,$3,$4,$5)");

//Insertion in transcript table
$sql_transcript = pg_prepare($db_conn, "insert_cds_transcript", "INSERT INTO website.transcript (Id_transcript,Id_genome,Genetic_support,Sequence_nt,LocBeginning,LocEnd,Strand,Size_nt,Annotation) VALUES ($1,$2,$3,$4,$5,$6,$7,$8,0)");
/*Annotation = 0 corresponds to not assigned transcript*/

//Update transcript table with proteic sequence and its size
$sql_pep = pg_prepare($db_conn, "update_pep", "UPDATE website.transcript SET (Sequence_p,Size_p)=($1,$2) WHERE Id_transcript = $3");


//PARSE GENOME
$genome = file_get_contents($genome_file) or die("Unable to open genome file !"); /*get the file content*/

// Split header/sequence
$header_seq = explode("REF", $genome); /*Split the header from the sequence to an array*/
$header = $header_seq[0]; /*Get header*/
$seq = preg_replace('/\s+/', "", $header_seq[1]); /*Get séquence et take out whitespaces*/

// Analyze header
$genome_id_size = array(); /*Array that will contain the matchs of the regex expression*/
preg_match('#chromosome:(.+):Chromosome:1:(.+):1#', $header, $genome_id_size);
/* $genome_id_size=[entire line that matches the regex, chromosome id, genome size]*/
/* (.+) is to capture the expression matching the regex context :
. = matches any character except line terminators
+ = 1 or more occurences of the preceding token*/

// Genome ID and size
$id_genome = $genome_id_size[1];
$genome_size = (int)$genome_id_size[2]; /*Cast string to int (the DB requires an int, not a string value)*/

// Insert in BD
$sql_genome = pg_execute($db_conn, "insert_genome", array($id_genome, $species, ($strain ? null : $strain), $seq, $genome_size)) or die (pg_last_error($db_conn));
echo "new_coli genome done \n";

//PARSE CDS
$cds = file($cds_file) or die("Unable to open cds file !"); /*Get file lines*/
$seq_nt = '';

foreach ($cds as $line) { /*Iterate on the file lines */
    if (str_starts_with($line, ">")) { /*It's the header line*/
        if (strlen($seq_nt) > 0) { /*if first sequence already in memory*/
            $seq_nt = preg_replace('/\s+/', "", $seq_nt); /*Delete whitespaces*/
            $size_nt = strlen($seq_nt); /*Get sequence size*/

            //Execute queries, annotated is mandatorily false (= f in postgres)
            $sql_transcript = pg_execute($db_conn, "insert_cds_transcript", array($id_transcript, $id_genome, $genetic_supp, $seq_nt, $Loc_beg, $Loc_end, null, $size_nt)) or die (pg_last_error($db_conn));

            //Reset sequence string
            $seq_nt = "";
        }

        $infos = array();

        //Transcript ID
        preg_match('#>(.+) cds#', $line, $infos);
        $id_transcript = $infos[1];

        //Genome ID, genetic support,localisation
        preg_match('#(chromosome|plasmid):(.+):(.+):(.+):(.+)$#', $line, $infos);
        /*$ is to tell it's the end of the line*/

        $id_genome = $infos[2];
        $genetic_supp = $infos[3];
        $Loc_beg = $infos[4];
        $Loc_end = $infos[5];

    } else { /*It's part of the sequence*/
        $seq_nt = $seq_nt . $line;
    }
}
echo "new_coli CDS file done";

//PARSE PEPTIDE
$pep = file($pep_file) or die("Unable to open peptide file !");
$seq_p = '';
foreach ($pep as $line) {
    $header = strpos($line, ">");
    if ($header !== false) { /*It's the header line*/
        if (strlen($seq_p) > 0) { /*if first sequence already in memory*/
            $seq_p = preg_replace('/\s+/', "", $seq_p);
            $size_p = strlen($seq_p);
            $sql = pg_execute($db_conn, "update_pep", array($seq_p, $size_p, $id_transcript)) or die (pg_last_error($db_conn));
            $seq_p = "";
        }
        $infos = array();

        //Transcript ID
        preg_match('#>(.+) pep #', $line, $infos);
        $id_transcript = $infos[1];
    } else {
        $seq_p = $seq_p . $line;
    }
}
echo "new_coli pep file done";

disconnect_db();
echo "New_coli has been added to the database";
?>
