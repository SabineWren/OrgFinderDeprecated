FrontEndApp.factory('checkboxService', function ($resource) {
    return $resource('frontEnd/data/:file',{file: "@file"});
});
