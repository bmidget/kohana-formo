$(document).ready(function(){
	function markdown_it() {
		marked.setOptions({
			gfm: true,
			sanitize: false
		});

		var $md = $('.md');
		if ($md.length) {
			$md.each(function(k, el) {
				var html = $(this).html();
				var new_html = marked(html);
				$(this).html(new_html);
			});
		}
	}

	markdown_it();

	$('.bs-docs-sidenav').affix({
	  offset: {
		top: function () { return $(window).width() <= 980 ? 290 : 170 }
	  , bottom: 0
	  }
	})
});