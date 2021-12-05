/*Utilisateur admin nécessaire pour acces à db via php */
-- CREATE USER admin WITH ENCRYPTED PASSWORD 'admin';
-- ALTER USER admin WITH SUPERUSER ;

-- CREATE SCHEMA AND TABLES
    -- SCHEMA
CREATE SCHEMA website;
SET SCHEMA 'website';

    -- SET TIME ZONE
SET timezone = 'UTC';

    -- TABLE
/*Table of the users*/
CREATE TABLE users
(
    Email VARCHAR(320) NOT NULL check ( Email ~* '^[a-zA-Z0-9.!#$%&''*+/=?^_`{|}~-]+@[a-zA-Z0-9](?:[a-zA-Z0-9-]{0,61}[a-zA-Z0-9])?(?:\.[a-zA-Z0-9](?:[a-zA-Z0-9-]{0,61}[a-zA-Z0-9])?)*$') , /*cf https://dba.stackexchange.com/questions/68266/what-is-the-best-way-to-store-an-email-address-in-postgresql/165923#165923 */
    /*TODO USERNAME ?*/
    /*An email adress has a maximal size of 320 characters https://www.rfc-editor.org/errata_search.php?rfc=3696*/
    Password VARCHAR(255) NOT NULL,
    FirstName VARCHAR(50) NOT NULL,
    LastName VARCHAR(50) NOT NULL,
    TelNr VARCHAR(15) NOT NULL, /*TODO RENDRE NULL POSSIBLE ?*/
    LastConnection timestamptz, /*Last date of connection*/
    Status VARCHAR(10) CHECK(Status='Reader' OR Status='Annotator' OR Status='Validator' OR Status='Admin') NOT NULL,
    Access BOOLEAN, /*True if user has still access to website and false otherwise*/
    /*By default the new users are reader until the admin changes their status*/
    PRIMARY KEY(Email)
);

/*Table des génomes remplie via add_genome ?à part pour les 3 génomes déja annotés ?*/
CREATE TABLE genome
(
-- Identifiers
    Id_genome VARCHAR(50) NOT NULL, /*Clé primaire qui correspond à chromosome:*/
    Species     VARCHAR(100) NOT NULL, /*complete name like Escherichia Coli*/
    Strain      VARCHAR(50),
-- Sequence and size
    Sequence    TEXT NOT NULL,
    Size_genome INT NOT NULL,
    PRIMARY KEY (Id_genome)
);

/*Table des transcript avec seulement les parties non annotables*/
CREATE TABLE transcript
(
-- Identifiants
    Id_transcript   VARCHAR(50) NOT NULL, /*Clé primaire qui correspond au premier élément après le chevron dans le header*/
    Id_genome VARCHAR(50) NOT NULL, /* chromosome:*/
    Genetic_support VARCHAR(50) NOT NULL, /*plasmid or bacterial chromosome*/
-- Info sur localisation dans génome
    LocBeginning    INTEGER NOT NULL,
    LocEnd          INTEGER NOT NULL,
    Strand          CHAR(2), /*Valeur après localisation dans génome. -1 ou 1, elle n'est pas présente dans les génomes non annotés*/
    /*TODO strand absent de new_coli, mais a priori non identifiable, donc dans annotation ou transcript ?*/
-- Séquences et leur tailles
    Sequence_nt     TEXT NOT NULL,
    Size_nt         INT NOT NULL,
    Sequence_p      TEXT, /*Till the parsing of the peptide file is not done, it can be null*/
    Size_p          INTEGER, /*Till the parsing of the peptide file is not done, it can be null*/
-- Verification of the presence of an annotation in annotate for the transcript
    Annotation       INTEGER NOT NULL, /*0 if no annotator assigned, 1 if annotation exists (even if not validated), 2 annotator assigned, but no annotation exists*/
-- Current annotator assigned. If his annotation is not validated, the validator can choose to change the annotator
    Annotator_email       VARCHAR(320),
    FOREIGN KEY (Annotator_email) REFERENCES users(Email),
    PRIMARY KEY (Id_transcript),
    FOREIGN KEY (Id_genome) REFERENCES genome (Id_genome)
);

/*Table that contains all the history of annotations, their status (validated/rejected/waiting for validation) and the commentary associated*/
CREATE TABLE annotate
(
    Id              SERIAL PRIMARY KEY, /*unique ID for the annotation*/
    Id_transcript    VARCHAR(50) NOT NULL, /*TODO create index ?*/
--  Annotations
    Id_gene         VARCHAR(50), /* gene:*/
    Gene_biotype       VARCHAR(50), /*gene_biotype:*/
    Transcript_biotype       VARCHAR(50), /*transcript_biotype:*/
    Symbol          VARCHAR(20), /*gene_symbol:*/
    Description     VARCHAR(200), /*description:*/
--  Eléments de validations
    Commentary      VARCHAR(500), /*Commentary added when validated/rejected*/
    Validated       int NOT NULL, /*0 waiting for validation, 1 validated, 2 rejected*/
    Date_annotation timestamptz NOT NULL, /*date of annotation*/
    Annotator_email VARCHAR(320), /*email of the annotator that made this annotation*/
    Validator_email VARCHAR(320), /*email adress from the validation that has validated/rejected*/
    unique(Id_transcript,Date_annotation,Annotator_email), /*natural key of the table*/
    FOREIGN KEY (Validator_email) REFERENCES users (Email),
    FOREIGN KEY (annotator_email) REFERENCES users (Email),
    FOREIGN KEY (Id_transcript) REFERENCES transcript (Id_transcript)
);
