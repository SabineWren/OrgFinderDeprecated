FrontEndApp.factory('getMembersService', function ($resource) {
    return $resource('http://sc-api.com/?api_source=live&system=organizations&action=organization_members&target_id=:orgName&start_page=1&end_page=1&expedite=0&format=pretty_json',{orgName: "@orgName"});
});

FrontEndApp.factory('getOrgsService', function ($resource) {
    return $resource('backEnd/orgList.json');
});
