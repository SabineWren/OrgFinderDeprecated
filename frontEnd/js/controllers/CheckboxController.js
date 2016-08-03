/*	
	@license magnet:?xt=urn:btih:0b31508aeb0634b347b8270c7bee4d411b5d4109&dn=agpl-3.0.txt
	
	Copyright (C) 2016 LucFauvel and SabineWren
	
	GNU AFFERO GENERAL PUBLIC LICENSE Version 3, 19 November 2007
	https://www.gnu.org/licenses/agpl-3.0.html
	
	@license-end
*/
FrontEndApp.controller('CheckboxController', ['$scope', '$http', 'readFileService', function($scope, $http, readFileService) {
	var orgData = function(SID, Name, Icon){
		this.SID        = SID;
		this.Name       = Name;
		this.Icon       = Icon;
	}
	
	function callbackParseSelection(data){
		if(data == "null"){
			alert("No more orgs found!\n");
		}
		else for(obj in data){
			var icon = "/org_icons/" + data[obj]["SID"];
			var field = new orgData( data[obj]["SID"], data[obj]["Name"], icon );
			
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
		$scope.isLoading = true;//callback sets to false
		
		$http.get('/backEnd/selects.php', { 
			params:{
				pagenum:   $scope.nextPage,
				NameOrSID: $scope.filterName,
				
				Activity:   $scope.checkboxModels[0].appliedFilter.toString(),
				Archetype:  $scope.checkboxModels[1].appliedFilter.toString(),
				Commitment: $scope.checkboxModels[2].appliedFilter.toString(),
				Recruiting: $scope.checkboxModels[3].appliedFilter.toString(),
				Roleplay:   $scope.checkboxModels[4].appliedFilter.toString()
			}
		} ).success(callbackParseSelection);
		
		$scope.nextPage++;
	}
	
	$scope.reapplyFilters = function(){
		$scope.nextPage = 0;
		$scope.results = [];
		$scope.loadMoreOrgs();
	}

	// Init **********************************************************************************************************
	$scope.nextPage = 0;
	$scope.pageSize = 10;
	$scope.results = [];
	$scope.isLoading = false;
	
	$scope.checkedOuter = {num: 0};
	$scope.checkboxModels = [];
	$scope.icons = null;
	$scope.filterName = "";
	
	//the database stores the back-end location of each activity icon
	//we GET that location via SELECT and store it in an array
	//therefore, we never GET more images than there are icons
	var getIcons = $http.get('/backEnd/activity_icons.php').success(function(data){
		$scope.icons = data;
	});
	
	// each list of checkboxes is stored as array elements in a single JSON file with its corresponding title
	var getCheckboxes = readFileService.query( {file: "Checkboxes.json"} );
	getCheckboxes.$promise.then(function(data){
		var jsonData = data;
		for(var object in jsonData){
			if( !isNaN(object) ){
				$scope.checkboxModels.push({
					category:      jsonData[object].category, 
					appliedFilter: [], 
					data:          jsonData[object].data
				});
			}
		}
		getIcons.then(function(){
			$scope.loadMoreOrgs();//once we have icons and checkboxes, query the database
		})
	});
	
}]);

