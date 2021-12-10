<!--Author : Camille RABIER-->
<!--Extract data according to checkbox selected-->

<!--https://stackoverflow.com/questions/49652894/export-csv-file-with-headers-from-postgresql-table-using-php-pdo-first-data-->
<?php
if (isset($_POST['ddl'])) {
    require_once 'libphp/db_utils.php';
    /*WARNING you need to have current*/
    connect_db();
    $query = "SELECT genome.id_genome, transcript.id_transcript";
    if (isset($_POST['toddl'])){
        $nr_fields = 0;
        foreach ($_POST['toddl'] as $field){
            $query.= ", $field";
            $nr_fields +=1;
        }
        $query .= " FROM website.genome,website.transcript, website.annotate WHERE genome.id_genome = transcript.id_genome AND annotate.id_transcript = transcript.id_transcript AND validated=1";
        $result = pg_query($db_conn, $query);
        $filename = "Data_extraction$nr_fields.txt";
        header('Content-type: application/csv');
        header('Content-Disposition: attachment; filename=' . $filename);
        header("Content-Transfer-Encoding: UTF-8");
        $file = fopen($filename, "w");
        while ($row = pg_fetch_assoc($result)) {
            fputcsv($file, $row, ";");
        }
        fclose($file);
        if (file_exists($filename)) {
            header('Content-Description: File Transfer');
            header('Content-Type: application/octet-stream');
            header('Content-Disposition: attachment; filename="'.basename($filename).'"');
            header('Expires: 0');
            header('Cache-Control: must-revalidate');
            header('Pragma: public');
            header('Content-Length: ' . filesize($filename));
            ob_clean(); /*TODO ???*/
            flush();/*TODO ???*/
            readfile($filename);
            exit;
        }
    }
}
echo '<b>Which fields of the database you want to download:</b><br>
<!--id_genome-->
    <input type="checkbox" id="box1" name="toddl[]" value="species, strain">
    <label for="box1"> Specie and strain </label>
    <!--id_transcript-->
    <input type="checkbox" id="box3" name="toddl[]" value="genetic_support">
    <label for="box3"> Genetic support</label>
    <input type="checkbox" id="box4" name="toddl[]" value="id_gene">
    <label for="box3"> Gene ID</label><br>
    <input type="checkbox" id="box4" name="toddl[]" value="locbeginning,locend">
    <label for="box3"> Localisation of gene in genome</label>
    <input type="checkbox" id="box4" name="toddl[]" value="strand">
    <label for="box3"> Strand</label><br>   
    <input type="checkbox" id="box4" name="toddl[]" value="gene_biotype,transcript_biotype">
    <label for="box3"> Gene and transcript biotype</label>    
    <input type="checkbox" id="box4" name="toddl[]" value="symbol">
        <label for="box3"> Gene symbol</label>
    <input type="checkbox" id="box4" name="toddl[]" value="description">
    <label for="box4"> Function of protein </label>
<button class="big_submit_button" name="ddl" type="submit" value="ddl"> Download</button>';


