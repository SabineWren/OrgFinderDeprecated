FrontEndApp.controller('CheckboxOuterController', ['$scope',function($scope) {
	
	
	
}]);

FrontEndApp.controller('CheckboxCommitmentController', ['$scope', '$http', 'checkboxService', function($scope, $http, checkboxService) {
	$scope.checkedOuter = 0;
	$scope.category = "Commitment";
	$scope.checkboxModels = [
		checkboxService.query({file: "Commitment.json"}),
		checkboxService.query({file: "Activities.json"}),
		checkboxService.query({file: "Archetype.json"}),
		checkboxService.query({file: "RolePlay.json"})
		/*checkboxService.query({file: "Activities.json"})
		checkboxService.query({file: "Activities.json"})
		checkboxService.query({file: "Activities.json"})
		checkboxService.query({file: "Activities.json"})*/
	]
	$scope.allAppliedFilters = [];
	
}]);
