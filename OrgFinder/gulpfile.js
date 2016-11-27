var gulp        = require('gulp');
var babel       = require('gulp-babel');
var concat      = require('gulp-concat');
var eventStream = require('event-stream');
var order       = require("gulp-order");
var minifyCSS   = require('gulp-minify-css');
var uglify      = require('gulp-uglify');
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

gulp.task('css', function() {
	var slider = gulp.src('AngularJS/angularjs-slider/dist/rzslider.css');
	var controls = gulp.src('frontEnd/css/userControls.css');
	var view     = gulp.src('frontEnd/css/views.css');
	var details  = gulp.src('frontEnd/css/details.css');
	var style    = gulp.src('frontEnd/css/stylesheet.css');
	
	return eventStream.merge(slider, controls, view, details, style)
	.pipe(order([
		'AngularJS/angularjs-slider/dist/rzslider.css',
		'frontEnd/css/userControls.css',
		'frontEnd/css/views.css',
		'frontEnd/css/details.css',
		'frontEnd/css/stylesheet.css'
	], { base: process.cwd() }))
	.pipe(minifyCSS())
	.pipe(concat('style.min.css'))
	.pipe(gulp.dest('frontEnd/build'));

});

