function show_warning_box(obj,username,uid,pid,tid){
	var pos = $(obj).offset();
	$('#warning_box').css("position","fixed");
	$('#warning_box').css("position","fixed");
	$('#warning_box').css("top",100+'px');
	$('#warning_box').css("left",pos.left+'px');
	$('#divusername').text(username);
	$('#w_username').attr("value",username);
	$('#w_uid').attr("value",uid);
	$('#w_tid').attr("value",tid);
	$('#w_pid').attr("value",pid);
	$('#warning_box').show('slow');
	return false;
}
function confirmwarningdel(){
	return confirm("Are you sure that you want to delete this warning?");
}
$(document).ready(function(){
	$('#frmwarning').submit(function(){
		var params= $('#frmwarning').serialize();
		$.ajax({
			type: 'GET',
			url: $('#frmwarning').attr('action'),
			data: params,
			success: function(result){
				alert(result);
				$('#warning_box').hide('slow');
				//ban the user
				var ban = $('#w_ban:checked').val();
				if(ban==1){
					//ask if the moderator wants to ban
					var con = confirm("Are you sure that you want to ban this user?");
					if(con){
						//redirect to ban page
						document.location = 'admin/bans.php?add_ban='+$('#w_uid').val();
					}
				}
				return false;
			},
		});
		return false;
	});
	$('.w_show').dblclick(function(){
		window.location = $(this).attr("href");
	});
});