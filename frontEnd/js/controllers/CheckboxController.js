FrontEndApp.controller('CheckboxController', ['$scope', '$http', 'checkboxService', 'getResultsService', function($scope, $http, checkboxService, getResultsService) {
	// Init
	$scope.checkedOuter = {num: 0};
	$scope.checkboxModels = [
		{category: "Commitment", appliedFilter: [], data: checkboxService.query({file: "Commitment.json"}) },
		{category: "RolePlay", appliedFilter: [], data: checkboxService.query({file: "RolePlay.json"}) },
		{category: "Archetype", appliedFilter: [], data: checkboxService.query({file: "Archetype.json"}) },
		{category: "Activities", appliedFilter: [], data: checkboxService.query({file: "Activities.json"}) },
		{category: "Recruiting", appliedFilter: [], data: checkboxService.query({file: "Recruiting.json"}) }
	];
	
	// Filter by Name or SID
	$scope.callSelect = function(){
		console.log("Button Pushed.");
	};
	
	/*********************
	 * Display Results */
	
	// Init
	$scope.currentPage = 1;
	$scope.pageSize = 5;
	$scope.results = [];
	$scope.newPageNumber = 5;
	
	
	$scope.getResults = function(newPageNumber){
		console.log("test2");
		var moreResults = getResultsService.query({file: "Activities.json"});
		for(result in moreResults){
			$scope.results.push(result.name);
		}
		console.log("test");
	}
	
	$scope.getResults($scope.pageSize);
	
}]);



FrontEndApp.factory('getResultsService', function ($resource) {
	console.log("test3");
    return $resource('frontEnd/data/:file',{file: "@file"});
});
