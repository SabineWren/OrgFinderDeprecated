-- Views for Selects without Filters
CREATE OR REPLACE VIEW View_Roleplaying as
SELECT orgs.SID as Organization, CASE
	WHEN rpr.Organization IS NOT NULL then "Yes"
	ELSE "No"
	END AS Roleplay
FROM tbl_Organizations orgs
LEFT JOIN tbl_RolePlayOrgs rpr
ON orgs.SID = rpr.Organization
LIMIT 2147483647 OFFSET 1;-- case statement generates first row as null

-- Needed for displays Yes/No/Excl under recruiting
CREATE OR REPLACE VIEW View_Recruiting as
SELECT SID as Organization, CASE
	WHEN FullOrgs.Organization IS NOT NULL then "No"
	WHEN XOrgs.Organization IS NOT NULL then "Excl."
	ELSE "Yes"
	END AS Recruiting
FROM tbl_Organizations orgs
LEFT JOIN tbl_FullOrgs FullOrgs
ON orgs.SID = FullOrgs.Organization
LEFT JOIN tbl_ExclusiveOrgs XOrgs
ON orgs.SID = XOrgs.Organization
LIMIT 2147483647 OFFSET 1;-- case statement generates first row as null

-- ***May not need
CREATE OR REPLACE VIEW View_Size as
SELECT Organization, MemberCount as Members, CASE
	WHEN OrgSize.MemberCountMain IS NULL then "NA"
	ELSE OrgSize.MemberCountMain
	END AS Mains, CASE
		WHEN OrgSize.MemberCountAffiliate IS NULL then "NA"
		ELSE OrgSize.MemberCountAffiliate
		END AS Affiliates
FROM tbl_OrgSize OrgSize;

-- Default View (no filtering)
CREATE OR REPLACE VIEW View_OrganizationsEverything as
SELECT orgs.SID as SID, orgs.Name as Name, orgs.Icon as Icon, OrgSize.Members as Size, OrgSize.Mains as Mains,
	OrgSize.Affiliates as Affiliates, Performs.PrimaryFocus as PrimaryFocus, Performs.SecondaryFocus as SecondaryFocus,
	Commitment, Language, Archetype,
	CASE
		WHEN Roleplay.Organization IS NOT NULL then "Yes"
		ELSE "No"
		END AS Roleplay,
	CASE
		WHEN FullOrgs.Organization IS NOT NULL then "No"
		WHEN ExclOrgs.Organization IS NOT NULL then "Excl."
		ELSE "Yes"
		END AS Recruiting
FROM tbl_Organizations orgs
LEFT JOIN tbl_Performs       Performs  ON orgs.SID = Performs.Organization
LEFT JOIN View_Size          OrgSize   ON orgs.SID = OrgSize.Organization
     JOIN tbl_Commits        Commits   ON orgs.SID = Commits.Organization
LEFT JOIN tbl_OrgFluencies   Language  ON orgs.SID = Language.Organization
LEFT JOIN tbl_OrgArchetypes  Archetype ON orgs.SID = Archetype.Organization
LEFT JOIN tbl_RolePlayOrgs   Roleplay  ON orgs.SID = Roleplay.Organization
LEFT JOIN tbl_FullOrgs       FullOrgs  ON orgs.SID = FullOrgs.Organization
LEFT JOIN tbl_ExclusiveOrgs  ExclOrgs  ON orgs.SID = ExclOrgs.Organization;

-- Views for Filtering

-- select * from View_OrgsFilterPrimary WHERE PrimaryFocus = "Exploration" LIMIT 300;
CREATE OR REPLACE VIEW View_OrgsFilterPrimary as
SELECT orgs.SID as SID, orgs.Name as Name, orgs.Icon as Icon, OrgSize.Members as Size, OrgSize.Mains as Mains, 
	OrgSize.Affiliates as Affiliates, PrimaryFocus as Focus, Commitment, Language, Archetype,
	CASE
		WHEN Roleplay.Organization IS NOT NULL then "Yes"
		ELSE "No"
		END AS Roleplay,
	CASE
		WHEN FullOrgs.Organization IS NOT NULL then "No"
		WHEN ExclOrgs.Organization IS NOT NULL then "Excl."
		ELSE "Yes"
		END AS Recruiting
FROM tbl_PrimaryFocus Prim
     JOIN tbl_Organizations  orgs      ON orgs.SID = Prim.Organization-- Join prevents NULL first row
LEFT JOIN View_Size          OrgSize   ON orgs.SID = OrgSize.Organization
LEFT JOIN tbl_Commits        Commits   ON orgs.SID = Commits.Organization
LEFT JOIN tbl_OrgFluencies   Language  ON orgs.SID = Language.Organization
LEFT JOIN tbl_OrgArchetypes  Archetype ON orgs.SID = Archetype.Organization
LEFT JOIN tbl_RolePlayOrgs   Roleplay  ON orgs.SID = Roleplay.Organization
LEFT JOIN tbl_FullOrgs       FullOrgs  ON orgs.SID = FullOrgs.Organization
LEFT JOIN tbl_ExclusiveOrgs  ExclOrgs  ON orgs.SID = ExclOrgs.Organization;

-- select * from View_OrgsFilterSecondary WHERE SecondaryFocus = "Exploration" LIMIT 300;
CREATE OR REPLACE VIEW View_OrgsFilterSecondary as
SELECT orgs.SID as SID, orgs.Name as Name, orgs.Icon as Icon, OrgSize.Members as Size, OrgSize.Mains as Mains, 
	OrgSize.Affiliates as Affiliates, SecondaryFocus as Focus, Commitment, Language, Archetype,
	CASE
		WHEN Roleplay.Organization IS NOT NULL then "Yes"
		ELSE "No"
		END AS Roleplay,
	CASE
		WHEN FullOrgs.Organization IS NOT NULL then "No"
		WHEN ExclOrgs.Organization IS NOT NULL then "Excl."
		ELSE "Yes"
		END AS Recruiting
FROM tbl_SecondaryFocus Second
     JOIN tbl_Organizations  orgs      ON orgs.SID = Second.Organization
LEFT JOIN View_Size          OrgSize   ON orgs.SID = OrgSize.Organization
LEFT JOIN tbl_Commits        Commits   ON orgs.SID = Commits.Organization
LEFT JOIN tbl_OrgFluencies   Language  ON orgs.SID = Language.Organization
LEFT JOIN tbl_OrgArchetypes  Archetype ON orgs.SID = Archetype.Organization
LEFT JOIN tbl_RolePlayOrgs   Roleplay  ON orgs.SID = Roleplay.Organization
LEFT JOIN tbl_FullOrgs       FullOrgs  ON orgs.SID = FullOrgs.Organization
LEFT JOIN tbl_ExclusiveOrgs  ExclOrgs  ON orgs.SID = ExclOrgs.Organization;


