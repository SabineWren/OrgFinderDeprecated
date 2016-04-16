-- degragment to cluster on indexes
-- ALTER TABLE tbl_name ENGINE=INNODB
/*
DELETE FROM tbl_Countries;
DELETE FROM tbl_Organizations
DELETE FROM tbl_Commitments
DELETE FROM tbl_Commits
*/

INSERT INTO tbl_Countries(Name) VALUES('Canada');
INSERT INTO tbl_Countries(Name) VALUES('United States');
INSERT INTO tbl_Countries(Name) VALUES('England');
INSERT INTO tbl_Countries(Name) VALUES('France');
INSERT INTO tbl_Countries(Name) VALUES('Germany');
ALTER TABLE tbl_Countries ENGINE=INNODB

INSERT INTO tbl_Organizations(SID, Name, Icon) VALUES('PARAMC','Paradigm Military Contracting', 'https://robertsspaceindustries.com/media/4e3xv65oud994r/logo/PARAMC-Logo.png');
INSERT INTO tbl_Organizations(SID, Name, Icon) VALUES('PARADIGM', 'Paradigm','https://robertsspaceindustries.com/rsi/static/images/organization/defaults/logo/corp.jpg');
INSERT INTO tbl_Organizations(SID, Name, Icon) VALUES('Paradigma', 'PARA', 'https://robertsspaceindustries.com/rsi/static/images/organization/defaults/logo/syndicate.jpg');
ALTER TABLE tbl_Organizations ENGINE=INNODB

INSERT INTO tbl_Commitments(Commitment) VALUES('Casual');
INSERT INTO tbl_Commitments(Commitment) VALUES('Regular');
INSERT INTO tbl_Commitments(Commitment) VALUES('Hardcore');
ALTER TABLE tbl_Organizations ENGINE=INNODB

INSERT INTO tbl_Commits(Organization, Commitment) VALUES('PARAMC','Hardcore');
INSERT INTO tbl_Commits(Organization, Commitment) VALUES('PARADIGM','Casual');
INSERT INTO tbl_Commits(Organization, Commitment) VALUES('Paradigma','Regular');
ALTER TABLE tbl_Organizations ENGINE=INNODB

