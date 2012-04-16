<?php
define('FORUM_ROOT','../../');
require FORUM_ROOT.'include/common.php';


$mypath = dirname(__FILE__);
$myurl = $base_url.'/'.basename(dirname(dirname(__FILE__))).'/'.basename(dirname(__FILE__));

//load language file
if (file_exists($mypath.'/lang/'.$forum_user['language'].'.php'))
	require $mypath.'/lang/'.$forum_user['language'].'.php';
else
	require $mypath.'/lang/English.php';

$action = isset($_GET['action']) ? $_GET['action'] : '';

$uid = (empty($_REQUEST['uid'])) ? $forum_user['id'] : $_REQUEST['uid'];
$uid = intval($uid);

if($forum_user['is_guest']) message($lang_common['No view']);

if($action == 'markread' OR $action == 'markunread'){
	if($forum_user['id'] != $uid AND !$forum_user['is_admmod']){
		message($lang_common['No view']);
	}
	//csrf_confirm_form();
	$val = ($_GET['action']=='markread') ? time() : 'NULL';
	$warning_msg = ($_GET['action']=='markread') ? $lang_warning['Marked as read'] : $lang_warning['Marked as unread'];
	$query = array(
		'UPDATE'	=>	'warning',
		'SET'		=>	"read_at=$val",
		'WHERE'		=>	"id='".$forum_db->escape($_REQUEST['wid'])."' AND user_id='".$forum_user['id']."'",
	);
	$result = $forum_db->query_build($query) or error(__FILE__, __LINE__);
	if($forum_db->affected_rows()>0){
		redirect($forum_user['prev_url'],$warning_msg);
	} else{
		message($lang_warning['Marking error']);
	}
}

//Delete block
if($action == 'delete'){
	if(!$forum_user['is_admmod'] OR empty($_REQUEST['wid'])) message($lang_common['No view']);

	$query = array(
		'DELETE'	=>	'warning',
		'WHERE'		=>	"id='".$forum_db->escape($_REQUEST['wid'])."'"
	);

	$result = $forum_db->query_build($query) or error(__FILE__, __LINE__);

	if($forum_db->affected_rows()>0){
		redirect($forum_user['prev_url'],$lang_warning['Warning deleted']);
	} else {
		message($lang_warning['Marking error']);
	}
}


//show warnings block
if ($action == 'show') {

if(!defined('FORUM_PAGE'))
	@define('FORUM_PAGE', 'warning');



	if(!$forum_user['is_admmod'] AND $forum_user['id']!=$uid)
		message($lang_common['Bad request']);

	//get username of the uid
	$query = $forum_db->query_build(array(
					'SELECT'	=>	'username',
					'FROM'		=> 	'users',
					'WHERE'		=>	"id='".$forum_db->escape($uid)."'"
	));
	$result = $forum_db->fetch_assoc($query) or error(__FILE__, __LINE__);


	// Setup breadcrumbs
	$forum_page['crumbs'] = array(
		array($forum_config['o_board_title'], forum_link($forum_url['index'])),
		array(sprintf($lang_warning['Breadcrumb'],forum_htmlencode($result['username'])),forum_link($forum_url['profile_about'], $uid)),
	);

	ob_start();
	require FORUM_ROOT.'header.php';


	if (!defined('FORUM_PARSER_LOADED'))
		require FORUM_ROOT.'include/parser.php';

	//prepare the summary.
	$query = array(
			'SELECT'	=>	'w.*, t.subject, u.username',
			'FROM'		=>	'warning as w',
			'JOINS'		=> array(
				array(
					'JOIN'		=>	'topics as t',
					'ON'		=>	't.id=w.topic_id'
				),
				array(
					'JOIN'		=>	'users as u',
					'ON'		=>	'u.id=w.moderator_id',
				),
			),
			'WHERE'		=>	"w.user_id = '".$forum_db->escape($uid)."'",
	);
	$result = $forum_db->query_build($query) or error(__FILE__, __LINE__);


	?>

	<div class="main-subhead">
		<h2 class="hn"><span><?php echo forum_htmlencode(sprintf($lang_warning['Num warnings'],forum_number_format($forum_db->num_rows($result))))?></span></h2>
	</div>
	<style>
	#reputation td{
	overflow:hidden;

	}
	.reason, .subject{
	width:30%;
	}

	</style>
	<div id="warning" class="main-content main-frm">
		<table>
			<thead><tr>

			<td class="subject"><b><?php echo $lang_warning['For topic']; ?></b></td><td class="reason"><b><?php echo $lang_warning['Warning reason']; ?></b></td><td><b><?php echo $lang_warning['Warning date']; ?></b></td>
			<?php
			if($forum_user['is_admmod']){
				echo '<td><b>'.$lang_warning['Warned by'].'</b></td>';
				echo '<td width="10px"><b>'.$lang_warning['Del'].'</b></td>';
			}
			if($forum_user['id']==$uid){
				echo '<td></td>';
			}
			?>
			</tr></thead>
			<?php
			while (false != ($row = $forum_db->fetch_assoc($result)))
			{
				?>
				<tbody><tr>
				<?php
				if(is_null($row['reason'])){
					echo '<td>'.$lang_warning['Deleted'].'</td>';
				} else {
					echo '<td class="subject"><a href="'.forum_link($forum_url['post'],$row['post_id']).'">'.$row['subject'].'</a></td>';
				}
				if(empty($row['read_at'])){
					echo '<td><b>'.parse_message($row['reason'],0).'</b></td>';
				} else {
					echo '<td>'.parse_message($row['reason'],0).'</td>';
				}
				echo '<td>'.format_time($row['posted']).'</td>';
				if($forum_user['is_admmod']) {
					echo '<td><a href="'.forum_link($forum_url['user'],$row['moderator_id']).'">'.forum_htmlencode($row['username']).'</td>';
					echo '<td><a onclick="return confirmwarningdel()" href="'.$myurl.'/userwarnings.php?action=delete&wid='.$row['id'].'" alt="X">X</a></td>';
				}

				if($forum_user['id']==$uid){
					if(empty($row['read_at'])){
						echo '<td><a href="'.$myurl.'/userwarnings.php?action=markread&wid='.$row['id'].'">'.$lang_warning['Mark as read'].'</a></td>';
					} else {
						echo '<td><a href="'.$myurl.'/userwarnings.php?action=markunread&wid='.$row['id'].'">'.$lang_warning['Mark as unread'].'</a></td>';
					}
				}
			}
			echo '</table></div>';
}
$tpl_temp = forum_trim(ob_get_contents());
$tpl_main = str_replace('<!-- forum_main -->', $tpl_temp, $tpl_main);
ob_end_clean();
// END SUBST - <!-- forum_qpost -->



require FORUM_ROOT.'footer.php';
