/*FrontEndApp.controller('MainController', ['$scope',function($scope) {
	
}]);*/

FrontEndApp.controller('CheckboxController', ['$scope', '$http', 'checkboxService', function($scope, $http, checkboxService) {
	$scope.checkedOuter = {num: 0};
	$scope.checkboxModels = [
		{category: "Commitment", appliedFilter: [], data: checkboxService.query({file: "Commitment.json"}) },
		{category: "RolePlay", appliedFilter: [], data: checkboxService.query({file: "RolePlay.json"}) },
		{category: "Archetype", appliedFilter: [], data: checkboxService.query({file: "Archetype.json"}) },
		{category: "Activities", appliedFilter: [], data: checkboxService.query({file: "Activities.json"}) },
		{category: "Recruiting", appliedFilter: [], data: checkboxService.query({file: "Recruiting.json"}) }
	];
	
	
}]);
