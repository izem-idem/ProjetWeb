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
    Species     VARCHAR(100) NOT NULL, /*complete name like Escherichia Coli*/
    Strain      VARCHAR(20) NOT NULL,
    Sequence    TEXT NOT NULL,
    Contributor varchar(500), /*has added the genome*/
    PRIMARY KEY (Species, Strain),
    FOREIGN KEY (Contributor) REFERENCES "user" (Email)
);

CREATE TABLE transcript
(
    Id_transcript   VARCHAR(50) NOT NULL,
    Id_gene         VARCHAR(50),
    Species         VARCHAR(100) NOT NULL,
    Strain          VARCHAR(20) NOT NULL,
    Genetic_support VARCHAR(50) NOT NULL, /*plasmid or bacterial chromosome*/
    Chromomose_id   VARCHAR(20) NOT NULL,
    gene_type       VARCHAR(50), /*gene_type on fasta*/
    Symbol          VARCHAR(10), /*gene_symbol on fasta*/
    Description     VARCHAR(100), /*protein description*/
    LocBeginning    INTEGER NOT NULL,
    LocEnd          INTEGER NOT NULL,
    Strand          INTEGER check ( Strand = -1 or Strand = 1 ),
    Sequence_nt     TEXT NOT NULL,
    Sequence_p      TEXT NOT NULL,
    Annotator       varchar(500),
    ValidatedBy     VARCHAR(500), /*Affects annotator and validates*/
    PRIMARY KEY (Id_transcript),
    FOREIGN KEY (Species, Strain) REFERENCES genome (species, strain),
    FOREIGN KEY (Annotator) REFERENCES "user" (Email),
    FOREIGN KEY (ValidatedBy) REFERENCES "user" (Email)
);


CREATE TABLE annotate
(
    Id              SERIAL PRIMARY KEY, /*instance se fait avec DEFAULT*/
    Id_trancript    VARCHAR(50) NOT NULL,
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
