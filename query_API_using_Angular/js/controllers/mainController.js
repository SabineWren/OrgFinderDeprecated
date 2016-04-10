app.controller('MainController', ['$scope', 'membersObject', function($scope, membersObject) {
  		membersObject.success(function(data) { 
				$scope.members = data;
  });
  
}]);
