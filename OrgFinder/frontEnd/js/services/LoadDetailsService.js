/*
	@license magnet:?xt=urn:btih:0b31508aeb0634b347b8270c7bee4d411b5d4109&dn=agpl-3.0.txt
	
	Copyright (C) 2016 SabineWren
	
	GNU AFFERO GENERAL PUBLIC LICENSE Version 3, 19 November 2007
	https://www.gnu.org/licenses/agpl-3.0.html
	
	@license-end
*/

FrontEndApp.factory('LoadDetailsService', ['$http', function($http){
	
	//we only label up to 7 points, so pick which ones
	function getNextValue(currentValue, domainSizeMinusOne){
		var numPointsToSkip = Math.floor(domainSizeMinusOne / 7);
		return (currentValue - numPointsToSkip - 1);
	}
	
	//build a list of 1 to 7 labels, with blanks inserted to fill out the data points
	function setDomainLabels(AgeOfOrg, MostRecentScrape){
	
		var currentValue  = AgeOfOrg;
		var nextValue     = 0;
		var skippedValues = 0;
		var drawElement   = true;
		var newLabels     = [];
		
		newLabels.push( currentValue.toString() );
		for(; currentValue > MostRecentScrape; currentValue = nextValue){
			nextValue = getNextValue(currentValue, AgeOfOrg);
			if(nextValue < 0){
				drawElement = false;
				nextValue = 0;
			}
			for(skippedValues = currentValue - nextValue - 1; skippedValues > 0; --skippedValues){
				newLabels.push('');
			}
			if(drawElement)newLabels.push( nextValue.toString() );
			else newLabels.push("");
		}
		
		config.labels = newLabels;
	}
	
	var config = {
		series:  ['Size', 'Main', 'Affiliate', 'Hidden'],
		labels: [],
		options: {
			elements: {
				line: {
					fill: false,
					borderWidth: 4
				}
			},
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
					gridLines:{
						color: "rgba(255,255,255,1.0)",
						drawOnChartArea: false,
						zeroLineWidth: 2
					},
					ticks: {
						suggestedMin: 0,
						stepSize: 1,
						callback: function(tickValue, index, ticks) {
							if(!(index % parseInt(ticks.length / 5))) {
								return tickValue;
							}
						}
					}
				}],
				xAxes: [{
					gridLines:{
						color: "rgba(255,255,255,1.0)",
						drawOnChartArea: false,
						drawTicks: false
					}
				}]
			}
		}
	}
	
	var chartData = {
		plots: [],
	}
	
	var rowData = {
		result: {}
	};
	
	function linearInterpolate(oldValue, newValue, daysApart){
		var dailyGrowth = (newValue - oldValue) / daysApart;
		return dailyGrowth + oldValue;
	}
	
	var parseHistory = function(history_json){
		var DaysAgoValue = history_json[0].DaysAgo;
		setDomainLabels(DaysAgoValue, history_json[history_json.length - 1].DaysAgo);
		
		var Size      = [];
		var Main      = [];
		var Affiliate = [];
		var Hidden    = [];
		var axisLabels = [];
		
		var oldValues = {
			Size: 0,
			Main: 0,
			Affiliate: 0,
			Hidden: 0,
			lastDay: history_json[0].DaysAgo
		}
		//replace chart data
		for(date in history_json){
			var days = history_json[date].DaysAgo;//when this data entry was scraped
			
			//linearly interpolate missing data entries
			while(DaysAgoValue !== days && DaysAgoValue >= 0){
				oldValues.Size      = (linearInterpolate( oldValues.Size,      history_json[date].Size,      (oldValues.lastDay - days) )  );
				oldValues.Main      = (linearInterpolate( oldValues.Main,      history_json[date].Main,      (oldValues.lastDay - days) )  );
				oldValues.Affiliate = (linearInterpolate( oldValues.Affiliate, history_json[date].Affiliate, (oldValues.lastDay - days) )  );
				oldValues.Hidden    = (linearInterpolate( oldValues.Hidden,     history_json[date].Hidden,    (oldValues.lastDay - days) )  );
				
				Size.push(     oldValues.Size);
				Main.push(     oldValues.Main);
				Affiliate.push(oldValues.Affiliate);
				Hidden.push(   oldValues.Hidden);
				
				oldValues.lastDay = DaysAgoValue;
				--DaysAgoValue;
			}
			//add today
			Size.push( history_json[date].Size );
			Main.push( history_json[date].Main );
			Affiliate.push( history_json[date].Affiliate );
			Hidden.push( history_json[date].Hidden );
			
			oldValues.Size      = history_json[date].Size;
			oldValues.Main      = history_json[date].Main;
			oldValues.Affiliate = history_json[date].Affiliate;
			oldValues.Hidden    = history_json[date].Hidden;
			oldValues.lastDay   = DaysAgoValue;
			
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
		document.getElementById('org-greeting').innerHTML  = description_json[0].Headline;
		document.getElementById('org-manifesto').innerHTML = description_json[0].Manifesto;
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
			"width" : document.getElementById('grid-view-results').offsetWidth.toString() + "px"
		}
	};
	
	return {
		config,
		chartData,
		rowData,
		loadDetails,
		widthDetails,
		gridWidthModifer
	};
	
}]);
