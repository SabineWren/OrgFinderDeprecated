insertsApp.filter('filterNulls', function(){
  return function(input){
    if( input != undefined && input != null ){
      return 1;
    } else {
      return 0;
    }
  }
});
