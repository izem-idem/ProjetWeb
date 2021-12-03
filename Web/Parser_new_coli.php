<!--Dans php.ini : upload_file_size = 15M et post_max_size=16M (j'ai aussi modifié max_execution_time à 600 mais aucune idée si nécessaire-->
<!--La database website doit être crée dans postgres (CREATE DATABASE website) -->
<?php
//Connect to database
        include_once "libphp/db_utils.php";
        connect_db();
//Get variables from add_genome.php
// Text variables
        $species = "Escherichia Coli";
        $strain = "";
// Files temporary path (les fichiers sont téléchargés temporairement et PHP utilise ces fichiers pour parser)
        $genome_file = "../Data/new_coli.fa";
        $cds_file = "../Data/new_coli_cds.fa";
        $pep_file = "../Data/new_coli_pep.fa";

//PREPARE SQL COMMANDS
        /* pour éviter injection sql, on utilise le couple pg_prepare et pg_execute*/
        /* pg_prepare prépare la commande SQL et pg_execute fournit les valeurs, ainsi on aura pas d'injection SQL a priori ?*/
        $sql_genome = pg_prepare($db_conn, "insert_genome", "INSERT INTO website.genome (Id_genome,Species, Strain, Sequence,Size_genome) VALUES ($1,$2,$3,$4,$5)");
        $sql_transcript = pg_prepare($db_conn, "insert_cds_transcript", "INSERT INTO website.transcript (Id_transcript,Id_genome,Genetic_support,Sequence_nt,LocBeginning,LocEnd,Strand,Size_nt,Annotation) VALUES ($1,$2,$3,$4,$5,$6,$7,$8,0)");
        $sql_pep = pg_prepare($db_conn, "update_pep", "UPDATE website.transcript SET (Sequence_p,Size_p)=($1,$2) WHERE Id_transcript = $3");


//PARSE GENOME
        $genome = file_get_contents($genome_file) or die("Unable to open genome file !"); /* récupère contenu du fichier*/
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
        $sql_genome = pg_execute($db_conn, "insert_genome", array($id_genome, $species, ($strain ? null : $strain), $seq, $genome_size)) or die (pg_last_error($db_conn));

//PARSE CDS
        $cds = file($cds_file) or die("Unable to open cds file !"); /*Récupère lignes du fichier*/
        $seq_nt = '';

        foreach ($cds as $line) { /*Itérations sur les lignes du fichier */
//    $header = strpos($line, ">"); /*regarde si*/
            if (str_starts_with($line, ">")) { /*It's the header line*/
                if (strlen($seq_nt) > 0) { /*if first sequence already in memory*/
                    $seq_nt = preg_replace('/\s+/', "", $seq_nt); /*Enlève saut de ligne*/
                    $size_nt = strlen($seq_nt); /*Récupère taille de la séquence*/

                    /*Execute commande, annotated is obligatory false (= f dans postgres) */
                    $sql_transcript = pg_execute($db_conn, "insert_cds_transcript", array($id_transcript, $id_genome, $genetic_supp, $seq_nt, $Loc_beg, $Loc_end, null, $size_nt)) or die (pg_last_error($db_conn));
                    $seq_nt = "";
                }
                $infos = array();
                preg_match('#>(.+) cds#', $line, $infos);
                $id_transcript = $infos[1];
                preg_match('#(chromosome|plasmid):(.+):(.+):(.+):(.+)$#', $line, $infos);
                $id_genome = $infos[2];
                $genetic_supp = $infos[3];
                $Loc_beg = $infos[4];
                $Loc_end = $infos[5];
            } else { /*It's part of the sequence*/
                $seq_nt = $seq_nt . $line;
            }
        }

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
                preg_match('#>(.+) pep#', $line, $infos);
                $id_transcript = $infos[1];
            } else {
                $seq_p = $seq_p . $line;
            }
        }

        disconnect_db();
        echo "New_coli has been added to the database";
?>