$=jQuery;
$(document).ready(function() { 
	$("input:radio").bind('click',function(e){
		$("#price_excel_input_name").css('display','block');
		if($(this).val()=='price_new'){
			$("#price_excel_name_new").css('display','block');
			$("#price_excel_name_list").css('display','none');
		}
		if(($(this).val()=='price_add')||($(this).val()=='price_upd')){
			$("#price_excel_name_new").css('display','none');
			$("#price_excel_name_list").css('display','block');			
		}
	});
});