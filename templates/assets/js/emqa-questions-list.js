jQuery(function($){

	// Search form
	$('form#emqa-search input').autocomplete({
		appendTo: 'form#emqa-search',
		source: function( request, resp ) {
			$.ajax({
				url: emqa.ajax_url,
				type: 'POST',
				dataType: 'json',
				data: {
					action: 'emqa-auto-suggest-search-result',
					title: request.term,
					nonce: $('form#emqa-search input').data('nonce')
				},
				success: function( data ) {
					console.log( data );
					resp( $.map( data.data, function( item ) {
						if ( true == data.success ) {
							return {
								label: item.title,
								value: item.title,
								url: item.url,
							}
						} else {
							return {
								label: item.message,
								value: item.message,
								click: false
							}
						}
					}))
				}
			});
		},
		select: function( e, ui ) {
			if ( ui.item.url ) {
				window.location.href = ui.item.url;
			} else {
				if ( ui.item.click ) {
					return true;
				} else {
					return false;
				}
			}
		},
		open: function( e, ui ) {
			var acData = $(this).data( 'uiAutocomplete' );
			acData.menu.element.addClass('emqa-autocomplete').find('li').each(function(){
				var $self = $(this),
					keyword = $.trim( acData.term ).split(' ').join('|');
					$self.html( $self.text().replace( new RegExp( "(" + keyword + ")", "gi" ), '<span class="emqa-text-highlight">$1</span>' ) );
			});
		}
	});
	
});