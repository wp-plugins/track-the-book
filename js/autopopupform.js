var TTB_AUTOLAUNCH = 1;

jQuery(document).ready(function(){
	if (typeof tb_show == 'function') {
		tb_show('','?view=trackthebook_form&width=560&height=360','');
	}
	else if (typeof TB_show == 'function') {
		TB_show('','?view=trackthebook_form&width=560&height=360','');
	}
});
