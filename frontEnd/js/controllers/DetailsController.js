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
		
		//if in grid view, reset grid with
		if(!$scope.StateUI.listViewTF){
			var widthBasis = document.getElementById('grid-view-results').offsetWidth;
			var widthExtension = document.getElementById('cellWidthJS').offsetWidth;
			$scope.gridWidthModifer.data = {
				"width" : ( widthBasis + widthExtension ).toString() + "px"
			};
		}
	}
	
	$scope.StateUI = GlobalStateUI.StateUI;
	$scope.widthDetails     = LoadDetailsService.widthDetails;
	$scope.gridWidthModifer = LoadDetailsService.gridWidthModifer;
	
	$scope.config      = LoadDetailsService.config;
	$scope.chartData   = LoadDetailsService.chartData;
	$scope.rowData     = LoadDetailsService.rowData;
	$scope.loadDetails = LoadDetailsService.loadDetails;
	
	Chart.defaults.global.defaultFontColor = '#ffffff';
	Chart.defaults.global.defaultFontSize = 18;
	
	$scope.colourConfig ={
		"colours": [
			"#FFFFFF",
			"#FFA500",
			"#88FF00",
			"#FF0000"
		]
	};

}]);
