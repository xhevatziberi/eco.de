( function( $ ) {

	$(window).on("resize", function() {
		// fixLines();
	});

	$(window).on("load", function() {
		// animateLogo();
		// animateBag();
		// animateSecondSVG();
	});

} )( jQuery );


// ready
document.addEventListener("DOMContentLoaded", () => {

	const copyTexts = document.querySelectorAll(".copy-text");

	copyTexts.forEach(copyText => {
		const button = copyText.querySelector("button");
		const input = copyText.querySelector(".qodef-m-text");

		button.addEventListener("click", () => {
			input.select();
			document.execCommand("copy");
			copyText.classList.add("active");
			window.getSelection().removeAllRanges();

			setTimeout(() => {
				copyText.classList.remove("active");
			}, 2500);
		});
	});

});
