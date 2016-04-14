FrontEndApp.controller('CheckboxOuterController', ['$scope',function($scope) {
	
	$scope.checkedOuter = 0;
	
}]);

/* Activities */
FrontEndApp.controller('CheckboxActivitiesController', ['$scope', '$http', 'checkChangedService', 'checkboxService', function($scope, $http, checkChangedService, checkboxService) {

	$scope.Category = "Activities";
	$scope.checkboxModel = checkboxService.query({file: "Activities.json"});
	$scope.checked = 0;
	$scope.appliedFilters = [];
	
	$scope.callCheckChanged = function(box){
		checkChangedService.checkChanged(box, $scope);
	}
	
}]);

FrontEndApp.controller('CheckboxArchetypeController', ['$scope', '$http', 'checkChangedService', 'checkboxService', function($scope, $http, checkChangedService, checkboxService) {

	$scope.Category = "Archetype";
	$scope.checkboxModel = checkboxService.query({file: "Archetype.json"});
	$scope.checked = 0;
	$scope.appliedFilters = [];
	
	$scope.callCheckChanged = function(box){
		checkChangedService.checkChanged(box, $scope);
	}
	
}]);

/* Commitment */
FrontEndApp.controller('CheckboxCommitmentController', ['$scope', '$http', 'checkChangedService', 'checkboxService', function($scope, $http, checkChangedService, checkboxService) {
	
	$scope.Category = "Commitment";
	$scope.checkboxModel = checkboxService.query({file: "Commitment.json"});
	$scope.checked = 0;
	$scope.appliedFilters = [];
	
	$scope.callCheckChanged = function(box){
		checkChangedService.checkChanged(box, $scope);
	}
	
}]);

/* Recruiting */
FrontEndApp.controller('CheckboxRecruitingController', ['$scope', '$http', 'checkChangedService', 'checkboxService', function($scope, $http, checkChangedService, checkboxService) {
	
	$scope.Category = "Recruiting";
	
	$scope.checkboxModel = checkboxService.query({file: "Recruiting.json"});
	$scope.checked = 0;
	$scope.appliedFilters = [];
	
	$scope.callCheckChanged = function(box){
		checkChangedService.checkChanged(box, $scope);
	}
	
}]);

/* Recruiting */
FrontEndApp.controller('CheckboxRolePlayController', ['$scope', '$http', 'checkChangedService', 'checkboxService', function($scope, $http, checkChangedService, checkboxService) {
	
	$scope.Category = "Role Play";
	
	$scope.checkboxModel = checkboxService.query({file: "RolePlay.json"});
	$scope.checked = 0;
	$scope.appliedFilters = [];
	
	$scope.callCheckChanged = function(box){
		checkChangedService.checkChanged(box, $scope);
	}
	
}]);
