// LOADING ANIMATION
let animationTimeout = 0;

function startAnimation() {
	$("#loader-background").show();
	$("#loader").show();
	$("#loader-sword").addClass("rotateOut");
	$("#loader-code").addClass("rotateIn");
	animationTimeout = setTimeout(
		setInterval(function () {
			$(".loader-icon").toggleClass("rotateOut rotateIn");
		}, 2000), 2000);
}

function stopAnimation() {
	$("#loader-background").hide();
	$("#loader").hide();
	clearInterval(animationTimeout);
	$("#loader-sword").removeClass("rotateOut rotateIn");
	$("#loader-code").removeClass("rotateOut rotateIn");
}

$(document).ready(function () {
	$("form.useLoader").submit(function () {
		startAnimation();
	});
});