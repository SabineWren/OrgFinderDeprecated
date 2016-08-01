/*	
	@license magnet:?xt=urn:btih:0b31508aeb0634b347b8270c7bee4d411b5d4109&dn=agpl-3.0.txt
	
	Copyright (C) 2016 LucFauvel and SabineWren
	
	GNU AFFERO GENERAL PUBLIC LICENSE Version 3, 19 November 2007
	https://www.gnu.org/licenses/agpl-3.0.html
	
	@license-end
*/

FrontEndApp.controller('CheckboxController', ['$scope', '$http', 'readFileService', 'getOrgsService', function($scope, $http, readFileService, getOrgsService) {
//FrontEndApp.controller('CheckboxController', ['$scope', '$http', 'readFileService', function($scope, $http, readFileService) {
	var orgData = function(SID, Name, Icon){
		this.SID        = SID;
		this.Name       = Name;
		this.Icon       = Icon;
	}
	
	function callbackParseSelection(data){
		if(data == "null"){
			alert("No more orgs found!\n");
			return;
		}
		for(obj in data){
			var url = "/org_icons/" + data[obj]["SID"];
			var field = new orgData( data[obj]["SID"], data[obj]["Name"], url );
			
			field.Members        = data[obj]["Members"];
			//field.Mains        = data[obj][""];
			//field.Affiliates   = data[obj][""];
			
			field.Commitment     = data[obj]["Commitment"];
			
			field.Recruiting     = data[obj]["Recruiting"];
			
			field.Language       = data[obj]["Language"];
			
			field.Roleplay       = data[obj]["Roleplay"];
			
			field.Archetype      = data[obj]["Archetype"];
			
			field.PrimaryFocus   = data[obj]["PrimaryFocus"];
			field.SecondaryFocus = data[obj]["SecondaryFocus"];
			field.PrimaryIcon    = $scope.icons[  data[obj]["PrimaryFocus"]  ];
			field.SecondaryIcon  = $scope.icons[  data[obj]["SecondaryFocus"]  ];
			
			$scope.results.push(field);
		}
		$scope.isLoading = false;
	}
	
	$scope.loadMoreOrgs = function(){
		//var moreResults = getOrgsService.query();
		/*moreResults.$promise.then(function(data){
			for(var object in data){
				$scope.results.push(object);
			}
		});*/
		var value = 0;
		$scope.isLoading = true;
		$http.get('/backEnd/selects.php/?pagenum=' + $scope.nextPage).success(callbackParseSelection);
		$scope.nextPage++;
		
	}
	
	// Filter by Name or SID
	$scope.callSelect = function(){
		$scope.nextPage = 0;
		$scope.results = [];
		$scope.loadMoreOrgs();
	}

	// Init **********************************************************************************************************
	$scope.nextPage = 0;
	$scope.pageSize = 10;
	$scope.results = [];
	$scope.isLoading = false;
	
	//the database stores the back-end location of each activity icon
	//we GET that location via SELECT and store it in an array
	//therefore, we never GET more images than there are icons
	$scope.icons = null;
	$http.get('/backEnd/activity_icons.php').success(function(data){
		$scope.icons = data;
		$scope.loadMoreOrgs();
	});
	
	$scope.checkedOuter = {num: 0};
	
	// each list of checkboxes is stored as array elements in a single JSON file with its corresponding title
	$scope.checkboxModels = [];
	var query = readFileService.query( {file: "Checkboxes.json"} );
	query.$promise.then(function(data){
		var rawFileData = data;
		for(var object in rawFileData){
			if( !isNaN(object) ){
				$scope.checkboxModels.push({
					category:      rawFileData[object].category, 
					appliedFilter: [], 
					data:          rawFileData[object].data
				});
			}
		}
	});
}]);

