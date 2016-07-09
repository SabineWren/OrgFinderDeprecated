FrontEndApp.controller('MemberController', ['$scope', '$http', 'readFileService', 'getMembersService', function($scope, $http, readFileService, getMembersService) {
	
	$scope.orgDataArray = [];
	
	var query = readFileService.query({file: "orgList.json"});
	query.$promise.then(function(data){
		$scope.orgData = data;
		
		for(var org in $scope.orgData){
			if(!isNaN(org)){//ignore promise and resolved
				var results = getMembersService.get({orgName: $scope.orgData[org].SID});
				results.$promise.then(function(apiObject){
					for(member in apiObject.data){
						$scope.orgDataArray.push(apiObject.data[member].handle);
					}
				});//end members subquery
			}
		}
	});//end orgList query
}]);

