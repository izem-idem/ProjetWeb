<?php

require_once 'web/libphp/db_utils.php'; # this include allows database connection
require('simple_html_dom.php'); # this include the allows the web pag parsing
connect_db();


####FOR GENOME DOWNLOAD

$query = "SELECT Id_genome,Species,Strain,Size_genome,Sequence FROM website.genome WHERE Id_genome =$1"; 
$result = pg_query_params($db_conn, $query,array($id)) or die("Error " . pg_last_error());
$row = pg_fetch_row($result);

$id_genome=$row[0];
$species=$row[1];
$strain=$row[2];
$size=$row[3];


if (empty($strain)){ #check if the strain is mentionned
   $strain = "Unknown";
}




 
$big_line=">Chromosome dna:chromosome chromosome:{$id_genome}:Chromosome:1:{$size}:1 REF\n".$row[4];


if (isset($_POST["Load"])){
    $file=$id_genome.".fasta";
    file_put_contents($file,$big_line); # write in $file the text contained in big_line
    
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
            ob_clean(); # Clean (erase) the output buffer
            flush(); #Flush system output buffer
            readfile($file); # Outputs the text contained in $file
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
foreach($html->find("dd") as $element){ #parsing to find the element common to all these types of ids
    if(str_contains($element,"GCF")){
        $GCF=explode("(",$element);
        
        $link_GTDB="https://gtdb.ecogenomic.org/genomes?gid=".strip_tags($GCF[0]);
       }

}


###

####LINK SEQVIEWER

$html2 =file_get_html("https://www.ncbi.nlm.nih.gov/assembly/?term={$id_genome}");
        foreach($html2->find(".refseq") as $ele)  { 
            $id2=strip_tags($ele);
        }
        
$link = "embedded=true&id={$id2}&v=10000:15000&appname=IZEM";

#####options : 
#embedded => choose if you dispaly Seq Viewer a page alone or embedded in page 
#id => give the ncbi (refseq or genbank) id 
#v => choose the region you want to zoom in 
# appname => specify the appname , if you don't specify it , by default it will be localhost




disconnect_db(); /*disconnect from the database*/
?>
