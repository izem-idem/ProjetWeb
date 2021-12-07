<?php
session_start();
if (!isset($_SESSION['Email'])){
    header("Location: LoginPage.php");
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title> Search </title>
    <link rel="stylesheet" type="text/css" href="website.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css"> <!--CSS for log out button-->
</head>
<body>
<header>
    <h1>CALI</h1>
</header>
<div class="topnav">
    <?php require_once 'libphp/Menu.php';
    echo Menu($_SESSION['Status'],"search_page.php")?>
</div>
<div class="center">
    <h2> Search </h2>
    <div class="container">
        <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="POST">

            <p> To use pattern search for the sequences you can use % before or after the pattern</p><br>
            <label for="genome_name"><b>Species name</b> </label>
            <input type="text" name="species_name" placeholder="Species name"><br>

            <label for="strain_name"><b>Strain name</b></label>
            <input type="text" name="strain_name" placeholder="Strain name"><br>

            <label for="genetic_support"><b>Genetic support (bacterial or plasmid typically)</b></label>
            <input type="text" name="genetic_support" placeholder="Genetic support"><br>

            <label for="chr_ID"><b>Chromosome ID</b></label>
            <input type="text" name="id_genome" placeholder="Chromosome ID"><br>

            <label for="seq_genome"><b>Genome sequence</b></label>
            <input type="text" name="genome_seq" placeholder="Genome sequence"><br>

            <label for="gene_id"><b>Gene ID</b></label>
            <input type="text" name="gene_id" placeholder="Gene ID"><br>

            <label for="gene_symbol"><b>Gene symbol</b></label>
            <input type="text" name="gene_symbol" placeholder="Gene symbol"><br>

            <label for="sequence_nt"><b>Nucleotide sequence or pattern</b></label>
            <input type="text" name="sequence_nt" placeholder="Nucleotide pattern"><br>

            <label for="prot_id"><b>Protein ID</b> </label>
            <input type="text" name="id_transcript" placeholder="Protein ID"><br>

            <label for="description"><b>Function</b></label>
            <input type="text" name="description" placeholder="Function"><br>

            <label for="prot_seq"><b>Protein sequence or pattern</b></label>
            <input type="text" name="prot_seq" placeholder="Protein pattern"><br><br>

            <label for="result_type"><b>Select type of results</b></label>
            <select name="result_type">
                <option value="gene_prot"> Gene / Protein</option>
                <option value="Genome"> Genome</option>
            </select><br><br>
            <button class="big_submit_button" name="submit" type="submit" value="Submit"> Submit</button>
        </form>
        <?php

        include_once 'libphp/db_utils.php';
        connect_db();
        if (isset($_POST["submit"])) {

            $info_formulaire = ["species_name", "strain_name", "genetic_support", "id_genome", /*"strand",*/
                "gene_beg", "gene_end", "sequence_nt", "id_transcript", "prot_seq", "gene_id", "gene_symbol", "description"];
            $col_table = ["genome.species", "genome.strain", "transcript.genetic_support", "genome.id_genome", /*"transcript.strand",*/
                "transcript.LocBeginning", "transcript.Loc_end", "transcript.sequence_nt", "transcript.id_transcript", "transcript.sequence_p", "annotate.id_gene", "annotate.symbol", "annotate.description"];

            for ($i = 0; $i <= count($info_formulaire) - 1; $i++) { //Pour chaque champ du formulaire
                $ch = $info_formulaire[$i];
                $col = $col_table[$i];
                if ($col == "id_genome") { //idgenome est ambigu
                    $col = "transcript." . $col;
                }
                if (!empty($_POST[$ch])) { //Si la champ est rempli
                    if (isset($query_sql)) {//Si la variable a été déclarée
                        if (($ch != "sequence_nt") && ($ch != "sequence_p")) {
                            $query_sql .= "AND " . $col . "='" . $_POST[$ch] . "'";
                        } else {
                            $query_sql .= "AND " . $col . " LIKE '%" . $_POST[$ch] . "%' ";
                        }

                    } else { // Si la requete est vide
                      if ($_POST["result_type"] == "gene_prot") {
                        if (($ch != "query_nuc") && ($ch != "query_prot")) {

                            $query_sql = "SELECT annotate.id_transcript FROM website.genome,website.transcript, website.annotate WHERE genome.id_genome = transcript.id_genome AND annotate.id_transcript = transcript.id_transcript AND " . $col . "='" . $_POST[$ch] . "'";
                        } else {
                            $query_sql = "SELECT annotate.id_transcript FROM website.genome,website.transcript, website.annotate WHERE genome.id_genome = transcript.id_genome AND  annotate.id_transcript = transcript.id_transcript AND " . $col . " LIKE '%" . $_POST[$ch] . "%'";

                        }
                      } else if ($_POST["result_type"] == "Genome") {
                        if (($ch != "query_nuc") && ($ch != "query_prot")) {

                            $query_sql = "SELECT  transcript.id_genome FROM website.genome,website.transcript, website.annotate WHERE genome.id_genome = transcript.id_genome AND annotate.id_transcript = transcript.id_transcript AND " . $col . "='" . $_POST[$ch] . "'";
                        } else {
                            $query_sql = "SELECT transcript.id_genome FROM website.genome,website.transcript, website.annotate WHERE genome.id_genome = transcript.id_genome AND  annotate.id_transcript = transcript.id_transcript AND " . $col . " LIKE '%" . $_POST[$ch] . "%'";

                        }
                      }

                    }
                }
            }


            echo $query_sql;

            $res = pg_query($db_conn, $query_sql) or die(pg_last_error());
            if (pg_num_rows($res) == 0) {
                echo "No results";
            } else {
                echo "<table>";
                while ($line = pg_fetch_assoc($res)) {
                    echo "\t<tr>\n";
                    foreach ($line as $col_value) {
                        // METTRE LES LIENS D'IZEM
                        if ($_POST["result_type"] == "gene_prot") {
                          echo "\t\t<td> <a href = 'Gene-ProtPage.php?id=$col_value'> $col_value </a></td>\n";
                        } else if ($_POST["result_type"] == "Genome") {
                        echo "\t\t<td> <a href = 'GenomePage.php?id=$col_value'> $col_value </a></td>\n";
                      }
                    }
                    echo "\t</tr>\n";
                }
                echo "</table>\n";
            }


            // Libère le résultat
            pg_free_result($res);
        }


        ?>
    </div>
</div>
<footer>
    <a href="Contact.php">Contact</a><br>
    <p>© CALI 2021</p>
</footer>
</body>
</html>
