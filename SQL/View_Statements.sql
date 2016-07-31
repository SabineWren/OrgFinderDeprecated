-- Views for Selects without Filters
CREATE OR REPLACE VIEW View_Roleplaying as
SELECT SID, CASE
	WHEN rpr.Organization IS NOT NULL then "Yes"
	ELSE "No"
	END AS Rolplay
FROM tbl_Organizations orgs
LEFT JOIN tbl_RolePlayOrgs rpr
ON orgs.SID = rpr.Organization;

CREATE OR REPLACE VIEW View_Recruiting as
SELECT SID, CASE
	WHEN FullOrgs.Organization IS NOT NULL then "No"
	WHEN XOrgs.Organization IS NOT NULL then "Excl."
	ELSE "Yes"
	END AS Recruiting
FROM tbl_Organizations orgs
LEFT JOIN tbl_FullOrgs FullOrgs
ON orgs.SID = FullOrgs.Organization
LEFT JOIN tbl_ExclusiveOrgs XOrgs
ON orgs.SID = XOrgs.Organization;

-- deprecated
CREATE OR REPLACE VIEW View_Performs1 as
SELECT A.Icon as PrimaryIcon, P1.Organization as SID
FROM tbl_Activities A JOIN tbl_Performs P1 
ON A.Activity = P1.PrimaryFocus;

-- deprecated
CREATE OR REPLACE VIEW View_Performs2 as
SELECT A.Icon as SecondaryIcon, P2.Organization as SID
FROM tbl_Activities A JOIN tbl_Performs P2 
ON A.Activity = P2.SecondaryFocus;

CREATE OR REPLACE VIEW View_Size as
SELECT Organization as SID, MemberCount as Members, CASE
	WHEN OrgSize.MemberCountMain IS NULL then "NA"
	ELSE OrgSize.MemberCountMain
	END AS Mains, CASE
		WHEN OrgSize.MemberCountAffiliate IS NULL then "NA"
		ELSE OrgSize.MemberCountAffiliate
		END AS Affiliates
FROM tbl_OrgSize OrgSize;

CREATE OR REPLACE VIEW View_OrganizationsEverything as
SELECT orgs.SID, orgs.Name, Members, Mains, Affiliates, Commitment, Language, Rolplay, Archetype, Recruiting, 
performs.PrimaryFocus as PrimaryFocus, performs.SecondaryFocus as SecondaryFocus, orgs.Icon
FROM tbl_Organizations orgs
LEFT JOIN View_Size ON orgs.SID = View_Size.SID
LEFT JOIN tbl_Commits cr ON orgs.SID = cr.Organization
LEFT JOIN tbl_OrgFluencies lang ON orgs.SID = lang.Organization
JOIN View_Roleplaying ON orgs.SID = View_Roleplaying.SID
LEFT JOIN tbl_OrgArchetypes arr ON orgs.SID = arr.Organization
JOIN View_Recruiting recr ON orgs.SID = recr.SID
LEFT JOIN tbl_Performs performs ON orgs.SID = performs.Organization;

-- Views for Filtering
CREATE OR REPLACE VIEW View_PrimaryFilter as
SELECT T1.Organization as SID, A1.Icon as PrimaryIcon
FROM tbl_PrimaryFocus T1 LEFT JOIN tbl_Activities A1
ON T1.PrimaryFocus = A1.Activity;

CREATE OR REPLACE VIEW View_SecondaryFilter as
SELECT T2.Organization as SID, A2.Icon as SecondaryIcon
FROM tbl_SecondaryFocus T2 LEFT JOIN tbl_Activities A2
ON T2.SecondaryFocus = A2.Activity;

CREATE OR REPLACE VIEW View_OrgsFilterActivity as
SELECT orgs.SID, orgs.Name, Members, Mains, Affiliates, Commitment, Language, Rolplay, Archetype, Recruiting, 
F1.PrimaryIcon as PrimaryFocus, F2.SecondaryIcon, orgs.Icon, SecondaryFocus
FROM tbl_Organizations orgs
LEFT JOIN View_Size ON orgs.SID = View_Size.SID
LEFT JOIN tbl_Commits cr ON orgs.SID = cr.Organization
LEFT JOIN tbl_OrgFluencies lang ON orgs.SID = lang.Organization
JOIN View_Roleplaying ON orgs.SID = View_Roleplaying.SID
LEFT JOIN tbl_OrgArchetypes arr ON orgs.SID = arr.Organization
JOIN View_Recruiting recr ON orgs.SID = recr.SID
LEFT JOIN View_PrimaryFilter F1 ON orgs.SID = F1.SID
LEFT JOIN View_SecondaryFilter F2 ON orgs.SID = F2.SID;

