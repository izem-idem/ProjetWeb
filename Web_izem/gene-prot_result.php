<?php
      
#############################################################GET GENE-PROT INFO ##################################################################
require_once 'web/libphp/db_utils.php'; #faut les mettre dans le répetoire !!!
connect_db();

#$idd = $_GET['id']; #ici je devrais récuperer l'id du géne-prot ok ? 

####FOR GENE-PROT DOWNLOAD

$query_1 = "SELECT genome.Id_genome,Genetic_support,LocBeginning,LocEnd,Sequence_nt,Sequence_p,Size_nt,Strand,Annotator_email,Species,Strain FROM website.transcript,website.genome WHERE genome.Id_genome = transcript.Id_genome AND Id_transcript =$1"; 
$result_1 = pg_query_params($db_conn, $query_1,array($idd)) or die("Error ". pg_last_error());
$line = pg_fetch_row($result_1);

#Submit the query_1 and extract the informations via pg_fetch_row


$id_genome=$line[0];
$support=$line[1];
$loc="${line[2]}:${line[3]}";
$nt=$line[4];
$prot=$line[5];
$size_nt=$line[6];
$strand=$line[7];
$mail=$line[8];
$organism=$line[9];
$strain=$li[10];


if (empty($strain)){ #check if the strain is mentionned
   $strain = "Unknown";
}

if (empty($mail)){ #check wether there is a annotator
   $mail = "Unknown";
}


$query_2 = "SELECT Gene_biotype,Symbol,Description,Id_gene,Validated FROM website.annotate WHERE Id_transcript =$1 AND Validated in (0,1)"; 
$result_2 = pg_query_params($db_conn, $query_2,array($idd)) or die("Error " . pg_last_error());
$ligne = pg_fetch_row($result_2);


#Submit the query_2 and extract the informations via pg_fetch_row

$biotype=$ligne[0];
$symbol=$ligne[1];
$fonction=$ligne[2];
$gene=$ligne[3];
$validated=$ligne[4];





if (empty($biotype)){ #check wether there is gene_biotype is mentionned
   $biotype = "Unknown";
}

if (empty($symbol)){ #check wether there is gene symbol is mentionned
   $symbol = "Unknown";
}

if (empty($fonction)){ #check wether a function is assigned
   $fonction = "Unknown";
}

if (empty($gene)){ #check wether a gene_id is mentionned
   $gene = "Unknown";
}



disconnect_db(); /*disconnect from the database*/

#########################################################################GET THE LINKS ###########################################################


#For the URL of NCBI#
$linkk = "https://www.ncbi.nlm.nih.gov/protein/?term="; 
$goto_ncbi=$linkk.$idd;

require('web/simple_html_dom.php');


$id_transcrit = $idd; # not necessary but id_transcrit is more appealing than idd .

#For the URL of Bacteria.Ensembl#
$url_request = "http://bacteria.ensembl.org/Multi/Search/Results?species=all;idx=;q={$id_transcrit};site=ensemblunit";
$txt = file_get_contents($url_request);
$results = array();
$test = preg_match_all('#<a class="name" href="/(.+?)"><strong>#', $txt, $results);
$url_ensembl = "http://bacteria.ensembl.org/{$results[1][0]}";



//For the URL of UNIPROT AND PFAM#
$html = file_get_html("https://www.uniprot.org/uniprot/?query={$id_transcrit}");
foreach($html->find(".entryID") as $element){ 
    $id=strip_tags($element);
}

$goto_uniprot= "https://www.uniprot.org/uniprot/{$id}"; #url uniprot

$goto_pfam= "http://pfam.xfam.org/protein/{$id}"; #url pfam


####################################################################################SEQ VIEWER ###################################################


$lien_SV ="embedded=true&id={$id_transcrit}&appname=IZEM";


######################################################SEQUENCE DOWNLOADING####################################################################


$url_gene="Download_gene-prot.php?gene={$idd}&prot=";

$url_prot="Download_gene-prot.php?gene=&prot={$idd}";

##################################################################LINK FOR GENOME PAGE##############################

$linker = "GenomePage.php?id={$id_genome}"


      
?>
