/*	
	@license magnet:?xt=urn:btih:0b31508aeb0634b347b8270c7bee4d411b5d4109&dn=agpl-3.0.txt
	
	Copyright (C) 2016 LucFauvel and SabineWren
	
	GNU AFFERO GENERAL PUBLIC LICENSE Version 3, 19 November 2007
	https://www.gnu.org/licenses/agpl-3.0.html
	
	@license-end
*/

//FrontEndApp.controller('CheckboxController', ['$scope', '$http', 'readFileService', 'getOrgsService', function($scope, $http, readFileService, getOrgsService) {
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
		console.log("Apply Filter Button Pushed.");
		$scope.nextPage = 0;
		$scope.results = [];
		$scope.loadMore();
	}
	
	/*********************
	 * Display Results */
	 
	$scope.loadMore = function(){
		
		
		//var moreResults = getOrgsService.query();
		/*moreResults.$promise.then(function(data){
			for(var object in data){
				$scope.results.push(object);
			}
		});*/
		var value = 0;
		$http.get('/backEnd/selects.php/?pagenum=' + $scope.nextPage).success(function(data){//localhost:8000
			if(data == "null"){
				alert("No more orgs found!\n");
				return;
			}
			for(obj in data){
				var $field = data[obj]["SID"];
				$scope.results.push($field);
			}
		});
		$scope.nextPage++;
		
	}
	
	// Init
	$scope.nextPage = 0;
	$scope.pageSize = 10;
	$scope.results = [];
	$scope.loadMore();
}]);

/*
FrontEndApp.factory('getResultsService', function ($resource) {
    return $resource('frontEnd/data/:file',{file: "@file"});
});*/
