<?php

require_once 'web/libphp/db_utils.php'; # faut les mettre dans le répetoire !!!
require('simple_html_dom.php'); # faut les mettre dans le répetoire !!!
connect_db();

#$id = $_GET['id']; #ici je devrais récuperer l'id du génome ok ? 

####FOR GENOME DOWNLOAD

$query = "SELECT Id_genome,Species,Strain,Size_genome,Sequence FROM website.genome WHERE Id_genome =$1"; #je vais $row[0:4]
$result = pg_query_params($db_conn, $query,array('ASM744v1')) or die("Error " . pg_last_error());
$row = pg_fetch_row($result);
#echo "$row[0] $row[1]\n";

$id_genome=$row[0];
$species=$row[1];
$strain=$row[2];
$size=$row[3];


 
#echo $row[4];


if (isset($_POST["Load"])){
    $file=$id_genome.".fasta";
    file_put_contents($file,$row[4]); #j'écris la séquence dans un fichier qui s'appelle id_genome.fasta 
    #Attention le current dir dans lequel vous allez mettre ce code doit avoir les droit d'écriture (faites chmod 777 dir/ ) 
    
    if(file_exists($file)) {
            echo "ok";
            header('Content-Description: File Transfer');
            header('Content-Type: application/octet-stream');
            header('Content-Disposition: attachment; filename='.basename($file));
            header('Content-Transfer-Encoding: binary');
            header('Expires: 0');
            header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
            header('Pragma: public');
            header('Content-Length: ' . filesize($file));
            ob_clean();
            flush();
            readfile($file);
            exit;

    }
}

#### GETTING THE EXTERNAL LINKS

#NCBI
$link_ncbi="https://www.ncbi.nlm.nih.gov/assembly/?term=".$id_genome;


###Bacteria.Ensembl
$html =file_get_html("http://bacteria.ensembl.org/Multi/Search/Results?species=all;idx=;q={$id_genome};site=ensemblunit");
foreach($html->find(".name") as $element)  { 
    $id_EBI=strip_tags($element); # strip_tags transform a hyperlink text into a text
}

$link_Bensembl="http://bacteria.ensembl.org/{$id_EBI}/Info/Index";
####


###GTDB
$html =file_get_html($link_ncbi);
foreach($html->find("dd") as $element){
    if(str_contains($element,"GCF")){
        $GCF=explode("(",$element);
        
        $link_GTDB="https://gtdb.ecogenomic.org/genomes?gid=".strip_tags($GCF[0]);
       }

}


###

####LINK SEQVIEWER

$html2 =file_get_html("https://www.ncbi.nlm.nih.gov/assembly/?term={$id_genome}"); # avec $_GET j'ai l'id !! 
        foreach($html2->find(".refseq") as $ele)  { 
            $id2=strip_tags($ele);
        }
        
$link = "embedded=true&id={$id2}&appname=IZEM";





disconnect_db(); /*disconnect from the database*/
?>
