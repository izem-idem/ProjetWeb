<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Result Page for Gene/Protein</title>
    <script type="text/javascript" src="https://www.ncbi.nlm.nih.gov/projects/sviewer/js/sviewer.js" id ="autoload"></script>
    <link rel="stylesheet" type="text/css" href="website.css">
</head>

<body>
<header>
    <h1>CALI</h1>
</header>
<div class="topnav">
    <a href="menu.html">Home</a>
    <a href="AnnotatorArea.html"> Annotator area</a>
    <a href="ValidatorArea.html"> Validator area</a>
    <a href="usermanag.html"> User management</a>
    <a href="Add_genome.html"> Add genome</a>
    <button type="button" class="LogOut" onclick="window.location.href = 'LoginPage.html'">Log out </button>
</div>

<div class="center">

    <h2>Gene/Protein Information</h2>

    <input class="info title" value="Gene ID" disabled><br>
    <label for="organism_name"> Organism name:</label>
    <input class="info" id="organism_name" type="text" value="XXXXXX"disabled><br>
    <label for="strain"> Strain:</label>
    <input class="info" id="strain" type="text" value="XXXXXX"disabled><br>
    <label for="genetic_support"> Genetic support:</label>
    <input class="info" id="genetic_support" type="text" value="XXXXXX"disabled><br>
    <label for="localisation"> Localisation:</label>
    <input class="info" id="localisation" type="text" value="XXXXXX" disabled><br>
    <label for="gene_type"> Gene type:</label>
    <input class="info" id="gene_type" type="text" value="XXXXXX" disabled><br>
    <label for="seq_length"> Sequence length(nt):</label>
    <input class="info" id="seq_length" type="text" value="XXXXXX" disabled><br>
    <label for="desc"> Description:</label>
    <input class="info" id="desc" type="text" value="XXXXXX" disabled><br>
    <label for="annot"> Annotated by:</label>
    <input class="info" id="annot" type="text" value="XXXXXX" disabled><br>

    <b>File to Download:</b>

    <input type="checkbox" id="box1" name="Angene_fasta" value=1>
    <label for="box1"> Annoted Gene </label>
    <input type="checkbox" id="box2" name="Anprot_fasta" value=2>
    <label for="box2"> Annoted Protein </label>
    <input type="checkbox" id="box3" name="gene_fasta" value=3>
    <label for="box3"> Gene sequence</label>
    <input type="checkbox" id="box4" name="prot_fasta" value=4>
    <label for="box4"> Protein sequence</label>&nbsp;&nbsp;

    <button class="little_submit_button" type="submit">Download</button>

    <svg alt="download" class="dlbtn-icon" x="0px" y="0px" width="15px" height="15px" viewBox="0 0 60 60"
    enable-background="new 0 0 60
    60" xml:space="preserve">
        <g>
            <polygon points="48.75,36.25 48.75,48.75 11.25,48.75 11.25,36.25 5,36.25 5,55 55,55 55,36.25  "></polygon>
            <polygon points="42.5,23.75 36.25,23.75 36.25,5 23.75,5 23.75,23.75 17.5,23.75 30,42.5  "></polygon>
        </g>
    </svg>
    <br><br>
    <label for="BLAST"><b>BLAST</b></label>
    
    <select id="seqtype">
        <option value="Protein"> Protein Sequence</option>
        <option value="Nucleic">Nucleic Acid Sequence</option>
    </select>
    
    <select id="db_prot">
        <option value="pdb"> Protein Data Bank</option>
        <option value="nt">Nucleotide collection</option>
        <option value="nr"> Non-redundant Protein Sequence</option>
        <option value="swissprot">UNIPROT</option>
        <option value="refseq_rna">Transcript Reference Sequences</option>
    </select>
    
    <br><br>
    
    <label for="database_link"><b>External links</b></label>
    <select id="database_link">
        <option value="Biocyc"> BioCyc</option>
        <option value="Bacteria.Ensembl">Bacteria.Ensembl</option>
        <option value="NCBI">NCBI</option>
        <option value="PFAM">PFAM</option>
        <option value="Uniprot">Uniprot</option>
       
    </select>
    <button class="little_submit_button" type="submit">Access</button> <br><br/>
    <div class="visu-part">
        <p class="Visu"><b>Protein Visualisation (soon) </b></p><br><br>
    </div>
    
</div>

<div id="sv2" class="SeqViewerApp">
    
        <?php 
        
        
        $link = "embedded=true&id=AAN78503";
        $appname = "&appname=IZEM";
       
        ?>        
    
      <a href="<?php echo $link.$id.$appname?>"></a> <!--lien vers seqViewerApp.js-->

    </div>
    <script>
      SeqViewOnReady(function() {
        var app = SeqView.App.findAppByDivId('sv2');
      });
      
    </script>




<footer>
    <a href="Contact.html">Contact</a><br>
    <p>© CALI 2021</p>
</footer>
</body>
</html>
