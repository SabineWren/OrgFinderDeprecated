FrontEndApp.directive('printCheckbox', function() {

  return { 
    restrict: 'E',
    
    scope: true,
	
	controller: function($scope){
		$scope.checked = 0;
		$scope.appliedFilters = [];
		
		$scope.callCheckChanged = function(box) {
			if(box.isSelected){
				$scope.$parent.checkedOuter.num++;
				$scope.checked++;
				$scope.appliedFilters.push(box.name);
				//$scope.$parent.allAppliedFilters.push(box.name);
			}
			else{
				$scope.$parent.checkedOuter.num--;
				$scope.checked--;
				$scope.appliedFilters.splice($scope.appliedFilters.indexOf(box.name), 1);
				//$scope.$parent.allAppliedFilters.splice($scope.appliedFilters.indexOf(box.name), 1);
			}
			console.log($scope.$parent.checkedOuter);
		};
	},
    
    templateUrl: 'frontEnd/js/directives/printCheckbox.html'
  }; 
});
