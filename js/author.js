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

		$scope.data = miaAuthorData.data ? JSON.parse( miaAuthorData.data ) : {};
		$scope.data.recordType = miaAuthorData.recordType;
		$scope.data.title = miaAuthorData.title;

		$scope.addRepeatable = function( collection ) {

			if( ! $scope.data.hasOwnProperty( collection ) ) {

				$scope.data[ collection ] = [];

			}

			var repeater = $scope.data[ collection ];

			repeater.push( {} );

			setTimeout( function(){
				$scope.swiper.reInit();
				$scope.swiper.swipeTo( $scope.swiper.slides.length - 1 )
			}, 100 );

		};

		$scope.removeRepeatable = function( collection, index ) {

			var repeater = $scope.data[ collection ];

			repeater.splice( index, 1 );

			setTimeout( function(){
				$scope.swiper.reInit();
				$scope.swiper.swipeTo( $scope.swiper.activeIndex );
			}, 200 );

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
				return "<div class='mia-author-field-wrap'>" +
					"<label>" + attrs.label + "</label>" +
					"<div class='mia-author-button' ng-click='addRepeatable( \"" + attrs.collection + "\" )' >Add</div>" +
					"<div class='swiper-container'>" +
						"<div class='swiper-wrapper'>" +
							"<div class='mia-author-fieldgroup-wrap swiper-slide' ng-repeat='elem in data." + attrs.collection + "'>" +
								"<div class='mia-author-button' ng-click='removeRepeatable( \"" + attrs.collection + "\", $index )'>Remove</div>" +
								"<div class='mia-author-fieldgroup-item' ng-transclude></div>" +
							"</div>"+
						"</div>" +
					"</div>" +
				"</div>"; 
			},
			link: function( scope, elem, attrs ) {
				setTimeout( function(){ 
					var swiper = new Swiper( '.swiper-container', {
						slidesPerView: '1.05'
					});
					console.log( swiper.slides );
					scope.swiper = swiper;
				}, 200 );
			}
		};

	});





	// Manually initialize Angular after WP environment is set up
	angular.bootstrap( document, ['miaAuthor'] );

});