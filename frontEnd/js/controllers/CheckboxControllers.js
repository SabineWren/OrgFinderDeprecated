FrontEndApp.controller('CheckboxOuterController', ['$scope',function($scope) {
	
	$scope.checkedOuter = 0;
	
}]);

/* Activities
 * @Dependencies:
 * -- directive checkChanged.js */
FrontEndApp.controller('CheckboxActiviesController', ['$scope', '$http', 'checkChangedService',function($scope, $http, checkChangedService) {

	/* Checkbox Elements */
	$http.get('frontEnd/data/Activities.json').success(function(data){
		$scope.checkboxModel = data;
	});
	
	$scope.checked = 0;
	
	$scope.callCheckChanged = function(box){
		checkChangedService.checkChanged(box, $scope);
	}
	
}]);

/* Archetype
 * @Dependencies:
 * -- directive checkChanged.js */
FrontEndApp.controller('CheckboxArchetypeController', ['$scope', '$http', 'checkChangedService',function($scope, $http, checkChangedService) {

	/* Checkbox Elements */
	$http.get('frontEnd/data/Archetype.json').success(function(data){
		$scope.checkboxModel = data;
	});
	
	$scope.checked = 0;
	
	$scope.callCheckChanged = function(box){
		checkChangedService.checkChanged(box, $scope);
	}
	
}]);

/* Commitment
 * @Dependencies:
 * -- directive checkChanged.js */
FrontEndApp.controller('CheckboxCommitmentController', ['$scope', '$http', 'checkChangedService',function($scope, $http, checkChangedService) {
	
	/* Checkbox Elements */
	$http.get('frontEnd/data/Commitment.json').success(function(data){
		$scope.checkboxModel = data;
	});
	
	$scope.checked = 0;
	
	$scope.callCheckChanged = function(box){
		checkChangedService.checkChanged(box, $scope);
	}
	
}]);

/* Recruiting
 * @Dependencies:
 * -- directive checkChanged.js */
FrontEndApp.controller('CheckboxRecruitingController', ['$scope', '$http', 'checkChangedService',function($scope, $http, checkChangedService) {
	
	/* Checkbox Elements */
	$http.get('frontEnd/data/Recruiting.json').success(function(data){
		$scope.checkboxModel = data;
	});
	
	$scope.checked = 0;
	
	$scope.callCheckChanged = function(box){
		checkChangedService.checkChanged(box, $scope);
	}
	
}]);

/* Recruiting
 * @Dependencies:
 * -- directive checkChanged.js */
FrontEndApp.controller('CheckboxRolePlayController', ['$scope', '$http', 'checkChangedService',function($scope, $http, checkChangedService) {
	
	/* Checkbox Elements */
	$http.get('frontEnd/data/RolePlay.json').success(function(data){
		$scope.checkboxModel = data;
	});
	
	$scope.checked = 0;
	
	$scope.callCheckChanged = function(box){
		checkChangedService.checkChanged(box, $scope);
	}
	
}]);
