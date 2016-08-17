/*
	@license magnet:?xt=urn:btih:0b31508aeb0634b347b8270c7bee4d411b5d4109&dn=agpl-3.0.txt
	
	Copyright (C) 2016 SabineWren
	
	GNU AFFERO GENERAL PUBLIC LICENSE Version 3, 19 November 2007
	https://www.gnu.org/licenses/agpl-3.0.html
	
	@license-end
*/

FrontEndApp.factory('LoadViewService', function(){
	
	var orgData = function(SID, Name, Size, Main, Icon, URL){
		this.SID  = SID;
		this.Name = Name;
		this.Size = Size;
		this.Main = Main;
		this.Icon = Icon;
		this.URL  = URL;
	};
	
	var orgResults = {
		results: [],
		nextPage: 0
	};
	
	var icons = {
		icons: {}
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
	}
	
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
		isLoading: false,
		listViewTF: true
	};
	
	function callbackParseSelection(data){
		if(data === "null"){
			console.log("No more orgs found!\n");
			alert("No more orgs found!\n");
		}
		else for(obj in data){
			var icon = "";
			if( data[obj]["CustomIcon"] === 1 )icon = "/org_icons/" + data[obj]["SID"];
			else icon = "/frontEnd/org_icons_default/" + data[obj]["Archetype"] + ".jpg";
			
			var field = new orgData(
				data[obj]["SID"],
				data[obj]["Name"],
				data[obj]["Size"],
				data[obj]["Main"],
				icon,
				data[obj]["URL"]
			);
			
			field.Commitment     = data[obj]["Commitment"];
			
			field.Recruiting     = data[obj]["Recruiting"];
			
			field.Language       = data[obj]["Language"];
			
			field.Roleplay       = data[obj]["Roleplay"];
			
			field.Archetype      = data[obj]["Archetype"];
			
			field.PrimaryFocus   = data[obj]["PrimaryFocus"];
			field.SecondaryFocus = data[obj]["SecondaryFocus"];
			field.PrimaryIcon    = icons.icons[  data[obj]["PrimaryFocus"]  ];
			field.SecondaryIcon  = icons.icons[  data[obj]["SecondaryFocus"]  ];
			field.GrowthRate     = data[obj]["GrowthRate"];
			
			orgResults.results.push(field);
		}
		loadStatus.isLoading = false;
	};
	
	return{
		callbackParseSelection,
		orgResults,
		loadStatus,
		sortStatus,
		clearSorting,
		clearResults,
		icons
	};
	
});
