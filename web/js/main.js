jQuery(document).ready(function($) {

	//Effet Carousel
    $("#cloud-carousel").CloudCarousel( {
		reflHeight: 40,
		reflGap: 2,
		buttonLeft: $('#but1'),
		buttonRight: $('#but2'),
		yRadius: 60,
		xPos: 285,
		yPos: 40,
		speed: 0.15,
		autoRotate: 'no',
		autoRotateDelay: 1500,
		mouseWheel: true
	});
			
});