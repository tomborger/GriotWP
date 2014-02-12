/**
 * .imagepicker directive
 *
 * Sets up a (non-isolate) scope and controller and prints fields needed to
 * add and annotate zoomable images.
 */
angular.module( 'griot' ).directive( 'imagepicker', function() {

	return{

		restrict: 'C',
		controller: function( $scope, $element, $attrs ) {

			var _this = this;

			this.imageTarget = false;

			this.frame = wp.media.griotImageLibrary.frame();

			this.frame.on( 'select', function() {

				if( _this.imageTarget ) {

					var selection = _this.frame.state().get( 'selection' );
					selection.each( function( attachment ) {

						$scope.$apply( function() {
							$scope.model[ $attrs.name ] = attachment.attributes.url;
						});

					});
					
					_this.imageTarget = false;

	    	}

    	});

			$scope.openFrame = function() {

				_this.frame.open();
				_this.imageTarget = true;

			}

		},
		link: function( scope, elem, attrs ) {

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