CREATE VIEW OrganizationsFullView as
SELECT SID, Name, Icon, Commitment
FROM tbl_Organizations orgs
LEFT JOIN tbl_Commits cr
ON orgs.SID = cr.Organization;

SELECT *
FROM OrganizationsFullView;
