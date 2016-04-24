FrontEndApp.controller('MemberController', ['$scope', '$http', 'readFileService', 'getMembersService', function($scope, $http, readFileService, getMembersService) {
	$scope.orgData = readFileService.query({file: "orgList.json"});
}]);

