CREATE SCHEMA website;
SET SCHEMA 'website';

CREATE TABLE utilisateur(
    Email VARCHAR(500) NOT NULL check ( Email ~* '^[a-zA-Z0-9.!#$%&''*+/=?^_`{|}~-]+@[a-zA-Z0-9](?:[a-zA-Z0-9-]{0,61}[a-zA-Z0-9])?(?:\.[a-zA-Z0-9](?:[a-zA-Z0-9-]{0,61}[a-zA-Z0-9])?)*$' ), /*cf https://dba.stackexchange.com/questions/68266/what-is-the-best-way-to-store-an-email-address-in-postgresql/165923#165923 */
    Password VARCHAR(50) NOT NULL,
    FirstName VARCHAR(50),
    LastName VARCHAR(50),
    TelNr VARCHAR(15),
    LastConnection timestamp,
    statut VARCHAR(10) CHECK(statut='Reader' OR statut='Annotator' OR statut='Validator' OR statut='Admin'),
    PRIMARY KEY(Email)
);

CREATE TABLE genome(
    Species VARCHAR(100),  /*complete name like Escherichia Coli*/
    Strain VARCHAR(20),
    Sequence TEXT,
    PRIMARY KEY (Species,Strain)
);

CREATE TABLE transcript(
  Id_transcript VARCHAR(50),
  Id_gene VARCHAR(50),
  Species VARCHAR(100),
  Strain VARCHAR(20),
  Genetic_support VARCHAR(50), /*plasmid or bacterial chromosome*/
  gene_type VARCHAR(50), /*gene_type on fasta*/
  Symbol VARCHAR(10),  /*gene_symbol on fasta*/
  Description VARCHAR(100), /*protein description*/
  LocBeginning INTEGER,
  LocEnd INTEGER,
  Strand INTEGER check ( Strand=-1 or Strand=1 ),
  Sequence_nt TEXT,
  Sequence_p TEXT,
  annotator TEXT,
  PRIMARY KEY(Id_transcript),
  FOREIGN KEY(Species,Strain) REFERENCES genome
);


CREATE TABLE annotate(
    Id SERIAL PRIMARY KEY, /*instance se fait avec DEFAULT*/
    Id_trancript VARCHAR(50),
    Description VARCHAR(5000),
    Commentary VARCHAR(500),
    Validated BOOLEAN,
    Date_annotation timestamp,
    Validator_email VARCHAR(500),
    FOREIGN KEY (Validator_email) REFERENCES validator,
    FOREIGN KEY (Id_trancript) REFERENCES transcript
);
