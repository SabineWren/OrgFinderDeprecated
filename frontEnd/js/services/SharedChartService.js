//send data to chart
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
