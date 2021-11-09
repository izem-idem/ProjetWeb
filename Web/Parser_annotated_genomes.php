<!--Dans php.ini : upload_file_size = 15M et post_max_size=16M (j'ai aussi modifié max_execution_time à 600 mais aucune idée si nécessaire-->
<!--La database website doit être crée dans postgres (CREATE DATABASE website) -->
<?php
//Connect to databse
include_once 'libphp/db_utils.php';
connect_db();
//Get variables from add_genome.php
    // Text variables
$species = ['Escherichia_coli','Escherichia_coli','Escherichia_coli'];
$strain = ['cft073','o157_h7_str_edl933','str_k_12_substr_mg1655'];
    // Files temporary path (les fichiers sont téléchargés temporairement et PHP utilise ces fichiers pour parser)
$genome_file = ['../Data/Escherichia_coli_cft073.fa', '../Data/Escherichia_coli_o157_h7_str_edl933.fa','../Data/Escherichia_coli_str_k_12_substr_mg1655.fa'];
$cds_file = ['../Data/Escherichia_coli_cft073_cds.fa', '../Data/Escherichia_coli_o157_h7_str_edl933_cds.fa','../Data/Escherichia_coli_str_k_12_substr_mg1655_cds.fa'];
$pep_file = ['../Data/Escherichia_coli_cft073_pep.fa', '../Data/Escherichia_coli_o157_h7_str_edl933_pep.fa','../Data/Escherichia_coli_str_k_12_substr_mg1655_pep.fa'];

//PREPARE SQL COMMANDS
/* pour éviter injection sql, on utilise le couple pg_prepare et pg_execute*/
/* pg_prepare prépare la commande SQL et pg_execute fournit les valeurs, ainsi on aura pas d'injection SQL a priori ?*/
$sql_genome = pg_prepare($db_conn, "insert_genome", "INSERT INTO website.genome (Id_genome,Species, Strain, Sequence,Size_genome) VALUES ($1,$2,$3,$4,$5)");
$sql_transcript = pg_prepare($db_conn, "insert_cds_transcript", "INSERT INTO website.transcript (Id_transcript,Id_genome,Genetic_support,Sequence_nt,LocBeginning,LocEnd,Strand,Size_nt,Annotation) VALUES ($1,$2,$3,$4,$5,$6,$7,$8,1)");
$sql_annotation = pg_prepare($db_conn, "insert_cds_annotation", "INSERT INTO website.annotate (Id_transcript,Id_gene, Gene_biotype, Transcript_biotype, Symbol, Description,date_annotation,validated) VALUES ($1,$2,$3,$4,$5,$6,$7,1)");
$sql_pep = pg_prepare($db_conn, "update_pep", "UPDATE website.transcript SET (Sequence_p,Size_p)=($1,$2) WHERE Id_transcript = $3");

//PARSE GENOME
for ($i = 0; $i <=2; $i++){
    $genome = file_get_contents($genome_file[$i]) or die("Unable to open genome file !"); /* récupère contenu du fichier*/
    // Séparation header/séquence
    $header_seq = explode("REF", $genome); /*Sépare header de la séquence --> array*/
    $header = $header_seq[0]; /*Récupère header*/
    $seq = preg_replace('/\s+/', "", $header_seq[1]); /*Récupère séquence et enlève saut de ligne*/
    // Analyse header
    $genome_id_size = array(); /*Array qui va contenir les matchs pour la reconnaissance par regex*/
    preg_match('#chromosome:(.+):Chromosome:1:(.+):1#', $header, $genome_id_size);
    /*$genome_id_size=[ligne entière matchant, identifiant chromosome, taille génome]*/
    /*(.+) permet de dire qu'on cherche cet élément :
    . = tous les charactères sauf nouvelle ligne
    + = 1 ou plus occurence de l'élément précédent*/
    $id_genome = $genome_id_size[1];
    $genome_size = (int)$genome_id_size[2]; /*Caste en int, sinon il sera inséré en tant que string dans la BD et donc erreur*/

    // Insertion dans BD
    $sql_genome = pg_execute($db_conn, "insert_genome", array($id_genome, $species[$i], $strain[$i], $seq, $genome_size)) or die ("Query failed with exception: ". pg_last_error());
    echo $genome_file[$i]." genome done \n";
//PARSE CDS
    $cds = file($cds_file[$i]) or die("Unable to open cds file !"); /*Récupère lignes du fichier*/
    $seq_nt = '';

    foreach ($cds as $line) { /*Itérations sur les lignes du fichier */
//    $header = strpos($line, ">"); /*regarde si*/
        if (str_starts_with($line,">")) { /*It's the header line*/
            if (strlen($seq_nt) > 0) { /*if first sequence already in memory*/
                $seq_nt = preg_replace('/\s+/', "", $seq_nt); /*Enlève saut de ligne*/
                $size_nt = strlen($seq_nt); /*Récupère taille de la séquence*/
                /*Execute commande, annotated is obligatory true */
                $sql_transcript = pg_execute($db_conn, "insert_cds_transcript", array($id_transcript, $id_genome, $genetic_supp, $seq_nt, $Loc_beg, $Loc_end, $strand, $size_nt)) or die ("Query failed with exception: ". pg_last_error());
                $sql_annotation = pg_execute($db_conn, "insert_cds_annotation", array($id_transcript, $id_gene, $gene_type, $prot_type, ($gene_symbol==0 ? null:$gene_symbol), $description, 'now')) or die ("Query failed with exception: ". pg_last_error());
                /*Reset seq_nt*/
                $seq_nt = "";
            }
            $infos = array();
            preg_match('#>(.+) cds#', $line, $infos);
            $id_transcript = $infos[1];
            if (strpos($line, "description")) {
                preg_match('#(chromosome|plasmid):(.+?):(.+?):(.+?):(.+?):(.+?) gene#', $line, $infos);
                $id_genome = $infos[2];
                $genetic_supp = $infos[3];
                $Loc_beg = (int)$infos[4];
                $Loc_end = (int)$infos[5];
                $strand = $infos[6];
                preg_match('#gene:(.+) gene_biotype#', $line, $infos);
                $id_gene = $infos[1];
                preg_match('#gene_biotype:(.+) transcript_biotype#', $line, $infos);
                $gene_type = $infos[1];
                if (strpos($line, "gene_symbol")) {
                    preg_match('#transcript_biotype:(.+) gene_symbol#', $line, $infos);
                    $prot_type = $infos[1];
                    preg_match('#gene_symbol:(.+) description:(.+)\s#', $line, $infos);
                    $gene_symbol = $infos[1];
                    $description = $infos[2];
                    if (strlen($description)>200){
                        echo "Pb ". strlen($description);
                    }
                } else {
                    preg_match('#transcript_biotype:(.+) description#', $line, $infos);
                    $prot_type = $infos[1];
                    preg_match('#description:(.+)\s#', $line, $infos);
                    $gene_symbol = 0 ;
                    $description = $infos[1];
                    if (strlen($description)>200){
                        echo "Pb ". strlen($description);
                    }
                }
            } else {
                echo "There is no description, it's not normal";
            }
        } else { /*It's part of the sequence*/
            $seq_nt = $seq_nt . $line;
        }
    }
    echo $genome_file[$i]." CDS done \n";
//    PARSE PEPTIDES
    $pep = file($pep_file[$i]) or die("Unable to open peptide file !");
    $seq_p = '';
    foreach ($pep as $line) {
        $header = strpos($line, ">");
        if ($header !== false) { /*It's the header line*/
            if (strlen($seq_p) > 0) { /*if first sequence already in memory*/
                $seq_p = preg_replace('/\s+/', "", $seq_p);
                $size_p = strlen($seq_p);
                $sql_pep = pg_execute($db_conn, "update_pep", array($seq_p, $size_p, $id_transcript)) or die ("Query failed with exception: ". pg_last_error());
                $seq_p = "";
            }
            $infos = array();
            preg_match('#>(.+) pep#', $line, $infos);
            $id_transcript = $infos[1];
        }else{
            $seq_p=$seq_p.$line;
        }
    }
    echo $genome_file[$i]." pep done \n";
}

disconnect_db();
?>