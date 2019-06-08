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
	$(".album-picture").each((index, element) => {
		$(element).find("img").attr("src", $(element).find("img").attr("data-src"));
		$(element).find("source").attr("srcset", $(element).find("source").attr("data-srcset"));
	});

	// Lazy-load video
	$(".album-video").each((index, element) => {
		$(element).attr("src", $(element).attr("data-src"));
	});

	// Redefine download href to download source image, not large
	$(document).on('afterShow.fb', function (e, instance, slide) {
		let href = $(slide).attr("src");
		href = href.replace(/large/, "source");
		$(".fancybox-button.fancybox-button--download").attr("href", href)
	});

}); // End Document Ready