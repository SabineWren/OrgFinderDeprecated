insertsApp.filter('filterNulls', function(){
  return function(input){
    if( input != undefined && input != null ){
      return input;
    } else {
      return 0;
    }
  }
});
