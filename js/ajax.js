/*
**	facebookLogin(): logs a user into Facebook and sets up page
**	hides #loginform
*/
function facebookLogin()
{
	var email = document.loginform.email.value;
	var pass = document.loginform.pass.value;
	
	$('#status').load('includes/remote.php', {login: true, email: email, pass: pass}, function(){
		//Initial buddy list load
		retrieveBuddyList();
		//create a periodical updater for the buddy list
		$.schedule({time: 60000, repeat: true},function(){
 			$('#buddylist').load("includes/remote.php", {buddylist: true});
 		});
 		
 		//Sets up ajax session for receiving messages
 		openChatConnection();
	});
	
	$("#loginform").hide('slow');
	
}

/*
**	retrieveBuddyList(): fetches buddy list from Facebook
**	updates #buddylist
*/
function retrieveBuddyList()
{
	$('#buddylist').load("includes/remote.php", {buddylist: true});	
}

/*
**	openChat(): Sets page up for chat with a user
**	updates #chatname and #chatbox
*/
function openChat(userID, username)
{
	$("#chatname").html("Chat with " + username);
	$("#chatbox").show('slow');
	document.chatform.userchatID.value = userID;
}

/*
**	sendChat(): pulls chat message from textarea and sends it to PHP for processing
**	updates #msglist with your message
*/
function sendChat()
{
	var userchatID = document.chatform.userchatID.value;
	var chatcontent = document.chatform.chatcontent.value;
	
	$.post('includes/send.php', {recipient: userchatID, chatcontent: chatcontent});
	
	$("#msglist").append("<li>You: "+chatcontent+"</li>");
	document.chatform.chatcontent.value = '';
}

/*
**	openChatConnection(): retrieves msg seq number and users channel number, then opens connection to Facebook for message receiving
**	updates nothing; behind the scenes ajax
*/
function openChatConnection()
{
	var userID='';
	var seq='';
	var channelnum = '';
	var splitdata='';
	var anything = Math.floor(Math.random()*100000);
	var keepalive = true;
	
	//Retrieves userID
	$.post('includes/remote.php', {getUserID: true}, function(data)
	{
		userID = data;
		
		//determine seq number and channel num for user
		$.post('includes/chat.php', {connectionURL: true}, function(data){
			splitdata = data.split(",");
			seq = splitdata[0];
			channelnum = splitdata[1];
			
			waitForMsg(channelnum, userID, seq);
			
		});
	});
}

/*
**	waitForMsg(): waits for a repsonse from Facebook's comet server, then parses it
**	updates nothing; behind the scenes ajax
*/
function waitForMsg(channelnum, userID, seq)
{
	//make our connection url
	var connectURL = 'http://0.channel'+channelnum+'.facebook.com/x/0/false/p_'+userID+'='+seq;
	
	$.ajax({
	    type: "GET",
	    url: 'includes/chat.php',
	    data: 'msgwait=true&URL='+connectURL,
	    success: function(data){
	    	var returndata = JSON.parse(data);

	    	if(returndata['t']=='continue')
	    	{
	    		waitForMsg(channelnum, userID, seq);
	    	}
	    	else if(returndata['t']=='msg' && returndata['ms'][0]['type']=='msg' && returndata['ms'][0]['from']!=userID)
	    	{
	    		$("#msglist").append("<li>"+returndata['ms'][0]['from_first_name']+": "+returndata['ms'][0]['msg']['text']+"</li>");
    			seq = parseInt(seq)+1;
    			waitForMsg(channelnum, userID, seq);
	    	
	    	}
	    	else if(returndata['t']=="msg" && returndata['ms'][0]['from']==userID)
	    	{
	    		seq = parseInt(seq)+1;
	    		waitForMsg(channelnum, userID, seq);
	    	}
	    	else if(returndata['t']=="msg" && returndata['ms'][0]['type']=='typ')
	    	{
	    		seq = parseInt(seq)+1;
	    		waitForMsg(channelnum, userID, seq);
	    	}
	    	else if(returndata['t']=="refresh")
	    	{	    		
	    		facebookLogin();
	    	}
	    }
	});
}