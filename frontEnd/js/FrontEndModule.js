/*
	@license magnet:?xt=urn:btih:0b31508aeb0634b347b8270c7bee4d411b5d4109&dn=agpl-3.0.txt
	
	Copyright (C) 2016 LucFauvel and SabineWren
	
	GNU AFFERO GENERAL PUBLIC LICENSE Version 3, 19 November 2007
	https://www.gnu.org/licenses/agpl-3.0.html
	
	@license-end
*/

var FrontEndApp = angular.module('FrontEndApp', ['rzModule', 'googlechart']);

/* pass back and forth between A and B
angular.module('app.A', [])
.service('ServiceA', function() {
    this.getValue = function() {
        return this.myValue;
    };

    this.setValue = function(newValue) {
        this.myValue = newValue;
    }
});

angular.module('app.B', ['app.A'])
.service('ServiceB', function(ServiceA) {
    this.getValue = function() {
        return ServiceA.getValue();
    };

    this.setValue = function() {
        ServiceA.setValue('New value');
    }
});
*/
