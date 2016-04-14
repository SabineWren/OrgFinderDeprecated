FrontEndApp.directive('printCheckbox', function() {

  return { 
    restrict: 'E',
    require: '^ngModel',
    
    scope: true,
	
	controller: function($scope){
		$scope.checked = 0;
		$scope.appliedFilters = [];
		$scope.$parent.checkedOuter = 50;
		
		$scope.callCheckChanged = function(box) {
			if(box.isSelected){
				$scope.$parent.checkedOuter++;
				$scope.checked++;
				$scope.appliedFilters.push(box.name);
			}
			else{
				$scope.$parent.checkedOuter--;
				$scope.checked--;
				$scope.appliedFilters.splice($scope.appliedFilters.indexOf(box.name), 1);
			}
		};
	},
    
    templateUrl: 'frontEnd/js/directives/printCheckbox.html'
  }; 
});
