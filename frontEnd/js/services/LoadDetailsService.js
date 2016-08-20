/*
	@license magnet:?xt=urn:btih:0b31508aeb0634b347b8270c7bee4d411b5d4109&dn=agpl-3.0.txt
	
	Copyright (C) 2016 SabineWren
	
	GNU AFFERO GENERAL PUBLIC LICENSE Version 3, 19 November 2007
	https://www.gnu.org/licenses/agpl-3.0.html
	
	@license-end
*/

FrontEndApp.factory('LoadDetailsService', ['$http', function($http){
	
	//we only label up to 12 points, so pick which ones
	function getNextValue(currentValue, domainSizeMinusOne){
		var numPointsToSkip = Math.floor(domainSizeMinusOne / 12);
		return (currentValue - numPointsToSkip - 1);
	}
	
	//build a list of 1 to 12 labels, with blanks inserted to fill out the data points
	function setDomainLabels(AgeOfOrg, MostRecentScrape){
	
		var currentValue = AgeOfOrg;
		var nextValue    = 0;
		var skippedValues= 0;
		var newLabels    = [];
		
		newLabels.push( currentValue.toString() );
		for(; currentValue > MostRecentScrape; currentValue = nextValue){
			nextValue = getNextValue(currentValue, AgeOfOrg);
			for(skippedValues = currentValue - nextValue - 1; skippedValues > 0; --skippedValues){
				newLabels.push('');
			}
			newLabels.push( nextValue.toString() );
		}
		
		config.labels = newLabels;
	}
	
	var config = {
		series:  ['Size', 'Main', 'Affiliate', 'Hidden'],
		labels: [],
		colours: ["#FFFFFF", "#FFAA44", "#00FF00", "#FF0000"],
		options: {
			responsive: true,
			legend: {
				display: true,
				position: 'top',
				fullWidth: true,
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
	
	var chartData = {
		plots: [],
	}
	
	var descriptionData = {
		Headline: "",
		Manifesto: ""
	}
	
	var rowData = {
		result: {}
	};
	
	var parseHistory = function(history_json){
		var DaysAgoValue = history_json[0].DaysAgo;
		setDomainLabels(DaysAgoValue, history_json[history_json.length - 1].DaysAgo);
		
		var Size      = [];
		var Main      = [];
		var Affiliate = [];
		var Hidden    = [];
		var axisLabels = [];
		
		var interpolateValues = {
			Size: 0,
			Main: 0,
			Affiliate: 0,
			Hidden: 0
		}
		//replace chart data
		for(date in history_json){
			var days = history_json[date].DaysAgo;//when this data entry was scraped
			//interpolate missing data entries with last known value (or 0 if it predates first scrape)
			while(DaysAgoValue !== days && DaysAgoValue >= 0){
				Size.push(      interpolateValues.Size );
				Main.push(      interpolateValues.Main );
				Affiliate.push( interpolateValues.Affiliate );
				Hidden.push(    interpolateValues.Hidden  );
				--DaysAgoValue;
			}
			//add today
			Size.push( history_json[date].Size );
			Main.push( history_json[date].Main );
			Affiliate.push( history_json[date].Affiliate );
			Hidden.push( history_json[date].Hidden );
			
			interpolateValues.Size      = history_json[date].Size;
			interpolateValues.Main      = history_json[date].Main;
			interpolateValues.Affiliate = history_json[date].Affiliate;
			interpolateValues.Hidden    = history_json[date].Hidden;
			
			--DaysAgoValue;
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
	
	var gridWidthModifer = {
		data: {
			"width" : document.getElementById('gridViewResults').offsetWidth.toString() + "px"
		}
	};
	
	return {
		config,
		chartData,
		descriptionData,
		rowData,
		loadDetails,
		widthDetails,
		gridWidthModifer
	};
	
}]);
