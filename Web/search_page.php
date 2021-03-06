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


            <label for="species_name"><b>Species name</b> </label>
            <input type="text" name="species_name" id="species_name" placeholder="Species name"><br>

            <label for="strain_name"><b>Strain name</b></label>
            <input type="text" name="strain_name" id="strain_name" placeholder="Strain name"><br>

            <label for="genetic_support"><b>Genetic support (bacterial or plasmid typically)</b></label>
            <input type="text" name="genetic_support" id="genetic_support" placeholder="Genetic support"><br>

            <label for="chr_ID"><b>Chromosome ID</b></label>
            <input type="text" id="chr_ID" name="id_genome" placeholder="Chromosome ID"><br>

            <label for="seq_genome"><b>Genome sequence</b></label>
            <input type="text" name="genome_seq" id="seq_genome" placeholder="Genome sequence"><br>

            <label for="gene_id"><b>Gene ID</b></label>
            <input type="text" id="gene_id" name="gene_id" placeholder="Gene ID"><br>

            <label for="gene_symbol"><b>Gene symbol</b></label>
            <input type="text" name="gene_symbol" id="gene_symbol" placeholder="Gene symbol"><br>

            <label for="sequence_nt"><b>Nucleotide sequence or pattern (3 to 950 characters)</b></label>
            <input type="text" id="sequence_nt" name="sequence_nt" placeholder="Nucleotide pattern" minlength="3" maxlength="950"><br>

            <label for="prot_id"><b>Protein ID</b> </label>
            <input type="text" id="prot_id" name="id_transcript" placeholder="Protein ID"><br>

            <label for="description"><b>Function</b></label>
            <input type="text" id="description" name="description" placeholder="Function"><br>

            <label for="prot_seq"><b>Protein sequence or pattern (3 to 320 characters)</b></label>
            <input type="text" id="prot_seq" name="prot_seq" placeholder="Protein pattern" minlength="3" maxlength="320"><br><br>

            <label for="result_type"><b>Select type of results</b></label>
            <select name="result_type" id="result_type">
                <option value="gene_prot"> Gene / Protein</option>
                <option value="Genome"> Genome</option>
            </select><br><br>
            <button class="big_submit_button" name="submit" type="submit" value="Submit"> Submit</button>
        </form>
        <?php

        include_once 'libphp/db_utils.php';
        connect_db();
        if (isset($_POST["submit"])) {

            $info_formulaire = ["species_name", "strain_name", "genetic_support", "id_genome", "sequence_nt", "id_transcript", "prot_seq", "gene_id", "gene_symbol", "description"];
            $col_table = ["genome.species", "genome.strain", "transcript.genetic_support", "genome.id_genome",
                "transcript.sequence_nt", "transcript.id_transcript", "transcript.sequence_p", "annotate.id_gene", "annotate.symbol", "annotate.description"];

            for ($i = 0; $i <= count($info_formulaire) - 1; $i++) { //Pour chaque champ du formulaire
                $ch = $info_formulaire[$i]; // $ch = ensemble des ID du formulaire
                $col = $col_table[$i]; // $col = ensemble des attributs des tables SQL
                if ($col == "id_genome") { // On a deux fois id_genome. Ici, on selectonne celui de la table transcript
                    $col = "transcript." . $col;
                }
                if (!empty($_POST[$ch])) { //Si le champ est rempli
                    if (isset($query_sql)) {
                        if (($ch != "sequence_nt") && ($ch != "sequence_p")) { // Si ce n'est pas des motifs, on utilise " = "
                            $query_sql .= "AND " . $col . "='" . $_POST[$ch] . "'";
                        } else { // Si on recherche  des motifs (nucleotidique ou proteique), on utilise un " LIKE % "
                            $query_sql .= "AND " . $col . " LIKE %" . $_POST[$ch] . "% ";
                        }

                    } else { // Si la requ??te est vide
                      if ($_POST["result_type"] == "gene_prot") { // Si on veut en r??sultat des genes ou des proteines
                        if (($ch != "sequence_nt") && ($ch != "sequence_p")) {

                            $query_sql = "SELECT annotate.id_transcript FROM website.genome,website.transcript, website.annotate WHERE genome.id_genome = transcript.id_genome AND annotate.id_transcript = transcript.id_transcript AND " . $col . "='" . $_POST[$ch] . "'";
                        } else {
                            $query_sql = "SELECT annotate.id_transcript FROM website.genome,website.transcript, website.annotate WHERE genome.id_genome = transcript.id_genome AND  annotate.id_transcript = transcript.id_transcript AND " . $col . " LIKE '%" . $_POST[$ch] . "%'";

                        }
                      } else if ($_POST["result_type"] == "Genome") { // Si on veut en r??sultat des g??nomes
                        if (($ch != "sequence_nt") && ($ch != "sequence_p")) {

                            $query_sql = "SELECT  DISTINCT(transcript.id_genome) FROM website.genome,website.transcript, website.annotate WHERE genome.id_genome = transcript.id_genome AND annotate.id_transcript = transcript.id_transcript AND " . $col . "='" . $_POST[$ch] . "'";
                        } else {
                            $query_sql = "SELECT  DISTINCT(transcript.id_genome) FROM website.genome,website.transcript, website.annotate WHERE genome.id_genome = transcript.id_genome AND  annotate.id_transcript = transcript.id_transcript AND " . $col . " LIKE '%" . $_POST[$ch] . "%'";

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


            // Lib??re le r??sultat
            pg_free_result($res);
        }


        ?>
    </div>
</div>
<footer>
    <a href="Contact.php">Contact</a><br>
    <p>?? CALI 2021</p>
</footer>
</body>
</html>
