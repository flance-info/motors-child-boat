"use strict";
(function ($) {
	$( window ).on(
		'elementor/frontend/init',
		function () {
			elementorFrontend.hooks.addAction( 'frontend/element_ready/global', onReadyJs );
		}
	);

	$( document ).ready( onReadyJs );

	function onReadyJs( config = false ) {
		$( "select:not(.hide, .filter-select, .qm-filter)" ).each(
			function () {
				let selectElement = $( this );

				if ( selectElement.hasClass( 'add_a_car-select' ) && typeof allowDealerAddCategory !== 'undefined' && allowDealerAddCategory == 1 ) {
					selectElement.select2(
						{
							width: '100%',
							dropdownParent: $( 'body' ),
							matcher: matchCustom,
							minimumResultsForSearch: Infinity,
							"language": {
								"noResults": function(){
									return noFoundSelect2;
								}
							}
						}
					);
				} else {
					selectElement.select2(
						{
							width: '100%',
							dropdownParent: $( 'body' ),
							minimumResultsForSearch: Infinity,
							"language": {
								"noResults": function(){
									return noFoundSelect2;
								}
							}
						}
					);
				}
			}
		);

		let select2 = $( "select:not(.hide)" );

		select2.on(
			"select2:open",
			function() {
				let stmClass = $( this ).data( 'class' );
				stmClass     = (typeof stmClass == 'undefined') ? $( this ).attr( 'name' ) : stmClass;

				$( '.select2-dropdown--below' ).parent().addClass( stmClass );

				window.scrollTo( 0, $( window ).scrollTop() + 1 );
				window.scrollTo( 0, $( window ).scrollTop() - 1 );
			}
		);

		select2.on(
			"select2:closing",
			function() {
				$( '.select2-search--dropdown' ).removeClass( 'plus-added-emeht-mts' );
				$( '.add-new-term' ).remove();
			}
		);

		$( '.single-product .product-type-variable table.variations select' ).on(
			"change",
			function() {
				$( this ).parent().find( '.select2-selection__rendered' ).text( $( this ).find( 'option[value="' + $( this ).val() + '"]' ).text() );
			}
		);

		$( "select[name='stm-multi-currency']" ).on(
			"select2:select",
			function () {
				let currency = $( this ).val();

				$.cookie( 'stm_current_currency', encodeURIComponent( currency ), { expires: 7, path: '/' } );
				let data         = $( this ).select2( 'data' ),
					selectedText = $( this ).attr( "data-translate" ).replace( "%s", data[0].text );

				$( ".stm-multiple-currency-wrap" ).find( "span.select2-selection__rendered" ).text( selectedText );
				location.reload();
			}
		);
	}

	function matchCustom(params, data) {
		if ( $.trim( params.term ) === '' ) {
			return data;
		}

		if ( typeof data.text === 'undefined' ) {
			return null;
		}

		let searchTerm      = params.term.toLowerCase(),
			optionText      = data.text.toLowerCase(),
			search_dropdown = $( '.select2-search--dropdown' );

		if ( optionText.includes( searchTerm ) ) {
			search_dropdown.removeClass( 'plus-added-emeht-mts' );
			$( '.add-new-term' ).remove();

			return $.extend( {}, data, true );
		}

		if ( optionText.indexOf( searchTerm ) === -1 ) {
			if ( ! search_dropdown.hasClass( 'plus-added-emeht-mts' )) {
				search_dropdown.append( '<i class="fas fa-plus-square add-new-term"></i>' );
			}
			search_dropdown.addClass( 'plus-added-emeht-mts' );
		}

		// Return `null` if the term should not be displayed
		return null;
	}
})( jQuery );
