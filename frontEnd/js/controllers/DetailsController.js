/*
	@license magnet:?xt=urn:btih:0b31508aeb0634b347b8270c7bee4d411b5d4109&dn=agpl-3.0.txt
	
	Copyright (C) 2016 SabineWren
	
	GNU AFFERO GENERAL PUBLIC LICENSE Version 3, 19 November 2007
	https://www.gnu.org/licenses/agpl-3.0.html
	
	@license-end
*/

angular.module('FrontEndApp').controller('DetailsController', ['$scope', 'LoadDetailsService', 'GlobalStateUI',
function ($scope, LoadDetailsService, GlobalStateUI) {
	
	$scope.exitDetails = function(){
		$scope.StateUI.Details  = false;
		$scope.StateUI.Controls = true;
	}
	
	$scope.StateUI = GlobalStateUI.StateUI;
	
	$scope.chartData   = LoadDetailsService.chartData;
	$scope.rowData     = LoadDetailsService.rowData;
	$scope.loadDetails = LoadDetailsService.loadDetails;
	
	$scope.labels = ["Past", "Current"];
	$scope.series = ['Total Size', 'Main Members'];
	
}]);
