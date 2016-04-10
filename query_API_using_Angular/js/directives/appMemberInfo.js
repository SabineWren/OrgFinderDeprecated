app.directive('appMemberInfo', function() { 
  return { 
    restrict: 'E', 
    scope: { 
      info: '=' 
    }, 
    templateUrl: 'js/directives/appMemberInfo.html' 
  }; 
});
