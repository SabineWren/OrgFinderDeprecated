/*
	@license magnet:?xt=urn:btih:0b31508aeb0634b347b8270c7bee4d411b5d4109&dn=agpl-3.0.txt
	
	Copyright (C) 2016 LucFauvel and SabineWren
	
	GNU AFFERO GENERAL PUBLIC LICENSE Version 3, 19 November 2007
	https://www.gnu.org/licenses/agpl-3.0.html
	
	@license-end
*/

FrontEndApp.directive('printCheckbox', function() {

	return { 
		restrict: 'E',
		scope: true,
	
		controller: function($scope){
		
			$scope.callCheckChanged = function(box) {
				
				for (var i=0; i < $scope.$parent.checkboxModels.length; i++) {
					if($scope.$parent.checkboxModels[i].category === $scope.checkboxModel.category) break;
				}
			
				if(box.isSelected){
					$scope.$parent.checkedOuter.num++;
					$scope.$parent.checkboxModels[i].appliedFilter.push(box.name);
				}
				else{
					$scope.$parent.checkedOuter.num--;
					var index = $scope.$parent.checkboxModels[i].appliedFilter.indexOf(box.name);
					$scope.$parent.checkboxModels[i].appliedFilter.splice(index, 1)
				}
			};
		},
    templateUrl: 'frontEnd/js/directives/printCheckbox.html'
  }; 
});
