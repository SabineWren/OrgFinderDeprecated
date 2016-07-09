/*
	@license magnet:?xt=urn:btih:0b31508aeb0634b347b8270c7bee4d411b5d4109&dn=agpl-3.0.txt
	
	Copyright (C) 2016 LucFauvel and SabineWren
	
	GNU AFFERO GENERAL PUBLIC LICENSE Version 3, 19 November 2007
	https://www.gnu.org/licenses/license-list.en.html#AGPL
	
	@license-end
*/

FrontEndApp.controller('CheckboxController', ['$scope', '$http', 'readFileService', 'getResultsService', function($scope, $http, readFileService, getResultsService) {
	// Init
	$scope.checkedOuter = {num: 0};
	$scope.checkboxModels = [
		{category: "Commitment", appliedFilter: [], data: readFileService.query({file: "Commitment.json"}) },
		{category: "RolePlay", appliedFilter: [], data: readFileService.query({file: "RolePlay.json"}) },
		{category: "Archetype", appliedFilter: [], data: readFileService.query({file: "Archetype.json"}) },
		{category: "Activities", appliedFilter: [], data: readFileService.query({file: "Activities.json"}) },
		{category: "Recruiting", appliedFilter: [], data: readFileService.query({file: "Recruiting.json"}) }
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
