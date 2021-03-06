<?php
require './include/common.inc.php';
$action = trim($action) ? $action : '';
if($action != 'ajaxcheckcode')
{
    if(!$M['ischecklogin']) {if(!$_userid) showmessage('请先登陆后在发表',$MODULE['member']['url'].'login.php?forward='.urlencode(URL));}
}
$head['keywords'] = $PHPCMS['keywords'];
$head['description'] = $PHPCMS['description'];
require_once PHPCMS_ROOT.'/include/form.class.php';
require_once  MOD_ROOT.'/include/comment.class.php';
$comments = new comment();

$comments->create_right_comment();
switch ( $action )
{
	
	case 'vote':
		$count = $comments->ajaxupdate($field, $id);
		echo ' '.$LANG[$field].'('.$count[$field].')';
	break;
	case 'add':
        $url = "?keyid=$keyid&verify=$verify";
		$content = new_htmlspecialchars($contenttext);
        $content = trim($content);
        if(strlen($content) >= 1000) showmessage('内容太长，最多1000个文字',$url);
		if(empty($content)) showmessage('内容不能为空',$url);
		$keyid = trim($keyid);
		if($comments->add($commentid, $content, $keyid)) showmessage('回复成功',$url);
	break;
	case 'comment':
		$post = $comments->ajaxpost();
		echo $post;
	break;
	default:
        $keyid = trim($keyid);
        $verify = trim($verify);
        if(empty($keyid) || !keyid_verify($keyid, $verify)) showmessage('非法操作');
		$setting = cache_read('module_comment.php');
		$content = keyid_get($keyid);
        $url      = reSite($content['url']);
		$head['title'] = $title   = $content['title'];
		$pagesize	= $setting['maxnum'] ? $setting['maxnum'] : 10;
		$page		= isset($page) ? intval($page) : 1;
		if($_GET['t']) 
			$t = 1; 
		else
			$t = 0;
		$comments = $comments->get_list($keyid,$page, $pagesize, $t);
		if(!$t) $pages = $comments['pages'];
		include template('comment', 'list');//reply
	break;
	case 'addpost':
		
        checkcode($checkcode,$M['enablecheckcode']);
		$content = new_htmlspecialchars($comment);
        $content = trim($content);
        if(strlen($content) >= 1000)
        {
            showmessage('内容太长，最多1000个文字');
        }
        
		if(empty($content)) showmessage('内容不能为空');
        $keyid = trim($keyid);

		if($comments->addpost($content, $keyid, $niming)){
        showmessage('发表成功', $M['url'].'?keyid='.$keyid.'&verify='.$verify);
        } else {
        	echo $M['url'].'?keyid='.$keyid.'&verify='.$verify;
        	phpinfo();exit;
        }
	break;

	case 'wcpost':
        checkcode($checkcode,$M['enablecheckcode']);
		$content = new_htmlspecialchars($comment);
        $content = trim($content);
        if(strlen($content) >= 1000)
        {
            showmessage('内容太长，最多1000个文字');
        }
		if(empty($content)) showmessage('内容不能为空');
        $keyid = trim($keyid);
		if(strpos($keyid,'18'))
		{
			if($comments->addpost($content, $keyid)) 
			{
				echo 'true';
				exit;
			}
		}
		else
		{
			if($comments->addpost($content, $keyid))
			showmessage('发表成功', $M['url'].'?keyid='.$keyid.'&verify='.$verify);
		}

	break;

	case 'wctest':
        checkcode($checkcode,$M['enablecheckcode']);
		$content = new_htmlspecialchars($comment);
        $content = trim($content);
        if(strlen($content) >= 1000)
        {
            showmessage('内容太长，最多1000个文字');
        }
		if(empty($content)) showmessage('内容不能为空');
        $keyid = trim($keyid);
		if(strpos($keyid,'18') || strpos($keyid,'ezone'))
		{
			if($comments->addpost($content, $keyid)) 
			{
				$get_callback=$_GET['callback'];
				echo $get_callback."('true')";
				exit;
			}
		}
		else
		{
			if($comments->addpost($content, $keyid))
			showmessage('发表成功', $M['url'].'?keyid='.$keyid.'&verify='.$verify);
		}

	break;

    case 'ajaxpost':
        if(empty($keyid) || !keyid_verify($keyid, $verify)) showmessage('非法操作');
        $content = keyid_get($keyid);
        $title   = $content['title'];
        include template('comment', 'load');
    break;
    case 'ajaxcheckcode':
        if($M['enablecheckcode'])
        {
            $code = form::checkcode('checkcode',5);
            echo $code;
        }
        else
        {
            $code = '';
            echo $code;
        }
    break;
}
mysql_close();
?>