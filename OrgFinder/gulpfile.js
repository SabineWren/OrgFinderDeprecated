var gulp = require('gulp');
var babel = require('gulp-babel');
var concat = require('gulp-concat');
var eventStream = require('event-stream');
var uglify = require('gulp-uglify');

var directory = "frontEnd/js/";

gulp.task('scripts', function () {
	var angularModule      = gulp.src(directory + 'FrontEndModule.js');
	var angularControllers = gulp.src(directory + 'controllers/*.js');
	var angularDirectives  = gulp.src(directory + 'directives/*.js');
	var angularServices    = gulp.src(directory + 'services/*.js');

	return eventStream.merge(angularModule, angularControllers, angularDirectives, angularServices)
	//return eventStream.merge(angularServices)
	.pipe(concat('local_scripts.min.js'))
	.pipe(babel({presets: ['es2015']}))
	.pipe(uglify())
	.pipe(gulp.dest(directory + 'build'));
});

