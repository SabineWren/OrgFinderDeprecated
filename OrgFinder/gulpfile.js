var gulp        = require('gulp');
var babel       = require('gulp-babel');
var concat      = require('gulp-concat');
var eventStream = require('event-stream');
var order       = require("gulp-order");
var minifyCSS   = require('gulp-minify-css');
var uglify      = require('gulp-uglify');

var embedTemplates = require('gulp-angular-embed-templates');

var smoosher       = require('gulp-smoosher');

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

gulp.task('stylesheet', function() {
	return gulp.src('frontEnd/css/stylesheet.css')
	.pipe(minifyCSS())
	.pipe(concat('stylesheet.min.css'))
	.pipe(gulp.dest('frontEnd/build'));
});

gulp.task('css', ['stylesheet'],function() {
	var slider = gulp.src('AngularJS/angularjs-slider/dist/rzslider.css');
	var controls = gulp.src('frontEnd/css/userControls.css');
	var view     = gulp.src('frontEnd/css/views.css');
	var details  = gulp.src('frontEnd/css/details.css');
	
	return eventStream.merge(slider, controls, view, details)
	.pipe(order([
		'AngularJS/angularjs-slider/dist/rzslider.css',
		'frontEnd/css/userControls.css',
		'frontEnd/css/views.css',
		'frontEnd/css/details.css'
	], { base: process.cwd() }))
	.pipe(minifyCSS())
	.pipe(concat('styles.min.css'))
	.pipe(gulp.dest('frontEnd/build'));

});

gulp.task('inlineSourceIndex', ['css', 'scripts'], function () {
	return gulp.src('./OrgFinder-dev.html')
	.pipe(smoosher())
	.pipe(concat('OrgFinder.html'))
	.pipe(gulp.dest('./'));
});

