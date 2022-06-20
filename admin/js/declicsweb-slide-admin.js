(function( $ ) {
	'use strict';

	/**
	 * All of the code for your admin-facing JavaScript source
	 * should reside in this file.
	 *
	 * Note: It has been assumed you will write jQuery code here, so the
	 * $ function reference has been prepared for usage within the scope
	 * of this function.
	 *
	 * This enables you to define handlers, for when the DOM is ready:
	 *
	 * $(function() {
	 *
	 * });
	 *
	 * When the window is loaded:
	 *
	 * $( window ).load(function() {
	 *
	 * });
	 *
	 * ...and/or other possibilities.
	 *
	 * Ideally, it is not considered best practise to attach more than a
	 * single DOM-ready or window-load handler for a particular page.
	 * Although scripts in the WordPress core, Plugins and Themes may be
	 * practising this, we should strive to set a better example in our own work.
	 */




    $( document ).ready( function() {

    	// Uploading files
    	var file_frame;

    	$.fn.upload_image = function( button ) {
    		var media_uploader = button.parents('.media-uploader');
    		
    		// Create the media frame.
    		file_frame = wp.media.frames.file_frame = wp.media({
    			title: $( this ).data( 'uploader_title' ),
  				button: {
  					text: $( this ).data( 'uploader_button_text' ),
  				},
  				multiple: false
    		});

			// When an image is selected, run a callback.
			file_frame.on( 'select', function() {
				var attachment = file_frame.state().get('selection').first().toJSON();
    		  
				media_uploader.find('input[type="hidden"]').val(attachment.id);
  				media_uploader.find('img').attr('src', attachment.url);
  				media_uploader.find('img').attr('srcset', attachment.url);
  				media_uploader.addClass('has-img');
			});

    		// Finally, open the modal
    		file_frame.open();
    	};

    	// On click upload image button
    	$('.media-uploader').on( 'click', '.button.upload', function( event ) {
    		event.preventDefault();
    		$.fn.upload_image( $(this) );
    	});

    	// On click delete image button
    	$('.media-uploader').on( 'click', '.delete', function( event ) {
			event.preventDefault();
			var media_uploader = $(this).parents('.media-uploader');

    		media_uploader.removeClass( 'has-img' );
    		media_uploader.find("input[type='hidden']").val('');
    	});            
        
	});


})( jQuery );
