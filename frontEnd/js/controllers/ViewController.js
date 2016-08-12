/*
	@license magnet:?xt=urn:btih:0b31508aeb0634b347b8270c7bee4d411b5d4109&dn=agpl-3.0.txt
	
	Copyright (C) 2016 LucFauvel and SabineWren
	
	GNU AFFERO GENERAL PUBLIC LICENSE Version 3, 19 November 2007
	https://www.gnu.org/licenses/agpl-3.0.html
	
	@license-end
*/

FrontEndApp.controller('ViewController', ['$scope', '$rootScope', 'LoadViewService', function($scope, $rootScope, LoadViewService){
	
	$scope.broadcastLoadMoreOrgs = function(){
		$rootScope.$broadcast('loadMoreOrgs');
	}
	
	$scope.clearFiltering = LoadViewService.clearFiltering;
	
	//init
	$scope.sortstatus     = LoadViewService.sortStatus;
	$scope.clearSorting   = LoadViewService.clearSorting;
	$scope.loadStatus     = LoadViewService.loadStatus;
	$scope.orgResults = LoadViewService.orgResults;
	
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
		$scope.clearFiltering();
		$scope.broadcastLoadMoreOrgs();
	}
	//sort size
	$scope.clickSize = function(){
		if($scope.sortstatus.sizeDouble){
			$scope.clearSorting();
			$scope.sortstatus.sizeDouble     = false;
			$scope.sortstatus.sizeAscending  = true;
		}
		else if($scope.sortstatus.sizeAscending){
			$scope.clearSorting();
			$scope.sortstatus.sizeDouble     = false;
			$scope.sortstatus.sizeDescending = true;
		}
		else if($scope.sortstatus.sizeDescending){
			$scope.clearSorting();
		}
		//reapply filters
		$scope.clearFiltering();
		$scope.broadcastLoadMoreOrgs();
	}
	
}]);

