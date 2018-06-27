
$(document).ready(function() {
				try{
					$("#billing_phone").mask("+38 (999) 999-9?9-99");//nomer znukaje jaksho menshe niz do ? zifr vvedeno
				}catch(err){ $.getScript(""); }

				$("#billing_phone").on("blur", function() {
				    var last = $(this).val().substr( $(this).val().indexOf("-") + 1 );
				    
				    if( last.length == 3 ) {
				        var move = $(this).val().substr( $(this).val().indexOf("-") - 1, 1 );
				        var lastfour = move + last;
				        
				        var first = $(this).val().substr( 0, 9 );
				        
				        $(this).val( first + '-' + lastfour );
				    }
				});


			})
