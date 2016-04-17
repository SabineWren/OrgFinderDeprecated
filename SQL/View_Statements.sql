CREATE VIEW View_Roleplaying as
SELECT SID, CASE
	WHEN rpr.Organization IS NOT NULL then "Yes"
	ELSE "No"
	END AS Rolplay
FROM tbl_Organizations orgs
LEFT JOIN tbl_RolePlayOrgs rpr
on orgs.SID = rpr.Organization;

CREATE VIEW View_OrganizationsEverything as
SELECT orgs.SID, Name, Icon, Commitment, Rolplay
FROM tbl_Organizations orgs
LEFT JOIN tbl_Commits cr
ON orgs.SID = cr.Organization
JOIN view_Roleplaying
ON orgs.SID = view_Roleplaying.SID;



