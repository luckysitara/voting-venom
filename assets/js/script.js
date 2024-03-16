$(document).ready(function(){
	
	$('.ewvwp-product').each(function(i, el){					

		// Lift card and show stats on Mouseover
		$(el).find('.ewvwp-make3D').hover(function(){
				$(this).parent().css('z-index', "20");
				$(this).addClass('animate');
				$(this).find('div.carouselNext, div.carouselPrev').addClass('visible');			
			 }, function(){
				$(this).removeClass('animate');			
				$(this).parent().css('z-index', "1");
				$(this).find('div.carouselNext, div.carouselPrev').removeClass('visible');
		});	
		
		// Flip card to the back side
		$(el).find('.ewvwp_view_gallery').click(function(){	
			
			$(el).find('div.carouselNext, div.carouselPrev').removeClass('visible');
			$(el).find('.ewvwp-make3D').addClass('flip-10');			
			setTimeout(function(){					
			$(el).find('.ewvwp-make3D').removeClass('flip-10').addClass('flip90').find('div.ewvwp-shadow').show().fadeTo( 80 , 1, function(){
					$(el).find('.ewvwp-product-front, .ewvwp-product-front div.ewvwp-shadow').hide();															
				});
			}, 50);
			
			setTimeout(function(){
				$(el).find('.ewvwp-make3D').removeClass('flip90').addClass('flip190');
				$(el).find('.ewvwp-product-back').show().find('div.ewvwp-shadow').show().fadeTo( 90 , 0);
				setTimeout(function(){				
					$(el).find('.ewvwp-make3D').removeClass('flip190').addClass('flip180').find('div.ewvwp-shadow').hide();						
					setTimeout(function(){
						$(el).find('.ewvwp-make3D').css('transition', '100ms ease-out');			
						$(el).find('.ewvwp-cx, .ewvwp-cy').addClass('s1');
						setTimeout(function(){$(el).find('.ewvwp-cx, .ewvwp-cy').addClass('s2');}, 100);
						setTimeout(function(){$(el).find('.ewvwp-cx, .ewvwp-cy').addClass('s3');}, 200);				
						$(el).find('div.carouselNext, div.carouselPrev').addClass('visible');				
					}, 100);
				}, 100);			
			}, 150);			
		});			
		
		// Flip card back to the front side
		$(el).find('.ewvwp-flip-back').click(function(){		
			
			$(el).find('.ewvwp-make3D').removeClass('flip180').addClass('flip190');
			setTimeout(function(){
				$(el).find('.ewvwp-make3D').removeClass('flip190').addClass('flip90');
		
				$(el).find('.ewvwp-product-back div.ewvwp-shadow').css('opacity', 0).fadeTo( 100 , 1, function(){
					$(el).find('.ewvwp-product-back, .ewvwp-product-back div.ewvwp-shadow').hide();
					$(el).find('.ewvwp-product-front, .ewvwp-product-front div.ewvwp-shadow').show();
				});
			}, 50);
			
			setTimeout(function(){
				$(el).find('.ewvwp-make3D').removeClass('flip90').addClass('flip-10');
				$(el).find('.ewvwp-product-front div.ewvwp-shadow').show().fadeTo( 100 , 0);
				setTimeout(function(){						
					$(el).find('.ewvwp-product-front div.ewvwp-shadow').hide();
					$(el).find('.ewvwp-make3D').removeClass('flip-10').css('transition', '100ms ease-out');		
					$(el).find('.ewvwp-cx, .ewvwp-cy').removeClass('s1 s2 s3');			
				}, 100);			
			}, 150);			
			
		});
	});
});