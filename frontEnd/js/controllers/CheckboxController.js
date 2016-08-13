/*	
	@license magnet:?xt=urn:btih:0b31508aeb0634b347b8270c7bee4d411b5d4109&dn=agpl-3.0.txt
	
	Copyright (C) 2016 LucFauvel and SabineWren
	
	GNU AFFERO GENERAL PUBLIC LICENSE Version 3, 19 November 2007
	https://www.gnu.org/licenses/agpl-3.0.html
	
	@license-end
*/
FrontEndApp.controller('CheckboxController', [
'$scope', '$http', 'SharedChartService', 'LoadViewService',
function($scope, $http, SharedChartService, LoadViewService){
	
	function btoi(theBool){
		if(theBool)return 1;
		else return 0;
	};
	
	var callbackParse = LoadViewService.callbackParseSelection;
	$scope.loadMoreOrgs = function(){
		$scope.loadStatus.isLoading = true;//callback sets to false when it's done
	
		//prevent DB from wasting time on bad size input
		if($scope.slider_bar_max.value > 0 && $scope.slider_bar_min.value > $scope.slider_bar_max.value + 1)
			$scope.slider_bar_max.value = $scope.slider_bar_min.value;
	
		//only filter by size if needed
		var minSize = null;
		if($scope.slider_bar_min.value > 1 )
			minSize = $scope.slider_bar_min.value.toString();
		var maxSize = null;
		if($scope.slider_bar_max.value > 0)
			maxSize = $scope.slider_bar_max.value.toString();
	
		//only sort if needed
		var directionName = null;
		if($scope.sortStatus.nameAscending)directionName = 'up';
		else if($scope.sortStatus.nameDescending)directionName = 'down';
		var directionSize = null;
		if($scope.sortStatus.sizeAscending)directionSize = 'up';
		else if($scope.sortStatus.sizeDescending)directionSize = 'down';
	
		$http.get('/backEnd/selects.php', { 
			params:{
				pagenum:    $scope.orgResults.nextPage,
				NameOrSID:  encodeURI( $scope.filterName ),
				nameDir:    directionName,
				sizeDir:    directionSize,
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
		} ).success(callbackParse);
	
		$scope.orgResults.nextPage++;
	};

	// Init **********************************************************************************************************
	$scope.pageSize = 12;
	$scope.Cog = false;//default to all orgs
	$scope.listViewTF = true;
	
	$scope.clearResults = LoadViewService.clearResults;
	$scope.sortStatus   = LoadViewService.sortStatus;
	$scope.loadStatus   = LoadViewService.loadStatus;
	$scope.orgResults   = LoadViewService.orgResults;
	
	$scope.reapplyFilters = function(){
		$scope.clearResults();
		$scope.loadMoreOrgs();
	};
	
	//this needs to be rebuilt more readably
	$scope.resetFilters = function(){
		$scope.checkedOuter.num = 0;
		for( checkboxModel in $scope.checkboxModels){
			$scope.checkboxModels[checkboxModel].appliedFilter.length = 0;
			for(checkboxLabel in $scope.checkboxModels[checkboxModel].data){
				$scope.checkboxModels[checkboxModel].data[ checkboxLabel ].isSelected = false;
			}
		}
		
		$scope.Cog                  = false;
		$scope.slider_bar_min.value = 1;
		$scope.slider_bar_max.value = 0;
		//if( angular.isDefined($scope.language) )delete $scope.language;
		$scope.language             = "Any";
		$scope.filterName           = "";
	};
	
	$scope.checkedOuter = {num: 0};
	$scope.checkboxModels = [];
	$scope.language = "Any";
	$scope.langs = [];
	$scope.filterName = "";
	
	//END INIT ****************************************************************************************************
	
	$scope.slider_bar_min = {
		value: 1,
		options: {
			floor: 1,
			ceil: 100,
			step: 1,
			showSelectionBar: true,
			getSelectionBarColor: function(value) {
				if(value <= 15) return 'red';
				if(value <= 30) return 'orange';
				if(value <= 45) return 'yellow';
				return '#2AE02A';
			}
		}
	};
	
	$scope.slider_bar_max = {
		value: 0,
		options: {
			floor: 0,
			ceil: 100,
			step: 1,
			showSelectionBar: true,
			getSelectionBarColor: function(value) {
				if(value <= 20) return 'red';
				if(value <= 40) return 'orange';
				if(value <= 60) return 'yellow';
				return '#2AE02A';
			}
		}
	};
	
	//the database stores the back-end location of each activity icon
	//we GET that location via SELECT and store it in an array for future use
	$scope.iconsObj = LoadViewService.icons;
	var getIcons = $http.get('/backEnd/activity_icons.php').success(function(data){
		$scope.iconsObj.icons = data;
	});
	
	//we need a list of valid languages for the dropdown filter
	$http.get('/data/lang.json').success(function(data){
		$scope.langs = data;
	});
	
	// each list of checkboxes is stored as array elements in a single JSON file with its corresponding title
	//	in hindsight, everything checkbox related is really hard to read; don't try to reuse any of it
	//	we should rebuild it legibly at some point
	$http.get('/data/Checkboxes.json').success(function(data){
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
	
	//updateChart( [5, 4, 3, 2, 1] );
	var updateChart = SharedChartService.updateChart;
	
	$scope.toggleView = function(){
		if($scope.loadStatus.listViewTF)$scope.loadStatus.listViewTF = false;
		else $scope.loadStatus.listViewTF = true;
	};
	
	//ViewController can set sorting, which requires refiltering
	$scope.$on( 'loadMoreOrgs', $scope.loadMoreOrgs );
	
}]);

