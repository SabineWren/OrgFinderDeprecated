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
		LoadDetailsService.loadDetails(currentRow);
		$scope.StateUI.Details  = true;
		$scope.StateUI.Controls = false;
	};
	
	$scope.clearResults = LoadViewService.clearResults;
	
	//init
	$scope.StateUI = GlobalStateUI.StateUI;
	
	$scope.sortstatus     = LoadViewService.sortStatus;
	$scope.clearSorting   = LoadViewService.clearSorting;
	$scope.loadStatus     = LoadViewService.loadStatus;
	$scope.orgResults     = LoadViewService.orgResults;
	
	//sort name
	$scope.clickName = function(){
		if($scope.sortstatus.nameDouble){
			$scope.clearSorting();
			$scope.sortstatus.nameDouble    = false;
			$scope.sortstatus.nameAscending = true;
		}
		else if($scope.sortstatus.nameAscending){
			$scope.clearSorting();
			$scope.sortstatus.nameDouble = false;
			$scope.sortstatus.nameDescending = true;
		}
		else if($scope.sortstatus.nameDescending){
			$scope.clearSorting();
		}
		//reapply filters
		$scope.clearResults();
		$scope.loadMoreOrgs();
	}
	//sort size
	$scope.clickSize = function(){
		if($scope.sortstatus.sizeDouble){
			$scope.clearSorting();
			$scope.sortstatus.sizeDouble     = false;
			$scope.sortstatus.sizeDescending  = true;
		}
		else if($scope.sortstatus.sizeDescending){
			$scope.clearSorting();
			$scope.sortstatus.sizeDouble     = false;
			$scope.sortstatus.sizeAscending = true;
		}
		else if($scope.sortstatus.sizeAscending){
			$scope.clearSorting();
		}
		//reapply filters
		$scope.clearResults();
		$scope.loadMoreOrgs();
	}
	//sort main
	$scope.clickMain = function(){
		if($scope.sortstatus.mainDouble){
			$scope.clearSorting();
			$scope.sortstatus.mainDouble     = false;
			$scope.sortstatus.mainDescending  = true;
		}
		else if($scope.sortstatus.mainDescending){
			$scope.clearSorting();
			$scope.sortstatus.mainDouble     = false;
			$scope.sortstatus.mainAscending = true;
		}
		else if($scope.sortstatus.mainAscending){
			$scope.clearSorting();
		}
		//reapply filters
		$scope.clearResults();
		$scope.loadMoreOrgs();
	}
	
}]);

