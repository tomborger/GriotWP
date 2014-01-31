jQuery( document ).ready( function() { 

	// Prepare WP environment
	jQuery( '#post-body-content' )

		// Define Angular app and controller
		.attr({
			'ng-app':'miaAuthor',
			'ng-controller':'miaAuthorCtrl'
		})

		// Create container for application
		.append( '<application></application>' )

		// Link title to model
		.find( '#title' ).attr({
			'ng-model':'data.title'
		});

	// Define main module
	var miaAuthor = angular.module( 'miaAuthor', [] );

	// Define main controller
	miaAuthor.controller( 'miaAuthorCtrl', function( $scope ) { 

		$scope.recordType = miaAuthorData.recordType;
		$scope.data = miaAuthorData.data ? JSON.parse( miaAuthorData.data ) : {};
		$scope.data.title = miaAuthorData.title;

		$scope.addRepeatable = function( prop ) {

			if( ! $scope.data.hasOwnProperty( prop ) ) {

				$scope.data[ prop ] = [];

			}

			var repeater = $scope.data[ prop ];

			repeater.push( {} );

		};

		$scope.removeRepeatable = function( prop, index ) {

			var repeater = $scope.data[ prop ];

			repeater.splice( index, 1 );

		}

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

	// Field containers
	miaAuthor.directive( 'field', function() {

		return {
			restrict: 'E',
			replace: true,
			transclude: true,
			template: function( elem, attrs ) {
				return "<div class='mia-author-field-wrap'><label>" + attrs.label + "</label><div class='mia-author-field' ng-transclude></div></div>";
			}
		};

	});

	// Repeaters!
	miaAuthor.directive( 'fieldgroup', function() {

		return {
			restrict: 'E',
			replace: true,
			transclude: true,
			template: function( elem, attrs ) {
				return "<div class='mia-author-field-wrap'><label>" + attrs.label + "</label><div ng-click='addRepeatable( \"" + attrs.prop + "\" )' >Add</div><div class='mia-author-fieldgroup-wrap' ng-repeat='elem in data." + attrs.prop + "'><div ng-click='removeRepeatable( \"" + attrs.prop + "\", $index )'>Remove</div><div class='mia-author-fieldgroup-item' ng-transclude></div></div></div>"; 
			}
		};

	});





	// Manually initialize Angular after WP environment is set up
	angular.bootstrap( document, ['miaAuthor'] );

});