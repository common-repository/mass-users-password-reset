jQuery( document ).ready( function( $ ) {
	// Get free plugin data
	var FreePluginData = function() {
		Ajax_Loader( 'visible' );
		$.post( MUPR_FREE.ajax_url, { action: 'mupr_plugin_data', nonce: MUPR_FREE.plugin_nonce }, function( response ) {
			if ( response.result == 1 ) {
				// Download
				var FreeDownload = $( '#download' ).find( 'h3' );
				FreeDownload.text( +response.downloaded + +FreeDownload.parent('li').data( 'download' ) );
				// Active Installs
				var FreeActiveInstalls = $( '#active_installs' ).find( 'h3' );
				FreeActiveInstalls.text( +response.active + +FreeActiveInstalls.parent('li').data( 'active' ) );
				EnvatoData();
			}
		}, 'json' ).fail( AjaxFail ); 
	};
	// Get envato market plugin data
	var EnvatoData = function() {
		jQuery.ajax( {
			url: 'https://api.envato.com/v1/market/new-files-from-user:krishaweb,codecanyon.json',
			type: 'GET',
			contentType: 'application/json',
			headers: {
				'Authorization': 'Bearer G1sejvskKcersEiwhPHyqKNQMker2MBh'
			},
			success: function( response ) {
				$.each( response, function( index, item ) {
					$.each( item, function( key, product ) {
						if ( product.item === 'Mass Users Password Reset Pro' ) {
							// Download
							var Download = $( '#download' ).find( 'h3' );
							Download.text( +product.sales + +Download.text() );
							// Active Installs
							var ActiveInstalls = $( '#active_installs' ).find( 'h3' );
							ActiveInstalls.text( +product.sales + +ActiveInstalls.text() );
							Ajax_Loader( 'none' );
						}
					} );
				} );
			},
		} );
	};
	// show/hide ajax loader
	var Ajax_Loader = function( action = 'visible' ) {
		$( '.spinner' ).removeAttr( 'style' );
		$( '.spinner' ).css( 'visibility', action );
	};
	// Ajax fail response
	var AjaxFail = function ( response ) {
		// Hide ajax loader
		Ajax_Loader( 'none' );
		$( '#reset' ).removeAttr( 'disabled' );
		$( '.notice-error' ).find( 'strong' ).text( MUPR_FREE.nonce_error );
		$( '.notice-error' ).removeClass( 'mupr-hidden' );
		setTimeout( function() {
			$( '.notice' ).addClass( 'mupr-hidden' );
		}, 5000 );
	};
	var $loop = 0;
	// Reset password process
	var recursiveMailSend = function() {
		// Loader show
		Ajax_Loader( 'visible' );
		var sendData = {
			action: 'send_reset_password_mail_action',
			role: $( 'select[name="role_filter"]' ).val(),
			nonce: MUPR_FREE.reset_nonce,
			offset: $loop * MUPR_FREE.per_page
		};
		$.post( MUPR_FREE.ajax_url, sendData, function ( response ) {
			if ( response.result == 1 && response.status == 'continue' ) {
				$( '.notice-success' ).find( 'strong' ).text( response.message );
				if ( $( '.notice-success' ).hasClass( 'mupr-hidden' ) ) {
					$( '.notice-success' ).removeClass( 'mupr-hidden' );
				}
				$loop++;
				recursiveMailSend();
			} else if ( response.result == 1 && response.status == 'end' ) {
				Ajax_Loader( 'none' );
				$( '#reset' ).removeAttr( 'disabled' );
				setTimeout( function() {
					$( '.notice-error, .notice-success' ).addClass( 'mupr-hidden' );
				}, 5000 );
				return;
			} else {
				Ajax_Loader( 'none' );
				if ( response.result == 2 ) {
					$( '.notice-error' ).find( 'strong' ).html( response.message );
				} else {
					$( '.notice-error' ).find( 'strong' ).text( response.message );
				}
				$( '.notice-error' ).removeClass( 'mupr-hidden' );
				$( '#reset' ).removeAttr( 'disabled' );
				setTimeout( function() {
					$( '.notice-error, .notice-success' ).addClass( 'mupr-hidden' );
				}, 5000 );
			}
		}, 'json' ).fail( AjaxFail ); 
	};
	// Create dialog box
	$( '#mupr_video' ).dialog( {
		autoOpen: false,
		draggable: false,
		width: 'auto',
		maxWidth: 600,
		modal: true,
		resizable: false,
		closeOnEscape: true,
		position: {
			my: "center",
			at: "center",
			of: window
		}
	} );
	// Resize dialog
	$( window ).resize( function() {
		$( "#mupr_video" ).dialog( {
			position: {
				my: "center",
				at: "center",
				of: window
			}
		} );
	} );
	// Close modal
	$( document ).on( 'click', '.ui-dialog-titlebar-close', function () {
		var Iframe = $( '.youtube-video iframe' );
		Iframe.attr( 'src', Iframe.attr( 'src' ) );
	} );
	// Open youtube video
	$( '.mupr-pro-video' ).on( 'click', function() {
		$( '#mupr_video' ).dialog( 'open' );
	} );
	// display users on change of role dropdown
	$( 'select[name="role_filter"]' ).change( function() {
		$( this ).parents( 'form' ).submit();
	} );
	// reset password mail send 
	$( document ).on( 'click', '#reset', function() {
		$loop = 0;
		$( this ).attr( 'disabled', 'disabled' );
		recursiveMailSend();
	} );
	// Init select picker
	$( '.mupr-selectpicker' ).selectpicker();

	if ( $( '.mupr-wrap' ).hasClass( 'mupr-pro-wrap' ) ) {
		$( window ).on( 'scroll', function() {
			if ( $( '.mupr-facts' ).hasClass( 'get-data' ) ) return;
  			if( $(window).scrollTop() + $(window).height() > $(document).height() - 100 ) {
  				$( '.mupr-facts' ).addClass( 'get-data' );
  				FreePluginData();
  			}
		} );
	}
} );
