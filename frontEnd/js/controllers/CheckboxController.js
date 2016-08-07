/*	
	@license magnet:?xt=urn:btih:0b31508aeb0634b347b8270c7bee4d411b5d4109&dn=agpl-3.0.txt
	
	Copyright (C) 2016 LucFauvel and SabineWren
	
	GNU AFFERO GENERAL PUBLIC LICENSE Version 3, 19 November 2007
	https://www.gnu.org/licenses/agpl-3.0.html
	
	@license-end
*/
FrontEndApp.controller('CheckboxController', ['$scope', '$http', 'readFileService', function($scope, $http, readFileService) {
	
	$scope.toggleView = function(){
		if($scope.listViewTF)$scope.listViewTF = false;
		else $scope.listViewTF = true;
	}
	
	var orgData = function(SID, Name, Icon, URL){
		this.SID  = SID;
		this.Name = Name;
		this.Icon = Icon;
		this.URL  = URL;
	}
	
	function callbackParseSelection(data){
		if(data == "null"){
			console.log("No more orgs found!\n");
			alert("No more orgs found!\n");
		}
		else for(obj in data){
			var icon = "/org_icons/" + data[obj]["SID"];
			var field = new orgData( data[obj]["SID"], data[obj]["Name"], icon, data[obj]["URL"] );
			
			field.Members        = data[obj]["Size"];
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
	
	function btoi(theBool){
		if(theBool)return 1;
		else return 0;
	}
	
	$scope.loadMoreOrgs = function(){
		$scope.isLoading = true;//callback sets to false when it's done
		
		//prevent DB from wasting time on bad input
		if($scope.filterSizeMax > 0 && $scope.filterSizeMin > $scope.filterSizeMax + 1)$scope.filterSizeMax = $scope.filterSizeMin;
		
		//only filter by size if needed
		var minSize = null;
		if($scope.filterSizeMin > 1 )minSize = $scope.filterSizeMin.toString();
		var maxSize = null;
		if($scope.filterSizeMax > 0)maxSize = $scope.filterSizeMax.toString();
		
		$http.get('/backEnd/selects.php', { 
			params:{
				pagenum:   $scope.nextPage,
				NameOrSID: encodeURI( $scope.filterName ),
				Min:        minSize,
				Max:        maxSize,
				Cog:        btoi($scope.Cog),
				Lang:       $scope.language,
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
	$scope.filterSizeMin = 1;
	$scope.filterSizeMax = 0;
	$scope.pageSize = 12;
	$scope.results = [];
	$scope.isLoading = false;
	$scope.Cog = false;//default to all orgs
	$scope.listViewTF = true;
	
	$scope.checkedOuter = {num: 0};
	$scope.checkboxModels = [];
	$scope.language = "Any";
	$scope.langs = [];
	$scope.icons = null;
	$scope.filterName = "";
	
	//the database stores the back-end location of each activity icon
	//we GET that location via SELECT and store it in an array
	//therefore, we never GET more images than there are icons
	var getIcons = $http.get('/backEnd/activity_icons.php').success(function(data){
		$scope.icons = data;
	});
	
	$http.get('/data/lang.json').success(function(data){
		$scope.langs = data;
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

