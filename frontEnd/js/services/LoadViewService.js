"use strict";
/*
	@license magnet:?xt=urn:btih:0b31508aeb0634b347b8270c7bee4d411b5d4109&dn=agpl-3.0.txt
	
	Copyright (C) 2016 SabineWren
	
	GNU AFFERO GENERAL PUBLIC LICENSE Version 3, 19 November 2007
	https://www.gnu.org/licenses/agpl-3.0.html
	
	@license-end
*/

FrontEndApp.factory('LoadViewService', function(){
	
	var orgResults = {
		results: [],
		nextPage: 0
	};
	
	var icons = {
		icons: {
			"Bounty Hunting": "frontEnd\/images\/activities\/Bounty_hunting.png",
			"Engineering":    "frontEnd\/images\/activities\/Engineering.png",
			"Exploration":    "frontEnd\/images\/activities\/Exploration.png",
			"Freelancing":    "frontEnd\/images\/activities\/Freelancing.png",
			"Infiltration":   "frontEnd\/images\/activities\/Infiltration.png",
			"Piracy":         "frontEnd\/images\/activities\/Piracy.png",
			"Resources":      "frontEnd\/images\/activities\/Resources.png",
			"Scouting":       "frontEnd\/images\/activities\/Scouting.png",
			"Security":       "frontEnd\/images\/activities\/Security.png",
			"Smuggling":      "frontEnd\/images\/activities\/Smuggling.png",
			"Social":         "frontEnd\/images\/activities\/Social.png",
			"Trading":        "frontEnd\/images\/activities\/Trade.png",
			"Transport":      "frontEnd\/images\/activities\/Transport.png"
		}
	};
	
	var clearResults = function(){
		orgResults.results = [];
		orgResults.nextPage = 0;
	};
	
	//arrows for sorting
	var sortStatus = {
		nameDouble    : true,
		nameAscending : false,
		nameDescending: false,
		
		sizeDouble    : true,
		sizeAscending : false,
		sizeDescending: false,
		
		mainDouble    : true,
		mainAscending : false,
		mainDescending: false,
		
		growthDouble    : false,
		growthAscending : false,
		growthDescending: true
	};
	
	function clearSorting(){
		sortStatus.nameDouble      = true;
		sortStatus.nameAscending   = false;
		sortStatus.nameDescending  = false;
		
		sortStatus.sizeDouble      = true;
		sortStatus.sizeAscending   = false;
		sortStatus.sizeDescending  = false;
		
		sortStatus.mainDouble      = true;
		sortStatus.mainAscending   = false;
		sortStatus.mainDescending  = false;
		
		sortStatus.growthDouble      = true;
		sortStatus.growthAscending   = false;
		sortStatus.growthDescending  = false;
	}
	
	var loadStatus = {
		isLoading: false
	};
	
	var focusFilterType = {
		restrictToPrimary: false
	};
	
	function callbackParseSelection(data){
		if(data === "null\n"){
			console.log(data);
			console.log("No more orgs found!\n");
			alert("No more orgs found!\n");
		}
		else{
			var icon = "";
			Object.keys(data).forEach(function(obj){
				if( data[obj].CustomIcon === 1 ){
					icon = "../org_icons/" + data[obj].SID;
				}
				else{
					icon = "frontEnd/org_icons_default/" + data[obj].Archetype + ".jpg";
				}
				
				//if the insertion scripts broke and DB allowed null
				if( data[obj].GrowthRate == null ){
					data[obj].GrowthRate = 0.0
				}
				
				orgResults.results.push({
					SID  : data[obj].SID,
					Name : data[obj].Name,
					Size : data[obj].Size,
					Main : data[obj].Main,
					Icon : icon,
					URL  : "https://robertsspaceindustries.com/orgs/" + data[obj].SID,
					GrowthRate     : data[obj].GrowthRate.toFixed(1),
					Commitment     : data[obj].Commitment,
					Recruiting     : data[obj].Recruiting,
					Language       : data[obj].Language,
					Roleplay       : data[obj].Roleplay,
					Archetype      : data[obj].Archetype,
					PrimaryFocus   : data[obj].PrimaryFocus,
					SecondaryFocus : data[obj].SecondaryFocus,
					PrimaryIcon    : icons.icons[ data[obj].PrimaryFocus ],
					SecondaryIcon  : icons.icons[ data[obj].SecondaryFocus ]
				});
			});
		}
		loadStatus.isLoading = false;
	}
	
	return {
		callbackParseSelection,
		orgResults,
		loadStatus,
		focusFilterType,
		sortStatus,
		clearSorting,
		clearResults,
		icons
	};
	
});
