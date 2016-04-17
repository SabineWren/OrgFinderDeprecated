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

INSERT INTO tbl_Organizations(SID, Name, Icon) VALUES ('PARAMC','Paradigm Military Contracting', 'https://robertsspaceindustries.com/media/4e3xv65oud994r/logo/PARAMC-Logo.png'), ('PARADIGM', 'Paradigm','https://robertsspaceindustries.com/rsi/static/images/organization/defaults/logo/corp.jpg'), ('PARA', 'Paradigma', 'https://robertsspaceindustries.com/rsi/static/images/organization/defaults/logo/syndicate.jpg'), ('PSG', 'Paradigm Shipping Group', 'https://robertsspaceindustries.com/media/d2ngblzh4soxqr/logo/PSG-Logo.jpg'), ('TRUST', 'The Trust', 'https://robertsspaceindustries.com/media/ncq0wwoyknjqvr/logo/TRUST-Logo.png');

INSERT INTO tbl_Commitments(Commitment) VALUES ('Casual'), ('Regular'), ('Hardcore');

INSERT INTO tbl_Commits(Organization, Commitment) VALUES ('PARAMC','Hardcore'), ('PARADIGM','Casual'), ('PARA','Regular'), ('PSG', 'Regular'), ('TRUST', 'Regular');

INSERT INTO tbl_RolePlayOrgs(Organization) VALUES('PARADIGM'), ('PSG'), ('TRUST');

INSERT INTO tbl_Archetypes(Archetype) VALUES ('Organization'), ('Corporation'), ('PMC'), ('Faith'), ('Syndicate');

INSERT INTO tbl_OrgArchetypes(Organization, Archetype) VALUES ('PARAMC', 'PMC'), ('PARADIGM', 'Corporation'), ('PARA', 'Syndicate'), ('PSG', 'Corporation'), ('TRUST', 'Corporation');

INSERT INTO tbl_FullOrgs(Organization) VALUES ('PSG');

INSERT INTO tbl_ExclusiveOrgs(Organization) VALUES ('TRUST');

INSERT INTO tbl_Fluencies(Language) VALUES ('English'), ('German');

INSERT INTO tbl_OrgFluencies(Organization, Language) VALUES ('PARADIGM', 'English'), ('PARA', 'German'), ('PARAMC', 'English'), ('PSG', 'English');

INSERT INTO tbl_Activities(Activity, Icon) VALUES 
('Bounty Hunting', 'https://robertsspaceindustries.com/media/bgdbhpzrkumy2r/icon/Bounty_hunting.png'), 
('Engineering', 'https://robertsspaceindustries.com/media/wlgjee4z43hhdr/icon/Engineering.png'), 
('Exploration', 'https://robertsspaceindustries.com/media/i07k1bkmcjnmpr/icon/Exploration.png'), 
('Freelancing', 'https://robertsspaceindustries.com/media/xhuvehkwn6qsnr/icon/Freelancing.png'), 
('Infiltration', 'https://robertsspaceindustries.com/media/7ypcg4mjtk6a3r/icon/Infiltration.png'), 
('Piracy', 'https://robertsspaceindustries.com/media/qvy8iu6s49xcvr/icon/Piracy.png'), 
('Resources', 'https://robertsspaceindustries.com/media/z2431c754d5yhr/icon/Resources.png'), 
('Scouting', 'https://robertsspaceindustries.com/media/evjzzb5o9frbkr/icon/Scouting.png'), 
('Security', 'https://robertsspaceindustries.com/media/7nk9059i8lnfmr/icon/Security.png'), 
('Smuggling', 'https://robertsspaceindustries.com/media/j2u845ltfuwrjr/icon/Smuggling.png'), 
('Social', 'https://robertsspaceindustries.com/media/5pcet2tbffqinr/icon/Social.png'), 
('Trading', 'https://robertsspaceindustries.com/media/svml2z3iniikjr/icon/Trade.png'), 
('Transport', 'https://robertsspaceindustries.com/media/19dxufo66bma7r/icon/Transport.png');


INSERT INTO tbl_Performs(Organization, PrimaryFocus, SecondaryFocus) VALUES
('PARADIGM', 'Engineering', 'Resources'), 
('PARA', 'Freelancing', 'Smuggling'), 
('PARAMC', 'Bounty Hunting', 'Security'), 
('PSG', 'Transport', 'Trading'), 
('TRUST', 'Trading', 'Security');

INSERT INTO tbl_PrimaryFocus(PrimaryFocus, Organization) VALUES 
('Engineering', 'PARADIGM'), 
('Freelancing', 'PARA'), 
('Bounty Hunting', 'PARAMC'), 
('Transport', 'PSG'), 
('Trading', 'TRUST');

INSERT INTO tbl_SecondaryFocus(SecondaryFocus, Organization) VALUES 
('Resources', 'PARADIGM'),
('Smuggling', 'PARA'), 
('Security', 'PARAMC'), 
('Trading', 'PSG'), 
('Security', 'TRUST');



-- degragment to cluster on indexes
ALTER TABLE tbl_Countries ENGINE=INNODB;
ALTER TABLE tbl_Organizations ENGINE=INNODB;
ALTER TABLE tbl_Commitments ENGINE=INNODB;
ALTER TABLE tbl_Commits ENGINE=INNODB;
ALTER TABLE tbl_RolePlayOrgs ENGINE=INNODB;
ALTER TABLE tbl_Archetypes ENGINE=INNODB;
ALTER TABLE tbl_OrgArchetypes ENGINE=INNODB;
ALTER TABLE tbl_FullOrgs ENGINE=INNODB;
ALTER TABLE tbl_ExclusiveOrgs ENGINE=INNODB;
ALTER TABLE tbl_Fluencies ENGINE=INNODB;
ALTER TABLE tbl_OrgFluencies ENGINE=INNODB;

