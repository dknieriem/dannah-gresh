;(function( $ ) {
	$(document).ready(function() {
		// Switch to default dashboard.
		$('.dismiss-clasic-editor-notice').on('click', function(e) {
			let $this = $(this);
			$.ajax(
				$this.data('link')
			)
			.success(function() {
				$this.parents('.notice-clasic-editor').remove()
			})

		})
	})
})( jQuery )
