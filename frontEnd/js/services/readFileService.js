FrontEndApp.factory('readFileService', function ($resource) {
    return $resource('data/:file',{file: "@file"});
});
