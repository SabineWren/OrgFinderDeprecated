var gulp = require('gulp');
var babel = require('gulp-babel');
var concat = require('gulp-concat');
var eventStream = require('event-stream');
var minifyCSS = require('gulp-minify-css');
var uglify = require('gulp-uglify');
var embedTemplates = require('gulp-angular-embed-templates');

gulp.task('scripts', function () {
	var directory = "frontEnd/js/";
	var angularModule      = gulp.src(directory + 'FrontEndModule.js');
	var angularControllers = gulp.src(directory + 'controllers/*.js');
	var angularDirectives  = gulp.src(directory + 'directives/*.js');
	var angularServices    = gulp.src(directory + 'services/*.js');

	return eventStream.merge(angularModule, angularControllers, angularDirectives, angularServices)
	.pipe(embedTemplates())
	.pipe(concat('local_scripts.min.js'))
	.pipe(babel({presets: ['es2015']}))
	.pipe(uglify())
	.pipe(gulp.dest('frontEnd/build'));
});

//still figuring out how to combine css files; currently just changing the name of the file changes font types
gulp.task('css', function() {
	var slider = gulp.src('AngularJS/angularjs-slider/dist/rzslider.css');
	var local  = gulp.src('frontEnd/*.css');
	
	var style  = gulp.src('frontEnd/stylesheet.css');
	var view  = gulp.src('frontEnd/view.css');
	
	return eventStream.merge(style)
	//.pipe(minifyCSS())
	.pipe(concat('style.min.css'))
	.pipe(gulp.dest('frontEnd/build'));
});

