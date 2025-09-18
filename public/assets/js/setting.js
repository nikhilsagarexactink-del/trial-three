jQuery(document).ready(function () {
	// SVG Create
	jQuery(function () {
		jQuery("img.svg").each(function () {
			var $img = jQuery(this);
			var imgID = $img.attr("id");
			var imgClass = $img.attr("class");
			var imgURL = $img.attr("src");
			jQuery.get(
				imgURL,
				function (data) {
					var $svg = jQuery(data).find("svg");
					if (typeof imgID !== "undefined") {
						$svg = $svg.attr("id", imgID);
					}
					if (typeof imgClass !== "undefined") {
						$svg = $svg.attr("class", imgClass + " replaced-svg");
					}
					$svg = $svg.removeAttr("xmlns:a");
					if (
						!$svg.attr("viewBox") &&
						$svg.attr("height") &&
						$svg.attr("width")
					) {
						$svg.attr(
							"viewBox",
							"0 0 " +
							$svg.attr("height") +
							" " +
							$svg.attr("width")
						);
					}
					$img.replaceWith($svg);
				},
				"xml"
			);
		});
	});

		jQuery('.red-dot').on('click', function() {
			var targetContentId = jQuery(this).data('target');
			jQuery('.skeleton-info li').hide();
			jQuery('#' + targetContentId).show();
			jQuery('.red-dot').removeClass('active');
        	jQuery(this).addClass('active');
		});
	
		jQuery('body').on('click', function(event) {
			if (!jQuery(event.target).closest('.red-dot, .content-group').length) {
				jQuery('.skeleton-info li').hide();
				jQuery('.red-dot').removeClass('active');
			}
		});
	
});