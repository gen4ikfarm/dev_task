/**
 * SauCal motive test.
 *
 * @package SAUCAL_Test_Plugin
 */

const SauCalTest_FrontEnd = function( $ ) {

	const $container = $( '.saucal-wrapper' );

	const saveData = function() {

		const data = $( '#saucal-user-settings-form' ).serialize();
		jQuery.ajax(
			{
				url: SauCalOptions.restUrlNormal,
				type: 'get',
				dataType: 'json',
				data: data,
				beforeSend: function( xhr ) {
					xhr.setRequestHeader( 'X-WP-Nonce', SauCalOptions.nonce );
				},
				success: function( response ) {

					if ('undefined' !== typeof response.error) {
						$container.html( response.error );
						return;
					}
					$( '.fa-spinner' ).remove();
					$( '#saucal-user-settings-form' ).find( ':submit' ).prop( 'disabled', false );
					$container.html( response );
				},
				fail: function( object, status ) {
					$container.html( SauCalOptions.string_array.no_data );
				},
			},
		);
	};

	$( document ).on(
		'submit',
		'#saucal-user-settings-form',
		function( e ) {
			e.preventDefault();
			$( this ).find( ':submit' ).after( ' <i class="fas fa-spinner fa-spin"></i>' ).prop( 'disabled', true );
			saveData();
		},
	);

};

jQuery( document ).ready(
	function( $ ) {
		new SauCalTest_FrontEnd( $ );
	},
);
