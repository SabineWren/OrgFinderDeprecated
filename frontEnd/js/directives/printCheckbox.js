FrontEndApp.directive('printCheckbox', function() {

	return { 
		restrict: 'E',
		scope: true,
	
		controller: function($scope){
			$scope.checked = 0;
		
			$scope.callCheckChanged = function(box) {
		
				for (var i=0; i < $scope.$parent.checkboxModels.length; i++) {
					if($scope.$parent.checkboxModels[i].category === $scope.checkboxModel.category) break;
				}
			
				if(box.isSelected){
					$scope.$parent.checkedOuter.num++;
					$scope.checked++;
					$scope.$parent.checkboxModels[i].appliedFilter.push(box.name);
				}
				else{
					$scope.$parent.checkedOuter.num--;
					$scope.checked--;
					var index = $scope.$parent.checkboxModels[i].appliedFilter.indexOf(box.name);
					$scope.$parent.checkboxModels[i].appliedFilter.splice(index, 1)
				}
			};
		},
    templateUrl: 'frontEnd/js/directives/printCheckbox.html'
  }; 
});
