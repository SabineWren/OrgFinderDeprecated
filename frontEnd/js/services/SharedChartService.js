/*
	@license magnet:?xt=urn:btih:0b31508aeb0634b347b8270c7bee4d411b5d4109&dn=agpl-3.0.txt
	
	Copyright (C) 2016 LucFauvel and SabineWren
	
	GNU AFFERO GENERAL PUBLIC LICENSE Version 3, 19 November 2007
	https://www.gnu.org/licenses/agpl-3.0.html
	
	@license-end
*/

FrontEndApp.factory('SharedChartService', function(){
	var chartData = {
		data: [0, 0, 0, 0, 0],
		redraw: false
	}
	
	var chartObject = {};
	
	return{
		chartData,
		chartObject,
		updateChart: function(newData){
			chartData.data = newData;
			
			chartObject.data = {
				"cols": [
					{id: "t", label: "Topping", type: "string"},
					{id: "s", label: "Slices", type: "number"}
				],
				"rows": [
					{c: [
						{v: "3"},
						{v: chartData.data[3]},
					]},
					{c: [
						{v: "2"},
						{v: chartData.data[2]}
					]},
					{c: [
						{v: "1"},
						{v: chartData.data[1]},
					]},
					{c: [
						{v: '0'},
						{v: chartData.data[0]},
					]}
				]
			};
		}//end update
	};
	
});
