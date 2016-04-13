
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
	Name VARCHAR(30) PRIMARY KEY)
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
	FOREIGN KEY FK_SID(SID) REFERENCES tbl_Organizations(SID),
	FOREIGN KEY FK_Representative(Representative) REFERENCES tbl_Persons(Name)
);

/* most common use: count number of main members within an org. */
CREATE TABLE tbl_Main(
	Organization VARCHAR(10) NOT NULL,
	Person VARCHAR(30) NOT NULL,
	FOREIGN KEY FK_Organization(Organization) REFERENCES tbl_Organizations(SID),
	FOREIGN KEY FK_Person(Person) REFERENCES tbl_Persons(Name),
	CONSTRAINT PK_Main PRIMARY KEY (Organization, Person),/* Set clustered Index */
	CONSTRAINT UNIQUE(Person)/* Player can only have 1 main; separate constraint for clustering */
);

/* most common use: count the number of affliate members within an org */
CREATE TABLE tbl_Affiliated(
	Organization VARCHAR(10) NOT NULL,
	Person VARCHAR(30) NOT NULL,
	FOREIGN KEY FK_Organization(Organization) REFERENCES tbl_Organizations(SID),
	FOREIGN KEY FK_Person(Person) REFERENCES tbl_Persons(Name),
	CONSTRAINT PK_Main PRIMARY KEY (Organization, Person)/* Set clustered Index */
);

