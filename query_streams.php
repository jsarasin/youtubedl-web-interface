<?php
set_time_limit(20);
// https://www.youtube.com/watch?v=BdXtIJNNVZM

/** Sample Arguments **/
// $cat = <<<'CAT'
// {"title":"PINK GUY - KILL YOURSELF | FilthyFrank (Sub Espa\u00f1ol) [HD]","is_live":null,"duration":156,"thumbnail":"https:\/\/i.ytimg.com\/vi\/5kC4_iEXhGc\/maxresdefault.jpg","description":"Se que ya estaba traducida pero prefer\u00ed traducirla yo tambi\u00e9n y as\u00ed queda en el canal","formats":[{"format_id":"249","filesize":935578,"format_string":"audio only (DASH audio)"},{"format_id":"250","filesize":1204968,"format_string":"audio only (DASH audio)"},{"format_id":"171","filesize":2009425,"format_string":"audio only (DASH audio)"},{"format_id":"140","filesize":2479666,"format_string":"audio only (DASH audio)"},{"format_id":"251","filesize":2311147,"format_string":"audio only (DASH audio)"},{"format_id":"278","filesize":1724395,"format_string":"256x144 (144p)"},{"format_id":"160","filesize":1196670,"format_string":"256x144 (144p)"},{"format_id":"242","filesize":2599218,"format_string":"426x240 (240p)"},{"format_id":"133","filesize":2354754,"format_string":"426x240 (240p)"},{"format_id":"243","filesize":4718667,"format_string":"640x360 (360p)"},{"format_id":"134","filesize":4524866,"format_string":"640x360 (360p)"},{"format_id":"244","filesize":7808421,"format_string":"854x480 (480p)"},{"format_id":"135","filesize":7998571,"format_string":"854x480 (480p)"},{"format_id":"247","filesize":13837140,"format_string":"1280x720 (720p)"},{"format_id":"136","filesize":14512847,"format_string":"1280x720 (720p)"},{"format_id":"17","filesize":1569112,"format_string":"176x144 (small)"},{"format_id":"36","filesize":4292558,"format_string":"320x180 (small)"},{"format_id":"18","filesize":10213970,"format_string":"640x360 (medium)"},{"format_id":"43","filesize":14837517,"format_string":"640x360 (medium)"},{"format_id":"22","filesize":-1,"format_string":"1280x720 (hd720)"}]}
// CAT;
// die($cat);


// $_POST['video_url'] = "https://www.youtube.com/watch?v=G60llMJepZI";
if(array_key_exists('video_url', $_POST) === False) {
	$cat = <<<'EOD'
<!DOCTYPE html>
<html>
<body>
<p>https://www.youtube.com/watch?v=BdXtIJNNVZM</p><br>
<form action="query_streams.php" method="post">
	<input style='width:60%' name="video_url"><br>
	<input type="hidden" name="debug" value="1">
	<button type="submit">Submit</button>
</form>
</body>
</html>
EOD;
	die($cat);
}

$target = $_POST['video_url'];

/** Since this script will execute stuff on the command line, let's take every precaution. **/

// $target = "&&\n";
// $target = "https://www.youtube.com/watch?v=G60llMJepZI";

// right now this script is called query_streams.php. they can replace the p with %50 which would resolve to the same thing
$cleaner_target = urldecode($target);
if (stripos($cleaner_target, basename(__FILE__))) {
	if($target != $cleaner_target) {
		die("You're an especially bad person...");
	}
	die("You're a bad person.");
}
if(stripos($cleaner_target, "\0")) {
	die("In theory you're not a bad person.");
}

$cleaner_target = escapeshellcmd($cleaner_target);

if(filter_var($cleaner_target, FILTER_VALIDATE_URL) === False) {
	die("Friends off!");
}

# A single % might be valid in a URL, but i'm too lazy to check
if(stripos($cleaner_target, "%")) {
	$json_error_sketchy_url = json_encode(Array("error"=>"There's something potentially sketchy with your URL. RCMP has been alerted."));
	die($json_error_sketchy_url);
}

$final_clean_target = $cleaner_target; // :S

// $stream_info = shell_exec("youtube-dl --dump-json \'" . $final_clean_target . "\'");
$execute_command = "youtube-dl --dump-json " . $final_clean_target;
$raw_return = shell_exec($execute_command);

// If we get NULL back then there was an error
if ($raw_return == "") {
	$json_return = Array();
	$json_return['Error'] = "Youtube-dl didn't return anything";
	die(json_encode($json_return));
}

$stream_info = json_decode($raw_return, True);

function print_array_nice($array) {
	print("<ul>");
	foreach(array_keys($array) as $key){
		print("<li>" . $key);
		print(" - ");
		if(is_array($array[$key])) {
			print_array_nice($array[$key]);
			print("<hr>");
		} else {
			print($array[$key]);
		}
		print('</li>');
	}
	print("</ul>");
}
/** Debug Function **/
// Only show what youtube-dl is giving back
if(array_key_exists('debug', $_POST)) {
	print("<!DOCTYPE html><html><body>");
	print("<h1>Stream Info</h1>");
	print("<p><b>Command: </b>" . $execute_command . "</p>");
	print("<p>Type:" . gettype($stream_info) . "</p>");
	
	$raw_return = str_replace("\n", "<br><h3>LINE FEED</h3></br>", $raw_return);
	$raw_return = str_replace("\r", "<br><h3>CARRIAGE RETURN</h3></br>", $raw_return);
	$raw_return = str_replace("\t", "<h3>TAB</h3>", $raw_return);

	switch(gettype($stream_info)) {
		case "string":
		print("<p><b>Return: </b>" . $raw_return . "</p>");
		die("Empty string");
			break;
		case "NULL":
		print("<p><b>Return: </b>" . $raw_return . "</p>");
		die("Empty null");
			break;
		case "array":
			break;
		default:
		print("<p><b>Return: </b>" . $raw_return . "</p>");
		die("Empty " . gettype($stream_info));
	}

	print_array_nice($stream_info);
}

if(array_key_exists('title', $stream_info))
	$title = $stream_info['title'];
else
	$title = "No Title";

if(array_key_exists('thumbnail', $stream_info))
	$thumbnail = $stream_info['thumbnail'];
else
	$thumbnail = "";

if(array_key_exists('description', $stream_info))
	$description = str_replace("\n", "<br>", $stream_info['description']);
else
	$description = "None";

$json_return = Array();

$json_return['title'] = $title;
$json_return['thumbnail'] = $thumbnail;
$json_return['description'] = $description;

/** Youtube Specific Keys **/
if(array_key_exists('duration', $stream_info)) {
	$duration = $stream_info['duration'];
	$json_return['duration'] = $duration;
} else {
	$json_return['duration'] = -1;
}

if(array_key_exists('is_live', $stream_info)) {
	$is_live = $stream_info['is_live'];
	$json_return['is_live'] = $is_live;
	if($is_live == True || $is_live == 1 || $is_live == "True" || $is_live == "true") { 
		die("Cant do this for a live stream asshole.");
	}
}
$formats = Array();

foreach($stream_info['formats'] as $format){
	$format_id = $format['format_id'];
	$format_string = $format['format'];
	$human_portition_start = stripos($format_string, " - ");
	$format_string = substr($format_string, $human_portition_start + 3);

	$format_string .= "<span style='float:right;'>";
		
	if(array_key_exists('vcodec', $format)) {
		if($format['vcodec'] != "none") {
			$format_string .= "<abbr title='";
			$format_string .= strval($format['vcodec']);
			$format_string .= "'><span class='glyphicon glyphicon-facetime-video'></span></abbr>";
		}
	}
	else if($format['ext'] == "mp4" || $format['ext'] == "3gp")
		$format_string .= "&nbsp;<abbr title='dunno'><span class='glyphicon glyphicon-facetime-video'></span</abbr>";


	if(array_key_exists('acodec', $format)) {
		if($format['acodec'] != "none") {
			$format_string .= "&nbsp;<abbr title='" . strval($format['acodec']) . "'><span class='glyphicon glyphicon-headphones'></span</abbr>";
		}
	}
	else if($format['ext'] == "mp3" || $format['ext'] == "opus")
		$format_string .= "&nbsp;<abbr title='dunno'><span class='glyphicon glyphicon-headphones'></span</abbr>";

		
	

	if(array_key_exists('filesize', $format)) {
		$filesize = $format['filesize'];
	} else {
		$filesize = -1;
	}
	$cleaned_format = Array();
	$cleaned_format['format_id'] = $format_id;
	$cleaned_format['filesize'] = $filesize;
	$cleaned_format['format_string'] = $format_string;
	$cleaned_format['format_ext'] = $format['ext'];
	$cleaned_format['url'] = $format['url'];

	array_push($formats, $cleaned_format);

	$format_string = $format['format'];
}
$json_return['formats'] = $formats;

if(array_key_exists('debug', $_POST)) {
	print("<code>");
	print(json_encode($json_return));
	print("</code>");
}
else 
	print(json_encode($json_return));
?>
