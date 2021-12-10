<?php

require_once 'web/libphp/db_utils.php'; # faut les mettre dans le répetoire !!!
connect_db();


if (empty($_GET['prot'])){ # downlad cds sequence
   $gene=$_GET['gene'];
   $query_1 = "SELECT Id_genome,Genetic_support,LocBeginning,LocEnd,Sequence_nt,Sequence_p,Strand FROM website.transcript WHERE Id_transcript =$1"; 
$result_1 = pg_query_params($db_conn, $query_1,array($gene)) or die("Error " . pg_last_error());
$line = pg_fetch_row($result_1);

$id_genome=$line[0];
$support=$line[1];
$loc="${line[2]}:${line[3]}";
$nt=$line[4];
$prot=$line[5];
$strand=$line[6];


$query_2 = "SELECT Gene_biotype,Symbol,Description,Id_gene,Transcript_biotype FROM website.annotate WHERE Id_transcript =$1"; 
$result_2 = pg_query_params($db_conn, $query_2,array($gene)) or die("Error " . pg_last_error());
$ligne = pg_fetch_row($result_2);

$biotype=$ligne[0];
$symbol=$ligne[1];
$fonction=$ligne[2];
$gene_id=$ligne[3];
$transcript=$ligne[4];

$big_line=">{$gene} cds {$support}:{$id_genome}:{$support}:{$loc}:{$strand} gene:{$gene_id} gene_biotype:{$biotype} transcript_biotype:{$transcript} gene_symbol:{$symbol} description:{$fonction}\n".$nt;


$file=$gene."_cds.fasta";
#echo $key=array_search($prefix, $array);
file_put_contents($file,$big_line); 
if(file_exists($file)) {
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
else{
     
     $prot_id=$_GET['prot'];
     $query_1 = "SELECT Id_genome,Genetic_support,LocBeginning,LocEnd,Sequence_nt,Sequence_p,Strand FROM website.transcript WHERE Id_transcript =$1"; 
    $result_1 = pg_query_params($db_conn, $query_1,array($prot_id)) or die("Error " . pg_last_error());
    $line = pg_fetch_row($result_1);

    $id_genome=$line[0];
    $support=$line[1];
    $loc="${line[2]}:${line[3]}";
    $nt=$line[4];
    $prot=$line[5];
    $strand=$line[6];


    $query_2 = "SELECT Gene_biotype,Symbol,Description,Id_gene,Transcript_biotype FROM website.annotate WHERE Id_transcript =$1"; 
    $result_2 = pg_query_params($db_conn, $query_2,array($prot_id)) or die("Error " . pg_last_error());
    $ligne = pg_fetch_row($result_2);

    $biotype=$ligne[0];
    $symbol=$ligne[1];
    $fonction=$ligne[2];
    $gene_id=$ligne[3];
    $transcript=$ligne[4];

    $big_line=">{$prot_id} pep {$support}:{$id_genome}:{$support}:{$loc}:{$strand} gene:{$gene_id} gene_biotype:{$biotype} transcript_biotype:{$transcript} gene_symbol:{$symbol} description:{$fonction}\n".$prot;


    $file=$prot_id."_pep.fasta";
    #echo $key=array_search($prefix, $array);
    file_put_contents($file,$big_line); 
    if(file_exists($file)) {
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




disconnect_db(); 

?>
