jQuery(function() {
	// Use this example, or...
        
        var lightBoxInit = {
		overlayBgColor: '#000',
		overlayOpacity: 0.8,
		imageLoading: '/img/jquery-lightbox/lightbox-ico-loading.gif',
		imageBtnClose: '/img//jquery-lightbox/lightbox-btn-close.gif',
		imageBtnPrev: '/img/jquery-lightbox/prevlabel.gif',
		imageBtnNext: '/img/jquery-lightbox/nextlabel.gif',
		containerResizeSpeed: 350,
		txtImage: 'Изображений',
		txtOf: 'из'

	};
        
	jQuery('a[rel*=lightbox]').lightBox(lightBoxInit);
	
	jQuery('a[rel*=lbox]').lightBox(lightBoxInit);
        
        jQuery('a[rel*=xbox]').lightBox(lightBoxInit);
        jQuery('a[rel*=txbox]').lightBox(lightBoxInit);
        
        
	

	// Select all links that contains lightbox in the attribute rel
	
	
});