<?php
if($_GET["source"] == "true") { highlight_file(__FILE__); exit; }
session_start();

if($_POST['login']){ login($_POST['email'], $_POST['pass']); }
if($_POST['buddylist']){ retrieveBuddyList(); }
if($_POST['debug']){ retrieveUserInfo(); }
if($_POST['getUserID']){ echo $_SESSION['userID']; }

/*
**	requestUrl(): generic cURL request
**	returns result of cURL session (string)
*/
function requestUrl($url, $post = false, $header = true)
{
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    if($post)
    {
        curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
    }
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST,  2);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_HEADER, $header);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, false);
    curl_setopt($ch, CURLOPT_COOKIE, $_SESSION['cookies']);
    curl_setopt($ch, CURLOPT_FRESH_CONNECT, true);
    curl_setopt($ch, CURLOPT_MAXCONNECTS, 20);
    curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Macintosh; U; Intel Mac OS X 10.4; en-US; rv:1.9b5) Gecko/2008032619 Firefox/3.0b5');
    $result = curl_exec($ch);
    preg_match_all('|Set-Cookie: (.*);|U', $result, $results);
    $_SESSION['cookies'] .= implode(';', $results[1]);
    curl_close($ch);
    return $result;
}

/*
** getId(): retrieves your Facebook UserID
** returns bool if successful or not
*/
function getId()
{
    $result = requestUrl("http://www.facebook.com/home.php");
    if(strpos($result, 'Facebook | Home') != false)
    {
    	//get userID
        $startIndex = strpos($result, 'value="', strpos($result, '<input type="hidden" id="user" name="user"')) + 7;
        $_SESSION['userID'] = substr($result, $startIndex, strpos($result, '"', $startIndex + 1) - $startIndex);
        
        //get post_form_id
        $startIndex = strpos($result, 'value="', strpos($result, '<input type="hidden" id="post_form_id" name="post_form_id"')) + 7;
        $_SESSION['postFormID'] = substr($result, $startIndex, strpos($result, '"', $startIndex + 1) - $startIndex);
        
        echo "post_form_id: ".$_SESSION['postFormID'];
        return true;
    }
    return false;
}

/*
** login(): logs a user into Facebook in order to receive the cookie
** returns a users ID (int)
*/
function login($email, $password)
{
    $_SESSION['cookies'] = ""; //Reset the cookies for every login, so it looks "fresh" every time the user screws up his password.
    if(getId())
    {
        return true;
    }
    else
    {
        requestUrl("https://login.facebook.com/login.php"); //We have to simulate requesting the login screen before submitting to it.
        requestUrl("https://login.facebook.com/login.php", 'email='.$email.'&pass='.$password);
        return getId();
    }
}

/*
** retrieveBuddyList(): fetches Facebook chat buddy list
** returns nothing, outputs buddy list in unordered html list
*/
function retrieveBuddyList()
{
	$result = requestUrl("http://www.facebook.com/ajax/presence/update.php?buddy_list=1", 'buddy_list=1&user='.$_SESSION['userID'].'&force_render=true&popped_out=false');
			
	$data = json_decode(substr($result, strpos($result, "{")), true);

	echo "<h3>Buddy List</h3>";
	
	echo "<ul>";
	
	//extract userID's from array
	$keys = array_keys($data['payload']['buddy_list']['userInfos']);
	$i=0;
	
	foreach($data['payload']['buddy_list']['userInfos'] as $user)
	{
		if($keys[$i]!=$_SESSION['userID'])
		{
			echo "<li><a href='javascript:openChat(".$keys[$i].", \"".$user['name']."\")'>".$user['name']."</a></li>";
		}
		$i++;
	}
	echo "</ul>";
	
	/*echo "<h3>Debugging Info</h3>";
	var_dump($data['payload']['buddy_list']['userInfos']);*/
}

?>