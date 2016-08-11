/*
	@license magnet:?xt=urn:btih:0b31508aeb0634b347b8270c7bee4d411b5d4109&dn=agpl-3.0.txt
	
	Copyright (C) 2016 LucFauvel and SabineWren
	
	GNU AFFERO GENERAL PUBLIC LICENSE Version 3, 19 November 2007
	https://www.gnu.org/licenses/agpl-3.0.html
	
	@license-end
*/

angular.module('FrontEndApp').controller('DetailsController', ['$scope', 'SharedChartService', function ($scope, SharedChartService) {	
	//init data binding
	$scope.test = SharedChartService.chartData;
	$scope.chartObject = SharedChartService.chartObject;
	
	//init default chart data (not really needed)
	$scope.chartObject.type = "LineChart";
	
	$scope.chartObject.data = {
		"cols": [
			{id: "t", label: "Topping", type: "string"},
			{id: "s", label: "Slices", type: "number"}
		],
		"rows": [
			{c: [
				{v: "3"},
				{v: 3},
			]},
			{c: [
				{v: "2"},
				{v: 0}
			]},
			{c: [
				{v: "1"},
				{v: 0},
			]},
			{c: [
				{v: '0'},
				{v: 0},
			]}
		]
	};
	$scope.chartObject.options = {
		'titlePosition': 'none',
		"hAxis": {
			"title": 'Days Ago'
		}
	};
	
}]);
