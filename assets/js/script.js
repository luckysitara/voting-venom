$(document).ready(function(){
	
	$('.venom-product').each(function(i, el){					

		// Lift card and show stats on Mouseover
		$(el).find('.venom-make3D').hover(function(){
				$(this).parent().css('z-index', "20");
				$(this).addClass('animate');
				$(this).find('div.carouselNext, div.carouselPrev').addClass('visible');			
			 }, function(){
				$(this).removeClass('animate');			
				$(this).parent().css('z-index', "1");
				$(this).find('div.carouselNext, div.carouselPrev').removeClass('visible');
		});	
		
		// Flip card to the back side
		$(el).find('.venom_view_gallery').click(function(){	
			
			$(el).find('div.carouselNext, div.carouselPrev').removeClass('visible');
			$(el).find('.venom-make3D').addClass('flip-10');			
			setTimeout(function(){					
			$(el).find('.venom-make3D').removeClass('flip-10').addClass('flip90').find('div.venom-shadow').show().fadeTo( 80 , 1, function(){
					$(el).find('.venom-product-front, .venom-product-front div.venom-shadow').hide();															
				});
			}, 50);
			
			setTimeout(function(){
				$(el).find('.venom-make3D').removeClass('flip90').addClass('flip190');
				$(el).find('.venom-product-back').show().find('div.venom-shadow').show().fadeTo( 90 , 0);
				setTimeout(function(){				
					$(el).find('.venom-make3D').removeClass('flip190').addClass('flip180').find('div.venom-shadow').hide();						
					setTimeout(function(){
						$(el).find('.venom-make3D').css('transition', '100ms ease-out');			
						$(el).find('.venom-cx, .venom-cy').addClass('s1');
						setTimeout(function(){$(el).find('.venom-cx, .venom-cy').addClass('s2');}, 100);
						setTimeout(function(){$(el).find('.venom-cx, .venom-cy').addClass('s3');}, 200);				
						$(el).find('div.carouselNext, div.carouselPrev').addClass('visible');				
					}, 100);
				}, 100);			
			}, 150);			
		});			
		
		// Flip card back to the front side
		$(el).find('.venom-flip-back').click(function(){		
			
			$(el).find('.venom-make3D').removeClass('flip180').addClass('flip190');
			setTimeout(function(){
				$(el).find('.venom-make3D').removeClass('flip190').addClass('flip90');
		
				$(el).find('.venom-product-back div.venom-shadow').css('opacity', 0).fadeTo( 100 , 1, function(){
					$(el).find('.venom-product-back, .venom-product-back div.venom-shadow').hide();
					$(el).find('.venom-product-front, .venom-product-front div.venom-shadow').show();
				});
			}, 50);
			
			setTimeout(function(){
				$(el).find('.venom-make3D').removeClass('flip90').addClass('flip-10');
				$(el).find('.venom-product-front div.venom-shadow').show().fadeTo( 100 , 0);
				setTimeout(function(){						
					$(el).find('.venom-product-front div.venom-shadow').hide();
					$(el).find('.venom-make3D').removeClass('flip-10').css('transition', '100ms ease-out');		
					$(el).find('.venom-cx, .venom-cy').removeClass('s1 s2 s3');			
				}, 100);			
			}, 150);			
			
		});
	});
});