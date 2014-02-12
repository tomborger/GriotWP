/**
 * <imagepicker> directive
 *
 * Sets up a (non-isolate) scope and controller and prints fields needed to
 * add and annotate zoomable images.
 */
angular.module( 'griot' ).directive( 'imagepicker', function( $compile ) {

	return{

		restrict: 'E',
		replace: true,
		template: function( elem, attrs ) {

			var templateHtml = "<div class='griot-imagepicker' >" +
					"<div class='griot-current-image' ng-class='{empty: !hasImage }' ng-style='{ backgroundImage: backgroundImage }'></div>" +
				"</div>";

			return templateHtml;

		},
		controller: function( $scope, $element, $attrs ) {

			$scope.backgroundImage = $scope.model[ $attrs.name ] ? 'url(' + $scope.model[ $attrs.name ] + ')' : 'auto';

			var _this = this;

			this.isImageTarget = false;

			this.frame = wp.media.griotImageLibrary.frame();

			this.frame.on( 'select', function() {

				if( _this.isImageTarget ) {

					var selection = _this.frame.state().get( 'selection' );
					selection.each( function( attachment ) {

						if( attachment.attributes.url ) {

							$scope.$apply( function() {
								$scope.model[ $attrs.name ] = attachment.attributes.url;
								$scope.backgroundImage = 'url(' + attachment.attributes.url + ')';
								_this.isImageTarget = false;
								$scope.hasImage = true;

							});

						}

					});
					

	    	}

    	});

    	$scope.hasImage = $scope.model[ $attrs.name ];

			$scope.openFrame = function() {

				_this.frame.open();
				_this.isImageTarget = true;

			}

			$scope.removeImage = function() {

				$scope.model[ $attrs.name ] = null;
				$scope.hasImage = false;
				$scope.backgroundImage = 'auto';

			}

		},
		link: function( scope, elem, attrs ) {

			var addImageBtn = angular.element( "<a class='griot-button griot-pick-image' ng-disabled='protected' ng-click='openFrame()'>Choose image</a><a class='griot-button griot-remove-image' ng-disabled='protected' ng-if='hasImage' ng-click='removeImage()'>Remove image</a>" );
			var compiled = $compile( addImageBtn );
			elem.closest( '.griot-field-wrap' ).find( '.griot-field-meta' ).append( addImageBtn );
			compiled( scope );

		}

	}

});

wp.media.griotImageLibrary = {
     
	frame: function() {
	
		if( this._frame ) {
			return this._frame;
		}

		this._frame = wp.media({
			id:         'griot-library-frame',                
			title:      'Insert an image',
			multiple:   false,
			editing:    true,
			library:    { type: 'image' },
			button:     { text: 'Insert' }
		});

		return this._frame;

	},

};