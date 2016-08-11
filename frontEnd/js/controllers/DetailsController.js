
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
