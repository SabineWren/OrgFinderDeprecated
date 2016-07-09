/*	
	@license magnet:?xt=urn:btih:0b31508aeb0634b347b8270c7bee4d411b5d4109&dn=agpl-3.0.txt
	
	Copyright (C) 2016 LucFauvel and SabineWren
	
	GNU AFFERO GENERAL PUBLIC LICENSE Version 3, 19 November 2007
	https://www.gnu.org/licenses/agpl-3.0.html
	
	@license-end
*/

//FrontEndApp.controller('CheckboxController', ['$scope', '$http', 'readFileService', 'getResultsService', function($scope, $http, readFileService, getResultsService) {
FrontEndApp.controller('CheckboxController', ['$scope', '$http', 'readFileService', function($scope, $http, readFileService) {
	// Init
	$scope.checkedOuter = {num: 0};
	$scope.checkboxModels = [];
	
	// each list of checkboxes is stored as array elements in a single JSON file with its corresponding title
	var query = readFileService.query( {file: "Checkboxes.json"} );
	query.$promise.then(function(data){
		var rawFileData = data;
		
		for(var object in rawFileData){
			if( !isNaN(object) ){
				$scope.checkboxModels.push( {category: rawFileData[object].category, appliedFilter: [], data: rawFileData[object].data} );
			}
		}
	});
	
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

/*
FrontEndApp.factory('getResultsService', function ($resource) {
    return $resource('frontEnd/data/:file',{file: "@file"});
});*/
