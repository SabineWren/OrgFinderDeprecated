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
		$scope.results = [1];
	}
	
	/*********************
	 * Display Results */
	 
	 $scope.loadMore = function(newPageNumber){
		var lastItem = $scope.results[$scope.results.length - 1];
		//var moreResults = getResultsService.query({file: "Activities.json"});
		//var moreResults = [];
		for(var i = 1; i < 8; i++){
			$scope.results.push(lastItem + i);
		}
	}
	
	// Init
	$scope.currentPage = 1;
	$scope.pageSize = 8;
	$scope.results = [];
	for(var i = 1; i < 10; i++){
		$scope.results.push(i);
	}
	$scope.newPageNumber = 5;
	
	$scope.loadMore($scope.pageSize);
	
}]);



FrontEndApp.factory('getResultsService', function ($resource) {
    return $resource('frontEnd/data/:file',{file: "@file"});
});
