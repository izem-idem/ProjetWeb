/*Utilisateur admin nécessaire pour acces à db via php */
-- CREATE USER admin WITH ENCRYPTED PASSWORD 'admin';
-- ALTER USER admin WITH SUPERUSER ;

/*CREATE SCHEMA AND TABLES*/
CREATE SCHEMA website;
SET SCHEMA 'website';

CREATE TABLE "user"
(
    Email VARCHAR(500) NOT NULL check ( Email ~* '^[a-zA-Z0-9.!#$%&''*+/=?^_`{|}~-]+@[a-zA-Z0-9](?:[a-zA-Z0-9-]{0,61}[a-zA-Z0-9])?(?:\.[a-zA-Z0-9](?:[a-zA-Z0-9-]{0,61}[a-zA-Z0-9])?)*$') , /*cf https://dba.stackexchange.com/questions/68266/what-is-the-best-way-to-store-an-email-address-in-postgresql/165923#165923 */
    Password VARCHAR(50) NOT NULL,
    FirstName VARCHAR(50) NOT NULL,
    LastName VARCHAR(50) NOT NULL,
    TelNr VARCHAR(15) NOT NULL,
    LastConnection timestamp,
    statut VARCHAR(10) CHECK(statut='Reader' OR statut='Annotator' OR statut='Validator' OR statut='Admin') NOT NULL,
    PRIMARY KEY(Email)
);

CREATE TABLE genome
(
    Id_genome VARCHAR(50) NOT NULL,
    Species     VARCHAR(100) NOT NULL, /*complete name like Escherichia Coli*/
    Strain      VARCHAR(20),
    Sequence    TEXT NOT NULL,
    Size_genome INT NOT NULL,
    PRIMARY KEY (Id_genome)
);

CREATE TABLE transcript
(
    Id_transcript   VARCHAR(50) NOT NULL,
    Id_gene         VARCHAR(50),
    Id_genome VARCHAR(50) NOT NULL,
    Genetic_support VARCHAR(50) NOT NULL, /*plasmid or bacterial chromosome*/
    Gene_biotype       VARCHAR(50), /*gene_biotype on fasta*/
    Transcript_biotype       VARCHAR(50), /*transcript_biotype on fasta*/
    Symbol          VARCHAR(20), /*gene_symbol on fasta*/
    Description     VARCHAR(100), /*protein description*/
    LocBeginning    INTEGER NOT NULL,
    LocEnd          INTEGER NOT NULL,
    Strand          CHAR(2),
    Sequence_nt     TEXT NOT NULL,
    Size_nt         INT NOT NULL,
    Sequence_p      TEXT,
    Size_p          INT,
    Annotator       varchar(500),
    ValidatedBy     VARCHAR(500), /*Affects annotator and validates*/
    PRIMARY KEY (Id_transcript),
    FOREIGN KEY (Id_genome) REFERENCES genome (Id_genome),
    FOREIGN KEY (Annotator) REFERENCES "user" (Email),
    FOREIGN KEY (ValidatedBy) REFERENCES "user" (Email)
);


CREATE TABLE annotate
(
    Id              SERIAL PRIMARY KEY, /*instance se fait avec DEFAULT*/
    Id_transcript   VARCHAR(50) NOT NULL,
    Description     VARCHAR(5000) NOT NULL,
    Commentary      VARCHAR(500),
    Validated       BOOLEAN NOT NULL,
    Date_annotation timestamp NOT NULL,
    Annotator_email VARCHAR(500) NOT NULL,
    Validator_email VARCHAR(500) NOT NULL, /*email adress from the validator that assigned and will validate*/
    FOREIGN KEY (Validator_email) REFERENCES "user" (Email),
    FOREIGN KEY (annotator_email) REFERENCES "user" (Email),
    FOREIGN KEY (Id_trancript) REFERENCES transcript (Id_transcript)
);
