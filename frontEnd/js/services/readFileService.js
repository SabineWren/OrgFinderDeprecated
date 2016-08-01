/*
	@license magnet:?xt=urn:btih:0b31508aeb0634b347b8270c7bee4d411b5d4109&dn=agpl-3.0.txt
	
	Copyright (C) 2016 LucFauvel and SabineWren
	
	GNU AFFERO GENERAL PUBLIC LICENSE Version 3, 19 November 2007
	https://www.gnu.org/licenses/agpl-3.0.html
	
	@license-end
*/

FrontEndApp.factory('readFileService', function ($resource) {
    return $resource('data/:file',{file: "@file"});
});

FrontEndApp.factory('getOrgsService', function ($resource) {
    return $resource('/backEnd/selects.php/?pagenum=:pagenum',{pagenum: "@pagenum"});
});
