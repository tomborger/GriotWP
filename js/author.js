jQuery( document ).ready( function() { 


	// Prepare WP environment
	jQuery( '#post-body-content' )

		// Define Angular app and controller
		.attr({
			'ng-app':'miaAuthor',
			'ng-controller':'miaAuthorCtrl'
		})

		// Create application container and hidden content field
		.append( "<div id='mia-author'>" +
					"<textarea name='content' id='mia-author-data'>{{ data | json }}</textarea>" +
					"<fieldset></fieldset>" +
				"</div>" )

		// Link title field to model
		.find( '#title' ).attr({
			'ng-model':'data.title'
		});


	/**
	 * Main application module
	 */
	var miaAuthor = angular.module( 'miaAuthor', [] );


	/**
	 * ModelChain service
	 *
	 * Tells nested fields and repeaters where in the $scope.data object to store
	 * their data.
	 * 
	 * The 'modelChain' and 'model' parameters are stored in each directive's
	 * (isolated) scope and updated based on the parent scope's value. 
	 * 
	 * modelChain maintains an array representing the chain of elements above the 
	 * current field element, i.e. ['data', 'repeater1', '0', 'repeater2', '1', 
	 * 'fieldname' ]. model converts modelChain into a reference to the proper
	 * storage location in $scope.data. 
	 * 
	 * NOTE: model in fact resolves to the level just above the field name, so 
	 * that the directive template can define ng-model as 'model.fieldname' and 
	 * Angular can interpret it correctly. If one were to assign to model a 
	 * reference to the actual property (rather than its parent) and elegantly 
	 * define ng-model as 'model', Angular would not evaluate the path and would
	 * instead bind the field to the property on the LOCAL scope called 'model'.
	 * Many Bothans died to bring us this information.
	 */
	miaAuthor.factory( 'ModelChain', function() {

		return {

			/**
			 * Initialize the model chain with 'data' value.
			 * (Called in main controller)
			 */
			initialize: function( scope ) {

				scope.modelChain = ['data'];

			},

			/**
			 * Update modelChain and model and store in local scope.
			 * (Called in directives)
			 */
			update: function( scope, name ) {

				// Get parent scope modelChain
				scope.modelChain = angular.copy( scope.$parent.modelChain );

				// Check if this directive is the child of a repeater
				// If so, push repeater item index into model chain
				if( typeof scope.$parent.$parent.$index !== "undefined" ) {
					scope.modelChain.push( scope.$parent.$parent.$index );
				}

				// Cycle through model chain to create reference to main data object
				scope.model = scope;
				for( var i = 0; i < scope.modelChain.length; i++ ) {
					scope.model = scope.model[ scope.modelChain[ i ] ];
				}

				// After model has been created (without the field name), add field
				// name to model chain so children receive the complete path.
				scope.modelChain.push( name );

			}

		}

	});


	/**
	 * Application controller
	 *
	 * Initializes data object and scope chain
	 */
	miaAuthor.controller( 'miaAuthorCtrl', function( $scope, ModelChain ) { 

		// Initialize data object and load previously saved data into model. 
		// See MIA_Author::print_data() in class-mia-author.php
		$scope.data = miaAuthorData.data ? JSON.parse( miaAuthorData.data ) : {};
		$scope.data.title = miaAuthorData.title;
		$scope.data.recordType = miaAuthorData.recordType;

		// Initialize model chain
		ModelChain.initialize( $scope );

	});

	
	/**
	 * <fieldset> directive
	 * 
	 * Renders fields from template
	 */
	miaAuthor.directive( 'fieldset', function() {

		return{

			restrict: 'E',
			replace: true,
			templateUrl: function() {
				return miaAuthorData.templateUrl;
			}

		}

	});


	/**
	 * <field> directive
	 * 
	 * Renders individual <field> elements into container, label, and input and 
	 * populates ng-model attributes.
	 */
	miaAuthor.directive( 'field', function() {

		return {

			restrict: 'E',
			replace: true,
			scope: {
				data:'='
			},
			controller: function( $scope, $element, $attrs, ModelChain ) {

				ModelChain.update( $scope, $attrs.name );

			},
			template: function( elem, attrs ) {

				switch( attrs.type ){

					case 'text':
						fieldhtml = "<input type='text' ng-model='model." + attrs.name + "' />";
						break;

					case 'wysiwyg':
						fieldhtml = "<textarea ng-model='model." + attrs.name + "'></textarea>";
						break;

				}

				return "<div class='mia-author-field-wrap' data='data'>" +
					"<div class='mia-author-field-meta'><span class='mia-author-label'>" + attrs.label + "</span></div>" +
					"<div class='mia-author-field'>" +
						fieldhtml +
					"</div>" +
				"</div>";

			}

		};

	});


	/**
	 * <repeater> directive
	 *
	 * Renders repeaters and provides an API for manipulating each repeater's
	 * underlying Swiper object (which is stored in the local scope). Most of
	 * these methods are internal, but some may be called from other directives.
	 */
	miaAuthor.directive( 'repeater', function() {

		return {

			restrict: 'E',
			replace: true,
			scope: {
				data:'='
			},
			transclude: true,
			controller: function( $scope, $element, $attrs, $timeout, ModelChain ) {

				// Update model chain properties
				ModelChain.update( $scope, $attrs.name );

				// Visibility of repeater item menu
				$scope.showMenu = false;

				// Status of repeater nav options
				$scope.prevDisabled = true;
				$scope.nextDisabled = true;


				/**
				 * Register an empty repeater array in main data object
				 */ 
				$scope.register = function( repeaterName ) {

					if( ! $scope.model.hasOwnProperty( repeaterName ) ) {

						$scope.model[ repeaterName ] = [];

					}

				};


				/**
				 * Create Swiper instance and add to local scope
				 */
				$scope.build = function( repeaterElement ) {

					var repeater = repeaterElement.find( '.swiper-container' ).first().swiper({
						calculateHeight: true,
						pagination: repeaterElement.find( '.mia-author-repeater-pagination' ).first()[0],
						paginationClickable: true,
						paginationElementClass: 'mia-author-repeater-bullet',
						paginationActiveClass: 'active',
						onSlideChangeStart: function(){
      				$scope.refreshNav();
    				}
					});

					$scope.repeater = repeater;

				};


				/**
				 * Keep repeater nav in sync with repeater position
				 */
				$scope.refreshNav = function() {
					
					// Test ability to regress
					if( $scope.repeater.activeIndex === 0 ) {

						$timeout( function() {
							$scope.prevDisabled = true;
						});

					} else {

						$timeout( function() {
							$scope.prevDisabled = false;
						});

					}

					// Test ability to advance
					if( $scope.repeater.activeIndex === $scope.repeater.slides.length - 1 ) {

						$timeout( function() {
							$scope.nextDisabled = true;
						});

					} else {

						$timeout( function() {
							$scope.nextDisabled = false;
						});

					}

				};


				/** 
				 * Reinitialize (and redraw) Swiper instance
				 */
				$scope.refresh = function() {
					repeater.reInit();
					$scope.refreshNav();
				}


				/**
				 * Add an item to a repeater
				 */
				$scope.add = function( repeaterModel ) {

					$scope.showMenu = false;

					repeaterModel.push( {} );

					$timeout( function() {
						$scope.refresh();
						$scope.repeater.swipeTo( $scope.repeater.slides.length - 1 );
					});

				};


				/**
				 * Remove an item from a repeater
				 */
				$scope.remove = function( repeaterModel, itemIndex ) {

					$scope.showMenu = false;

					repeaterModel.splice( itemIndex, 1 );

					$timeout( function() { 
						$scope.refresh();
						$scope.repeater.swipeTo( $scope.repeater.activeIndex );
					});

				};


				/**
				 * Rearrange repeater items
				 */
				$scope.rearrange = function( repeaterModel, currentItemIndex, newItemIndex ) {

					repeaterModel.splice( newItemIndex, 0, repeaterModel.splice( currentItemIndex, 1)[0] );

					$timeout( function(){ 
						$scope.refresh();
						$scope.repeater.swipeTo( newItemIndex );
					});

				};


				/**
				 * Toggle the repeater menu
				 */
				$scope.toggleMenu = function() {

					$scope.showMenu = !$scope.showMenu;

				};


				/**
				 * Reinitialize (and redraw) Swiper instance (external reference)
				 */
				this.refresh = function() {

					$scope.refresh();

				};


				/** 
				 * Advance repeater and set focus on first input element (external)
				 */
				this.nextFocus = function() {

					if( ! $scope.nextDisabled ) {

						$scope.repeater.swipeNext();

						/* Wait for animation to finish before setting focus */
						setTimeout( function() {
							jQuery( $scope.repeater.slides[ $scope.repeater.activeIndex ] ).find( 'input,textarea' ).first().focus();
						}, $scope.repeater.params.speed + 10 );

					}

				};


				/** 
				 * Regress repeater and set focus on last input element (external)
				 */
				this.prevFocus = function() { 

					if( ! $scope.prevDisabled ) {

						$scope.repeater.swipePrev();

						/* Wait for animation to finish before setting focus */
						setTimeout( function() {
							jQuery( $scope.repeater.slides[ $scope.repeater.activeIndex ] ).find( 'input,textarea' ).last().focus();
						}, $scope.repeater.params.speed + 10 );

					}

				};


				/** 
				 * Expose nav status (external)
				 */
				this.nextDisabled = function() {

					return $scope.nextDisabled;

				};
				this.prevDisabled = function() {

					return $scope.prevDisabled;

				}

			},
			template: function( elem, attrs ) {

				return "<div class='mia-author-field-wrap' data='data'>" +
					"<p class='mia-author-field-meta'><span class='mia-author-label'>" + attrs.label + "</span> <a class='mia-author-button' ng-click='add( model." + attrs.name + ", \"" + attrs.name + "\" )' >Add " + attrs.labelSingular + "</a></p>" +
					"<div class='mia-author-repeater'>" +
						"<div class='mia-author-repeater-header' ng-show='repeater.slides.length !== 0'>" +
							"<div class='mia-author-repeater-pagination'></div>" +
							"<span class='mia-author-repeater-nav prev' ng-click='repeater.swipePrev()' ng-class=\"{'disabled':prevDisabled}\">◀</span>" +
							"<span class='mia-author-repeater-nav next' ng-click='repeater.swipeNext()' ng-class=\"{'disabled':nextDisabled}\">▶</span>" +
						"</div>" +
						"<div class='mia-author-repeater-container swiper-container'>" +
							"<div class='mia-author-repeater-wrapper swiper-wrapper'>" +
								"<div class='mia-author-repeater-item swiper-slide' ng-repeat='elem in model." + attrs.name + "'>" +
									"<div class='mia-author-repeater-meta-wrap'>"+
										"<span class='mia-author-repeater-meta'>" + attrs.labelSingular + "&nbsp;&nbsp;{{$index + 1}}&nbsp;&nbsp;of&nbsp;&nbsp;{{repeater.slides.length}}</span>" +
										"<a class='mia-author-repeater-menu-toggle' ng-click='toggleMenu()' ng-class='{active:showMenu}'>☰</a>" +
										"<div class='mia-author-repeater-menu' ng-show='showMenu'>" +
											"<div class='mia-author-repeater-menu-option'>" +
												"<a class='mia-author-repeater-menu-option-icon' ng-click='remove(model." + attrs.name + ", $index)'>✕</a>" +
												"<a class='mia-author-repeater-menu-option-label' ng-click='remove(model." + attrs.name + ", $index)'>Delete " + attrs.labelSingular + "</a>" +
											"</div>" +											
											"<div class='mia-author-repeater-menu-option' ng-class='{disabled:$index === 0}'>" +
												"<a class='mia-author-repeater-menu-option-icon' ng-click='rearrange(model." + attrs.name + ", $index, $index - 1)'>⤺</a>" +
												"<a class='mia-author-repeater-menu-option-label' ng-click='rearrange(model." + attrs.name + ", $index, $index - 1)'>Shift left</a>" +
											"</div>" +
											"<div class='mia-author-repeater-menu-option' ng-class='{disabled:$index === repeater.slides.length - 1}'>" +
												"<a class='mia-author-repeater-menu-option-icon' ng-click='rearrange(model." + attrs.name + ", $index, $index + 1)'>⤻</a>" +
												"<a class='mia-author-repeater-menu-option-label' ng-click='rearrange(model." + attrs.name + ", $index, $index + 1)'>Shift right</a>" +
											"</div>" +
										"</div>" +
									"</div>" +
									"<div class='mia-author-repeater-fields' ng-transclude></div>" +
								"</div>"+
							"</div>" +
						"</div>" +
					"</div>" +
				"</div>"; 

			},
			link: function( scope, elem, attrs ) {
				
				// Register repeater array in main data object
				scope.register( attrs.name );

				// Build repeater
				scope.build( elem );

				// Initialize nav
				scope.refreshNav();

				// Close menu on click outside of element
				jQuery( window ).on( 'click', function( e ) {

					var thisMenu = elem.find( '.mia-author-repeater-meta-wrap' );

					if( ! jQuery( e.target ).closest( '.mia-author-repeater-meta-wrap' ).is( thisMenu ) ) {

						scope.$apply(
							scope.showMenu = false
						);

					}

				});

			}

		};

	});
	

	/**
	 * .mia-author-repeater-fields directive
	 *
	 * Reinitializes Swiper instance of parent repeater when the last repeater
	 * item is printed and when height changes, and controls tabbing behavior
	 * between fields in separate repeater items
	 */
	miaAuthor.directive( 'miaAuthorRepeaterFields', function() {

		return {

			restrict:'C',
			require:'^repeater',
			link: function( scope, elem, attrs, repeater ) {

				// Reinitialize Swiper instance when last repeater item is printed
				if( scope.$last ) {
					repeater.refresh();
				}

				// Reinitialize Swiper instance when height changes
				scope.$watch( 
					function( ) { 
						return elem.height();
					}, 
					function( newValue, oldValue ) {
						if( newValue != oldValue ) {
							repeater.refresh();
						}
					}
				);

				// Intercept tabbing and move destination into view before focusing on 
				// the next field. This prevents repeater items from getting "stuck"
				// halfway in view and helps create a logical tabbing workflow.
				elem.find( 'input,textarea' ).last().on( 'keydown', function( e ) {

					// Tabbing forward from last input of a repeater item
					if( e.keyCode === 9 && e.shiftKey === false && !repeater.nextDisabled() ) {
						repeater.nextFocus();
						return false;
					}

					// Tabbing backward from first input of a repeater item
					if( e.keyCode === 9 && e.shiftKey === true && !repeater.prevDisabled() ) {
						repeater.prevFocus();
						return false;
					}

				});

			}

		}

	});


	/**
	 * Manually initialize Angular after environment is set up
	 */
	angular.bootstrap( document, ['miaAuthor'] );

});