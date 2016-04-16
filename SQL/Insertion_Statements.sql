DELETE FROM tbl_OrgSize;
DELETE FROM tbl_OrgMemberHistory;
DELETE FROM tbl_OrgArchetypes;
DELETE FROM tbl_Archetypes;
-- person fluencies?
DELETE FROM tbl_OrgFluencies;
DELETE FROM tbl_Fluencies;
DELETE FROM tbl_OrgLocated;
DELETE FROM tbl_OrgRegions;
DELETE FROM tbl_SecondaryFocus;
DELETE FROM tbl_PrimaryFocus;
DELETE FROM tbl_Performs;
DELETE FROM tbl_Activities;
DELETE FROM tbl_Commits;
DELETE FROM tbl_Commitments;
DELETE FROM tbl_ExclusiveOrgs;
DELETE FROM tbl_FullOrgs;
DELETE FROM tbl_RolePlayOrgs;
DELETE FROM tbl_Represents;
DELETE FROM tbl_Affiliated;
DELETE FROM tbl_Main;
DELETE FROM tbl_RepresentsCog;
DELETE FROM tbl_Organizations;
DELETE FROM tbl_FromCountry;
DELETE FROM tbl_Persons;
DELETE FROM tbl_Countries;


INSERT INTO tbl_Countries(Name) VALUES ('Canada'), ('United States'), ('England'), ('France'), ('Germany');

INSERT INTO tbl_Organizations(SID, Name, Icon) VALUES ('PARAMC','Paradigm Military Contracting', 'https://robertsspaceindustries.com/media/4e3xv65oud994r/logo/PARAMC-Logo.png'), ('PARADIGM', 'Paradigm','https://robertsspaceindustries.com/rsi/static/images/organization/defaults/logo/corp.jpg'), ('Paradigma', 'PARA', 'https://robertsspaceindustries.com/rsi/static/images/organization/defaults/logo/syndicate.jpg');

INSERT INTO tbl_Commitments(Commitment) VALUES ('Casual'), ('Regular'), ('Hardcore');

INSERT INTO tbl_Commits(Organization, Commitment) VALUES ('PARAMC','Hardcore'), ('PARADIGM','Casual'), ('Paradigma','Regular');

INSERT INTO tbl_RolePlayOrgs(Organization) VALUES('PARADIGM');

INSERT INTO tbl_Archetypes(Archetype) VALUES ('Organization'), ('Corporation'), ('PMC'), ('Faith'), ('Syndicate');

INSERT INTO tbl_OrgArchetypes(Organization, Archetype) VALUES ('PARAMC', 'PMC'), ('PARADIGM', 'Corporation'), ('Paradigma', 'Syndicate');



-- degragment to cluster on indexes
ALTER TABLE tbl_Countries ENGINE=INNODB;
ALTER TABLE tbl_Organizations ENGINE=INNODB;
ALTER TABLE tbl_Organizations ENGINE=INNODB;
ALTER TABLE tbl_Organizations ENGINE=INNODB;
ALTER TABLE tbl_RolePlayOrgs ENGINE=INNODB;
ALTER TABLE tbl_Archetypes ENGINE=INNODB;
ALTER TABLE tbl_OrgArchetypes ENGINE=INNODB;

