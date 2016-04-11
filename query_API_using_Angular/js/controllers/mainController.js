/**
app.controller('MainController', ['$scope', 'membersFactory', function($scope, membersFactory) {

  		membersFactory.success(function(data) { 
				$scope.members = data;
 		});
 		
		$scope.getMembers = function() {
				membersFactory.getData($scope.SID, function(data) {
						$scope.members = data;
				});
		}
  
}]);
**/

insertsApp.controller('MainController', function($scope, $http) {
    $scope.members = {data: []}; //initialize to empty array so ng-repeat doesn't complain

	//sloppy because data should not be hard coded to a controller
	url_part_1 = 'http://www.sc-api.com/?api_source=cache&start_date=&end_date=&system=organizations&action=organization_members&target_id=';
	url_part_2 = '&start_page=1&end_page=1&format=json';
	$scope.SID = 'PARAMC';

    $scope.getMembers = function getMembers() {
      $http.get(url_part_1 + $scope.SID + url_part_2).then(function(result) {
        $scope.members = result.data;
      });
    }
    
  });
