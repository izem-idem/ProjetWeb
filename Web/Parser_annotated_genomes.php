<!--Launch this page from your browser, it will add the three already annotated genomes-->
<!--WARNING : you can change $PATH if the files are not in a Data directory in the parent directory -->
<?php
//Required libraries
include_once "libphp/db_utils.php"; /*DB connection functions*/
include_once 'libphp/Functions_ph8.php'; /*There are php8 functions used, for older php version, the functions are replaced*/

//Connect to database
connect_db();

//Species and strains names for already annotated genomes
$species = ['Escherichia coli', 'Escherichia coli', 'Escherichia coli'];
$strain = ['cft073', 'O157:H7 str. EDL933', 'str. K-12 substr. MG1655'];
// Files path
$PATH = '../Data/';
$genome_file = [$PATH . 'Escherichia_coli_cft073.fa', $PATH . 'Escherichia_coli_o157_h7_str_edl933.fa', $PATH . 'Escherichia_coli_str_k_12_substr_mg1655.fa'];
$cds_file = [$PATH . 'Escherichia_coli_cft073_cds.fa', $PATH . 'Escherichia_coli_o157_h7_str_edl933_cds.fa', $PATH . 'Escherichia_coli_str_k_12_substr_mg1655_cds.fa'];
$pep_file = [$PATH . 'Escherichia_coli_cft073_pep.fa', $PATH . 'Escherichia_coli_o157_h7_str_edl933_pep.fa', $PATH . 'Escherichia_coli_str_k_12_substr_mg1655_pep.fa'];

//PREPARE SQL COMMANDS
//Insertion in genome table
$sql_genome = pg_prepare($db_conn, "insert_genome", "INSERT INTO website.genome (Id_genome,Species, Strain, Sequence,Size_genome) VALUES ($1,$2,$3,$4,$5)");

//Insertion in transcript table
$sql_transcript = pg_prepare($db_conn, "insert_cds_transcript", "INSERT INTO website.transcript (Id_transcript,Id_genome,Genetic_support,Sequence_nt,LocBeginning,LocEnd,Strand,Size_nt,Annotation) VALUES ($1,$2,$3,$4,$5,$6,$7,$8,1)");
/*Annotation = 1 corresponds to annotation exists for the transcript*/

//Insertion in annotation table
$sql_annotation = pg_prepare($db_conn, "insert_cds_annotation", "INSERT INTO website.annotate (Id_transcript,Id_gene, Gene_biotype, Transcript_biotype, Symbol, Description,date_annotation,validated) VALUES ($1,$2,$3,$4,$5,$6,'now',1)");
/*validated = 1 corresponds to a validated annotation and 'now gives the current time'*/

//Update transcript table with proteic sequence and its size
$sql_pep = pg_prepare($db_conn, "update_pep", "UPDATE website.transcript SET (Sequence_p,Size_p)=($1,$2) WHERE Id_transcript = $3");

//PARSE GENOME
for ($i = 0; $i <= 2; $i++) {
    $genome = file_get_contents($genome_file[$i]) or die("Unable to open genome file !"); /*get the file content*/

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

    //Genome ID and size
    $id_genome = $genome_id_size[1];
    $genome_size = (int)$genome_id_size[2]; /*Cast string to int (the DB requires an int, not a string value)*/

    // Insert in BD
    $sql_genome = pg_execute($db_conn, "insert_genome", array($id_genome, $species[$i], $strain[$i], $seq, $genome_size)) or die ("Query failed with exception: " . pg_last_error());
    echo $genome_file[$i] . " genome done \n";

//PARSE CDS
    $cds = file($cds_file[$i]) or die("Unable to open cds file !"); /*Get file lines*/
    $seq_nt = '';

    foreach ($cds as $line) { /*Iterate on the file lines */
        if (str_starts_with($line, ">")) { /*It's the header line*/
            if (strlen($seq_nt) > 0) { /*if first sequence already in memory*/
                $seq_nt = preg_replace('/\s+/', "", $seq_nt); /*To delete whitespaces*/
                $size_nt = strlen($seq_nt); /*Get sequence size*/

                //Execute queries
                $sql_transcript = pg_execute($db_conn, "insert_cds_transcript", array($id_transcript, $id_genome, $genetic_supp, $seq_nt, $Loc_beg, $Loc_end, $strand, $size_nt)) or die ("Query failed with exception: " . pg_last_error());
                $sql_annotation = pg_execute($db_conn, "insert_cds_annotation", array($id_transcript, $id_gene, $gene_type, $prot_type, ($gene_symbol == 0 ? null : $gene_symbol), $description)) or die ("Query failed with exception: " . pg_last_error());

                //Reset seq_nt
                $seq_nt = "";
            }

            $infos = array();

            //Transcript ID
            preg_match('#>(.+) cds #', $line, $infos);
            $id_transcript = $infos[1];

            //Genome ID, genetic support, localisation, strand
            preg_match('#(chromosome|plasmid):(.+?):(.+?):(.+?):(.+?):(.+?) gene#', $line, $infos);
            $id_genome = $infos[2];
            $genetic_supp = $infos[3];
            $Loc_beg = (int)$infos[4];
            $Loc_end = (int)$infos[5];
            $strand = $infos[6];

            //Gene ID
            preg_match('#gene:(.+) gene_biotype#', $line, $infos);
            $id_gene = $infos[1];

            //Gene biotype
            preg_match('#gene_biotype:(.+) transcript_biotype#', $line, $infos);
            $gene_type = $infos[1];

            //Gene symbol (if present), protein biotype, description
            if (strpos($line, "gene_symbol")) { /*If gene symbole in header*/
                preg_match('#transcript_biotype:(.+) gene_symbol#', $line, $infos);
                $prot_type = $infos[1];

                preg_match('#gene_symbol:(.+) description:(.+)\s#', $line, $infos);
                $gene_symbol = $infos[1];
                $description = $infos[2];
                if (strlen($description) > 200) {
                    echo "Pb " . strlen($description);
                }
            } else { /*Gene symbol absent*/
                preg_match('#transcript_biotype:(.+) description#', $line, $infos);
                $prot_type = $infos[1];
                preg_match('#description:(.+)\s#', $line, $infos);
                $gene_symbol = 0;
                $description = $infos[1];
            }
        } else { /*It's part of the sequence*/
            $seq_nt = $seq_nt . $line;
        }
    }
    echo $genome_file[$i] . " CDS done \n";

//    PARSE PEPTIDES
    $pep = file($pep_file[$i]) or die("Unable to open peptide file !");
    $seq_p = '';
    foreach ($pep as $line) {
        $header = strpos($line, ">");
        if ($header !== false) { /*It's the header line*/
            if (strlen($seq_p) > 0) { /*if first sequence already in memory, get sequence and its size*/
                $seq_p = preg_replace('/\s+/', "", $seq_p);
                $size_p = strlen($seq_p);
                $sql_pep = pg_execute($db_conn, "update_pep", array($seq_p, $size_p, $id_transcript)) or die ("Query failed with exception: " . pg_last_error());
                $seq_p = "";
            }
            $infos = array();

            //Id transcript
            preg_match('#>(.+) pep #', $line, $infos);
            $id_transcript = $infos[1];
        } else {
            $seq_p = $seq_p . $line;
        }
    }
    echo $genome_file[$i] . " pep done \n";
}

disconnect_db();
?>