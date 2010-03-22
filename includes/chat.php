<?php
include_once('remote.php');

if($_POST['connectionURL']){ getSequenceNum(); }
if($_GET['msgwait']){ waitForMsg($_GET['URL']); }

/*
**	getSequenceNum(): finds sequence number of current conversation
**	echoes sequence number
*/
function getSequenceNum()
{
	$trychannel=true;
	$channelnum=1;
	while($trychannel)
	{
		if($channelnum<10){ $channel='0'.$channelnum; }
		else{ $channel=$channelnum; }
		
		$url="http://0.channel$channel.facebook.com/x/0/false/p_".$_SESSION['userID']."=-1";
		$result = requestUrl($url);
		$data = json_decode(substr($result, strpos($result, "{")), true);
		//var_dump($data);
		if(count($data)==2)
		{
			$_SESSION['userChannel'] = $channel;
			$trychannel=false;
			echo $data['seq'].",".$channel;
		}
		elseif($channelnum>20)
		{
			echo "Channel find fail";
			$trychannel=false;
		}
		else
		{
			$channelnum++;
		}
	}
}

/*
**	waitForMsg(): opens cURL connection to Facebook comet server for message response
**	echoes returned data from Facebook
*/
function waitForMsg($url)
{
	$result = requestUrl($url, false, false);
	$data = substr($result, strpos($result, "{"));
	echo $data;
}




?>