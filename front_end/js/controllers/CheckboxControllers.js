/* @Dependencies:
 * -- directive checkChanged.js */

FrontEndApp.controller('CheckboxCommitmentController', ['$scope', 'checkChangedService',function($scope, checkChangedService) {

	/* Checkbox Elements */
	$scope.checkboxModel = [
		{name: 'Casual', isSelected: false},
		{name: 'Regular', isSelected: false},
		{name: 'Hardcore', isSelected: false}
	];
	
	$scope.checked = 0;
	
	$scope.callCheckChanged = function(box){
		checkChangedService.checkChanged(box, $scope);
	}
	
}]);
