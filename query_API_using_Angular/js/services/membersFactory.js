/**
app.factory('membersFactory', ['$http', function($http) { 
	return $http.get('http://www.sc-api.com/?api_source=cache&start_date=&end_date=&system=organizations&action=organization_members&target_id=PARAMC&start_page=1&end_page=1&format=json') 
            .success(function(data) { 
              return data; 
            }) 
            .error(function(err) { 
              return err; 
            }); 
}]);

app.factory( 'membersFactory', function ($http) {
    return { 
        getData: function(code, callback) { //note the callback argument
            $http.get("${createLink(controller:'kats', action:'loadBreedInfo')}",
            params:{code: code}}) //place your code argument here
                .success(function (data, status, headers, config) {
                    callback(data); //pass the result to your callback
                });
        };
    };
});


app.factory('membersFactory', function ($resource) {
    return $resource('URL/:SID/stuff', {
      SID : '@paramSID'
    });
  })
  **/
