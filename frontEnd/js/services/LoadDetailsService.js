/*
	@license magnet:?xt=urn:btih:0b31508aeb0634b347b8270c7bee4d411b5d4109&dn=agpl-3.0.txt
	
	Copyright (C) 2016 SabineWren
	
	GNU AFFERO GENERAL PUBLIC LICENSE Version 3, 19 November 2007
	https://www.gnu.org/licenses/agpl-3.0.html
	
	@license-end
*/

FrontEndApp.factory('LoadDetailsService', ['$http', function($http){
	
	var chartData = {
		plots: [
			[65, 59, 80, 81, 56, 55, 40],
			[28, 48, 40, 19, 86, 27, 90]
		]
	}
	
	var parseHistory = function(history_json){
		var Size = [];
		var Main = [];
		for(date in history_json){
			Size.push( history_json[date].Size );
			Main.push( history_json[date].Main );
		}
		chartData.plots = [
			Size,
			Main
		];
	};
	
	var loadDetails = function(SID){
		console.log(SID);
		
		$http.get('/backEnd/org_history.php', { 
			params:{
				SID: SID
			}
		} ).success(parseHistory);
		
	};
	
	return {
		chartData,
		loadDetails
	};
	
}]);
