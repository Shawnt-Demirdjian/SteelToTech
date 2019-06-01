$(document).ready(function () {

	// FancyBox Options
	$('[data-fancybox="gallery"]').fancybox({
		buttons: [
			"zoom",
			"slideShow",
			"fullScreen",
			"download",
			"thumbs",
			"close"
		]
	});

	// Lazy-load images
	$(".album-image").each((index, element) => {
		$(element).attr("src", $(element).attr("data-src"));
	});

	// Lazy-load video
	$(".album-video").each((index, element) => {
		$(element).attr("src", $(element).attr("data-src"));
	});

}); // End Document Ready