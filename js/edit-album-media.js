$(document).ready(function () {

	// ROTATION BUTTONS

	$(".rotate-left-btn").on('click', function () {
		let img = $(this).parent(".btn-group").next("img");
		$(img).removeClass("save-success save-fail");
		let angle = Number.parseInt($(img).attr("data-angle"));
		angle -= 90;
		angle = ((angle / 90) % 4) * 90;
		$(img).attr("data-angle", angle);
		$(img).css("transform", "rotate(" + angle + "deg)");
	});

	$(".rotate-right-btn").on('click', function () {
		let img = $(this).parent(".btn-group").next("img");
		$(img).removeClass("save-success save-fail");
		let angle = Number.parseInt($(img).attr("data-angle"));
		angle += 90;
		angle = ((angle / 90) % 4) * 90;
		$(img).attr("data-angle", angle);
		$(img).css("transform", "rotate(" + angle + "deg)");
	});

	$(".save-rotation").on('click', function () {
		let img = $(this).parent(".btn-group").next("img");
		let angle = Number.parseInt($(img).attr("data-angle"));
		let filename = $(this).parents(".media-item").children(".media-checkbox").val()

		$.post("/rotate-image", {
			angle,
			filename
		})
			.done(function (data) {
				$(img).addClass("save-success");
			})
			.fail(function (data) {
				$(img).addClass("save-fail");
			});
	});
}); // End Document Ready