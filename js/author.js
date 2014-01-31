jQuery( document ).ready( function() { 

	// Prepare WP environment
	jQuery( '#post-body-content' )

		// Define Angular app and controller
		.attr({
			'ng-app':'miaAuthor',
			'ng-controller':'miaAuthorCtrl'
		})

		// Create container for application
		.append('<application></application>');

	// Define main module
	var miaAuthor = angular.module( 'miaAuthor', [] );

	// Define main controller
	miaAuthor.controller( 'miaAuthorCtrl', function( $scope ) { 

		$scope.recordType = miaAuthorData.recordType;
		$scope.json = miaAuthorData.json;

	});

	// Set up workspace
	miaAuthor.directive( 'application', function() {

		return {
			restrict: 'E',
			replace: true,
			templateUrl: function() {
				return miaAuthorData.templateUrl;
			}
		};

	});

	// Manually initialize Angular after WP environment is set up
	angular.bootstrap( document, ['miaAuthor'] );

});