app.factory('membersObject', ['$http', function($http) { 
  return $http.get('http://www.sc-api.com/?api_source=cache&start_date=&end_date=&system=organizations&action=organization_members&target_id=paramc&start_page=1&end_page=1&format=json') 
            .success(function(data) { 
              return data; 
            }) 
            .error(function(err) { 
              return err; 
            }); 
}]);
