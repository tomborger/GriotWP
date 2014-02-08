jQuery( document ).ready( function() { 


	// Prepare WP environment
	jQuery( '#poststuff' )

		// Define Angular app and controller
		.attr({
			'ng-app':'griot',
			'ng-controller':'griotCtrl'
		})

		// Create main application container and hidden content field
		.find( '#post-body-content' ).append( "<div id='griot'>" +
					"<textarea name='content' id='griot-data'>{{ data | json }}</textarea>" +
					"<fieldset></fieldset>" +
				"</div>" )

		// Link title field to model
		.find( '#title' ).attr({
			'ng-model':'data.title'
		});


	/**
	 * Main application module
	 */
	var griot = angular.module( 'griot', [] );


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
	griot.factory( 'ModelChain', function() {

		return {

			/**
			 * Initialize the model chain with 'data' value.
			 * (Called in main controller)
			 */
			initialize: function( scope ) {

				scope.modelChain = ['data'];
				scope.model = scope.data;

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
	 * getTitle filter
	 *
	 * For use in making connections between records. Returns the title if one
	 * is set, otherwise 'Untitled (post #{ID})'
	 */
	griot.filter( 'getTitle', function() {

	  return function( record ) {

	    return record.post_title === '' ? 'Untitled (post #' + record.ID + ')' : record.post_title;

	  };

	});


	/**
	 * Application controller
	 *
	 * Initializes data object and scope chain
	 */
	griot.controller( 'griotCtrl', function( $scope, ModelChain ) { 

		// Initialize data object and load previously saved data into model. 
		// See Griot::print_data() in class-griot.php
		$scope.data = griotData.data ? JSON.parse( griotData.data ) : {};
		$scope.data.title = griotData.title;
		$scope.ui = {
			recordType: griotData.recordType,
			oppositeRecordType: griotData.recordType == 'object' ? 'story' : 'object',
			directory: griotData.directory,
		}

		// Initialize model chain
		ModelChain.initialize( $scope );

	});

	
	/**
	 * <fieldset> directive
	 * 
	 * Renders fields from template
	 */
	griot.directive( 'fieldset', function() {

		return{

			restrict: 'E',
			replace: true,
			templateUrl: function() {
				return griotData.templateUrl;
			}

		}

	});


	/**
	 * <field> directive
	 * 
	 * Renders individual <field> elements into container, label, and input and 
	 * populates ng-model attributes.
	 */
	griot.directive( 'field', function() {

		return {

			restrict: 'E',
			replace: true,
			scope: {
				data:'=',
				ui:'='
			},
			controller: function( $scope, $element, $attrs, ModelChain ) {

				ModelChain.update( $scope, $attrs.name );

			},
			template: function( elem, attrs ) {

				var fieldhtml;

				switch( attrs.type ){

					case 'text':
						fieldhtml = "<input type='text' ng-model='model." + attrs.name + "' />";
						break;

					case 'textarea':
						fieldhtml = "<textarea ng-model='model." + attrs.name + "'></textarea>";
						break;

					case 'wysiwyg':
						fieldhtml = "<textarea ng-model='model." + attrs.name + "' ck-editor></textarea>";
						break;

					case 'connection':
						fieldhtml = "<select ng-model='model." + attrs.name + "' ng-options='record.ID as ( record | getTitle ) for record in ui.directory[ ui.oppositeRecordType ]' multiple></select>";
						break;

					case 'zoomer':
						// TODO: Set ID based on model chain OR (better yet) update 
						// flat_image_zoom.js to accept a jQuery object
						fieldhtml = "<div class='griot-zoomer-container'><div class='griot-zoomer' id='zoomer" + Math.floor( Math.random() * 10000000 ) + "'></div>";
						break;
				}

				var templatehtml = "<div class='griot-field-wrap' data='data' ui='ui'>";

				if( attrs.label ) {

					templatehtml += "<div class='griot-field-meta' ><span class='griot-label'>" + attrs.label + "</span></div>";

				}

				templatehtml += "<div class='griot-field'>" +
						fieldhtml +
					"</div>" +
				"</div>";

				return templatehtml;

			}

		};

	});


	/**
	 * ck-editor directive
	 * 
	 * Renders CKEditor on WYSIWYG fields and keeps model updated (CKEditor 
	 * doesn't update textarea natively)
	 *
	 * See: http://stackoverflow.com/questions/11997246/bind-ckeditor-value-to-model-text-in-angularjs-and-rails
	 */
	griot.directive('ckEditor', function() {

	  return {

	  	restrict: 'A',
	    require: ['?^repeater', '?ngModel'],
	    link: function( scope, elem, attr, ctrls ) {

	    	var repeater = ctrls[0];
	    	var ngModel = ctrls[1];

	      var ck = CKEDITOR.replace( elem[0] );

	      if( repeater ) {
	      	ck.on( 'instanceReady', function() {
	      		repeater.refresh();
	      	});
	      }

	      if( !ngModel ) {
	      	return;
	      }

	      ck.on( 'pasteState', function() {

	        scope.$apply( function() {

	          ngModel.$setViewValue( ck.getData() );

	        });

	      });

	      ngModel.$render = function( value ) {

	        ck.setData( ngModel.$viewValue );

	      };

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
	griot.directive( 'repeater', function() {

		return {

			restrict: 'E',
			replace: true,
			scope: {
				data:'=',
				ui:'='
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
						pagination: repeaterElement.find( '.griot-repeater-pagination' ).first()[0],
						paginationClickable: true,
						paginationElementClass: 'griot-repeater-bullet',
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

					$timeout( function() {
						$scope.repeater.reInit();
						$scope.refreshNav();
					});

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


				this.add = function( repeaterModel ) {

					$scope.add( repeaterModel );

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


				/**
				 * Slide to active index (external)
				 */
				this.swipeToActive = function() {

					$timeout( function() {

						$scope.repeater.swipeTo( $scope.repeater.activeIndex );

					});

				}

			},
			template: function( elem, attrs ) {

				return "<div class='griot-field-wrap' data='data' ui='ui'>" +
					"<p class='griot-field-meta'><span class='griot-label'>" + attrs.label + "</span> <a class='griot-button' ng-click='add( model." + attrs.name + ", \"" + attrs.name + "\" )' >Add " + attrs.labelSingular + "</a></p>" +
					"<div class='griot-repeater'>" +
						"<div class='griot-repeater-header' ng-show='repeater.slides.length !== 0'>" +
							"<div class='griot-repeater-pagination'></div>" +
							"<span class='griot-repeater-nav prev' ng-click='repeater.swipePrev()' ng-class=\"{'disabled':prevDisabled}\">◀</span>" +
							"<span class='griot-repeater-nav next' ng-click='repeater.swipeNext()' ng-class=\"{'disabled':nextDisabled}\">▶</span>" +
						"</div>" +
						"<div class='griot-repeater-container swiper-container'>" +
							"<div class='griot-repeater-wrapper swiper-wrapper'>" +
								"<div class='griot-repeater-item swiper-slide' ng-repeat='elem in model." + attrs.name + "'>" +
									"<div class='griot-repeater-meta-wrap'>"+
										"<span class='griot-repeater-meta'>" + attrs.labelSingular + "&nbsp;&nbsp;{{$index + 1}}&nbsp;&nbsp;of&nbsp;&nbsp;{{repeater.slides.length}}</span>" +
										"<a class='griot-repeater-menu-toggle' ng-click='toggleMenu()' ng-class='{active:showMenu}'>☰</a>" +
										"<div class='griot-repeater-menu' ng-show='showMenu'>" +
											"<div class='griot-repeater-menu-option'>" +
												"<a class='griot-repeater-menu-option-icon' ng-click='remove(model." + attrs.name + ", $index)'>✕</a>" +
												"<a class='griot-repeater-menu-option-label' ng-click='remove(model." + attrs.name + ", $index)'>Delete " + attrs.labelSingular + "</a>" +
											"</div>" +											
											"<div class='griot-repeater-menu-option' ng-class='{disabled:$index === 0}'>" +
												"<a class='griot-repeater-menu-option-icon' ng-click='rearrange(model." + attrs.name + ", $index, $index - 1)'>⤺</a>" +
												"<a class='griot-repeater-menu-option-label' ng-click='rearrange(model." + attrs.name + ", $index, $index - 1)'>Shift left</a>" +
											"</div>" +
											"<div class='griot-repeater-menu-option' ng-class='{disabled:$index === repeater.slides.length - 1}'>" +
												"<a class='griot-repeater-menu-option-icon' ng-click='rearrange(model." + attrs.name + ", $index, $index + 1)'>⤻</a>" +
												"<a class='griot-repeater-menu-option-label' ng-click='rearrange(model." + attrs.name + ", $index, $index + 1)'>Shift right</a>" +
											"</div>" +
										"</div>" +
									"</div>" +
									"<div class='griot-repeater-fields' ng-transclude></div>" +
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

					var thisMenu = elem.find( '.griot-repeater-meta-wrap' );

					if( ! jQuery( e.target ).closest( '.griot-repeater-meta-wrap' ).is( thisMenu ) ) {

						scope.$apply(
							scope.showMenu = false
						);

					}

				});

			}

		};

	});
	

	/**
	 * .griot-repeater-fields directive
	 *
	 * Reinitializes Swiper instance of parent repeater when the last repeater
	 * item is printed and when height changes, and controls tabbing behavior
	 * between fields in separate repeater items
	 */
	griot.directive( 'griotRepeaterFields', function() {

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
	 * <annotatedimage> directive
	 *
	 * Sets up a (non-isolate) scope and controller and prints fields needed for
	 * zoomable, annotatable images.
	 */
	griot.directive( 'annotatedimage', function() {

		return {

			restrict: 'E',
			template: function ( elem, attrs ) {

				// Recursive directives work; recursive transclusion doesn't.
				// A workaround ...
				var transcrude = elem.html();

				return "<div class='griot-annotated-image'>" +
					"<field label='Image ID' name='" + attrs.name + "' type='text' />" +
					"<div class='griot-zoomer' id='zoomer" + Math.floor( Math.random() * 100000000 ) + "'></div>" +
					"<repeater annotations label='Annotations' name='annotations' label-singular='annotation' label-plural='annotations'>" +
						transcrude +
					"</repeater>" +
				"</div>";

			},
			controller: function( $scope, $element, $attrs, $http ) {

				// Reference to currently selected image ID in data object
				var imgid = $scope.model[ $attrs.name ];

				// Reference to annotations repeater collection in data object
				var annotations = $scope.model[ 'annotations' ] ? $scope.model[ 'annotations' ] : [];

				// ID of zoomer container, required by flat_image_zoom
				// Currently this is randomly generated
				$scope.container_id = $element.find( '.griot-zoomer' ).first().attr( 'id' );

				// Local repository of image areas (i.e. layers) drawn by user
				// Used by Leaflet to populate zoomer
				$scope.imageLayers = L.featureGroup();

				// Add saved image layers
				angular.forEach( annotations, function( annotation ) {

					// Get geoJSON from annotation
					var geoJSON = annotation.geoJSON;

		    	// Convert geoJSON to layer
		    	var layer = L.GeoJSON.geometryToLayer( geoJSON.geometry );

		    	// Store reference to annotation in layer
					layer.annotation = annotation;

					// Add to local image layers collection
					$scope.imageLayers.addLayer( layer );

				});


				/**
				 * Set up zoomer and link to local image area collection.
				 */
				$scope.build = function() {

					// Get tile JSON
					// TODO: Make tilesLocation dynamic
					var tilesLocation = 'http://tilesaw.dx.artsmia.org/' + imgid + '.tif';
					var http = $http.get( tilesLocation );
					http.success( function( data ) {

						var tileURL = data.tiles[0].replace('http://0', '//{s}');

						// Build zoomer and store instance in scope
						$scope.zoomer = Zoomer.zoom_image({
							container: $scope.container_id,
							tileURL: tileURL,
							imageWidth: data.width,
							imageHeight: data.height
						});

						// Add feature group to zoomer
						$scope.zoomer.map.addLayer( $scope.imageLayers );

						// Create drawing controls
				    var drawControl = new L.Control.Draw({

				      draw: {
				        circle: false,
				        polyline: false,
				        marker: false,
				        rectangle: {
				        	shapeOptions: {
				        		color: '#eee'
				        	}
				       	}
				      },
				      edit: {
				      	featureGroup: $scope.imageLayers
				      }

				    });

				    // Add drawing controls to map
				    $scope.zoomer.map.addControl( drawControl );

				    $scope.sync();

				  });

				 };


				/**
				 * Keep repeater, zoomer, and data object in sync
				 */
				$scope.sync = function() {


					/**
					 * Sync annotations added via zoomer
					 */
					$scope.zoomer.map.on( 'draw:created', function( e ) {

						var geoJSON = e.layer.toGeoJSON();

		    		$scope.$apply( function() {

		    			// Add geoJSON to data object
			    		var length = annotations.push({
				    		geoJSON: geoJSON
				    	});

			    		// Get a reference to the new annotation
				    	var annotation = annotations[ length - 1 ];

				    	// Convert geoJSON to layer
				    	var layer = L.GeoJSON.geometryToLayer( geoJSON.geometry );

				    	// Store in layer
							layer.annotation = annotation;

							// Add to local image layers collection
							$scope.imageLayers.addLayer( layer );

						});

					});


					/**
					 * Sync annotations deleted via zoomer
					 */
					$scope.zoomer.map.on( 'draw:deleted', function( e ) {

						angular.forEach( e.layers._layers, function( layer ) {

							var index = jQuery.inArray( layer, annotations );

							$scope.$apply( function() {

								annotations.splice( index, 1 );
							
							});

						});

					});


					/**
					 * Sync annotations edited via zoomer
					 */
					$scope.zoomer.map.on( 'draw:edited', function( e ) {

						angular.forEach( e.layers._layers, function( layer ) {

							var geoJSON = layer.toGeoJSON();

							$scope.$apply( function() {

								layer.annotation.geoJSON = geoJSON;

							});
							
						});

					});

				};


				/**
				 * Sync annotations deleted via repeater
				 */
				$scope.$watchCollection(

					function() {
						return annotations;
					},
					function() {

						angular.forEach( $scope.imageLayers._layers, function( layer ) {

							if( -1 == jQuery.inArray( layer.annotation, annotations ) ) {

								$scope.imageLayers.removeLayer( layer );

							}

						});

					}

				);

				// Initialize zoomer
				$scope.build();

			}

		}

	});



	/**
	 * annotations directive
	 * 
	 * Makes a repeater talk to a zoomer
	 */
	griot.directive( 'annotations', function() {

		return {

			restrict: 'A',
			require: ['repeater', '^annotatedimage'],
			link: function( scope, elem, attrs, ctrls ) {

				var repeaterCtrl = ctrls[0];
				var imageCtrl = ctrls[1];


				/**
				 * Remove 'Add Annotation' button
				 */
				elem.find( 'a.griot-button' ).first().remove();


				/**
				 * Update repeater when data object changes
				 * TODO: This should be core functionality for repeater
				 */
				scope.$watchCollection( 

					function(){ 
						return scope.model[ 'annotations' ];
					}, 
					function() {
						repeaterCtrl.refresh();
						repeaterCtrl.swipeToActive();
					}

				);

			}
		}
	})

	
	/**
	 * Extend Leaflet
	 */
	L.extend( L.LatLngBounds.prototype, {

	  toGeoJSON: function() {

	    L.Polygon.prototype.toGeoJSON.call( this );

	  },

	  getLatLngs: function() {

	    L.Polygon.prototype._convertLatLngs([
	      [this.getSouthWest().lat, this.getSouthWest().lng],
	      [this.getNorthWest().lat, this.getNorthWest().lng],
	      [this.getNorthEast().lat, this.getNorthEast().lng],
	      [this.getSouthEast().lat, this.getSouthEast().lng]
	    ]);

	  }

	});


	/**
	 * Manually initialize Angular after environment is set up
	 */
	angular.bootstrap( document, ['griot'] );


});

