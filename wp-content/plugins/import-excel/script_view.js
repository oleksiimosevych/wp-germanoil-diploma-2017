$=jQuery;
$(document).ready(function() { 
	//$('#price_excel_table tr:eq(10) td:eq(5)').css('color','green');
	$('#price_excel_table .red').bind('dblclick',function(e){
		var el=this;
		var value=$(el).text();
		var id=$(el).attr('id');
		$(el).html("<input type=\"text\" value='"+value+"' id=\"input_"+id+"\"/>");

		$("input[type='text']#input_"+id).focus();

		$("input[type='text']#input_"+id).keypress(function (e) {
      		if (e.which == 13) {
				var str=$("#price_excel_data_update").html();
				$("#price_excel_data_update").html(str+"<input type=\"hidden\" value='"+$(this).val()+"' name=\""+id+"\"/>");
	      		$("#"+id).text($(this).val());
      		}
    	});
		$("input[type='text']#input_"+id).bind('blur',function (e) {
			var str=$("#price_excel_data_update").html();
			$("#price_excel_data_update").html(str+"<input type=\"hidden\" value='"+$(this).val()+"' name=\""+id+"\"/>");
	      	$("#"+id).text($(this).val());
    	});

		$('#price_excel_save').css('display','block');
	});

	$('#func_stolbec').bind('change',function(){
		var func_stolbec=this;
		if($(func_stolbec).val()=='none'){
			$('#position_stolbec').css('display','none');
			$('#nomer_stolbec').css('display','none');
		}
		if($(func_stolbec).val()=='del'){
			$('#position_stolbec').css('display','none');
			$('#nomer_stolbec').css('display','block');
		}
		if($(func_stolbec).val()=='add'){
			$('#nomer_stolbec').css('display','none');
			$('#position_stolbec').css('display','block');
			$('#position_stolbec').bind('change',function(){
				var position_stolbec=this;
				if($(position_stolbec).val()!='none'){
					$('#nomer_stolbec').css('display','block');
				}
			});
		}
	});

});
