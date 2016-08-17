/*
	@license magnet:?xt=urn:btih:0b31508aeb0634b347b8270c7bee4d411b5d4109&dn=agpl-3.0.txt
	
	Copyright (C) 2016 SabineWren
	
	GNU AFFERO GENERAL PUBLIC LICENSE Version 3, 19 November 2007
	https://www.gnu.org/licenses/agpl-3.0.html
	
	@license-end
*/

FrontEndApp.factory('LoadDetailsService', ['$http', function($http){
	
	var chartData = {
		plots: []
	}
	
	var descriptionData = {
		Headline: "",
		Manifesto: ""
	}
	
	var rowData = {
		result: {}
	};
	
	var parseHistory = function(history_json){
		var Size      = [];
		var Main      = [];
		var Affiliate = [];
		var Hidden    = [];
		for(date in history_json){
			Size.push( history_json[date].Size );
			Main.push( history_json[date].Main );
			Affiliate.push( history_json[date].Affiliate );
			Hidden.push( history_json[date].Hidden );
		}
		chartData.plots = [
			Size,
			Main,
			Affiliate,
			Hidden
		];
	};
	
	var parseDescription = function(description_json){
		descriptionData.Headline  = description_json[0].Headline;
		descriptionData.Manifesto = description_json[0].Manifesto;
	};
	
	var loadDetails = function(currentRow){
		
		rowData.result = currentRow;
		
		$http.get('/backEnd/org_history.php', { 
			params:{
				SID: currentRow.SID
			}
		} ).success(parseHistory);
		
		$http.get('/backEnd/org_description.php', { 
			params:{
				SID: currentRow.SID
			}
		} ).success(parseDescription);
		
	};
	
	var widthDetails = {
		data: {
			"width" : "100px"
		}
	};
	
	return {
		chartData,
		descriptionData,
		rowData,
		loadDetails,
		widthDetails
	};
	
}]);
