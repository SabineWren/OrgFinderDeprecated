/* @Description: prove a resource get data from a local file
 * @Usage: from a controller, $scope.<data object> = checkboxService.query(<filename>); */
FrontEndApp.factory('checkboxService', function ($resource) {
    return $resource('frontEnd/data/:file',{file: "@file"});
});
