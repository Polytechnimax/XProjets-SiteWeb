jQuery(document).ready(function($) {
	$('#map').gMap({
							controls: {
								panControl: false,
								zoomControl: false,
								mapTypeControl: false,
								scaleControl: false,
								streetViewControl: false,
								overviewMapControl: false
							},
							maptype: 'ROADMAP',
							scrollwheel: true,
							zoom: 13,
							markers: [
								{
									address: 'Xprojets Ecole polytechnique Route de Saclay 1120 Palaiseau FRANCE', // Your Adress Here
									html: '',
									popup: false,
								}

							],

						});
						
						$('#mapBg').gMap({
							controls: {
								panControl: false,
								zoomControl: false,
								mapTypeControl: false,
								scaleControl: false,
								streetViewControl: false,
								overviewMapControl: false
							},
							maptype: 'ROADMAP',
							scrollwheel: true,
							zoom: 13,
							markers: [
								{
									address: 'Xprojets Ecole polytechnique Route de Saclay 1120 Palaiseau FRANCE', // Your Adress Here
									html: '',
									popup: false,
								}

							],

						});
});