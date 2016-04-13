
DROP TABLE tbl_Affiliated
DROP TABLE tbl_Main;
DROP TABLE tbl_OrgsInCog;
DROP TABLE tbl_Organizations;
DROP TABLE tbl_Persons;
DROP TABLE tbl_Countries;

CREATE TABLE tbl_Countries(
	Name VARCHAR(30) PRIMARY KEY
);

/* Second most important table - many tables FK to it */
CREATE TABLE tbl_Persons(
	Name VARCHAR(30) PRIMARY KEY,
	Country VARCHAR(30)
);

CREATE TABLE tbl_FromCountry(
	Person VARCHAR(30) UNIQUE NOT NULL,
	Country VARCHAR(30) NOT NULL,
	FOREIGN KEY FK_Person(Person) REFERENCES tbl_Persons(Name),
	FOREIGN KEY FK_Country(Country) REFERENCES tbl_Countries(Name)
);

/* Most important table - most tables FK to it */
CREATE TABLE tbl_Organizations(
	SID VARCHAR(10) PRIMARY KEY,
	Name VARCHAR(30) NOT NULL,
	Icon VARCHAR(100)/* can be saved locally or as URL to RSI */
);

/* most common use: filter org list to only show orgs in Cognition Corp */
CREATE TABLE tbl_OrgsInCog(
	SID VARCHAR(10) UNIQUE NOT NULL,
	Representative VARCHAR(30) NOT NULL,
	FOREIGN KEY SID REFERENCES tbl_Organizations(SID),
	FOREIGN KEY Representative REFERENCES tbl_Persons(Name)
);

/* most common use: count number of main members within an org. */
CREATE TABLE tbl_Main(
	Organization VARCHAR(10) NOT NULL,
	Name VARCHAR(30) NOT NULL,
	FOREIGN KEY FK_Organization REFERENCES tbl_Organizations(SID),
	FOREIGN KEY FK_Person REFERENCES tbl_Persons(Name),
	CONSTRAINT PK_Main PRIMARY KEY (Organization, Name),/* Set clustered Index */
	CONSTRAINT UNIQUE(Name)/* Player can only have 1 main; separate constraint for clustering */
);

/* most common use: count the number of affliate members within an org */
CREATE TABLE tbl_Affiliated(
	Organization VARCHAR(10) NOT NULL,
	Name VARCHAR(30) NOT NULL,
	FOREIGN KEY FK_Organization REFERENCES tbl_Organizations(SID),
	FOREIGN KEY FK_Person REFERENCES tbl_Persons(Name),
	CONSTRAINT PK_Main PRIMARY KEY (Organization, Name)/* Set clustered Index */
);

/* Inserts */
INSERT INTO tbl_Countries(Name) VALUES('Canada');
INSERT INTO tbl_Countries(Name) VALUES('United States');
INSERT INTO tbl_Countries(Name) VALUES('England');
INSERT INTO tbl_Countries(Name) VALUES('France');
INSERT INTO tbl_Countries(Name) VALUES('Germany');

SELECT * FROM tbl_Countries;

/* degragment to cluster on indexes */
ALTER TABLE tbl_name ENGINE=INNODB

SELECT * FROM tbl_Countries;

