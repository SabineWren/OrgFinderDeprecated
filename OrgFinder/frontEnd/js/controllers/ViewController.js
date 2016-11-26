/*
	@license magnet:?xt=urn:btih:0b31508aeb0634b347b8270c7bee4d411b5d4109&dn=agpl-3.0.txt
	
	Copyright (C) 2016 SabineWren
	
	GNU AFFERO GENERAL PUBLIC LICENSE Version 3, 19 November 2007
	https://www.gnu.org/licenses/agpl-3.0.html
	
	@license-end
*/

FrontEndApp.controller('ViewController', ['$scope', '$rootScope', 'LoadViewService', 'LoadDetailsService', 'GlobalStateUI',
function($scope, $rootScope, LoadViewService, LoadDetailsService, GlobalStateUI){
	
	$scope.loadMoreOrgs = function(){
		$rootScope.$broadcast('loadMoreOrgs');
	};
	
	$scope.loadDetails = function(currentRow){
		//calculate width of details window from width of elements it's replacing
		var widthBasis     = 0;
		var widthExtension = 0;
		
		//list view
		if($scope.StateUI.listViewTF){
			if( document.getElementById('commitment-language').offsetWidth !== 0 ){
				widthBasis     = document.getElementById('raw-block-width').offsetWidth;
				widthExtension = document.getElementById('commitment-language').offsetWidth;
				$scope.widthDetails.data = {
					"width" : ( widthExtension + widthBasis ).toString() + "px"
				};
			};
		}
		//grid view
		else {
			if(widthBasis = document.getElementById('left-block-controls').offsetWidth !== 0){
				widthBasis     = document.getElementById('left-block-controls').offsetWidth;
				widthExtension = document.getElementById('cellWidthJS').offsetWidth;
				$scope.widthDetails.data = {
					"width" : ( widthExtension + widthBasis ).toString() + "px"
				};
			}
			
			//adjust width of grid if we pushed it right
			if(!$scope.StateUI.Details){
				widthBasis = document.getElementById('grid-view-results').offsetWidth;
				$scope.gridWidthModifer.data = {
					"width" : ( widthBasis - widthExtension ).toString() + "px"
				};
			}
		}
		
		//now load details for current org
		$scope.curSelection   = currentRow.SID;
		
		LoadDetailsService.loadDetails(currentRow);
		$scope.StateUI.Details  = true;
		$scope.StateUI.Controls = false;
	};
	
	$scope.clearResults = LoadViewService.clearResults;
	
	//init
	$scope.StateUI = GlobalStateUI.StateUI;
	
	$scope.focusFilterType = LoadViewService.focusFilterType;
	$scope.sortstatus      = LoadViewService.sortStatus;
	$scope.clearSorting    = LoadViewService.clearSorting;
	$scope.loadStatus      = LoadViewService.loadStatus;
	$scope.orgResults      = LoadViewService.orgResults;
	$scope.curSelection    = "";
	
	//sort name
	$scope.clickName = function(){
		if($scope.sortstatus.nameDouble){
			$scope.clearSorting();
			$scope.sortstatus.nameDouble    = false;
			$scope.sortstatus.nameAscending = true;
		}
		else if($scope.sortstatus.nameAscending){
			$scope.clearSorting();
			$scope.sortstatus.nameDouble     = false;
			$scope.sortstatus.nameDescending = true;
		}
		else if($scope.sortstatus.nameDescending){
			$scope.clearSorting();
		}
		//reapply filters
		$scope.clearResults();
		$scope.loadMoreOrgs();
	};
	//sort size
	$scope.clickSize = function(){
		if($scope.sortstatus.sizeDouble){
			$scope.clearSorting();
			$scope.sortstatus.sizeDouble     = false;
			$scope.sortstatus.sizeDescending  = true;
		}
		else if($scope.sortstatus.sizeDescending){
			$scope.clearSorting();
			$scope.sortstatus.sizeDouble    = false;
			$scope.sortstatus.sizeAscending = true;
		}
		else if($scope.sortstatus.sizeAscending){
			$scope.clearSorting();
		}
		//reapply filters
		$scope.clearResults();
		$scope.loadMoreOrgs();
	};
	//sort main
	$scope.clickMain = function(){
		if($scope.sortstatus.mainDouble){
			$scope.clearSorting();
			$scope.sortstatus.mainDouble      = false;
			$scope.sortstatus.mainDescending  = true;
		}
		else if($scope.sortstatus.mainDescending){
			$scope.clearSorting();
			$scope.sortstatus.mainDouble    = false;
			$scope.sortstatus.mainAscending = true;
		}
		else if($scope.sortstatus.mainAscending){
			$scope.clearSorting();
		}
		//reapply filters
		$scope.clearResults();
		$scope.loadMoreOrgs();
	};
	//sort growth
	$scope.clickGrowth = function(){
		if($scope.sortstatus.growthDescending){
			$scope.clearSorting();
			$scope.sortstatus.growthDouble    = false;
			$scope.sortstatus.growthAscending = true;
		}
		else if($scope.sortstatus.growthAscending){
			$scope.clearSorting();
		}
		else if($scope.sortstatus.growthDouble){
			$scope.clearSorting();
			$scope.sortstatus.growthDouble     = false;
			$scope.sortstatus.growthDescending = true;
		}
		//reapply filters
		$scope.clearResults();
		$scope.loadMoreOrgs();
	};
	
	$scope.focusFilterTypeToggle = function(){
		if($scope.focusFilterType.restrictToPrimary)$scope.focusFilterType.restrictToPrimary = false;
		else $scope.focusFilterType.restrictToPrimary = true;
	};
	
	$scope.widthDetails     = LoadDetailsService.widthDetails;
	$scope.gridWidthModifer = LoadDetailsService.gridWidthModifer;
}]);

