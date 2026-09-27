-- Mohamed El Brrah & Michael Djeatsa

CREATE TABLE personnel (
    Id_personnel      INT,
    nom_personnel     VARCHAR(30)  NOT NULL,
    prenom_personnel  VARCHAR(30)  NOT NULL,
    type_personnel    VARCHAR(30)  NOT NULL,
    mot_de_passe     VARCHAR(255)    NOT NULL,
    salaire          INT,
    Id_gerant        INT,
    CONSTRAINT PK_personnel         PRIMARY KEY (Id_personnel),
    CONSTRAINT FK_personnel_gerant  FOREIGN KEY (Id_gerant) REFERENCES personnel(Id_personnel)
);

CREATE TABLE Contrat (
    Id_contrat       INT,
    Date_contrat     DATE         NOT NULL,
    Type_de_contrat  VARCHAR(30)  NOT NULL,
    Id_Personnel     INT          NOT NULL,
    CONSTRAINT PK_Contrat            PRIMARY KEY (Id_contrat),
    CONSTRAINT FK_Contrat_Personnel  FOREIGN KEY (Id_Personnel) REFERENCES Personnel(Id_Personnel)
);

CREATE TABLE Specialite (
    Id_specialite   INT,
    Nom_specialite  VARCHAR(30)  NOT NULL,
    Est_remplacant  CHAR(3)      NOT NULL,
    CONSTRAINT PK_Specialite  PRIMARY KEY (Id_specialite)
);

CREATE TABLE Medicament (
    Id_medication  INT,
    Type_soin      VARCHAR(30)  NOT NULL,
    Dose_soin      NUMBER       NOT NULL,
    CONSTRAINT PK_Medicament  PRIMARY KEY (Id_medication)
);

CREATE TABLE Regime_alimentaire (
    Nom_regime  VARCHAR(30),
    CONSTRAINT PK_Regime  PRIMARY KEY (Nom_regime)
);

CREATE TABLE Nourriture (
    Nom_nourriture   VARCHAR(30),
    Dose_nourriture  NUMBER       NOT NULL,
    Date_expiration  DATE         NOT NULL,
    Nom_regime       VARCHAR(30)  NOT NULL,
    CONSTRAINT PK_nourriture2        PRIMARY KEY (Nom_nourriture),
    CONSTRAINT FK_Nourriture_Regime FOREIGN KEY (Nom_regime) REFERENCES Regime_alimentaire(Nom_regime)
);

CREATE TABLE Espece (
    Nom_espece        VARCHAR(30),
    Nom_latin_espece  VARCHAR(80)  NOT NULL,
    Menace            CHAR(3)      NOT NULL,
    CONSTRAINT PK_Espece  PRIMARY KEY (Nom_espece)
);

CREATE TABLE Animal (
    Id_rfid         INT,
    Nom_animal      VARCHAR(30)  NOT NULL,
    Date_naissance  DATE,
    Poids           NUMBER,
    Id_rfid_fils    INT,
    Nom_espece      VARCHAR(30)  NOT NULL,
    CONSTRAINT PK_Animal        PRIMARY KEY (Id_rfid),
    CONSTRAINT FK_Animal_Espece FOREIGN KEY (Nom_espece)   REFERENCES Espece(Nom_espece),
    CONSTRAINT FK_Animal_fils   FOREIGN KEY (Id_rfid_fils) REFERENCES Animal(Id_rfid)
);

CREATE TABLE Zone (
    Nom_zone  VARCHAR(30),
    CONSTRAINT PK_Zone  PRIMARY KEY (Nom_zone)
);

CREATE TABLE Enclos (
    Id_enclos  INT,
    Longitude  NUMBER       NOT NULL,
    Latitude   NUMBER       NOT NULL,
    Nom_zone   VARCHAR(30)  NOT NULL,
    CONSTRAINT PK_Enclos      PRIMARY KEY (Id_enclos),
    CONSTRAINT FK_Enclos_Zone FOREIGN KEY (Nom_zone) REFERENCES Zone(Nom_zone)
);

CREATE TABLE Particularite (
    Type_particularite  VARCHAR(30),
    CONSTRAINT PK_Particularite  PRIMARY KEY (Type_particularite)
);

CREATE TABLE Boutique (
    Id_boutique         INT,
    Id_gerant_boutique  INT  NOT NULL,
    CONSTRAINT PK_Boutique            PRIMARY KEY (Id_boutique),
    CONSTRAINT FK_Boutique_Personnel  FOREIGN KEY (Id_gerant_boutique) REFERENCES Personnel(Id_Personnel)
);

CREATE TABLE CA_journalier (
    Id_ca        INT,
    Montant      NUMBER  NOT NULL,
    Date_ca      DATE    NOT NULL,
    Id_boutique  INT     NOT NULL,
    CONSTRAINT PK_CA           PRIMARY KEY (Id_ca),
    CONSTRAINT FK_CA_Boutique  FOREIGN KEY (Id_boutique) REFERENCES Boutique(Id_boutique)
);

CREATE TABLE Prestataire (
    Id_prestataire   INT,
    Nom_prestataire  VARCHAR(30)  NOT NULL,
    CONSTRAINT PK_Prestataire  PRIMARY KEY (Id_prestataire)
);

CREATE TABLE Parrain (
    Id_parrain              INT,
    Num_telephone_visiteur  CHAR(10)  NOT NULL,
    CONSTRAINT PK_Parrain  PRIMARY KEY (Id_parrain)
);

CREATE TABLE habite (
    Id_rfid    INT  NOT NULL,
    Id_enclos  INT  NOT NULL,
    CONSTRAINT PK_habite        PRIMARY KEY (Id_rfid, Id_enclos),
    CONSTRAINT FK_habite_Animal FOREIGN KEY (Id_rfid)   REFERENCES Animal(Id_rfid),
    CONSTRAINT FK_habite_Enclos FOREIGN KEY (Id_enclos) REFERENCES Enclos(Id_enclos)
);

CREATE TABLE peuvent_cohabiter (
    Nom_espece1  VARCHAR(30)  NOT NULL,
    Nom_espece2  VARCHAR(30)  NOT NULL,
    CONSTRAINT PK_peuvent_cohabiter  PRIMARY KEY (Nom_espece1, Nom_espece2),
    CONSTRAINT FK_cohabiter_espece1  FOREIGN KEY (Nom_espece1) REFERENCES Espece(Nom_espece),
    CONSTRAINT FK_cohabiter_espece2  FOREIGN KEY (Nom_espece2) REFERENCES Espece(Nom_espece)
);

CREATE TABLE mange (
    Id_rfid         INT          NOT NULL,
    Nom_nourriture  VARCHAR(30)  NOT NULL,
    CONSTRAINT PK_mange            PRIMARY KEY (Id_rfid, Nom_nourriture),
    CONSTRAINT FK_mange_Animal     FOREIGN KEY (Id_rfid)        REFERENCES Animal(Id_rfid),
    CONSTRAINT FK_mange_Nourriture FOREIGN KEY (Nom_nourriture) REFERENCES Nourriture(Nom_nourriture)
);

CREATE TABLE consommer (
    Id_rfid        INT  NOT NULL,
    Id_medication  INT  NOT NULL,
    CONSTRAINT PK_consommer             PRIMARY KEY (Id_rfid, Id_medication),
    CONSTRAINT FK_consommer_Animal      FOREIGN KEY (Id_rfid)       REFERENCES Animal(Id_rfid),
    CONSTRAINT FK_consommer_Medicament  FOREIGN KEY (Id_medication) REFERENCES Medicament(Id_medication)
);

CREATE TABLE confere (
    Id_Personnel    INT          NOT NULL,
    Nom_nourriture  VARCHAR(30)  NOT NULL,
    CONSTRAINT PK_confere            PRIMARY KEY (Id_Personnel, Nom_nourriture),
    CONSTRAINT FK_confere_Personnel  FOREIGN KEY (Id_Personnel)   REFERENCES Personnel(Id_Personnel),
    CONSTRAINT FK_confere_Nourriture FOREIGN KEY (Nom_nourriture) REFERENCES Nourriture(Nom_nourriture)
);

CREATE TABLE utilise (
    Id_Personnel   INT  NOT NULL,
    Id_specialite  INT  NOT NULL,
    Id_medication  INT  NOT NULL,
    CONSTRAINT PK_utilise             PRIMARY KEY (Id_Personnel, Id_specialite, Id_medication),
    CONSTRAINT FK_utilise_Personnel   FOREIGN KEY (Id_Personnel)  REFERENCES Personnel(Id_Personnel),
    CONSTRAINT FK_utilise_Specialite  FOREIGN KEY (Id_specialite) REFERENCES Specialite(Id_specialite),
    CONSTRAINT FK_utilise_Medicament  FOREIGN KEY (Id_medication) REFERENCES Medicament(Id_medication)
);

CREATE TABLE travaille (
    Id_Personnel  INT  NOT NULL,
    Id_boutique   INT  NOT NULL,
    CONSTRAINT PK_travaille           PRIMARY KEY (Id_Personnel, Id_boutique),
    CONSTRAINT FK_travaille_Personnel FOREIGN KEY (Id_Personnel) REFERENCES Personnel(Id_Personnel),
    CONSTRAINT FK_travaille_Boutique  FOREIGN KEY (Id_boutique)  REFERENCES Boutique(Id_boutique)
);

CREATE TABLE affecte (
    Id_Personnel  INT          NOT NULL,
    Nom_zone      VARCHAR(30)  NOT NULL,
    CONSTRAINT PK_affecte           PRIMARY KEY (Id_Personnel, Nom_zone),
    CONSTRAINT FK_affecte_Personnel FOREIGN KEY (Id_Personnel) REFERENCES Personnel(Id_Personnel),
    CONSTRAINT FK_affecte_Zone      FOREIGN KEY (Nom_zone)     REFERENCES Zone(Nom_zone)
);

CREATE TABLE intervient (
    Id_Personnel  INT          NOT NULL,
    Id_enclos     INT          NOT NULL,
    Date_interv   DATE         NOT NULL,
    Nature        VARCHAR(50),
    CONSTRAINT PK_intervient           PRIMARY KEY (Id_Personnel, Id_enclos, Date_interv),
    CONSTRAINT FK_intervient_Personnel FOREIGN KEY (Id_Personnel) REFERENCES Personnel(Id_Personnel),
    CONSTRAINT FK_intervient_Enclos    FOREIGN KEY (Id_enclos)    REFERENCES Enclos(Id_enclos)
);

CREATE TABLE intervention_prestataire (
    Id_intervention_prestataire  INT,
    Id_prestataire               INT   NOT NULL,
    Id_enclos                    INT   NOT NULL,
    Date_prestataire             DATE  NOT NULL,
    CONSTRAINT PK_Interv_Prest        PRIMARY KEY (Id_intervention_prestataire),
    CONSTRAINT FK_Interv_Prestataire  FOREIGN KEY (Id_prestataire) REFERENCES Prestataire(Id_prestataire),
    CONSTRAINT FK_Interv_Enclos       FOREIGN KEY (Id_enclos)      REFERENCES Enclos(Id_enclos)
);

CREATE TABLE possede (
    Id_enclos           INT          NOT NULL,
    Type_particularite  VARCHAR(30)  NOT NULL,
    CONSTRAINT PK_possede                PRIMARY KEY (Id_enclos, Type_particularite),
    CONSTRAINT FK_possede_Enclos         FOREIGN KEY (Id_enclos)          REFERENCES Enclos(Id_enclos),
    CONSTRAINT FK_possede_Particularite  FOREIGN KEY (Type_particularite) REFERENCES Particularite(Type_particularite)
);

CREATE TABLE EstDans (
    Nom_zone     VARCHAR(30)  NOT NULL,
    Id_boutique  INT          NOT NULL,
    CONSTRAINT PK_EstDans          PRIMARY KEY (Nom_zone, Id_boutique),
    CONSTRAINT FK_EstDans_Zone     FOREIGN KEY (Nom_zone)    REFERENCES Zone(Nom_zone),
    CONSTRAINT FK_EstDans_Boutique FOREIGN KEY (Id_boutique) REFERENCES Boutique(Id_boutique)
);

CREATE TABLE parrainer (
    Id_parrainage     INT,
    Id_parrain        INT  NOT NULL,
    Id_rfid           INT  NOT NULL,
    Niv_contribution  INT  NOT NULL,
    CONSTRAINT PK_Parrainer         PRIMARY KEY (Id_parrainage),
    CONSTRAINT FK_Parrainer_Parrain FOREIGN KEY (Id_parrain) REFERENCES Parrain(Id_parrain),
    CONSTRAINT FK_Parrainer_Animal  FOREIGN KEY (Id_rfid)    REFERENCES Animal(Id_rfid)
);

CREATE TABLE se_situe(
    Id_enclos INT,
    Nom_zone varchar(30) ,
    CONSTRAINT pk_situe PRIMARY KEY (Id_enclos ,Nom_zone),
    CONSTRAINT FK_situe_enclos FOREIGN KEY (id_enclos) REFERENCES enclos(id_enclos),
    CONSTRAINT FK_situe_zone  FOREIGN KEY (nom_zone)    REFERENCES zone(nom_zone)
);

--  Regime_alimentaire 
INSERT INTO Regime_alimentaire VALUES ('Carnivore');
INSERT INTO Regime_alimentaire VALUES ('Herbivore');
INSERT INTO Regime_alimentaire VALUES ('Omnivore');
INSERT INTO Regime_alimentaire VALUES ('Piscivore');

--  Specialite 
INSERT INTO Specialite VALUES (1, 'Veterinaire','Non');
INSERT INTO Specialite VALUES (2, 'Physiothérapeute','Non');
INSERT INTO Specialite VALUES (3, 'Cardiologue','Non');
INSERT INTO Specialite VALUES (4, 'Chirurgien',  'Non');
INSERT INTO Specialite VALUES (5, 'infectiologue','Oui');

--personnel
INSERT INTO  personnel VALUES ( 1, 'Djeatsa El brrah','Mohamed','gerant','$2y$10$FV.DA4E3KWNj0JGj1MzJvuf48Xu1Dr9gLBH8VPr2ThDRLqpq37ddS', 3200, NULL); 
INSERT INTO  personnel VALUES ( 2, 'Likop','Nouh','gerant','$2y$10$pgzYwsT56Lb/ZHY8RC7BeunjJtsiGpLxQRd5sjkLwCzC6WHXNlqXW', 2800, 1);
INSERT INTO  personnel VALUES ( 3, 'Rakoto','Toky','gerant','$2y$10$pgzYwsT56Lb/ZHY8RC7BeunjJtsiGpLxQRd5sjkLwCzC6WHXNlqXW', 2600, 1);
INSERT INTO  personnel VALUES ( 4, 'Hakimi','Achraf','gerant','$2y$10$pgzYwsT56Lb/ZHY8RC7BeunjJtsiGpLxQRd5sjkLwCzC6WHXNlqXW', 2500, 1);
INSERT INTO  personnel VALUES ( 5, 'Corre','Jean','soignant','$2y$10$pgzYwsT56Lb/ZHY8RC7BeunjJtsiGpLxQRd5sjkLwCzC6WHXNlqXW', 2500, 2);
INSERT INTO  personnel VALUES ( 6, 'Petit','Sophie','soignant','$2y$10$pgzYwsT56Lb/ZHY8RC7BeunjJtsiGpLxQRd5sjkLwCzC6WHXNlqXW', 2100, 2);
INSERT INTO  personnel VALUES ( 7, 'Beauvisage','Manon','soignant','$2y$10$pgzYwsT56Lb/ZHY8RC7BeunjJtsiGpLxQRd5sjkLwCzC6WHXNlqXW', 2100, 2);
INSERT INTO  personnel VALUES ( 8, 'Pattinson','Robert','soignant','$2y$10$pgzYwsT56Lb/ZHY8RC7BeunjJtsiGpLxQRd5sjkLwCzC6WHXNlqXW', 2000, 3);
INSERT INTO  personnel VALUES ( 9, 'Margot','Robie','soignant','$2y$10$pgzYwsT56Lb/ZHY8RC7BeunjJtsiGpLxQRd5sjkLwCzC6WHXNlqXW', 2000, 3);
INSERT INTO  personnel VALUES (10, 'Velirinho','Enzo','soignant','$2y$10$pgzYwsT56Lb/ZHY8RC7BeunjJtsiGpLxQRd5sjkLwCzC6WHXNlqXW', 2000, 3);
INSERT INTO  personnel VALUES (11, 'Plus','Rien','agent entretient enclos','$2y$10$pgzYwsT56Lb/ZHY8RC7BeunjJtsiGpLxQRd5sjkLwCzC6WHXNlqXW', 1950, 4);
INSERT INTO  personnel VALUES (12, 'moins','Isabelle','agent entretient enclos','$2y$10$pgzYwsT56Lb/ZHY8RC7BeunjJtsiGpLxQRd5sjkLwCzC6WHXNlqXW', 1950, 4);
INSERT INTO  personnel VALUES (13, 'Fois','Melanie','agent entretient enclos','$2y$10$pgzYwsT56Lb/ZHY8RC7BeunjJtsiGpLxQRd5sjkLwCzC6WHXNlqXW', 1900, 4);
INSERT INTO  personnel VALUES (14, 'Maths','Nathalie','agent entretient enclos','$2y$10$pgzYwsT56Lb/ZHY8RC7BeunjJtsiGpLxQRd5sjkLwCzC6WHXNlqXW', 1900, 5);
INSERT INTO  personnel VALUES (15, 'Blanc','Christophe','agent entretient enclos','$2y$10$pgzYwsT56Lb/ZHY8RC7BeunjJtsiGpLxQRd5sjkLwCzC6WHXNlqXW', 1900, 5);
INSERT INTO  personnel VALUES (16, 'Noire','Valerie','agent entretient enclos','$2y$10$pgzYwsT56Lb/ZHY8RC7BeunjJtsiGpLxQRd5sjkLwCzC6WHXNlqXW', 1850, 5);
INSERT INTO  personnel VALUES (17, 'Chevalier','Frederic','gerant boutique','$2y$10$pgzYwsT56Lb/ZHY8RC7BeunjJtsiGpLxQRd5sjkLwCzC6WHXNlqXW', 1850, 6);
INSERT INTO  personnel VALUES (18, 'Leclerc','Sandrine','gerant boutique','$2y$10$pgzYwsT56Lb/ZHY8RC7BeunjJtsiGpLxQRd5sjkLwCzC6WHXNlqXW', 1850, 6);
INSERT INTO  personnel VALUES (19, 'Hamilton','Olivier','gerant boutique','$2y$10$pgzYwsT56Lb/ZHY8RC7BeunjJtsiGpLxQRd5sjkLwCzC6WHXNlqXW', 1800, 6);
INSERT INTO  personnel VALUES (20, 'Kimi','Laurence','gerant boutique','$2y$10$pgzYwsT56Lb/ZHY8RC7BeunjJtsiGpLxQRd5sjkLwCzC6WHXNlqXW', 1800, 7);
INSERT INTO  personnel VALUES (21, 'Vincent','Stephane','gerant boutique','$2y$10$pgzYwsT56Lb/ZHY8RC7BeunjJtsiGpLxQRd5sjkLwCzC6WHXNlqXW', 1800, 7);
INSERT INTO  personnel VALUES (22, 'Muller','Caroline','gerant boutique','$2y$10$pgzYwsT56Lb/ZHY8RC7BeunjJtsiGpLxQRd5sjkLwCzC6WHXNlqXW', 1750, 7);
INSERT INTO  personnel VALUES (23, 'Laaraj','Moussa','comptable','$2y$10$pgzYwsT56Lb/ZHY8RC7BeunjJtsiGpLxQRd5sjkLwCzC6WHXNlqXW', 1750, 8);
INSERT INTO  personnel VALUES (24, 'Maktoub','Jalil','comptable','$2y$10$pgzYwsT56Lb/ZHY8RC7BeunjJtsiGpLxQRd5sjkLwCzC6WHXNlqXW', 1750, 8); 
INSERT INTO  personnel VALUES (25, 'Fester','Jhonny','comptable','$2y$10$pgzYwsT56Lb/ZHY8RC7BeunjJtsiGpLxQRd5sjkLwCzC6WHXNlqXW', 1700, 8); 
INSERT INTO  personnel VALUES (26, 'Abracadabra','Patricia','comptable','$2y$10$pgzYwsT56Lb/ZHY8RC7BeunjJtsiGpLxQRd5sjkLwCzC6WHXNlqXW', 1700, 9);
INSERT INTO  personnel VALUES (27, 'Bradabra','Mickael','soignant','$2y$10$pgzYwsT56Lb/ZHY8RC7BeunjJtsiGpLxQRd5sjkLwCzC6WHXNlqXW', 1700, 9);
INSERT INTO  personnel VALUES (28, 'Verstapen','Max','soignant','$2y$10$pgzYwsT56Lb/ZHY8RC7BeunjJtsiGpLxQRd5sjkLwCzC6WHXNlqXW', 1650, 9); 
INSERT INTO  personnel VALUES (29, 'Renard','Damien','soignant','$2y$10$pgzYwsT56Lb/ZHY8RC7BeunjJtsiGpLxQRd5sjkLwCzC6WHXNlqXW', 1650, 10);
INSERT INTO  personnel VALUES (30, 'Norris','Helene','agent entretient enclos','$2y$10$pgzYwsT56Lb/ZHY8RC7BeunjJtsiGpLxQRd5sjkLwCzC6WHXNlqXW', 1650, 10);
INSERT INTO  personnel VALUES (31, 'Hadjar','Ilyess','agent entretient enclos','$2y$10$pgzYwsT56Lb/ZHY8RC7BeunjJtsiGpLxQRd5sjkLwCzC6WHXNlqXW', 1600, 10);
INSERT INTO  personnel VALUES (32, 'Arnaud','Florence','agent entretient enclos','$2y$10$pgzYwsT56Lb/ZHY8RC7BeunjJtsiGpLxQRd5sjkLwCzC6WHXNlqXW', 1600, 11); 
INSERT INTO  personnel VALUES (33, 'Picard','Sebastien','soignant','$2y$10$pgzYwsT56Lb/ZHY8RC7BeunjJtsiGpLxQRd5sjkLwCzC6WHXNlqXW', 1600, 11); 
INSERT INTO  personnel VALUES (34, 'Lucas','Melanie','soignant','$2y$10$pgzYwsT56Lb/ZHY8RC7BeunjJtsiGpLxQRd5sjkLwCzC6WHXNlqXW', 1580, 11); 
INSERT INTO  personnel VALUES (35, 'Noel','Benjamin','soignant','$2y$10$pgzYwsT56Lb/ZHY8RC7BeunjJtsiGpLxQRd5sjkLwCzC6WHXNlqXW', 1580, 12);
INSERT INTO  personnel VALUES (36, 'Adam','Juliette','gerant boutique','$2y$10$pgzYwsT56Lb/ZHY8RC7BeunjJtsiGpLxQRd5sjkLwCzC6WHXNlqXW', 1580, 12);
INSERT INTO  personnel VALUES (37, 'Philippe','Romain','gerant boutique','$2y$10$pgzYwsT56Lb/ZHY8RC7BeunjJtsiGpLxQRd5sjkLwCzC6WHXNlqXW', 1550, 12);
INSERT INTO  personnel VALUES (38, 'Colin','Amandine','comptable','$2y$10$pgzYwsT56Lb/ZHY8RC7BeunjJtsiGpLxQRd5sjkLwCzC6WHXNlqXW', 1550, 13);
INSERT INTO  personnel VALUES (39, 'Henry','Matthieu','agent entretient enclos','$2y$10$pgzYwsT56Lb/ZHY8RC7BeunjJtsiGpLxQRd5sjkLwCzC6WHXNlqXW', 1550, 13);
INSERT INTO  personnel VALUES (40, 'Rousseau','Camille','soignant','$2y$10$pgzYwsT56Lb/ZHY8RC7BeunjJtsiGpLxQRd5sjkLwCzC6WHXNlqXW', 1500, 13);

--  Contrat 
INSERT INTO contrat VALUES ( 1, TO_DATE('2018-01-10','YYYY-MM-DD'), 'CDI', 1);
INSERT INTO contrat VALUES ( 2, TO_DATE('2019-03-15','YYYY-MM-DD'), 'CDI', 2);
INSERT INTO contrat VALUES ( 3, TO_DATE('2020-06-01','YYYY-MM-DD'), 'CDI', 3);
INSERT INTO contrat VALUES ( 4, TO_DATE('2021-02-20','YYYY-MM-DD'), 'CDI', 4);
INSERT INTO contrat VALUES ( 5, TO_DATE('2021-09-01','YYYY-MM-DD'), 'CDI', 5);
INSERT INTO contrat VALUES ( 6, TO_DATE('2022-01-15','YYYY-MM-DD'), 'CDD', 6);
INSERT INTO contrat VALUES ( 7, TO_DATE('2022-01-15','YYYY-MM-DD'), 'CDD', 7);
INSERT INTO contrat VALUES ( 8, TO_DATE('2022-04-01','YYYY-MM-DD'), 'CDI', 8);
INSERT INTO contrat VALUES ( 9, TO_DATE('2022-04-01','YYYY-MM-DD'), 'CDI', 9);
INSERT INTO contrat VALUES (10, TO_DATE('2022-07-01','YYYY-MM-DD'), 'CDI', 10);
INSERT INTO contrat VALUES (11, TO_DATE('2022-09-01','YYYY-MM-DD'), 'CDD', 11);
INSERT INTO contrat VALUES (12, TO_DATE('2022-09-01','YYYY-MM-DD'), 'CDD', 12);
INSERT INTO contrat VALUES (13, TO_DATE('2023-01-10','YYYY-MM-DD'), 'CDD', 13);
INSERT INTO contrat VALUES (14, TO_DATE('2023-01-10','YYYY-MM-DD'), 'CDD', 14);
INSERT INTO contrat VALUES (15, TO_DATE('2023-03-01','YYYY-MM-DD'), 'CDI', 15);
INSERT INTO contrat VALUES (16, TO_DATE('2023-03-01','YYYY-MM-DD'), 'CDD', 16);
INSERT INTO contrat VALUES (17, TO_DATE('2023-05-15','YYYY-MM-DD'), 'CDD', 17);
INSERT INTO contrat VALUES (18, TO_DATE('2023-05-15','YYYY-MM-DD'), 'CDD', 18);
INSERT INTO contrat VALUES (19, TO_DATE('2023-06-01','YYYY-MM-DD'), 'CDI', 19);
INSERT INTO contrat VALUES (20, TO_DATE('2023-06-01','YYYY-MM-DD'), 'CDD', 20);
INSERT INTO contrat VALUES (21, TO_DATE('2023-07-01','YYYY-MM-DD'), 'CDD', 21);
INSERT INTO contrat VALUES (22, TO_DATE('2023-07-01','YYYY-MM-DD'), 'CDD', 22);
INSERT INTO contrat VALUES (23, TO_DATE('2023-09-01','YYYY-MM-DD'), 'CDI', 23);
INSERT INTO contrat VALUES (24, TO_DATE('2023-09-01','YYYY-MM-DD'), 'CDD', 24);
INSERT INTO contrat VALUES (25, TO_DATE('2023-10-01','YYYY-MM-DD'), 'CDD', 25);
INSERT INTO contrat VALUES (26, TO_DATE('2024-01-15','YYYY-MM-DD'), 'CDD', 26);
INSERT INTO contrat VALUES (27, TO_DATE('2024-01-15','YYYY-MM-DD'), 'CDD', 27);
INSERT INTO contrat VALUES (28, TO_DATE('2024-02-01','YYYY-MM-DD'), 'CDD', 28);
INSERT INTO contrat VALUES (29, TO_DATE('2024-03-01','YYYY-MM-DD'), 'CDI', 29);
INSERT INTO contrat VALUES (30, TO_DATE('2024-03-01','YYYY-MM-DD'), 'CDD', 30);
INSERT INTO contrat VALUES (31, TO_DATE('2024-04-01','YYYY-MM-DD'), 'CDD', 31);
INSERT INTO contrat VALUES (32, TO_DATE('2024-04-01','YYYY-MM-DD'), 'CDD', 32);
INSERT INTO contrat VALUES (33, TO_DATE('2024-05-15','YYYY-MM-DD'), 'CDD', 33);
INSERT INTO contrat VALUES (34, TO_DATE('2024-06-01','YYYY-MM-DD'), 'CDD', 34);
INSERT INTO contrat VALUES (35, TO_DATE('2024-06-01','YYYY-MM-DD'), 'CDD', 35);
INSERT INTO contrat VALUES (36, TO_DATE('2024-07-01','YYYY-MM-DD'), 'CDD', 36);
INSERT INTO contrat VALUES (37, TO_DATE('2024-08-01','YYYY-MM-DD'), 'CDD', 37);
INSERT INTO contrat VALUES (38, TO_DATE('2024-09-01','YYYY-MM-DD'), 'CDD', 38);
INSERT INTO contrat VALUES (39, TO_DATE('2025-01-10','YYYY-MM-DD'), 'CDD', 39);
INSERT INTO contrat VALUES (40, TO_DATE('2025-03-01','YYYY-MM-DD'), 'CDD', 40);

--  Espece 
INSERT INTO Espece VALUES ('Lion',        'Panthera leo',              'Non');
INSERT INTO Espece VALUES ('Tigre',       'Panthera tigris',           'Oui');
INSERT INTO Espece VALUES ('Girafe',      'Giraffa camelopardalis',    'Non');
INSERT INTO Espece VALUES ('Zebre',       'Equus quagga',              'Non');
INSERT INTO Espece VALUES ('Elephant',    'Loxodonta africana',        'Oui');
INSERT INTO Espece VALUES ('Chimpanze',   'Pan troglodytes',           'Oui');
INSERT INTO Espece VALUES ('Gorille',     'Gorilla beringei',          'Oui');
INSERT INTO Espece VALUES ('Rhinoceros',  'Ceratotherium simum',       'Oui');
INSERT INTO Espece VALUES ('Hippopotame', 'Hippopotamus amphibius',    'Oui');
INSERT INTO Espece VALUES ('Leopard',     'Panthera pardus',           'Non');
INSERT INTO Espece VALUES ('Flamant',     'Phoenicopterus roseus',     'Non');
INSERT INTO Espece VALUES ('Manchot',     'Spheniscus demersus',       'Oui');

--  Medicament 
INSERT INTO Medicament VALUES ( 1, 'Antibiotique',       50);
INSERT INTO Medicament VALUES ( 2, 'Antiparasitaire',    30);
INSERT INTO Medicament VALUES ( 3, 'Vitamines',          10);
INSERT INTO Medicament VALUES ( 4, 'Anti-inflammatoire', 20);
INSERT INTO Medicament VALUES ( 5, 'Vermifuge',          15);
INSERT INTO Medicament VALUES ( 6, 'Vaccin grippe',       5);
INSERT INTO Medicament VALUES ( 7, 'Analgesique',        25);
INSERT INTO Medicament VALUES ( 8, 'Antifongique',       18);

--  Nourriture 
INSERT INTO  nourriture VALUES ('Viande bovine',5.0, TO_DATE('2026-06-01','YYYY-MM-DD'), 'Carnivore');
INSERT INTO  nourriture VALUES ('Poulet frais',     3.0, TO_DATE('2026-05-15','YYYY-MM-DD'), 'Carnivore');
INSERT INTO  nourriture VALUES ('Poisson entier',   4.0, TO_DATE('2026-04-20','YYYY-MM-DD'), 'Piscivore');
INSERT INTO  nourriture VALUES ('Crevettes',        1.5, TO_DATE('2026-04-10','YYYY-MM-DD'), 'Piscivore');
INSERT INTO  nourriture VALUES ('Foin premium',    10.0, TO_DATE('2026-12-01','YYYY-MM-DD'), 'Herbivore');
INSERT INTO  nourriture VALUES ('Herbe fraiche',    8.0, TO_DATE('2026-04-30','YYYY-MM-DD'), 'Herbivore');
INSERT INTO  nourriture VALUES ('Fruits melanges',  2.0, TO_DATE('2026-04-15','YYYY-MM-DD'), 'Omnivore');
INSERT INTO  nourriture VALUES ('Legumes varies',   3.0, TO_DATE('2026-04-25','YYYY-MM-DD'), 'Omnivore');
INSERT INTO  nourriture VALUES ('Crevettes roses',  1.0, TO_DATE('2026-04-05','YYYY-MM-DD'), 'Piscivore');
INSERT INTO  nourriture VALUES ('Feuilles acacia',  6.0, TO_DATE('2026-05-01','YYYY-MM-DD'), 'Herbivore');

--  Zone 
INSERT INTO Zone VALUES ('Savane');
INSERT INTO Zone VALUES ('Foret tropicale');
INSERT INTO Zone VALUES ('Plaine africaine');
INSERT INTO Zone VALUES ('Jungle');
INSERT INTO Zone VALUES ('Zone aquatique');
INSERT INTO Zone VALUES ('Montagne');

--  Enclos (20) 
INSERT INTO Enclos VALUES (101, 2.3200, 48.8600, 'Savane');
INSERT INTO Enclos VALUES (102, 2.3210, 48.8610, 'Savane');
INSERT INTO Enclos VALUES (103, 2.3220, 48.8620, 'Savane');
INSERT INTO Enclos VALUES (104, 2.3250, 48.8650, 'Foret tropicale');
INSERT INTO Enclos VALUES (105, 2.3260, 48.8660, 'Foret tropicale');
INSERT INTO Enclos VALUES (106, 2.3270, 48.8670, 'Foret tropicale');
INSERT INTO Enclos VALUES (107, 2.3300, 48.8700, 'Plaine africaine');
INSERT INTO Enclos VALUES (108, 2.3310, 48.8710, 'Plaine africaine');
INSERT INTO Enclos VALUES (109, 2.3320, 48.8720, 'Plaine africaine');
INSERT INTO Enclos VALUES (110, 2.3350, 48.8750, 'Jungle');
INSERT INTO Enclos VALUES (111, 2.3360, 48.8760, 'Jungle');
INSERT INTO Enclos VALUES (112, 2.3370, 48.8770, 'Jungle');
INSERT INTO Enclos VALUES (113, 2.3400, 48.8800, 'Zone aquatique');
INSERT INTO Enclos VALUES (114, 2.3410, 48.8810, 'Zone aquatique');
INSERT INTO Enclos VALUES (115, 2.3420, 48.8820, 'Zone aquatique');
INSERT INTO Enclos VALUES (116, 2.3450, 48.8850, 'Montagne');
INSERT INTO Enclos VALUES (117, 2.3460, 48.8860, 'Montagne');
INSERT INTO Enclos VALUES (118, 2.3470, 48.8870, 'Savane');
INSERT INTO Enclos VALUES (119, 2.3480, 48.8880, 'Plaine africaine');
INSERT INTO Enclos VALUES (120, 2.3490, 48.8890, 'Foret tropicale');

--  Particularite 
INSERT INTO Particularite VALUES ('Fosse a eau');
INSERT INTO Particularite VALUES ('Arbre grimpable');
INSERT INTO Particularite VALUES ('Abri climatise');
INSERT INTO Particularite VALUES ('Rochers artificiels');
INSERT INTO Particularite VALUES ('Mare naturelle');
INSERT INTO Particularite VALUES ('Vegetation dense');
INSERT INTO Particularite VALUES ('Tunnel souterrain');
INSERT INTO Particularite VALUES ('Herbe fraiche');

--  Prestataire 
INSERT INTO Prestataire VALUES (1, 'VetExterne SARL');
INSERT INTO Prestataire VALUES (2, 'NettoyagePro');
INSERT INTO Prestataire VALUES (3, 'BioFaune Conseil');
INSERT INTO Prestataire VALUES (4, 'AquaZoo Services');
INSERT INTO Prestataire VALUES (5, 'FloraZoo SARL');

--  Parrain 
INSERT INTO Parrain VALUES ( 1, '0612345678');
INSERT INTO Parrain VALUES ( 2, '0698765432');
INSERT INTO Parrain VALUES ( 3, '0623456789');
INSERT INTO Parrain VALUES ( 4, '0634567890');
INSERT INTO Parrain VALUES ( 5, '0645678901');
INSERT INTO Parrain VALUES ( 6, '0656789012');
INSERT INTO Parrain VALUES ( 7, '0667890123');
INSERT INTO Parrain VALUES ( 8, '0678901234');
INSERT INTO Parrain VALUES ( 9, '0689012345');
INSERT INTO Parrain VALUES (10, '0690123456');

--  Boutique (8) 
-- gerants : personnels 1 a 4
INSERT INTO Boutique VALUES (1, 1);
INSERT INTO Boutique VALUES (2, 2);
INSERT INTO Boutique VALUES (3, 3);
INSERT INTO Boutique VALUES (4, 4);
INSERT INTO Boutique VALUES (5, 1);
INSERT INTO Boutique VALUES (6, 2);
INSERT INTO Boutique VALUES (7, 3);
INSERT INTO Boutique VALUES (8, 4);

--  CA_journalier 
INSERT INTO ca_journalier VALUES ( 1,  1250.50, TO_DATE('2026-03-20','YYYY-MM-DD'), 1);
INSERT INTO ca_journalier VALUES ( 2,   870.00, TO_DATE('2026-03-21','YYYY-MM-DD'), 1);
INSERT INTO ca_journalier VALUES ( 3,   540.75, TO_DATE('2026-03-20','YYYY-MM-DD'), 2);
INSERT INTO ca_journalier VALUES ( 4,   980.00, TO_DATE('2026-03-21','YYYY-MM-DD'), 2);
INSERT INTO ca_journalier VALUES ( 5,  1100.25, TO_DATE('2026-03-20','YYYY-MM-DD'), 3);
INSERT INTO ca_journalier VALUES ( 6,   760.50, TO_DATE('2026-03-21','YYYY-MM-DD'), 3);
INSERT INTO ca_journalier VALUES ( 7,  1380.00, TO_DATE('2026-03-20','YYYY-MM-DD'), 4);
INSERT INTO ca_journalier VALUES ( 8,   620.75, TO_DATE('2026-03-21','YYYY-MM-DD'), 4);
INSERT INTO ca_journalier VALUES ( 9,   890.00, TO_DATE('2026-03-20','YYYY-MM-DD'), 5);
INSERT INTO ca_journalier VALUES (10,  1050.25, TO_DATE('2026-03-21','YYYY-MM-DD'), 5);
INSERT INTO ca_journalier VALUES (11,   730.50, TO_DATE('2026-03-20','YYYY-MM-DD'), 6);
INSERT INTO ca_journalier VALUES (12,  1200.00, TO_DATE('2026-03-21','YYYY-MM-DD'), 6);
INSERT INTO ca_journalier VALUES (13,   460.75, TO_DATE('2026-03-20','YYYY-MM-DD'), 7);
INSERT INTO ca_journalier VALUES (14,   810.00, TO_DATE('2026-03-21','YYYY-MM-DD'), 7);
INSERT INTO ca_journalier VALUES (15,  1560.50, TO_DATE('2026-03-20','YYYY-MM-DD'), 8);
INSERT INTO ca_journalier VALUES (16,   940.25, TO_DATE('2026-03-21','YYYY-MM-DD'), 8);

--  Animal (70) 
-- Id_rfid, Nom, Date_naissance, Poids, Id_rfid_fils, Nom_espece
-- Lions (8)
INSERT INTO animal VALUES (1001, 'Simba',     TO_DATE('2019-05-10','YYYY-MM-DD'), 190.0, NULL, 'Lion');
INSERT INTO animal VALUES (1002, 'Nala',      TO_DATE('2020-03-22','YYYY-MM-DD'), 135.0, NULL, 'Lion');
INSERT INTO animal VALUES (1003, 'Mufasa',    TO_DATE('2015-07-14','YYYY-MM-DD'), 210.0, 1001, 'Lion');
INSERT INTO animal VALUES (1004, 'Sarabi',    TO_DATE('2016-11-02','YYYY-MM-DD'), 140.0, NULL, 'Lion');
INSERT INTO animal VALUES (1005, 'Kopa',      TO_DATE('2022-01-18','YYYY-MM-DD'), 160.0, NULL, 'Lion');
INSERT INTO animal VALUES (1006, 'Kiara',     TO_DATE('2021-06-30','YYYY-MM-DD'), 128.0, NULL, 'Lion');
INSERT INTO animal VALUES (1007, 'Kovu',      TO_DATE('2023-03-05','YYYY-MM-DD'), 145.0, NULL, 'Lion');
INSERT INTO animal VALUES (1008, 'Vitani',    TO_DATE('2022-08-19','YYYY-MM-DD'), 122.0, NULL, 'Lion');
INSERT INTO animal VALUES (1009, 'Raja',      TO_DATE('2018-04-12','YYYY-MM-DD'), 220.0, NULL, 'Tigre');
INSERT INTO animal VALUES (1010, 'Shiva',     TO_DATE('2019-09-25','YYYY-MM-DD'), 185.0, NULL, 'Tigre');
INSERT INTO animal VALUES (1011, 'Akbar',     TO_DATE('2020-12-07','YYYY-MM-DD'), 230.0, NULL, 'Tigre');
INSERT INTO animal VALUES (1012, 'Priya',     TO_DATE('2021-02-14','YYYY-MM-DD'), 178.0, NULL, 'Tigre');
INSERT INTO animal VALUES (1013, 'Raju',      TO_DATE('2023-05-20','YYYY-MM-DD'), 195.0, NULL, 'Tigre');
INSERT INTO animal VALUES (1014, 'Meera',     TO_DATE('2022-10-03','YYYY-MM-DD'), 170.0, NULL, 'Tigre');
INSERT INTO animal VALUES (1015, 'Savannah',  TO_DATE('2017-07-15','YYYY-MM-DD'), 900.0, NULL, 'Girafe');
INSERT INTO animal VALUES (1016, 'Kili',      TO_DATE('2019-01-28','YYYY-MM-DD'), 820.0, NULL, 'Girafe');
INSERT INTO animal VALUES (1017, 'Twiga',     TO_DATE('2020-11-11','YYYY-MM-DD'), 780.0, 1015, 'Girafe');
INSERT INTO animal VALUES (1018, 'Jua',       TO_DATE('2021-04-22','YYYY-MM-DD'), 750.0, NULL, 'Girafe');
INSERT INTO animal VALUES (1019, 'Duma',      TO_DATE('2022-08-09','YYYY-MM-DD'), 690.0, NULL, 'Girafe');
INSERT INTO animal VALUES (1020, 'Bahari',    TO_DATE('2023-02-17','YYYY-MM-DD'), 650.0, NULL, 'Girafe');
INSERT INTO animal VALUES (1021, 'Stripes',   TO_DATE('2018-06-03','YYYY-MM-DD'), 320.0, NULL, 'Zebre');
INSERT INTO animal VALUES (1022, 'Dotty',     TO_DATE('2019-10-16','YYYY-MM-DD'), 295.0, NULL, 'Zebre');
INSERT INTO animal VALUES (1023, 'Zigzag',    TO_DATE('2020-05-29','YYYY-MM-DD'), 310.0, NULL, 'Zebre');
INSERT INTO animal VALUES (1024, 'Flash',     TO_DATE('2021-09-12','YYYY-MM-DD'), 280.0, NULL, 'Zebre');
INSERT INTO animal VALUES (1025, 'Dash',      TO_DATE('2022-03-25','YYYY-MM-DD'), 270.0, NULL, 'Zebre');
INSERT INTO animal VALUES (1026, 'Bolt',      TO_DATE('2023-07-08','YYYY-MM-DD'), 260.0, NULL, 'Zebre');
INSERT INTO animal VALUES (1027, 'Storm',     TO_DATE('2023-11-21','YYYY-MM-DD'), 250.0, NULL, 'Zebre');
INSERT INTO animal VALUES (1028, 'Tembo',     TO_DATE('2010-03-18','YYYY-MM-DD'), 5200.0, NULL, 'Elephant');
INSERT INTO animal VALUES (1029, 'Ndovu',     TO_DATE('2012-08-24','YYYY-MM-DD'), 4800.0, NULL, 'Elephant');
INSERT INTO animal VALUES (1030, 'Tantor',    TO_DATE('2015-01-05','YYYY-MM-DD'), 5500.0, 1028, 'Elephant');
INSERT INTO animal VALUES (1031, 'Ellie',     TO_DATE('2017-06-30','YYYY-MM-DD'), 4200.0, NULL, 'Elephant');
INSERT INTO animal VALUES (1032, 'Dumbo',     TO_DATE('2020-11-14','YYYY-MM-DD'), 3800.0, NULL, 'Elephant');
INSERT INTO animal VALUES (1033, 'Ivory',     TO_DATE('2022-04-02','YYYY-MM-DD'), 3500.0, NULL, 'Elephant');
INSERT INTO animal VALUES (1034, 'Tusk',      TO_DATE('2023-09-19','YYYY-MM-DD'), 3200.0, NULL, 'Elephant');
INSERT INTO animal VALUES (1035, 'Kesi',      TO_DATE('2016-02-11','YYYY-MM-DD'),   55.0, NULL, 'Chimpanze');
INSERT INTO animal VALUES (1036, 'Bobo',      TO_DATE('2018-07-23','YYYY-MM-DD'),   62.0, NULL, 'Chimpanze');
INSERT INTO animal VALUES (1037, 'Zuri',      TO_DATE('2019-12-04','YYYY-MM-DD'),   48.0, NULL, 'Chimpanze');
INSERT INTO animal VALUES (1038, 'Jomo',      TO_DATE('2021-05-17','YYYY-MM-DD'),   58.0, NULL, 'Chimpanze');
INSERT INTO animal VALUES (1039, 'Pili',      TO_DATE('2022-09-30','YYYY-MM-DD'),   45.0, NULL, 'Chimpanze');
INSERT INTO animal VALUES (1040, 'Safi',      TO_DATE('2023-03-13','YYYY-MM-DD'),   40.0, NULL, 'Chimpanze');
INSERT INTO animal VALUES (1041, 'Kondo',     TO_DATE('2012-05-06','YYYY-MM-DD'),  180.0, NULL, 'Gorille');
INSERT INTO animal VALUES (1042, 'Amara',     TO_DATE('2015-10-19','YYYY-MM-DD'),  120.0, NULL, 'Gorille');
INSERT INTO animal VALUES (1043, 'Jabari',    TO_DATE('2018-03-02','YYYY-MM-DD'),  165.0, NULL, 'Gorille');
INSERT INTO animal VALUES (1044, 'Nia',       TO_DATE('2020-08-15','YYYY-MM-DD'),  110.0, NULL, 'Gorille');
INSERT INTO animal VALUES (1045, 'Kofi',      TO_DATE('2022-01-28','YYYY-MM-DD'),   90.0, NULL, 'Gorille');
INSERT INTO animal VALUES (1046, 'Kisumu',    TO_DATE('2014-07-09','YYYY-MM-DD'), 2200.0, NULL, 'Rhinoceros');
INSERT INTO animal VALUES (1047, 'Bakari',    TO_DATE('2017-12-22','YYYY-MM-DD'), 2100.0, NULL, 'Rhinoceros');
INSERT INTO animal VALUES (1048, 'Horns',     TO_DATE('2019-04-05','YYYY-MM-DD'), 1950.0, NULL, 'Rhinoceros');
INSERT INTO animal VALUES (1049, 'Rocky',     TO_DATE('2021-09-18','YYYY-MM-DD'), 1800.0, NULL, 'Rhinoceros');
INSERT INTO animal VALUES (1050, 'Petra',     TO_DATE('2023-02-01','YYYY-MM-DD'), 1600.0, NULL, 'Rhinoceros');
INSERT INTO animal VALUES (1051, 'Kiboko',    TO_DATE('2013-06-14','YYYY-MM-DD'), 3000.0, NULL, 'Hippopotame');
INSERT INTO animal VALUES (1052, 'Maji',      TO_DATE('2016-11-27','YYYY-MM-DD'), 2800.0, NULL, 'Hippopotame');
INSERT INTO animal VALUES (1053, 'Paka',      TO_DATE('2019-05-10','YYYY-MM-DD'), 2500.0, NULL, 'Hippopotame');
INSERT INTO animal VALUES (1054, 'Tamu',      TO_DATE('2021-10-23','YYYY-MM-DD'), 2200.0, NULL, 'Hippopotame');
INSERT INTO animal VALUES (1055, 'Kito',      TO_DATE('2023-04-06','YYYY-MM-DD'), 1900.0, NULL, 'Hippopotame');
INSERT INTO animal VALUES (1056, 'Chui',      TO_DATE('2018-01-19','YYYY-MM-DD'),   75.0, NULL, 'Leopard');
INSERT INTO animal VALUES (1057, 'Doa',       TO_DATE('2019-06-02','YYYY-MM-DD'),   68.0, NULL, 'Leopard');
INSERT INTO animal VALUES (1058, 'Paka2',     TO_DATE('2021-11-15','YYYY-MM-DD'),   72.0, NULL, 'Leopard');
INSERT INTO animal VALUES (1059, 'Shadow',    TO_DATE('2022-04-28','YYYY-MM-DD'),   65.0, NULL, 'Leopard');
INSERT INTO animal VALUES (1060, 'Spot',      TO_DATE('2023-09-11','YYYY-MM-DD'),   58.0, NULL, 'Leopard');
INSERT INTO animal VALUES (1061, 'Rosa',      TO_DATE('2017-08-04','YYYY-MM-DD'),    2.8, NULL, 'Flamant');
INSERT INTO animal VALUES (1062, 'Blush',     TO_DATE('2018-12-17','YYYY-MM-DD'),    2.6, NULL, 'Flamant');
INSERT INTO animal VALUES (1063, 'Coral',     TO_DATE('2020-05-01','YYYY-MM-DD'),    2.9, NULL, 'Flamant');
INSERT INTO animal VALUES (1064, 'Blossom',   TO_DATE('2021-09-14','YYYY-MM-DD'),    2.7, NULL, 'Flamant');
INSERT INTO animal VALUES (1065, 'Petal',     TO_DATE('2023-02-27','YYYY-MM-DD'),    2.5, NULL, 'Flamant');
INSERT INTO animal VALUES (1066, 'Tux',       TO_DATE('2018-03-12','YYYY-MM-DD'),    3.5, NULL, 'Manchot');
INSERT INTO animal VALUES (1067, 'Waddle',    TO_DATE('2019-07-25','YYYY-MM-DD'),    3.8, NULL, 'Manchot');
INSERT INTO animal VALUES (1068, 'Skipper',   TO_DATE('2021-01-08','YYYY-MM-DD'),    3.6, NULL, 'Manchot');
INSERT INTO animal VALUES (1069, 'Kowalski',  TO_DATE('2022-05-21','YYYY-MM-DD'),    3.4, NULL, 'Manchot');
INSERT INTO animal VALUES (1070, 'Private',   TO_DATE('2023-10-04','YYYY-MM-DD'),    3.2, NULL, 'Manchot');
--  habite (Animal -> Enclos) 
-- Lions -> 101,102
INSERT INTO habite VALUES (1001, 101); 
INSERT INTO habite VALUES (1002, 101);
INSERT INTO habite VALUES (1003, 102); 
INSERT INTO habite VALUES (1004, 102);
INSERT INTO habite VALUES (1005, 101); 
INSERT INTO habite VALUES (1006, 101);
INSERT INTO habite VALUES (1007, 102); 
INSERT INTO habite VALUES (1008, 102);
-- Tigres -> 103,118
INSERT INTO habite VALUES (1009, 103);
 INSERT INTO habite VALUES (1010, 103);
INSERT INTO habite VALUES (1011, 118); 
INSERT INTO habite VALUES (1012, 118);
INSERT INTO habite VALUES (1013, 103); 
INSERT INTO habite VALUES (1014, 118);
-- Girafes -> 107,108
INSERT INTO habite VALUES (1015, 107); 
INSERT INTO habite VALUES (1016, 107);
INSERT INTO habite VALUES (1017, 108); 
INSERT INTO habite VALUES (1018, 108);
INSERT INTO habite VALUES (1019, 107); 
INSERT INTO habite VALUES (1020, 108);
-- Zebres -> 108,119
INSERT INTO habite VALUES (1021, 108); 
INSERT INTO habite VALUES (1022, 119);
INSERT INTO habite VALUES (1023, 119); 
INSERT INTO habite VALUES (1024, 108);
INSERT INTO habite VALUES (1025, 119); 
INSERT INTO habite VALUES (1026, 108);
INSERT INTO habite VALUES (1027, 119);
-- Elephants -> 109,119
INSERT INTO habite VALUES (1028, 109); 
INSERT INTO habite VALUES (1029, 109);
INSERT INTO habite VALUES (1030, 109); 
INSERT INTO habite VALUES (1031, 119);
INSERT INTO habite VALUES (1032, 119); 
INSERT INTO habite VALUES (1033, 109);
INSERT INTO habite VALUES (1034, 119);
-- Chimpanzes -> 110,111
INSERT INTO habite VALUES (1035, 110); 
INSERT INTO habite VALUES (1036, 110);
INSERT INTO habite VALUES (1037, 111); 
INSERT INTO habite VALUES (1038, 111);
INSERT INTO habite VALUES (1039, 110); 
INSERT INTO habite VALUES (1040, 111);
-- Gorilles -> 112,120
INSERT INTO habite VALUES (1041, 112); 
INSERT INTO habite VALUES (1042, 112);
INSERT INTO habite VALUES (1043, 120); 
INSERT INTO habite VALUES (1044, 120);
INSERT INTO habite VALUES (1045, 112);
-- Rhinoceros -> 104,105
INSERT INTO habite VALUES (1046, 104); 
INSERT INTO habite VALUES (1047, 104);
INSERT INTO habite VALUES (1048, 105); 
INSERT INTO habite VALUES (1049, 105);
INSERT INTO habite VALUES (1050, 104);
-- Hippopotames -> 113,114
INSERT INTO habite VALUES (1051, 113); 
INSERT INTO habite VALUES (1052, 113);
INSERT INTO habite VALUES (1053, 114); 
INSERT INTO habite VALUES (1054, 114);
INSERT INTO habite VALUES (1055, 113);
-- Leopards -> 116,117
INSERT INTO habite VALUES (1056, 116); 
INSERT INTO habite VALUES (1057, 116);
INSERT INTO habite VALUES (1058, 117); 
INSERT INTO habite VALUES (1059, 117);
INSERT INTO habite VALUES (1060, 116);
-- Flamants -> 115
INSERT INTO habite VALUES (1061, 115); 
INSERT INTO habite VALUES (1062, 115);
INSERT INTO habite VALUES (1063, 115); 
INSERT INTO habite VALUES (1064, 115);
INSERT INTO habite VALUES (1065, 115);
-- Manchots -> 113,114
INSERT INTO habite VALUES (1066, 113); 
INSERT INTO habite VALUES (1067, 114);
INSERT INTO habite VALUES (1068, 113); 
INSERT INTO habite VALUES (1069, 114);
INSERT INTO habite VALUES (1070, 113);

--  peuvent_cohabiter 
INSERT INTO peuvent_cohabiter VALUES ('Girafe',  'Zebre');
INSERT INTO peuvent_cohabiter VALUES ('Zebre',   'Girafe');
INSERT INTO peuvent_cohabiter VALUES ('Flamant', 'Manchot');
INSERT INTO peuvent_cohabiter VALUES ('Manchot', 'Flamant');
INSERT INTO peuvent_cohabiter VALUES ('Girafe',  'Elephant');
INSERT INTO peuvent_cohabiter VALUES ('Elephant','Girafe');
INSERT INTO peuvent_cohabiter VALUES ('Zebre',   'Elephant');
INSERT INTO peuvent_cohabiter VALUES ('Elephant','Zebre');

--  mange (Animal -> Nourriture) 
-- Lions
INSERT INTO mange VALUES (1001,'Viande bovine'); 
INSERT INTO mange VALUES (1001,'Poulet frais');
INSERT INTO mange VALUES (1002,'Viande bovine'); 
INSERT INTO mange VALUES (1003,'Viande bovine');
INSERT INTO mange VALUES (1004,'Poulet frais');  
INSERT INTO mange VALUES (1005,'Viande bovine');
INSERT INTO mange VALUES (1006,'Poulet frais');  
INSERT INTO mange VALUES (1007,'Viande bovine');
INSERT INTO mange VALUES (1008,'Poulet frais');
-- Tigres
INSERT INTO mange VALUES (1009,'Viande bovine'); 
INSERT INTO mange VALUES (1010,'Poulet frais');
INSERT INTO mange VALUES (1011,'Viande bovine'); 
INSERT INTO mange VALUES (1012,'Poulet frais');
INSERT INTO mange VALUES (1013,'Viande bovine'); 
INSERT INTO mange VALUES (1014,'Poulet frais');
-- Girafes
INSERT INTO mange VALUES (1015,'Foin premium');  
INSERT INTO mange VALUES (1015,'Feuilles acacia');
INSERT INTO mange VALUES (1016,'Feuilles acacia');
INSERT INTO mange VALUES (1017,'Foin premium');
INSERT INTO mange VALUES (1018,'Feuilles acacia');
INSERT INTO mange VALUES (1019,'Foin premium');
INSERT INTO mange VALUES (1020,'Feuilles acacia');
-- Zebres
INSERT INTO mange VALUES (1021,'Foin premium');  
INSERT INTO mange VALUES (1022,'Herbe fraiche');
INSERT INTO mange VALUES (1023,'Herbe fraiche'); 
INSERT INTO mange VALUES (1024,'Foin premium');
INSERT INTO mange VALUES (1025,'Herbe fraiche'); 
INSERT INTO mange VALUES (1026,'Foin premium');
INSERT INTO mange VALUES (1027,'Herbe fraiche');
-- Elephants
INSERT INTO mange VALUES (1028,'Foin premium');  
INSERT INTO mange VALUES (1028,'Fruits melanges');
INSERT INTO mange VALUES (1029,'Herbe fraiche'); 
INSERT INTO mange VALUES (1030,'Foin premium');
INSERT INTO mange VALUES (1031,'Fruits melanges');
INSERT INTO mange VALUES (1032,'Herbe fraiche');
INSERT INTO mange VALUES (1033,'Foin premium');  
INSERT INTO mange VALUES (1034,'Fruits melanges');
-- Chimpanzes
INSERT INTO mange VALUES (1035,'Fruits melanges');
INSERT INTO mange VALUES (1035,'Legumes varies');
INSERT INTO mange VALUES (1036,'Fruits melanges');
INSERT INTO mange VALUES (1037,'Legumes varies');
INSERT INTO mange VALUES (1038,'Fruits melanges');
INSERT INTO mange VALUES (1039,'Legumes varies');
INSERT INTO mange VALUES (1040,'Fruits melanges');
-- Gorilles
INSERT INTO mange VALUES (1041,'Fruits melanges');
INSERT INTO mange VALUES (1041,'Legumes varies');
INSERT INTO mange VALUES (1042,'Legumes varies'); 
INSERT INTO mange VALUES (1043,'Fruits melanges');
INSERT INTO mange VALUES (1044,'Legumes varies'); 
INSERT INTO mange VALUES (1045,'Fruits melanges');
-- Rhinoceros
INSERT INTO mange VALUES (1046,'Herbe fraiche');  
INSERT INTO mange VALUES (1047,'Foin premium');
INSERT INTO mange VALUES (1048,'Herbe fraiche');  
INSERT INTO mange VALUES (1049,'Foin premium');
INSERT INTO mange VALUES (1050,'Herbe fraiche');
-- Hippopotames
INSERT INTO mange VALUES (1051,'Herbe fraiche');  
INSERT INTO mange VALUES (1052,'Foin premium');
INSERT INTO mange VALUES (1053,'Herbe fraiche');  
INSERT INTO mange VALUES (1054,'Legumes varies');
INSERT INTO mange VALUES (1055,'Herbe fraiche');
-- Leopards
INSERT INTO mange VALUES (1056,'Viande bovine'); 
 INSERT INTO mange VALUES (1057,'Poulet frais');
INSERT INTO mange VALUES (1058,'Viande bovine'); 
 INSERT INTO mange VALUES (1059,'Poulet frais');
INSERT INTO mange VALUES (1060,'Viande bovine');
-- Flamants
INSERT INTO mange VALUES (1061,'Crevettes roses');
INSERT INTO mange VALUES (1062,'Crevettes');
INSERT INTO mange VALUES (1063,'Crevettes roses');
INSERT INTO mange VALUES (1064,'Crevettes');
INSERT INTO mange VALUES (1065,'Crevettes roses');
-- Manchots
INSERT INTO mange VALUES (1066,'Poisson entier');
 INSERT INTO mange VALUES (1067,'Crevettes');
INSERT INTO mange VALUES (1068,'Poisson entier');
 INSERT INTO mange VALUES (1069,'Crevettes');
INSERT INTO mange VALUES (1070,'Poisson entier');

--  consommer (Animal -> Medicament) 
INSERT INTO consommer VALUES (1001, 1);
 INSERT INTO consommer VALUES (1003, 4);
INSERT INTO consommer VALUES (1009, 1); 
INSERT INTO consommer VALUES (1011, 2);
INSERT INTO consommer VALUES (1015, 3); 
INSERT INTO consommer VALUES (1017, 4);
INSERT INTO consommer VALUES (1028, 3);
 INSERT INTO consommer VALUES (1030, 5);
INSERT INTO consommer VALUES (1035, 6); 
INSERT INTO consommer VALUES (1041, 7);
INSERT INTO consommer VALUES (1046, 4); 
INSERT INTO consommer VALUES (1051, 2);
INSERT INTO consommer VALUES (1056, 1); 
INSERT INTO consommer VALUES (1061, 8);
INSERT INTO consommer VALUES (1066, 6); 
INSERT INTO consommer VALUES (1021, 5);

--  confere (Personnel -> Nourriture) 
INSERT INTO confere VALUES ( 8, 'Viande bovine');   
INSERT INTO confere VALUES ( 8, 'Poulet frais');
INSERT INTO confere VALUES ( 9, 'Foin premium');    
INSERT INTO confere VALUES ( 9, 'Herbe fraiche');
INSERT INTO confere VALUES (10, 'Fruits melanges'); 
INSERT INTO confere VALUES (10, 'Legumes varies');
INSERT INTO confere VALUES (11, 'Poisson entier'); 
 INSERT INTO confere VALUES (11, 'Crevettes');
INSERT INTO confere VALUES (12, 'Crevettes roses'); 
INSERT INTO confere VALUES (12, 'Feuilles acacia');
INSERT INTO confere VALUES (13, 'Viande bovine');  
 INSERT INTO confere VALUES (14, 'Foin premium');
INSERT INTO confere VALUES (15, 'Fruits melanges');
 INSERT INTO confere VALUES (16, 'Poisson entier');

--  utilise (Personnel, Specialite, Medicament) 
INSERT INTO utilise VALUES ( 5, 1, 2);
 INSERT INTO utilise VALUES ( 6, 1, 5);
INSERT INTO utilise VALUES ( 6, 1, 2); 
INSERT INTO utilise VALUES ( 7, 3, 7);
INSERT INTO utilise VALUES ( 8, 2, 3);
INSERT INTO utilise VALUES ( 8, 2, 4);
 INSERT INTO utilise VALUES ( 9, 5,4 );
INSERT INTO utilise VALUES ( 9, 5, 1);
 INSERT INTO utilise VALUES ( 10, 5, 2);

--  travaille (Personnel -> Boutique) 
INSERT INTO travaille VALUES (17, 1); 
INSERT INTO travaille VALUES (18, 1);
INSERT INTO travaille VALUES (19, 2); 
INSERT INTO travaille VALUES (20, 2);
INSERT INTO travaille VALUES (21, 3); 
INSERT INTO travaille VALUES (22, 3);
INSERT INTO travaille VALUES (23, 4); 
INSERT INTO travaille VALUES (24, 4);
INSERT INTO travaille VALUES (25, 5); 
INSERT INTO travaille VALUES (26, 5);
INSERT INTO travaille VALUES (27, 6); 
INSERT INTO travaille VALUES (28, 6);
INSERT INTO travaille VALUES (29, 7); 
INSERT INTO travaille VALUES (30, 7);
INSERT INTO travaille VALUES (31, 8); 
INSERT INTO travaille VALUES (32, 8);

--  affecte (Personnel -> Zone) 
INSERT INTO affecte VALUES ( 8, 'Savane');
INSERT INTO affecte VALUES ( 9, 'Savane');
INSERT INTO affecte VALUES (10, 'Plaine africaine');
INSERT INTO affecte VALUES (11, 'Plaine africaine');
INSERT INTO affecte VALUES (12, 'Foret tropicale');
INSERT INTO affecte VALUES (13, 'Foret tropicale');
INSERT INTO affecte VALUES (14, 'Jungle');
INSERT INTO affecte VALUES (15, 'Jungle');
INSERT INTO affecte VALUES (16, 'Zone aquatique');
INSERT INTO affecte VALUES (17, 'Zone aquatique');
INSERT INTO affecte VALUES (18, 'Montagne');
INSERT INTO affecte VALUES (19, 'Montagne');
INSERT INTO affecte VALUES (20, 'Savane');
INSERT INTO affecte VALUES (21, 'Plaine africaine');

--  intervient (Personnel, Enclos, Date) 
INSERT INTO intervient VALUES ( 16,101, TO_DATE('2026-03-01','YYYY-MM-DD'), 'Nettoyage');
INSERT INTO intervient VALUES ( 15,102, TO_DATE('2026-03-02','YYYY-MM-DD'), 'Nettoyage');
INSERT INTO intervient VALUES ( 14,103, TO_DATE('2026-03-03','YYYY-MM-DD'), 'Soin veterinaire');
INSERT INTO intervient VALUES ( 13,104, TO_DATE('2026-03-04','YYYY-MM-DD'), 'Soin veterinaire');
INSERT INTO intervient VALUES (12,105, TO_DATE('2026-03-05','YYYY-MM-DD'), 'Nettoyage');
INSERT INTO intervient VALUES (12,108, TO_DATE('2026-03-07','YYYY-MM-DD'), 'Alimentation');
INSERT INTO intervient VALUES (13,109, TO_DATE('2026-03-08','YYYY-MM-DD'), 'Soin veterinaire');
INSERT INTO intervient VALUES (15,110, TO_DATE('2026-03-09','YYYY-MM-DD'), 'Nettoyage');
INSERT INTO intervient VALUES (14,111, TO_DATE('2026-03-10','YYYY-MM-DD'), 'Nettoyage');
INSERT INTO intervient VALUES ( 16,112, TO_DATE('2026-03-11','YYYY-MM-DD'), 'Soin veterinaire');
INSERT INTO intervient VALUES (12,113, TO_DATE('2026-03-12','YYYY-MM-DD'), 'Alimentation');
INSERT INTO intervient VALUES (16,114, TO_DATE('2026-03-13','YYYY-MM-DD'), 'Alimentation');
INSERT INTO intervient VALUES (12,115, TO_DATE('2026-03-14','YYYY-MM-DD'), 'Nettoyage');
INSERT INTO intervient VALUES (15,116, TO_DATE('2026-03-15','YYYY-MM-DD'), 'Soin veterinaire');
INSERT INTO intervient VALUES (16,117, TO_DATE('2026-03-16','YYYY-MM-DD'), 'Nettoyage');
INSERT INTO intervient VALUES (11, 101, TO_DATE('2026-04-01','YYYY-MM-DD'), 'Nettoyage');
INSERT INTO intervient VALUES (11, 102, TO_DATE('2026-04-02','YYYY-MM-DD'), 'Nettoyage');
INSERT INTO intervient VALUES (11, 103, TO_DATE('2026-04-03','YYYY-MM-DD'), 'Nettoyage');
INSERT INTO intervient VALUES (11, 104, TO_DATE('2026-04-04','YYYY-MM-DD'), 'Nettoyage');
INSERT INTO intervient VALUES (11, 105, TO_DATE('2026-04-05','YYYY-MM-DD'), 'Nettoyage');
INSERT INTO intervient VALUES (11, 107, TO_DATE('2026-04-06','YYYY-MM-DD'), 'Nettoyage');
INSERT INTO intervient VALUES (11, 108, TO_DATE('2026-04-07','YYYY-MM-DD'), 'Nettoyage');
INSERT INTO intervient VALUES (11, 109, TO_DATE('2026-04-08','YYYY-MM-DD'), 'Nettoyage');
INSERT INTO intervient VALUES (11, 110, TO_DATE('2026-04-09','YYYY-MM-DD'), 'Nettoyage');
INSERT INTO intervient VALUES (11, 111, TO_DATE('2026-04-10','YYYY-MM-DD'), 'Nettoyage');
INSERT INTO intervient VALUES (11, 112, TO_DATE('2026-04-11','YYYY-MM-DD'), 'Nettoyage');
INSERT INTO intervient VALUES (11, 113, TO_DATE('2026-04-12','YYYY-MM-DD'), 'Nettoyage');
INSERT INTO intervient VALUES (11, 114, TO_DATE('2026-04-13','YYYY-MM-DD'), 'Nettoyage');
INSERT INTO intervient VALUES (11, 115, TO_DATE('2026-04-14','YYYY-MM-DD'), 'Nettoyage');
INSERT INTO intervient VALUES (11, 116, TO_DATE('2026-04-15','YYYY-MM-DD'), 'Nettoyage');
INSERT INTO intervient VALUES (11, 117, TO_DATE('2026-04-16','YYYY-MM-DD'), 'Nettoyage');
INSERT INTO Intervient VALUES (11, 106, TO_DATE('2026-04-17','YYYY-MM-DD'), 'Nettoyage');
INSERT INTO Intervient VALUES (11, 118, TO_DATE('2026-04-18','YYYY-MM-DD'), 'Nettoyage');
INSERT INTO Intervient VALUES (11, 119, TO_DATE('2026-04-19','YYYY-MM-DD'), 'Nettoyage');
INSERT INTO Intervient VALUES (11, 120, TO_DATE('2026-04-20','YYYY-MM-DD'), 'Nettoyage');


--  intervention_prestataire 
INSERT INTO intervention_prestataire VALUES ( 1,1,101, TO_DATE('2026-02-01','YYYY-MM-DD'));
INSERT INTO intervention_prestataire VALUES ( 2,1,103, TO_DATE('2026-02-10','YYYY-MM-DD'));
INSERT INTO intervention_prestataire VALUES ( 3,2,104, TO_DATE('2026-02-15','YYYY-MM-DD'));
INSERT INTO intervention_prestataire VALUES ( 4,2,110, TO_DATE('2026-02-20','YYYY-MM-DD'));
INSERT INTO intervention_prestataire VALUES ( 5,3,107, TO_DATE('2026-03-01','YYYY-MM-DD'));
INSERT INTO intervention_prestataire VALUES ( 6,4,113, TO_DATE('2026-03-05','YYYY-MM-DD'));
INSERT INTO intervention_prestataire VALUES ( 7,4,114, TO_DATE('2026-03-06','YYYY-MM-DD'));
INSERT INTO intervention_prestataire VALUES ( 8,5,105, TO_DATE('2026-03-10','YYYY-MM-DD'));
INSERT INTO intervention_prestataire VALUES ( 9,5,120, TO_DATE('2026-03-12','YYYY-MM-DD'));
INSERT INTO intervention_prestataire VALUES (10,3,119, TO_DATE('2026-03-15','YYYY-MM-DD'));


--  possede (Enclos -> Particularite) 
INSERT INTO possede VALUES (101, 'Fosse a eau');
INSERT INTO possede VALUES (102, 'Abri climatise');
INSERT INTO possede VALUES (103, 'Abri climatise');
INSERT INTO possede VALUES (104, 'Rochers artificiels');
INSERT INTO possede VALUES (105, 'Vegetation dense');
INSERT INTO possede VALUES (107, 'Fosse a eau');
INSERT INTO possede VALUES (108, 'Herbe fraiche');
INSERT INTO possede VALUES (109, 'Mare naturelle');
INSERT INTO possede VALUES (110, 'Arbre grimpable');
INSERT INTO possede VALUES (111, 'Arbre grimpable');
INSERT INTO possede VALUES (112, 'Vegetation dense');
INSERT INTO possede VALUES (113, 'Mare naturelle');
INSERT INTO possede VALUES (114, 'Fosse a eau');
INSERT INTO possede VALUES (115, 'Mare naturelle');
INSERT INTO possede VALUES (116, 'Rochers artificiels');
INSERT INTO possede VALUES (117, 'Tunnel souterrain');

--  EstDans (Zone -> Boutique) 
INSERT INTO EstDans VALUES ('Savane',          1);
INSERT INTO EstDans VALUES ('Savane',          5);
INSERT INTO EstDans VALUES ('Plaine africaine',2);
INSERT INTO EstDans VALUES ('Foret tropicale', 3);
INSERT INTO EstDans VALUES ('Jungle',          4);
INSERT INTO EstDans VALUES ('Zone aquatique',  6);
INSERT INTO EstDans VALUES ('Montagne',        7);
INSERT INTO EstDans VALUES ('Montagne',        8);

--  parrainer (Parrain -> Animal) 
INSERT INTO parrainer VALUES ( 1, 1, 1001, 3);
INSERT INTO parrainer VALUES ( 2, 1, 1015, 2);
INSERT INTO parrainer VALUES ( 3, 2, 1028, 5);
INSERT INTO parrainer VALUES ( 4, 2, 1041, 3);
INSERT INTO parrainer VALUES ( 5, 3, 1009, 4);
INSERT INTO parrainer VALUES ( 6, 3, 1046, 2);
INSERT INTO parrainer VALUES ( 7, 4, 1051, 3);
INSERT INTO parrainer VALUES ( 8, 5, 1066, 1);
INSERT INTO parrainer VALUES ( 9, 6, 1035, 2);
INSERT INTO parrainer VALUES (10, 7, 1061, 1);
INSERT INTO parrainer VALUES (11, 8, 1056, 3);
INSERT INTO parrainer VALUES (12, 9, 1021, 2);
INSERT INTO parrainer VALUES (13,10, 1002, 4);
INSERT INTO parrainer VALUES (14, 4, 1003, 2);
INSERT INTO parrainer VALUES (15, 5, 1030, 3);

commit ;
