<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" lang="en-US" xml:lang="en-US">
<head>
<title>FBChat</title>
<script src="js/json2.js" type="text/javascript"></script>
<script src="js/jquery-1.2.6.js" type="text/javascript"></script>
<script src="js/jquery.schedule.js" type="text/javascript"></script>
<script src="js/ajax.js" type="text/javascript"></script>
</head>
<body>
<div id="links"><p><a href="javascript:retrieveBuddyList()">Manually Refresh Buddy List</a></p></div>
<div id="loginform">
<form action="javascript:facebookLogin()" name="loginform">
	<p>Email: <input name="email" type="text" /></p>
	<p>Password: <input name="pass" type="password" /></p>
	<p><input type="submit" value="login" name="submitbutton" /></p>
</form>
</div>
<div id="status"></div>
<div id="buddylist"></div>
<div id="chat">
	<h3 id="chatname">Select a user to begin chatting</h3>
	<div id="msgarea">
		<ul id="msglist">
			
		</ul>
	</div>
	<form action="javascript:sendChat()" name="chatform">
		<textarea rows='10' cols='50' name='chatcontent' tabindex='2'></textarea>
		<input type="hidden" name="userchatID" value="" />
		<p><input type="submit" name="submitchat" value="Send" /></p>
	</form>
</div>
</body>
</html>