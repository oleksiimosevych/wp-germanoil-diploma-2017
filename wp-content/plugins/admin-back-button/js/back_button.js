jQuery(document).ready(function($) {

"use strict";

var edit=0;
if (ba_bu_settings.version=='tag')
	{
	$('p.submit input#submit').after('<input type="button" name="ba_bu_back" id="ba_bu_back" class="button button-primary ba_bu_tag" value="'+ba_bu_settings.value_back+'">');
	$('#edittag input').change( function() {
		edit=1;
		});
	}
else if (ba_bu_settings.version=='post' || ba_bu_settings.version=='post-new')
	{
	$('#major-publishing-actions #publishing-action').after('<div id="ba_bu_action"><input type="button" name="ba_bu_back" id="ba_bu_back" class="button button-primary ba_bu_post" value="'+ba_bu_settings.value_back+'"></div>');
	}
else if (ba_bu_settings.version=='link')
	{
	localStorage.setItem('link',ba_bu_settings.link);
	}

$('input#ba_bu_back').click( function ()
{
var finall;
if (ba_bu_settings.version!='post-new')
	{
	finall=ba_bu_settings.link+localStorage.getItem('link');
	}
else
	{
	finall=ba_bu_settings.link;
	}
if (edit==1)
	{
	if (confirm(ba_bu_settings.value_question)) {
        window.location=finall;
		}
	}
else
	{
	window.location=finall;
	}
});

});
