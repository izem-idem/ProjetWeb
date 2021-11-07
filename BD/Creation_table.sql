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
/*Table des utilisateurs remplie via Account_creation avec le statut donné via usermanag*/
CREATE TABLE users
(
    Email VARCHAR(320) NOT NULL check ( Email ~* '^[a-zA-Z0-9.!#$%&''*+/=?^_`{|}~-]+@[a-zA-Z0-9](?:[a-zA-Z0-9-]{0,61}[a-zA-Z0-9])?(?:\.[a-zA-Z0-9](?:[a-zA-Z0-9-]{0,61}[a-zA-Z0-9])?)*$') , /*cf https://dba.stackexchange.com/questions/68266/what-is-the-best-way-to-store-an-email-address-in-postgresql/165923#165923 */
    /*An email adress has a maximal size of 320 characters https://www.rfc-editor.org/errata_search.php?rfc=3696*/
    Password VARCHAR(50) NOT NULL, /*TODO doit être crypté*/
    FirstName VARCHAR(50) NOT NULL,
    LastName VARCHAR(50) NOT NULL,
    TelNr VARCHAR(15) NOT NULL,
    LastConnection timestamptz,
    statut VARCHAR(10) CHECK(statut='Reader' OR statut='Annotator' OR statut='Validator' OR statut='Admin') NOT NULL,
    PRIMARY KEY(Email)
);

/*Table des génomes remplie via add_genome ?à part pour les 3 génomes déja annotés ?*/
CREATE TABLE genome
(
-- Identifiants
    Id_genome VARCHAR(50) NOT NULL, /*Clé primaire qui correspond à chromosome:*/
    Species     VARCHAR(100) NOT NULL, /*complete name like Escherichia Coli*/
    Strain      VARCHAR(50),
-- Séquence et taille
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
    Sequence_p      TEXT, /*Tant que update (parsing de pep) pas faite, peut être nul*/
    Size_p          INTEGER, /*Tant que update (parsing de pep) pas faite, peut être nul*/
-- Vérification si annoté (TRUE) ou non (FALSE)
    Annotated       BOOLEAN NOT NULL,
    PRIMARY KEY (Id_transcript),
    FOREIGN KEY (Id_genome) REFERENCES genome (Id_genome)
);

/*Table qui contient les annotations faites et validées (déja dans BD ou ajoutée par utilisateur)*/
/*TODO quels champs mettre not null*/
CREATE TABLE annotations(
    Id_transcript   VARCHAR(50) NOT NULL,
    Id_gene         VARCHAR(50), /* gene:*/
    Gene_biotype       VARCHAR(50), /*gene_biotype:*/
    Transcript_biotype       VARCHAR(50), /*transcript_biotype:*/
    Symbol          VARCHAR(20), /*gene_symbol:*/
    Description     VARCHAR(200), /*description:*/
    PRIMARY KEY (Id_transcript),
    FOREIGN KEY (Id_transcript) REFERENCES transcript(Id_transcript)
);

/*Table qui contient l'historique des annotations non validées/rejetées/à valider et les commentaires associés*/
CREATE TABLE annotate
(
    Id              SERIAL PRIMARY KEY, /*instance se fait avec DEFAULT*/
    Id_transcript    VARCHAR(50) NOT NULL,
    Description     VARCHAR(5000) NOT NULL, /*Annotations faites par annotateur*/
    /*TODO laisser en une seule ligne ou plusieurs attributs ?*/
    Commentary      VARCHAR(500), /*Commentaire ajouté lors de la validation/rejet*/
    Validated       int NOT NULL, /*0 en cours de validation, 1 validé, 2 rejeté*/
    Date_annotation timestamptz NOT NULL,
    Annotator_email VARCHAR(320) NOT NULL,
    Validator_email VARCHAR(320) /*NOT NULL*/, /*email adress from the validation that has validated/rejected*/
    unique(Id_transcript,Date_annotation,Annotator_email), /*TODO ou date seulement ? */
    FOREIGN KEY (Validator_email) REFERENCES users (Email),
    FOREIGN KEY (annotator_email) REFERENCES users (Email),
    FOREIGN KEY (Id_transcript) REFERENCES transcript (Id_transcript)
);

/*Table d'assignement du transcript à l'annotateur*/
/*TODO on pourrait ajouter juste annotateur à annotation je pense*/
CREATE TABLE assignment(
  Id_transcript varchar(50) NOT NULL,
  Annotator   varchar(320) NOT NULL,
  PRIMARY KEY (Id_transcript,Annotator),
  FOREIGN KEY (Annotator) REFERENCES users(Email),
  FOREIGN KEY (Id_transcript) REFERENCES transcript(Id_transcript)
);