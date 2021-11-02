<!--Dans php.ini : upload_file_size = 15M et post_max_size=16M (j'ai aussi modifié max_execution_time à 600 mais aucune idée si nécessaire-->
<!--La database website doit être crée dans postgres (CREATE DATABASE website) -->
<?php
$db = pg_connect("host=localhost port=5432 dbname=website user=admin password=admin") or die ("Connection failed");
$species = $_POST['genome_name'];
$strain = $_POST['strain_name'];
$genome_file = $_FILES['genome_file']['tmp_name'];
$cds_file = $_FILES['cds_file']['tmp_name'];
$pep_file = $_FILES['pep_file']['tmp_name'];


//PARSE GENOME

$genome = file_get_contents($genome_file) or die("Unable to open file !");
$header_seq = explode("REF", $genome);
$header = $header_seq[0];
$genome_id_size = array();
preg_match('#chromosome:(.+?):Chromosome:1:(.+?):1#', $header, $genome_id_size);
$id_genome = $genome_id_size[1];
$genome_size = (int)$genome_id_size[2];
$seq = preg_replace('/\s+/', "", $header_seq[1]);

$sql = pg_prepare($db, "insert_genome", "INSERT INTO website.genome (Id_genome,Species, Strain, Sequence,Size_genome) VALUES ($1,$2,$3,$4,$5)");
$sql = pg_execute($db, "insert_genome", array($id_genome, $species, $strain, $seq, $genome_size)) or die (pg_last_error($db));

//PARSE CDS
$cds = file($cds_file) or die("Unable to open file !");
$seq_nt = '';
$sql = pg_prepare($db, "insert_cds", "INSERT INTO website.transcript (Id_transcript,Id_genome,Genetic_support,Sequence_nt,LocBeginning,LocEnd,Strand,Size_nt,Id_gene, Gene_biotype, Transcript_biotype, Symbol, Description) VALUES ($1,$2,$3,$4,$5,$6,$7,$8,$9,$10,$11,$12,$13)");
foreach ($cds as $line) {
    $header = strpos($line, ">");
    if ($header !== false) { /*It's the header line*/
        if (strlen($seq_nt) > 0) { /*if first sequence already in memory*/
            $seq_nt = preg_replace('/\s+/', "", $seq_nt);
            $size_nt = strlen($seq_nt);
            $sql = pg_execute($db, "insert_cds", array($id_transcript, $id_genome, $genetic_supp, $seq_nt, $Loc_beg, $Loc_end, $strand, $size_nt, $id_gene, $gene_type, $prot_type, $gene_symbol, $description)) or die (pg_last_error($db));
            $seq_nt = "";
        }
        $infos = array();
        preg_match('#>(.+?) cds#', $line, $infos);
        $id_transcript = $infos[1];
        if (strpos($line, "description")) {
            preg_match('#(chromosome|plasmid):(.+?):(.+?):(.+?):(.+?):(.+?) gene#', $line, $infos);
            $id_genome = $infos[2];
            $genetic_supp = $infos[3];
            $Loc_beg = (int)$infos[4];
            $Loc_end = (int)$infos[5];
            $strand = $infos[6];
            preg_match('#gene:(.+?) gene_biotype#', $line, $infos);
            $id_gene = $infos[1];
            preg_match('#gene_biotype:(.+?) transcript_biotype#', $line, $infos);
            $gene_type = $infos[1];
            if (strpos($line, "gene_symbol")) {
                preg_match('#transcript_biotype:(.+?) gene_symbol#', $line, $infos);
                $prot_type = $infos[1];
                preg_match('#gene_symbol:(.+?) description:(.+?)\s#', $line, $infos);
                $gene_symbol = $infos[1];
                $description = $infos[2];
            } else {
                preg_match('#transcript_biotype:(.+?) description#', $line, $infos);
                $prot_type = $infos[1];
                preg_match('#description:(.+?)\s#', $line, $infos);
                $gene_symbol = ' ';
                $description = $infos[1];
            }
        } else {
            preg_match('#(chromosome|plasmid):(.+?):(.+?):(.+?):(.+?)$#', $line, $infos);
            $id_genome = $infos[2];
            $genetic_supp = $infos[3];
            $Loc_beg = $infos[4];
            $Loc_end = $infos[5];
            $strand = ' ';
            $id_gene = ' ';
            $gene_type = ' ';
            $prot_type = ' ';
            $gene_symbol = ' ';
            $description = ' ';
        }
    } else { /*It's part of the sequence*/
        $seq_nt = $seq_nt . $line;
    }
}

$pep = file($pep_file) or die("Unable to open file !");
$seq_p = '';
$sql = pg_prepare($db, "update_pep", "UPDATE website.transcript SET (Sequence_p,Size_p)=($1,$2) WHERE Id_transcript = $3");
foreach ($pep as $line) {
    $header = strpos($line, ">");
    if ($header !== false) { /*It's the header line*/
        if (strlen($seq_p) > 0) { /*if first sequence already in memory*/
            $seq_p = preg_replace('/\s+/', "", $seq_p);
            $size_p = strlen($seq_p);
            $sql = pg_execute($db, "update_pep", array($seq_p, $size_p, $id_transcript)) or die (pg_last_error($db));
            $seq_p = "";
        }
        $infos = array();
        preg_match('#>(.+?) pep#', $line, $infos);
        $id_transcript = $infos[1];
    }else{
        $seq_p=$seq_p.$line;
    }
}

pg_close($db);
?>