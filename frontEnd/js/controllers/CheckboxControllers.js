FrontEndApp.controller('CheckboxOuterController', ['$scope',function($scope) {
	
	$scope.checkedOuter = 0;
	
}]);

/* Activities */
FrontEndApp.controller('CheckboxActivitiesController', ['$scope', '$http', 'checkChangedService', 'checkboxService', function($scope, $http, checkChangedService, checkboxService) {

	$scope.Category = "Activities";

	/* Checkbox Elements */
	$scope.checkboxModel = checkboxService.query({file: "Activities.json"});
	
	$scope.checked = 0;
	
	$scope.callCheckChanged = function(box){
		checkChangedService.checkChanged(box, $scope);
	}
	
}]);

/* Archetype */
FrontEndApp.controller('CheckboxArchetypeController', ['$scope', '$http', 'checkChangedService', 'checkboxService', function($scope, $http, checkChangedService, checkboxService) {

	$scope.Category = "Archetype";

	/* Checkbox Elements */
	$scope.checkboxModel = checkboxService.query({file: "Archetype.json"});
	
	$scope.checked = 0;
	
	$scope.callCheckChanged = function(box){
		checkChangedService.checkChanged(box, $scope);
	}
	
}]);

/* Commitment
 * @Dependencies:
 * -- directive checkChanged.js */
FrontEndApp.controller('CheckboxCommitmentController', ['$scope', '$http', 'checkChangedService', 'checkboxService', function($scope, $http, checkChangedService, checkboxService) {
	
	$scope.Category = "Commitment";
	
	/* Checkbox Elements */
	$scope.checkboxModel = checkboxService.query({file: "Commitment.json"});
	
	$scope.checked = 0;
	
	$scope.callCheckChanged = function(box){
		checkChangedService.checkChanged(box, $scope);
	}
	
}]);

/* Recruiting
 * @Dependencies:
 * -- directive checkChanged.js */
FrontEndApp.controller('CheckboxRecruitingController', ['$scope', '$http', 'checkChangedService', 'checkboxService', function($scope, $http, checkChangedService, checkboxService) {
	
	$scope.Category = "Recruiting";
	
	/* Checkbox Elements */
	$scope.checkboxModel = checkboxService.query({file: "Recruiting.json"});
	
	$scope.checked = 0;
	
	$scope.callCheckChanged = function(box){
		checkChangedService.checkChanged(box, $scope);
	}
	
}]);

/* Recruiting
 * @Dependencies:
 * -- directive checkChanged.js */
FrontEndApp.controller('CheckboxRolePlayController', ['$scope', '$http', 'checkChangedService', 'checkboxService', function($scope, $http, checkChangedService, checkboxService) {
	
	$scope.Category = "Role Play";
	
	/* Checkbox Elements */
	$scope.checkboxModel = checkboxService.query({file: "RolePlay.json"});
	
	$scope.checked = 0;
	
	$scope.callCheckChanged = function(box){
		checkChangedService.checkChanged(box, $scope);
	}
	
}]);
