<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title> Search </title>
    <link rel="stylesheet" type="text/css" href="website.css">
</head>
<body>


<header>
    <h1>CALI</h1>
</header>
<br><br>


<div class="center">
    <h2> Search </h2>
    <div class="container">

        <form action= <?php echo $_SERVER['PHP_SELF']; ?> method="post">

        <p> You can use multiple arguments per search area by using "AND". </p>
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

        <label for="strand"><b>Strand</b></label>
        <select name="strand">
            <option value="-1"> -1 </option>
            <option value="+1"> +1 </option>
        </select><br><br>

        <b> Gene localisation</b><br>
        <div class="double">
            <label for="gene_beg">Beginning :</label><br>
            <input class="localisation_input" type="text" name="gene_beg" placeholder="Begin">
        </div>
        <div class="double">
            <label for="gene_end">Ending :</label><br>
            <input class="localisation_input" type="text" name="gene_end" placeholder="End">
        </div><br>

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
    </div>
  </form>
</div>

<?php
// REQUETE SQL

include_once 'libphp/db_utils.php';
connect_db ();

if(isset($_POST["submit"])){
  $query_sql = "";
  	$info_formulaire = ["species_name","strain_name","genetic_support","id_genome","strand","gene_beg","gene_end","sequence_nt","id_transcript","prot_seq", "gene_id", "gene_symbol", "description"];
  	$col_table = ["id_genome","strain","genetic_support","id_genome", "strand","LocBeginning","LocEnd","sequence_nt","id_transcript","sequence_p", "id_gene", "symbol", "description"];

  	for ($i = 0; $i <=12; $i++) { //Pour chaque champ du formulaire
  		$ch = $info_formulaire[$i];
  		$col = $col_table[$i];
  		if ($col=="id_genome"){ //idgenome est ambigu
  			$col = "transcript.".$col;
  		}
  		if (!empty($_POST[$ch])){ //Si la champ est rempli
  			if (!empty($query_sql)){//Si la requete n'est pas vide
  				if(($ch != "sequence_nt")&&($ch != "sequence_p")){
  					$query_sql .= "AND ".$col."='".$_POST[$ch]."'";
  				}else{
  					$query_sql .= "AND ".$col." LIKE '%".$_POST[$ch]."%' ";
  				}

  			}else{ // Si la requete est vide
  				if(($ch != "query_nuc")&&($ch != "query_prot")){
  					$query_sql .= "SELECT * FROM website.genome, website.transcript WHERE genome.id_genome = transcript.id_genome AND annotate.id_transcript = transcript.id_transcript AND ".$col."='".$_POST[$ch]."'";
  				}else{
  					$query_sql .= "SELECT * FROM website.genome,website.transcript WHERE genome.id_genome = transcript.id_genome AND  annotate.id_transcript = transcript.id_transcript AND ".$col." LIKE '%".$_POST[$ch]."%' ";

  				}

  			}
  		}
  	}


  echo "<table>\n";

  $res = pg_query($db_conn, $query_sql) or die (pg_last_error());
  while ($line = pg_fetch_array($res, null, PGSQL_ASSOC)) {
  echo "\t<tr>\n";
  foreach ($line as $col_value) {
  echo "\t\t<td>$col_value</td>\n";
  }
  echo "\t</tr>\n";
  }
  echo "</table>\n";
  // Libère le résultat
  pg_free_result($res);
}


deconnect_db();
?>

<footer>
    <a href="Contact.html">Contact</a><br>
    <p>© CALI 2021</p>
</footer>
</body>
</html>
