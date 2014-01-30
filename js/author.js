jQuery( document ).ready( function() { 

	// Append app container to #post-body-content
	// Is there a better way to do this?
	jQuery( '#post-body-content' ).attr({
		'ng-app':'miaAuthor',
		'ng-controller':'miaAuthorCtrl'
	}).append('<application></application>');

	// Define module
	var miaAuthor = angular.module( 'miaAuthor', [] );

	// Fields (as defined in PHP)
	miaAuthor.factory( 'Fields', function() {

		return miaAuthorData.fields;

	});

	// Template location
	miaAuthor.factory( 'Templates', function() {

		return miaAuthorData.templates;

	});

	// Controller
	miaAuthor.controller( 'miaAuthorCtrl', function( $scope, Fields, Templates ) { 

		$scope.fields = Fields;
		$scope.templates = Templates;

	});

	// Set up workspace
	miaAuthor.directive( 'application', function() {

		return {
			restrict: 'E',
			replace: true,
			templateUrl: function(){
				return miaAuthorData.template;
			},
			link: function( scope ) {
				console.log( scope.fields );
				console.log( scope.templates );
			}
		}

	});

	angular.bootstrap( document, ['miaAuthor'] );

});