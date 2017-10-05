/*	
	@license magnet:?xt=urn:btih:0b31508aeb0634b347b8270c7bee4d411b5d4109&dn=agpl-3.0.txt
	
	Copyright (C) 2016 SabineWren
	
	GNU AFFERO GENERAL PUBLIC LICENSE Version 3, 19 November 2007
	https://www.gnu.org/licenses/agpl-3.0.html
	
	@license-end
*/
FrontEndApp.controller('CheckboxController', ['$scope', '$http', 'LoadViewService', 'GlobalStateUI',
function($scope, $http, LoadViewService, GlobalStateUI){
	
	function btoi(theBool){
		if(theBool)return 1;
		else return 0;
	};
	
	var callbackParse = LoadViewService.callbackParseSelection;
	$scope.loadMoreOrgs = function(){
		$scope.loadStatus.isLoading = true;//callback sets to false when it's done
	
		//prevent DB from wasting time on bad size input
		if($scope.slider_bar_max.value > 0 && $scope.slider_bar_min.value > $scope.slider_bar_max.value)
			$scope.slider_bar_max.value = $scope.slider_bar_min.value;
	
		//only filter by size if needed
		var minSize = null;
		if($scope.slider_bar_min.value > 1 ){
			minSize = $scope.slider_bar_min.value.toString();
		}
		var maxSize = null;
		if($scope.slider_bar_max.value > 0)
			maxSize = $scope.slider_bar_max.value.toString();
	
		//only sort if needed
		var directionName = null;
		if($scope.sortStatus.nameAscending)directionName = 'up';
		else if($scope.sortStatus.nameDescending)directionName = 'down';
		var directionSize = null;
		if($scope.sortStatus.sizeAscending)directionSize = 'up';
		else if($scope.sortStatus.sizeDescending)directionSize = 'down';
		var directionMain = null;
		if($scope.sortStatus.mainAscending)directionMain = 'up';
		else if($scope.sortStatus.mainDescending)directionMain = 'down';
		var directionGrowth = null;
		if($scope.sortStatus.growthAscending)directionGrowth = 'up';
		else if($scope.sortStatus.growthDescending)directionGrowth = 'down';
		
		//optionally ignore secondary focus
		var restrictToPrimary = 0;
		if($scope.focusFilterType.restrictToPrimary)restrictToPrimary = 1;
	
		$http.get('backEnd/selects.php', { 
			params:{
				pagenum:    $scope.orgResults.nextPage,
				primary:    restrictToPrimary,
				NameOrSID:  encodeURI( $scope.filterName ),
				Manifesto:  encodeURI( $scope.filterManifesto ),
				nameDir:    directionName,
				sizeDir:    directionSize,
				mainDir:    directionMain,
				Growth:     directionGrowth,
				Min:        minSize,
				Max:        maxSize,
				Cog:        btoi($scope.Cog),
				OPPF:       btoi($scope.OPPF),
				STAR:       btoi($scope.STAR),
				Reddit:     btoi($scope.Reddit),
				Lang:       $scope.language,
				//numeric index break when we change order in the json -- BAD DESIGN
				Activity:   $scope.checkboxModels[0].appliedFilter.toString(),
				Archetype:  $scope.checkboxModels[4].appliedFilter.toString(),
				Commitment: $scope.checkboxModels[1].appliedFilter.toString(),
				Recruiting: $scope.checkboxModels[2].appliedFilter.toString(),
				Roleplay:   $scope.checkboxModels[3].appliedFilter.toString()
			}
		} ).success(callbackParse);
	
		$scope.orgResults.nextPage++;
	};

	// Init **********************************************************************************************************
	$scope.StateUI = GlobalStateUI.StateUI;
	
	$scope.pageSize = 12;
	$scope.Cog    = false;
	$scope.OPPF   = false;
	$scope.STAR   = false;
	$scope.Reddit = false;
	
	$scope.clearResults    = LoadViewService.clearResults;
	$scope.focusFilterType = LoadViewService.focusFilterType;
	$scope.sortStatus      = LoadViewService.sortStatus;
	$scope.loadStatus      = LoadViewService.loadStatus;
	$scope.orgResults      = LoadViewService.orgResults;
	
	$scope.reapplyFilters = function(){
		$scope.clearResults();
		$scope.loadMoreOrgs();
	};
	
	//this needs to be rebuilt more readably
	$scope.resetFilters = function(){
		for( var checkboxModel in $scope.checkboxModels){
			$scope.checkboxModels[checkboxModel].appliedFilter.length = 0;
			for(var checkboxLabel in $scope.checkboxModels[checkboxModel].data){
				$scope.checkboxModels[checkboxModel].data[ checkboxLabel ].isSelected = false;
			}
		}
		
		$scope.Cog                  = false;
		$scope.OPPF                 = false;
		$scope.STAR                 = false;
		$scope.Reddit               = false;
		$scope.slider_bar_min.value = 1;
		$scope.slider_bar_max.value = 0;
		$scope.language             = "Any";
		$scope.filterName           = "";
		$scope.filterManifesto      = "";
	};
	
	$scope.checkboxModels = [];
	$scope.language = "Any";
	$scope.filterName = "";
	$scope.filterManifesto = "";
	
	//END INIT ****************************************************************************************************
	
	$scope.slider_bar_min = {
		value: 1,
		options: {
			floor: 1,
			ceil: 100,
			step: 1,
			showSelectionBar: true,
			getSelectionBarColor: function(value) {
				if(value <= 15) return 'red';
				if(value <= 30) return 'orange';
				if(value <= 45) return 'yellow';
				return '#2AE02A';
			}
		}
	};
	
	$scope.slider_bar_max = {
		value: 0,
		options: {
			floor: 0,
			ceil: 100,
			step: 1,
			showSelectionBar: true,
			getSelectionBarColor: function(value) {
				if(value <= 20) return 'red';
				if(value <= 40) return 'orange';
				if(value <= 60) return 'yellow';
				return '#2AE02A';
			}
		}
	};
	
	//formerly saved in database for easy updates; now hard coded in the service for faster loading
	$scope.iconsObj = LoadViewService.icons;
	
	//we need a list of valid languages for the dropdown filter
	//formerly saved in a JSON file for easy updates
	//since it's unlikely to change, it's now hardcoded here for faster loading
	$scope.langs = [
		"Abkhazian","Amharic","Afar","Afrikaans","Akan","Albanian","Arabic","Aragonese","Armenian","Assamese","Avaric","Avestan","Aymara","Azerbaijani","Bambara","Bashkir","Basque","Belarusian","Bengali","Bihari languages","Bislama","Bokm\u00e5l, Norwegian","Bosnian","Breton","Bulgarian","Burmese","Catalan","Central Khmer","Chamorro","Chechen","Chichewa","Chinese","Church Slavic","Chuvash","Cornish","Corsican","Cree","Croatian","Czech","Danish","Dutch","Dzongkha","English","Esperanto","Estonian","Ewe","Faroese","Fijian","Finnish","French","Fulah","Gaelic","Galician","Ganda","Georgian","German","Greek, Modern","Guarani","Gujarati","Haitian Creole","Hausa","Hebrew","Herero","Hindi","Hiri Motu","Hungarian","Icelandic","Ido","Igbo","Indonesian","Interlingua","Interlingue","Inuktitut","Inupiaq","Irish","Italian","Japanese","Javanese","Kalaallisut","Kannada","Kanuri","Kashmiri","Kazakh","Kikuyu","Kinyarwanda","Kirghiz","Komi","Kongo","Korean","Kuanyama","Kurdish","Lao","Luba-Katanga","Latin","Latvian","Limburgan","Lingala","Lithuanian","Luxembourgish","Macedonian","Malagasy","Malay","Malayalam","Maldivian","Maltese","Manx","Maori","Marathi","Marshallese","Mongolian","Nauru","Navajo","Ndebele, North","Ndebele, South","Ndonga","Nepali","Northern Sami","Norwegian","Nynorsk, Norwegian","Occitan","Ojibwa","Oriya","Oromo","Ossetian","Pali","Panjabi","Persian","Polish","Portuguese","Pushto","Quechua","Romanian","Romansh","Rundi","Russian","Samoan","Sango","Sanskrit","Sardinian","Serbian","Shona","Sichuan Yi","Sindhi","Sinhala","Slovak","Slovenian","Somali","Sotho, Southern","Spanish","Sundanese","Swahili","Swati","Swedish","Tagalog","Tahitian","Tajik","Tamil","Tatar","Telugu","Thai","Tibetan","Tigrinya","Tonga","Tsonga","Tswana","Turkish","Turkmen","Twi","Uighur","Ukrainian","Urdu","Uzbek","Venda","Vietnamese","Volap\u00fck","Walloon","Welsh","Western Frisian","Wolof","Xhosa","Yiddish","Yoruba","Zhuang","Zulu"
	]
	
	//THIS IS THE WORST CODE IN THE ENTIRE APP -- SORRY
	// each list of checkboxes is stored as array elements in a single JSON file with its corresponding title
	//	in hindsight, everything checkbox related is really hard to read; don't try to reuse any of it
	//	we should rebuild it legibly at some point
		//hard coding the original JSON file here for faster loading (see next code block)
		var jsonData = [
		{
			"category": "Activities",
	
			"data":
			[
				{
					"name": "Bounty Hunting",
					"isSelected": false
				},
				{
					"name": "Engineering",
					"isSelected": false
				},
				{
					"name": "Exploration",
					"isSelected": false
				},
				{
					"name": "Freelancing",
					"isSelected": false
				},
				{
					"name": "Infiltration",
					"isSelected": false
				},
				{
					"name": "Piracy",
					"isSelected": false
				},
				{
					"name": "Resources",
					"isSelected": false
				},
				{
					"name": "Scouting",
					"isSelected": false
				},
				{
					"name": "Security",
					"isSelected": false
				},
				{
					"name": "Smuggling",
					"isSelected": false
				},
				{
					"name": "Social",
					"isSelected": false
				},
				{
					"name": "Trading",
					"isSelected": false
				},
				{
					"name": "Transport",
					"isSelected": false
				}
			]
		},

		{
			"category": "Commitment",
	
			"data":
			[
				{
					"name": "Casual",
					"isSelected": false
				},
				{
					"name": "Regular",
					"isSelected": false
				},
				{
					"name": "Hardcore",
					"isSelected": false
				}
			]
		},

		{
			"category": "Recruiting",
	
			"data":
			[
				{
					"name": "Yes",
					"isSelected": false
				},
				{
					"name": "No",
					"isSelected": false
				}
			]
		},

		{
			"category": "Roleplay",
	
			"data":
			[
				{
					"name": "Yes",
					"isSelected": false
				},
				{
					"name": "No",
					"isSelected": false
				}
			]
		},

		{
			"category": "Archetype",
	
			"data":
			[
				{
					"name": "Organization",
					"isSelected": false
				},
				{
					"name": "Corporation",
					"isSelected": false
				},
				{
					"name": "PMC",
					"isSelected": false
				},
				{
					"name": "Faith",
					"isSelected": false
				},
				{
					"name": "Syndicate",
					"isSelected": false
				}
			]
		}
	]
	for(var object in jsonData){
		$scope.checkboxModels.push({
			category:      jsonData[object].category, 
			appliedFilter: [], 
			data:          jsonData[object].data
		});
	}
	$scope.loadMoreOrgs();//once we have icons and checkboxes, query the database
	
	$scope.toggleView = function(){
		if($scope.StateUI.listViewTF)$scope.StateUI.listViewTF = false;
		else $scope.StateUI.listViewTF = true;
	};
	
	//ViewController can set sorting, which requires refiltering
	$scope.$on( 'loadMoreOrgs', $scope.loadMoreOrgs );
	
}]);

