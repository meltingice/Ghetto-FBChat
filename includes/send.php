<?php
include_once('remote.php');

if($_POST['recipient']){ sendChat($_POST['recipient'], $_POST['chatcontent']); }

/*
**	sendChat(): Sends a message to a specified user
**	echoes sent chat content for debugging purposes
*/
function sendChat($recipient, $chatcontent)
{
	$post = "msg_text=".stripslashes(str_replace('&','%26',$chatcontent))."&msg_id=".rand(100000000,999999999)."&to=$recipient&client_time=".time()."&post_form_id=".$_SESSION['postFormID'];
	$result = requestUrl("http://www.facebook.com/ajax/chat/send.php", $post);
	echo $chatcontent;
}
?>