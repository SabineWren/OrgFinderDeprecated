FrontEndApp.controller('CheckboxController', ['$scope', '$http', 'getOrgsService', 'getMembersService', function($scope, $http, getOrgsService, getMembersService) {
	// Init
	$scope.orgData = getOrgsService.query({file: "orgList.json"});
	
}]);

