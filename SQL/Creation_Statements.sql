/*
DROP TABLE tbl_OrgMemberHistory;
DROP TABLE tbl_OrgArchetypes;
DROP TABLE tbl_Archetypes;
DROP TABLE tbl_FilterFluencies;
DROP TABLE tbl_OrgFluencies;
DROP TABLE tbl_Fluencies;
DROP TABLE tbl_OrgLocated;
DROP TABLE tbl_OrgRegions;
DROP TABLE tbl_SecondaryFocus;
DROP TABLE tbl_PrimaryFocus;
DROP TABLE tbl_Performs;
DROP TABLE tbl_Activities;
DROP TABLE tbl_Commits;
DROP TABLE tbl_Commitments;
DROP TABLE tbl_ExclusiveOrgs;
DROP TABLE tbl_FullOrgs;
DROP TABLE tbl_RolePlayOrgs;
DROP TABLE tbl_Affiliated;
DROP TABLE tbl_Main;
DROP TABLE tbl_RepresentsCog;
DROP TABLE tbl_OrgNames;
DROP TABLE tbl_Organizations;
DROP TABLE tbl_FromCountry;
DROP TABLE tbl_Persons;
DROP TABLE tbl_Countries;
*/

CREATE TABLE tbl_Countries(
	Name VARCHAR(30) PRIMARY KEY -- Clustered Index
);

-- Second most important table - many tables FK to it
CREATE TABLE tbl_Persons(
	Name VARCHAR(30) PRIMARY KEY -- Clustered Index
);

CREATE TABLE tbl_FromCountry(
	Person  VARCHAR(30) UNIQUE NOT NULL, -- Clustered Index
	Country VARCHAR(30)        NOT NULL,
	FOREIGN KEY FK_Person(Person)   REFERENCES tbl_Persons(Name),
	FOREIGN KEY FK_Country(Country) REFERENCES tbl_Countries(Name)
);

-- Most important table - most tables FK to it
CREATE TABLE tbl_Organizations(
	SID  VARCHAR(10) PRIMARY KEY, -- Clustered Index
	Name VARCHAR(30)  NOT NULL,
	Size INT NOT NULL,
	Main INT,
	Icon VARCHAR(100) NOT NULL, -- link to RSI
	URL  VARCHAR(100) NOT NULL -- org website (default RSI)
);
ALTER TABLE tbl_Organizations ADD INDEX(Size, SID);
ALTER TABLE tbl_Organizations ADD INDEX(Name, SID);

-- Used exclusively for selecting orgs based on Name (case insensitive)
CREATE TABLE tbl_OrgNames(
	NameUpper VARCHAR(30) NOT NULL,
	SID  VARCHAR(10) NOT NULL,
	FOREIGN KEY FK_OrgName(SID) REFERENCES tbl_Organizations(SID),
	CONSTRAINT PK_OrgNames PRIMARY KEY (NameUpper, SID), -- Clustered Index
	CONSTRAINT UNIQUE(SID)
);

CREATE TABLE tbl_RepresentsCog(
	SID VARCHAR(10) UNIQUE NOT NULL, -- Clustered Index
	Representative VARCHAR(30) NOT NULL,
	FOREIGN KEY FK_SID(SID) REFERENCES tbl_Organizations(SID),
	FOREIGN KEY FK_Representative(Representative) REFERENCES tbl_Persons(Name)
);

-- most common use: count number of main members within an org.
CREATE TABLE tbl_Main(
	Organization VARCHAR(10) NOT NULL,
	Person       VARCHAR(30) NOT NULL,
	FOREIGN KEY FK_Organization(Organization) REFERENCES tbl_Organizations(SID),
	FOREIGN KEY FK_Person(Person) REFERENCES tbl_Persons(Name),
	CONSTRAINT PK_Main PRIMARY KEY (Organization, Person), -- Clustered Index
	CONSTRAINT UNIQUE(Person) -- Player can only have 1 main
);

-- most common use: count the number of affliate members within an org
CREATE TABLE tbl_Affiliated(
	Organization VARCHAR(10) NOT NULL,
	Person VARCHAR(30) NOT NULL,
	FOREIGN KEY FK_Organization(Organization) REFERENCES tbl_Organizations(SID),
	FOREIGN KEY FK_Person(Person) REFERENCES tbl_Persons(Name),
	CONSTRAINT PK_Main PRIMARY KEY (Organization, Person) -- Clustered Index
);

CREATE TABLE tbl_RolePlayOrgs(
	Organization VARCHAR(10) UNIQUE NOT NULL, -- Clustered Index
	FOREIGN KEY FK_Organization(Organization) REFERENCES tbl_Organizations(SID)
);

CREATE TABLE tbl_FullOrgs(
	Organization VARCHAR(10) UNIQUE NOT NULL, -- Clustered Index
	FOREIGN KEY FK_Organization(Organization) REFERENCES tbl_Organizations(SID)
);

CREATE TABLE tbl_ExclusiveOrgs(
	Organization VARCHAR(10) UNIQUE NOT NULL, -- Clustered Index
	FOREIGN KEY FK_Organization(Organization) REFERENCES tbl_Organizations(SID)
);

CREATE TABLE tbl_Commitments(
	Commitment VARCHAR(8) PRIMARY KEY
);

CREATE TABLE tbl_Commits(
	Organization VARCHAR(10) UNIQUE NOT NULL, -- Clustered Index
	Commitment VARCHAR(8) NOT NULL,
	FOREIGN KEY FK_Organization(Organization) REFERENCES tbl_Organizations(SID),
	FOREIGN KEY FK_Commitment(Commitment) REFERENCES tbl_Commitments(Commitment)
);

CREATE TABLE tbl_Activities(
	Activity VARCHAR(14) PRIMARY KEY, -- Clustered Index
	Icon VARCHAR(100) UNIQUE
);

-- meant for populating the orgs spreadsheet; use primary/secondary for faster filtering
CREATE TABLE tbl_Performs(
	Organization VARCHAR(10) UNIQUE NOT NULL, -- Clustered Index
	PrimaryFocus VARCHAR(14) NOT NULL,
	SecondaryFocus VARCHAR(14) NOT NULL,
	FOREIGN KEY FK_Organization(Organization) REFERENCES tbl_Organizations(SID),
	FOREIGN KEY FK_PrimaryFocus(PrimaryFocus) REFERENCES tbl_Activities(Activity),
	FOREIGN KEY FK_SecondaryFocus(SecondaryFocus) REFERENCES tbl_Activities(Activity)
);

-- used only for listing all orgs with a specific primary focus (fast filtering)
CREATE TABLE tbl_PrimaryFocus(
	PrimaryFocus VARCHAR(14) NOT NULL,
	Organization VARCHAR(10) NOT NULL,
	FOREIGN KEY FK_Organization(Organization) REFERENCES tbl_Organizations(SID),
	FOREIGN KEY FK_Primary(PrimaryFocus) REFERENCES tbl_Activities(Activity),
	CONSTRAINT PK_PrimaryFocus PRIMARY KEY (PrimaryFocus, Organization), -- Clustered Index
	CONSTRAINT UNIQUE(Organization) -- only one primary focus per org
);

-- used only for listing all orgs with a specific secondary focus (fast filtering)
CREATE TABLE tbl_SecondaryFocus(
	SecondaryFocus VARCHAR(14) NOT NULL,
	Organization VARCHAR(10) NOT NULL,
	FOREIGN KEY FK_Organization(Organization) REFERENCES tbl_Organizations(SID),
	FOREIGN KEY FK_Secondary(SecondaryFocus) REFERENCES tbl_Activities(Activity),
	CONSTRAINT PK_SecondaryFocus PRIMARY KEY (SecondaryFocus, Organization), -- Clustered Index
	CONSTRAINT UNIQUE(Organization) -- only one secondary focus per org
);

CREATE TABLE tbl_OrgRegions(
	Region VARCHAR(30) PRIMARY KEY
);

-- meant for populating the orgs spreadsheet rather than fast filtering
CREATE TABLE tbl_OrgLocated(
	Organization VARCHAR(10) UNIQUE NOT NULL, -- Clustered Index
	Region VARCHAR(30) NOT NULL,
	FOREIGN KEY FK_Organization(Organization) REFERENCES tbl_Organizations(SID),
	FOREIGN KEY FK_Region(Region) REFERENCES tbl_OrgRegions(Region)
);

CREATE TABLE tbl_Fluencies(
	Language VARCHAR(20) PRIMARY KEY
);

-- meant for populating the orgs spreadsheet rather than fast filtering
CREATE TABLE tbl_OrgFluencies(
	Organization VARCHAR(10) UNIQUE NOT NULL, -- Clustered Index
	Language VARCHAR(30) NOT NULL,
	FOREIGN KEY FK_Organization(Organization) REFERENCES tbl_Organizations(SID),
	FOREIGN KEY FK_Language(Language) REFERENCES tbl_Fluencies(Language)
);

CREATE TABLE tbl_FilterFluencies(
	Language VARCHAR(30) NOT NULL,
	Organization VARCHAR(10) UNIQUE NOT NULL,
	FOREIGN KEY FK_Language(Language) REFERENCES tbl_Fluencies(Language),
	FOREIGN KEY FK_Organization(Organization) REFERENCES tbl_Organizations(SID),
	CONSTRAINT PK_FilterFluencies PRIMARY KEY(Language, Organization), -- Clustered Index
	CONSTRAINT UNIQUE(Organization) -- only one language per og
);


-- optionally add PersonFluencies as well??

CREATE TABLE tbl_Archetypes(
	Archetype VARCHAR(12) PRIMARY KEY-- Clustered Index
);

-- meant for populating the orgs spreadsheet rather than fast filtering
CREATE TABLE tbl_OrgArchetypes(
	Organization VARCHAR(10) UNIQUE NOT NULL, -- Clustered Index
	Archetype VARCHAR(12) NOT NULL,
	FOREIGN KEY FK_Organization(Organization) REFERENCES tbl_Organizations(SID),
	FOREIGN KEY FK_Archetype(Archetype) REFERENCES tbl_Archetypes(Archetype)
);

-- meant for fast filtering
CREATE TABLE tbl_FilterArchetypes(
	Archetype VARCHAR(12) NOT NULL,
	Organization VARCHAR(10) NOT NULL,
	FOREIGN KEY FK_Organization(Organization) REFERENCES tbl_Organizations(SID),
	FOREIGN KEY FK_Archetype(Archetype) REFERENCES tbl_Archetypes(Archetype),
	CONSTRAINT PK_FilterArchetypes PRIMARY KEY(Archetype, Organization), -- Clustered Index
	CONSTRAINT UNIQUE(Organization) -- only one archetype per org
);

-- need to individually count members to check main and affiliate, which can be an added feature but allow null for now
CREATE TABLE tbl_OrgMemberHistory(
	Organization VARCHAR(10) UNIQUE NOT NULL, -- Clustered Index
	ScrapeDate DATE NOT NULL,
	MemberCount INT NOT NULL,
	MemberCountMain INT,
	MemberCountAffiliate INT,
	FOREIGN KEY FK_Organization(Organization) REFERENCES tbl_Organizations(SID)
);


