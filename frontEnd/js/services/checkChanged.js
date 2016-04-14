/* @Description: Service to check whether or not any boxes are checked inside a checkbox model */
FrontEndApp.factory('checkChangedService', function() {
    return {
        checkChanged: function(box, $scope) {
            if(box.isSelected){
            	$scope.checked++;
            	$scope.$parent.checkedOuter++;
            	$scope.appliedFilters.push(box.name);
            }
			else{
				$scope.checked--;
				$scope.$parent.checkedOuter--;
				$scope.appliedFilters.splice($scope.appliedFilters.indexOf(box.name), 1);
			}
        }
    };
});
