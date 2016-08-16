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
	$scope.widthDetails = LoadDetailsService.widthDetails;
	
	$scope.chartData   = LoadDetailsService.chartData;
	$scope.rowData     = LoadDetailsService.rowData;
	$scope.loadDetails = LoadDetailsService.loadDetails;
	
	Chart.defaults.global.defaultFontColor = '#ffffff';
	Chart.defaults.global.defaultFontSize = 18;
	
	$scope.config = {
		series:  ['Total Size', 'Main Members'],
		labels:  ["Past", "Current"],
		colours: ["#FFFFAA", "#FFAA44"],
		options: {
			responsive: true,
			legend: {
				display: true,
				position: 'top',
				fullWidth: false,
				labels: {
					fontColor: '#FFFFFF',
					fontStyle: 'bold',
					fontSize: 20,
					boxWidth: 50,
				}
			},
			scales: {
				yAxes: [{
					ticks: {
						suggestedMin: 0,
						stepSize: 1,
						callback: function(tickValue, index, ticks) {
							if(!(index % parseInt(ticks.length / 5))) {
								return tickValue;
							}
						}
					}
				}]
			}
		}
	}
	
}]);
