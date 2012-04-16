<?php
define('FORUM_ROOT','../../');
require FORUM_ROOT.'include/common.php';


$mypath = dirname(__FILE__);
if (file_exists($mypath.'/lang/'.$forum_user['language'].'php'))
	require $mypath.'/lang/'.$forum_user['language'].'.php';
else
	require $mypath.'/lang/English.php';
//csrf_confirm_form();

if(!$forum_user['is_admmod']) die($lang_common['No view']);


//check if the user already received a warning for this post
$query = array(
		'SELECT'		=>	'id',
		'FROM'			=>	'warning',
		'WHERE'			=>	"user_id='".$forum_db->escape($_REQUEST['w_uid'])."' AND post_id = '".$forum_db->escape($_REQUEST['w_pid'])."'",
		'LIMIT'			=>	1,
);

$result = $forum_db->query_build($query) or die($forum_db->error());

if($forum_db->num_rows($result)>0){
	die($lang_warning['Already warned']);
}

$query = array(
			'INSERT'		=> 'user_id, reason, topic_id, post_id, moderator_id, posted',
			'INTO'			=> 'warning',
			'VALUES'		=> "'".$forum_db->escape($_REQUEST['w_uid'])."', '".$forum_db->escape($_REQUEST['w_reason'])."', '".$forum_db->escape($_REQUEST['w_tid'])."', '".$forum_db->escape($_REQUEST['w_pid'])."', '".$forum_user['id']."','".time()."'",
			);

$result = $forum_db->query_build($query) or die($forum_db->error());

if($result) {
	//email the user about this warning
	email_user_warning($_REQUEST['w_uid'],$_REQUEST['w_reason'],$_REQUEST['w_tid'],$_REQUEST['w_pid']);
	printf($lang_warning['Warning sent'],$_REQUEST['w_username']);
} else {
	printf($lang_warning['Warning send failed'],$_REQUEST['w_username']);
}

function email_user_warning($uid,$reason,$topic,$post){
	global $forum_config, $forum_db,$mypath,$base_url,$forum_url, $lang_warning;
	//get the language of the user who are going to be warned so that we can send warning on his own language
	$query = array(
		'SELECT'		=>	'username, language, email',
		'FROM'			=>	'users',
		'WHERE'			=>	"id='".$uid."'",
		'LIMIT'			=>	'1',
	);
	$result = $forum_db->query_build($query) or die($forum_db->error());
	$result = $forum_db->fetch_assoc($result);
	if(file_exists($mypath.'/lang/'.$result['language'].'.tpl')){
		$temp = file_get_contents($mypath.'/lang/'.$result['language'].'.tpl');
	} else {
		$temp = file_get_contents($mypath.'/lang/English.tpl');
	}
//echo $temp;
	$search =array('%user%','%forum%','%post%','%reason%');
	$replace = array(forum_htmlencode($result['username']),$forum_config['o_board_title'],forum_link($forum_url['post'],$post),forum_htmlencode($reason));

	$temp = str_replace($search,$replace,$temp);

	//now email user
	require FORUM_ROOT.'include/email.php';
	forum_mail($result['email'],$lang_warning['Warning subject'],$temp);
	//echo 'Mail sent';
}