/**
	Purppose of app is to upload images, and display images using Albus RESTful API, display images fancy with angular-masonry
*/
(function() {

	var app = angular.module('App', ['ngRoute', 'ngResource', 'wu.masonry']);

	app.config(['$routeProvider', function($routeProvider) {
		$routeProvider.when('/', {
			templateUrl: 'templates/home.html',
			resolve: {
				images: function($q, Image) {
					// Load images links before rendered
					var deferred = $q.defer();

					Image.query(function(response) {
						deferred.resolve(response);
					}, function(response) {
						deferred.reject(false);
					});
					return deferred.promise;
				}
			},
			controller: 'HomeCtrl'
		}).when('/:image', {
			templateUrl: 'templates/image.html',
			controller: 'ImageCtrl'
		}).otherwise({
			redirectTo: '/'
		});
	}]);

	// Main app controller over index.html
	app.controller('AppCtrl', ['$rootScope', '$scope', 'Image', function($rootScope, $scope, Image) {

		$rootScope.$on("$routeChangeStart", function (event, next, current) {
			$scope.loadingView = true;
		});

		$rootScope.$on("$routeChangeSuccess", function (event, current, previous) {
			$scope.loadingView = false;	
		});

		$scope.error = false;
		$scope.loading = false;
		$scope.upload = function() {
			$scope.loading = true;
			var fd = new FormData();
			fd.append('image', $scope.image);
		
			Image.save(fd, function(response) {
				$scope.loading = false;
				// success
				$scope.$broadcast('addImage', response);
				$scope.error = false;
			}, function(response) {
				// error
				$scope.loading = false;
				$scope.error = response.data;
			});
		};
	}]);

	app.controller('ImageCtrl', ['$scope', '$routeParams', function($scope, $routeParams) {
		$scope.image = $routeParams.image;
	}]);

	app.controller('HomeCtrl', ['$scope', 'images', function($scope, images) {
		$scope.links = images;

		$scope.$on('addImage', function(event, args) {
			console.log('adding image...');
			var image = args;
			image.src = image.src + '?thumb'; // Request thumb version
			$scope.links.unshift(image);
		});

		// always reload images, safari is weird and doesn't render correctly. Especially on iDevices
		// This has no effect on chrome, ie, firefox
		setTimeout(function() {
			$scope.$broadcast('reloadMasonry', true);
		}, 1000);

		// Manual reload of images
		// $scope.reload = function() {
		// 	console.log('sending request...');
		// 	$scope.$broadcast('reloadMasonry', true);
		// };
	}]);

	app.directive('fileModel', ['$parse', function($parse) {
		return {
			restrict: 'A',
			link: function(scope, element, attrs) {
				var model = $parse(attrs.fileModel);
				var modelSetter = model.assign;

	            element.bind('change', function(){
	                scope.$apply(function(){
	                    modelSetter(scope, element[0].files[0]);
	                });
	            });
			}
		};
	}]);

	app.factory('Image', ['$resource', function($resource) {
		return $resource('api/images/:image', {}, {
			save: {
				method: 'POST',
				transformRequest: angular.identity,
				headers: {
					'Content-Type': undefined
				}
			}
		});
	}]);

})();

