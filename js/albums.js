$(document).ready(() => {

	// Lazy-load images
	$(".card-img").each((index, element) => {
		$(element).attr("src", $(element).attr("data-src"));
	});

});