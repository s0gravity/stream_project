<?
// --- GOLD MEDIA --- //
error_reporting(0);
session_start();

define('GOLD_BASE', dirname(empty($_SERVER['SCRIPT_FILENAME']) ? __FILE__ : $_SERVER['SCRIPT_FILENAME']).'/');
$document = '' . $_SERVER['DOCUMENT_ROOT'] . dirname('../../', $_SERVER['SCRIPT_NAME']);

require_once('../../gold-config.php');
require_once('TMDb.php');

if(GOLD_SUB_FOLDER != '') { $sub_folder = '/'.GOLD_SUB_FOLDER.''; }
$root .= 'http://' . $_SERVER['SERVER_NAME'] . $sub_folder;
$document .= $sub_folder;

		
	// GOLD CONNECT
	mysql_connect(GOLD_HOSTNAME,GOLD_USERNAME,GOLD_PASSWORD) or die("Gold MEDIA could not connect to MYSQL database " . mysql_error());
    mysql_select_db(GOLD_DATABASE) or die("Gold MEDIA could not select database " . mysql_error());
	mysql_query("SET CHARACTER SET utf8"); 
	mysql_query("SET NAMES 'utf8'"); 
	
	// GOLD FUNCTIONS
	function set($set_name)
    {
		$q = mysql_query("SELECT * FROM gold_settings WHERE set_name='".$set_name."' LIMIT 1");
		while($row = mysql_fetch_assoc($q)){
				$GOLD_echo = $row['set_content']; // Inside while loop
		}
		return $GOLD_echo;
    }
    function widget_echo($set_name)
    {
		$q = mysql_query("SELECT * FROM gold_widgets WHERE widget_title='".$set_name."' LIMIT 1");
		while($row = mysql_fetch_assoc($q)){
				$GOLD_echo = $row['widget_code']; // Inside while loop
		}
		return $GOLD_echo;
    }
	function LANG($title) {
		include('../../gold-app/gold-lang/'.set('gold_lang').'.php');
		return $LANG[$title];
  	}
	function filter($string) {
     $search = array ("'<script[?>]*?>.*?</script>'si",  // Remove javascript.
                  "'<[\/\!]*?[^<?>]*?>'si",  // Remove HTML tags.
				  "'<>'si",  // Remove HTML tags.
                  "'([\r\n])[\s]+'",  // Remove spaces.
                  "'&(quot|#34);'i",  // Remove HTML entites.
                  "'&(amp|#38);'i",
                  "'&(lt|#60);'i",
                  "'&(gt|#62);'i",
                  "'&(nbsp|#160);'i",
                  "'&(iexcl|#161);'i",
                  "'&(cent|#162);'i",
                  "'&(pound|#163);'i",
                  "'&(copy|#169);'i",
                  "'&#(\d+);'e");  // Evaluate like PHP.
     $replace = array ("",
                   "",
                   "\\1",
                   "\"",
                   "&",
                   "<",
                   "?>",
                   " ",
                   chr(161),
                   chr(162),
                   chr(163),
                   chr(169),
                   "chr(\\1)");
     return mysql_real_escape_string(preg_replace ($search, $replace, $string));
	}

	function ru2lat($str){
		$tr = array(
		    "ა"=>"a", "ბ"=>"b", "გ"=>"g", "დ"=>"d", "ე"=>"e",
		    "ვ"=>"v", "ზ"=>"z", "თ"=>"t", "ი"=>"i", "კ"=>"k", 
		    "ლ"=>"l", "მ"=>"m", "ნ"=>"n", "ო"=>"o", "პ"=>"p", 
		    "ჟ"=>"j", "რ"=>"r", "ს"=>"s", "ტ"=>"t", "უ"=>"u", 
		    "ფ"=>"f", "ქ"=>"q", "ღ"=>"gh", "ყ"=>"y", "შ"=>"sh", 
		    "ჩ"=>"ch", "ც"=>"c", "ძ"=>"dz", "წ"=>"w", "ჭ"=>"w", 
		    "ხ"=>"x", "ჯ"=>"j", "ჰ"=>"h", " "=>"-", "."=>"",
			","=>"", "/"=>"-", ":"=>"", ";"=>"","—"=>"", "–"=>"-"
		    );
		return strtr($str,$tr);
	}

	function slug($str) {
        $str = preg_replace("/(å|ä|à|á|ạ|ả|ã|â|ầ|ấ|ậ|ẩ|ẫ|ă|ằ|ắ|ặ|ẳ|ẵ|ą)/", 'a', $str);
        $str = preg_replace("/(è|é|ẹ|ẻ|ẽ|ê|ề|ế|ệ|ể|ễ|ę)/", 'e', $str);
        $str = preg_replace("/(ì|í|ị|ỉ|ĩ|ı)/", 'i', $str);
        $str = preg_replace("/(ö|ò|ó|ọ|ỏ|õ|ô|ồ|ố|ộ|ổ|ỗ|ơ|ờ|ớ|ợ|ở|ỡ)/", 'o', $str);
        $str = preg_replace("/(ü|ù|ú|ụ|ủ|ũ|ư|ừ|ứ|ự|ử|ữ)/", 'u', $str);
        $str = preg_replace("/(ỳ|ý|ỵ|ỷ|ỹ)/", 'y', $str);
        $str = preg_replace("/(đ)/", 'd', $str);
        $str = preg_replace("/(č|ç|ć)/", 'c', $str);
        $str = preg_replace("/(š,ş,ś)/", 's', $str);
        $str = preg_replace("/(ğ)/", 'g', $str);
        $str = preg_replace("/(Ğ)/", 'g', $str);
        $str = preg_replace("/(ž|ż|ź)/", 'z', $str);
        $str = preg_replace("/(Ä|Å|À|Á|Ạ|Ả|Ã|Â|Ầ|Ấ|Ậ|Ẩ|Ẫ|Ă|Ằ|Ắ|Ặ|Ẳ|Ẵ|Ą)/", 'A', $str);
        $str = preg_replace("/(È|É|Ẹ|Ẻ|Ẽ|Ê|Ề|Ế|Ệ|Ể|Ễ|Ę)/", 'E', $str);
        $str = preg_replace("/(Ì|Í|Ị|Ỉ|Ĩ|İ)/", 'I', $str);
        $str = preg_replace("/(Ö|Ò|Ó|Ọ|Ỏ|Õ|Ô|Ồ|Ố|Ộ|Ổ|Ỗ|Ơ|Ờ|Ớ|Ợ|Ở|Ỡ)/", 'O', $str);
        $str = preg_replace("/(Ù|Ú|Ụ|Ủ|Ũ|Ư|Ừ|Ứ|Ự|Ử|Ữ)/", 'U', $str);
        $str = preg_replace("/(Ỳ|Ý|Ỵ|Ỷ|Ỹ)/", 'Y', $str);
        $str = preg_replace("/(Č|Ç|Ć)/", 'C', $str);
        $str = preg_replace("/(Đ)/", 'D', $str);
		$str = preg_replace("/(ł)/", 'l', $str);
		$str = preg_replace("/(Ł)/", 'L', $str);
		$str = preg_replace("/(Ń)/", 'n', $str);
		$str = preg_replace("/(ń)/", 'n', $str);
        $str = preg_replace("/(Š|Ś)/", 'S', $str);
		$str = preg_replace("/(Ž|Ż|Ź)/", 'Z', $str);
		$str = str_replace(" ", "-", str_replace("&*#39;","",$str));
		$str = preg_replace('/[^A-Za-z0-9\-._]/', '', $str); // Removes special chars.
		$str = preg_replace('/-+/', '-', $str);
		$str = strtolower($str);
        return $str;
    }
	
	function create_thumb($src, $dest, $desired_width) {
		/* read the source image */
    	$info = pathinfo($src);
    	// continue only if this is a JPEG image
    	if ( strtolower($info['extension']) == 'gif' ) 
    	{
			$source_image = imagecreatefromgif($src);
			$width = imagesx($source_image);
			$height = imagesy($source_image);
			
			/* find the "desired height" of this thumbnail, relative to the desired width  */
			$desired_height = floor($height * ($desired_width / $width));
			
			/* create a new, "virtual" image */
			$virtual_image = imagecreatetruecolor($desired_width, $desired_height);
			
			/* copy source image at a resized size */
			imagecopyresampled($virtual_image, $source_image, 0, 0, 0, 0, $desired_width, $desired_height, $width, $height);
			
			/* create the physical thumbnail image to its destination */
			imagejpeg($virtual_image, $dest);
		}
		elseif ( strtolower($info['extension']) == 'png' ) 
    	{
			$source_image = imagecreatefrompng($src);
			$width = imagesx($source_image);
			$height = imagesy($source_image);
			
			/* find the "desired height" of this thumbnail, relative to the desired width  */
			$desired_height = floor($height * ($desired_width / $width));
			
			/* create a new, "virtual" image */
			$virtual_image = imagecreatetruecolor($desired_width, $desired_height);
			
			/* copy source image at a resized size */
			imagecopyresampled($virtual_image, $source_image, 0, 0, 0, 0, $desired_width, $desired_height, $width, $height);
			
			/* create the physical thumbnail image to its destination */
			imagejpeg($virtual_image, $dest);
		} elseif ( strtolower($info['extension']) == 'jpg' || strtolower($info['extension']) == 'jpeg' )
		{
			$source_image = imagecreatefromjpeg($src);
			$width = imagesx($source_image);
			$height = imagesy($source_image);
			
			/* find the "desired height" of this thumbnail, relative to the desired width  */
			$desired_height = floor($height * ($desired_width / $width));
			
			/* create a new, "virtual" image */
			$virtual_image = imagecreatetruecolor($desired_width, $desired_height);
			
			/* copy source image at a resized size */
			imagecopyresampled($virtual_image, $source_image, 0, 0, 0, 0, $desired_width, $desired_height, $width, $height);
			
			/* create the physical thumbnail image to its destination */
			imagejpeg($virtual_image, $dest);
		
		}
	}
	function image_url_upload($file_url, $filename){
		$file = file_get_contents($file_url);
		$upload_folder = $document.'/gold-app/gold-uploads/media/';
		if (!file_exists($upload_folder)) {
			mkdir($upload_folder, 0777, true);
		}
		if(strpos($file_url, '.gif') > 0){
			$extension = '.gif';
		} elseif(strpos($file_url, '.jpg') > 0){
			$extension = '.jpg';
		} elseif(strpos($file_url, '.jpeg') > 0){
			$extension = '.jpeg';
		} elseif(strpos($file_url, '.png') > 0){
			$extension = '.png';
		}
		$filename = $filename . $extension;
		if (file_exists($upload_folder.$filename)) {
			$filename =  uniqid() . '-' . $filename . $extension;
		}
		if(strpos($file_url, '.gif') > 0){
			$img = imagecreatefromstring(file_get_contents($file_url));
			if ($img !== false)
			imagejpeg($img, $document."/uploads/media_photos/$filename.jpg", 100);
		}
	    file_put_contents($upload_folder.$filename, $file);
		return '/' . $filename;
	}
	function http_decode($link) {
		if (preg_match("#https?://#", $link) === 0)
    	$link = 'http://'.$link;
		return $link;
	}
	function getdomain($url) 
	{
		$parsed = parse_url($url); 
		return str_replace('www.','', strtolower($parsed['host'])); 
	}
	function get_youtube_thumb($url) {
		$queryString = parse_url($url, PHP_URL_QUERY);
		parse_str($queryString, $params);
		if (isset($params['v'])) 
		{
			return "http://i3.ytimg.com/vi/" . trim($params['v']) . "/mqdefault.jpg";
		}
		return true;
	}
	function get_vimeo_thumb($url) {
		preg_match('/(\d+)/', $url, $output);
		$id = trim($output[0]);
		$data = file_get_contents("http://vimeo.com/api/v2/video/$id.json");
		$data = json_decode($data);
		return $data[0]->thumbnail_medium;
	}
	function get_facebook_thumb($url) {
		$queryString = parse_url($url, PHP_URL_QUERY);
		parse_str($queryString, $params);
		return "http://graph.facebook.com/" . trim($params['v']) . "/picture";
	}
	function get_vine_thumb($url) {
		$id = trim(preg_replace('/^.*\//','',$url));
		$vine_url = "http://vine.co/v/{$id}";
		$data = file_get_contents($vine_url);
		preg_match('~<\s*meta\s+property="(og:image)"\s+content="([^"]*)~i', $data, $matches);
		return ($matches[2]) ? $matches[2] : false;
	}
	function get_dailymotion_thumb($url) {
		$output = parse_url($url, PHP_URL_PATH);
		$pieces = explode('/', $output);
		$id = $pieces[2];
		echo $id;
		return "http://www.dailymotion.com/thumbnail/video/{$id}";
	}
	function get_metacafe_thumb($url) {
		$path = parse_url($url, PHP_URL_PATH);
		$pieces = explode('/', $path);
		$id = $pieces[2];
		$title = $pieces[3];
		if($title=="")
		$title = $id;
		if($id && $title)
		return "http://s4.mcstatic.com/thumb/{$id}/0/6/videos/0/6/{$title}.jpg";      
		else
		return "";
	}
	function get_instagram_thumb($url) {
		$path = parse_url($url, PHP_URL_PATH);
		$pieces = explode('/', $path);
		$id = $pieces[2];
		return "http://instagr.am/p/".$id."/media/";
	}
	function time_ago($postedDateTime, $systemDateTime, $typeOfTime) {
		$changePostedTimeDate=strtotime($postedDateTime);
		$changeSystemTimeDate=strtotime($systemDateTime);
		$timeCalc=$changeSystemTimeDate-$changePostedTimeDate;
		if ($typeOfTime == "second") {
			if ($timeCalc > 0) {
				$typeOfTime = "second";
			}
			if ($timeCalc > 60) {
				$typeOfTime = "minute";
			}
			if ($timeCalc > (60*60)) {
				$typeOfTime = "hour";
			}
			if ($timeCalc > (60*60*24)) {
				$typeOfTime = "day";
			}
			if ($timeCalc > (60*60*24*7)) {
				$typeOfTime = "week";
			}
			if ($timeCalc > (60*60*24*30)) {
				$typeOfTime = "month";
			}
			if ($timeCalc > (60*60*24*365)) {
				$typeOfTime = "year";
			}
		}
		if ($typeOfTime == "second") {
			$timeCalc .= " second ago";
		}
		if ($typeOfTime == "minute") {
			$timeCalc = round($timeCalc/60) . " minute ago";
		}
		if ($typeOfTime == "hour") {
			$timeCalc = round($timeCalc/60/60) . " hour ago";
		}
		if ($typeOfTime == "day") {
			$timeCalc = round($timeCalc/60/60/24) . " days ago";
		}
		if ($typeOfTime == "week") {
			$timeCalc = round($timeCalc/60/60/24/7) . " week ago";
		}
		if ($typeOfTime == "month") {
			$timeCalc = round($timeCalc/60/60/24/30) . " month ago";
		}
		if ($typeOfTime == "year") {
			$timeCalc = round($timeCalc/60/60/24/365) . " year ago";
		}
	return $timeCalc;
	}
	function smilies( $text ) {
    		$smilies = array(
     		   ':D' => ' <img src="'.$root.'/gold-skins/default/images/smilies/laugh.png" />',
			   ':)' => ' <img src="'.$root.'/gold-skins/default/images/smilies/happy.png" />',
			   ':(' => ' <img src="'.$root.'/gold-skins/default/images/smilies/bored.png" />',
			   ';)' => ' <img src="'.$root.'/gold-skins/default/images/smilies/wink.png" />',
			   ':P' => ' <img src="'.$root.'/gold-skins/default/images/smilies/tongue.png" />',
			   ':X' => ' <img src="'.$root.'/gold-skins/default/images/smilies/not_even.png" />',
			   ':O' => ' <img src="'.$root.'/gold-skins/default/images/smilies/agape.png" />',
			   ':grin:' => ' <img src="'.$root.'/gold-skins/default/images/smilies/grin.png" />',
			   ':shocked:' => ' <img src="'.$root.'/gold-skins/default/images/smilies/shocked.png" />',
			   ':cry:' => ' <img src="'.$root.'/gold-skins/default/images/smilies/cry.png" />',
			   ':sunglasses:' => ' <img src="'.$root.'/gold-skins/default/images/smilies/sunglasses.png" />',
			   ':wink:' => ' <img src="'.$root.'/gold-skins/default/images/smilies/wink.png" />'
   			);
			return str_replace( array_keys( $smilies ), array_values( $smilies ), $text );
	}
	function GOLD_smilies($data_id) {
		$GOLD_html .= '<a href="javascript:;" data-id="'.$data_id.'" title=":D">="'.$root.'/gold-skins/default/images/smilies/laugh.png" /></a>';
		$GOLD_html .= '<a href="javascript:;" data-id="'.$data_id.'" title=":)"><img src="'.$root.'/gold-skins/default/images/smilies/happy.png" /></a>';
		$GOLD_html .= '<a href="javascript:;" data-id="'.$data_id.'" title=":("><img src="'.$root.'/gold-skins/default/images/smilies/bored.png" /></a>';
		$GOLD_html .= '<a href="javascript:;" data-id="'.$data_id.'" title=";)"><img src="'.$root.'/gold-skins/default/images/smilies/wink.png" /></a>';
		$GOLD_html .= '<a href="javascript:;" data-id="'.$data_id.'" title=":P"><img src="'.$root.'/gold-skins/default/images/smilies/tongue.png" /></a>';
		$GOLD_html .= '<a href="javascript:;" data-id="'.$data_id.'" title=":X"><img src="'.$root.'/gold-skins/default/images/smilies/not_even.png" /></a>';
		$GOLD_html .= '<a href="javascript:;" data-id="'.$data_id.'" title=":O"><img src="'.$root.'/gold-skins/default/images/smilies/agape.png" /></a>';
		$GOLD_html .= '<a href="javascript:;" data-id="'.$data_id.'" title=":grin:"><img src="'.$root.'/gold-skins/default/images/smilies/grin.png" /></a>';
		$GOLD_html .= '<a href="javascript:;" data-id="'.$data_id.'" title=":shocked:"><img src="'.$root.'/gold-skins/default/images/smilies/shocked.png" /></a>';
		$GOLD_html .= '<a href="javascript:;" data-id="'.$data_id.'" title=":cry:"><img src="'.$root.'/gold-skins/default/images/smilies/cry.png" /></a>';
		$GOLD_html .= '<a href="javascript:;" data-id="'.$data_id.'" title=":sunglasses:"><img src="'.$root.'/gold-skins/default/images/smilies/sunglasses.png" /></a>';
		
    return $GOLD_html;
  }
  
  function watermarkImage ($SourceFile, $DestinationFile, $img_type) {
	if($img_type == 'jpg' || $img_type == 'jpeg') {
		$imgpath = $SourceFile;
		$watermarkfile=$DestinationFile;
		$watermark = imagecreatefrompng($watermarkfile);
		list($watermark_width,$watermark_height) = getimagesize($watermarkfile);
		$image = imagecreatefromjpeg($imgpath);
		$size = getimagesize($imgpath);
		$dest_x = $size[0] - $watermark_width - 15;
		$dest_y = $size[1] - $watermark_height - 15;
		imagecopy($image, $watermark, $dest_x, $dest_y, 0, 0, $watermark_width, $watermark_height);
		//Finalize the image:
		imagejpeg($image, $imgpath);
		//Destroy the image and the watermark handles
		imagedestroy($image);
		imagedestroy($watermark);
	}
	elseif($img_type == 'png') {
		$imgpath = $SourceFile;
		$watermarkfile=$DestinationFile;
		$watermark = imagecreatefrompng($watermarkfile);
		list($watermark_width,$watermark_height) = getimagesize($watermarkfile);
		$image = imagecreatefrompng($imgpath);
		$size = getimagesize($imgpath);
		$dest_x = $size[0] - $watermark_width - 15;
		$dest_y = $size[1] - $watermark_height - 15;
		imagecopy($image, $watermark, $dest_x, $dest_y, 0, 0, $watermark_width, $watermark_height);
		//Finalize the image:
		imagepng($image, $imgpath);
		//Destroy the image and the watermark handles
		imagedestroy($image);
		imagedestroy($watermark);
	}
	return true;
  }

  	function cmp_cast($a, $b) {
		  return strcmp($a['order'], $b['order']);
	}


  if($_REQUEST['API'] == '1') {

  	$title = filter(htmlspecialchars_decode($_REQUEST['API_TITLE']));
  	$year = filter(htmlspecialchars_decode($_REQUEST['API_YEAR']));
  	if(widget_echo("TMDB_API_KEY") != '') {
	    $tmdb = new TMDb(widget_echo('TMDB_API_KEY'));
	    $tmdbConfig['config'] = $tmdb->getConfiguration();
	    $row = $tmdb->searchMovie($title, '1', FALSE, $year);
		foreach ($row['results'] as $key => $movie) {
		      if($key >= 1){
		        break; // Get out after 10 movies.
		      }
		      $searchMovieId = $movie['id'];
		      $year = date("Y", strtotime($movie['release_date']));
		      $overview = $movie['overview'];
		      $imdb = $movie['vote_average'];
	  	}


	  	// Get movie cast.
		$movie_cast = $tmdb->getMovieCast($searchMovieId);
		$m_cast = $movie_cast['cast'];
		// Set cast in the correct order.
		usort($m_cast, "cmp_cast");
		if ($m_cast != null) {
		  foreach($m_cast as $member) {
		    if (count($actors) < 5) {
		      $actors[] = $member['name'];
		    }
		  }
		}
		if ($movie_cast['crew'] != null) {
		  foreach($movie_cast['crew'] as $member) {
		    if ($member['job'] == "Director") {
		      $directors[] = $member['name'];
		    }
		  }
		}

		$trailers = $tmdb->getMovieTrailers($searchMovieId);

		if ($trailers["youtube"]){
		  if ($trailers["youtube"][0]) {
		    $t_id = $trailers["youtube"][0]["source"];
		    $yt_link = "//youtube.com/embed/$t_id?VQ=HD720";
		  }
		}

	  	$data = array(
	  				  'year' => $year,
	  				  'description' => htmlspecialchars_decode($overview),
	  				  'imdb' => $imdb,
	  				  'directors' => implode(", ", $directors),
	  				  'casts' => implode(", ", $actors),
	  				  'youtube' => $yt_link
	  				  );
	  	
		echo json_encode($data);
	}
  }
	
	// GOLD if($_POST['gold'] == '{POST VALUE}'))
	if($_POST['submit_image']) {
		$post_title = filter($_POST['title']);
		$year = filter($_POST['year']);
		$imdb = filter($_POST['imdb']);
		$directed_by = filter($_POST['directed_by']);
		$casts = filter($_POST['casts']);
		$post_content = mysql_real_escape_string($_POST['description']);
		$category_id = filter($_POST['genre']);
		$movie_flv = filter($_POST['movie_flv']);
		$movie_iframe = filter($_POST['movie_iframe']);
		$path = "gold-app/gold-uploads/media/";
		$user = mysql_fetch_array(mysql_query("SELECT * FROM gold_users WHERE user_username='".$_SESSION['user_username']."' OR user_email='".$_SESSION['user_email']."'"));
		if($user['user_group'] == '1' || $user['user_group'] == '2') { $post_status = '1'; } else { $post_status = '0'; }
		if($user['user_id'] == '') { $user_id = "1"; } else { $user_id = $user['user_id']; }
		if($_SESSION['user_username'] == '') { $user_id = "1"; }
		$user_ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
		
		$categories = $_POST['genre'];
		foreach ($categories as $v) {
			$genre .= "$v,";
		}
		
	  	if($post_title && $year && $imdb && $directed_by && $casts && $post_content){
			$post_title_for = $post_title;
			$GOLD_POST_RESULT_CHECK = mysql_query("SELECT * FROM gold_posts WHERE post_name='".slug(strip_tags(ru2lat(strip_tags(trim($post_title_for)))))."'");
			if(mysql_num_rows($GOLD_POST_RESULT_CHECK) == '0') {
				$post_name = slug(strip_tags(ru2lat(strip_tags(trim($post_title_for)))));
			} else {
				$title_seed = str_split('0123456789');
				shuffle($title_seed);
				$title_rand = '';
				foreach (array_rand($title_seed, 7) as $k) $title_rand .= $title_seed[$k];
				$post_name = slug(strip_tags(ru2lat(strip_tags(trim($post_title_for))))).'-'.$title_rand;
			}
			
			if(!move_uploaded_file($_FILES['file']['tmp_name'], $document."/gold-app/gold-uploads/media/".$post_name.$rand.".jpg")) {
				echo "sdsd";
				$url_thumb = $post_name.$rand.".jpg";
				$result = mysql_query("INSERT INTO gold_posts (year, imdb, directed_by, casts, category_id, user_id, user_ip, post_created, post_updated, post_title, post_name, post_content, post_thumb, movie_flv, movie_iframe, post_status) 
                       VALUES ('".$year."','".$imdb."','".str_replace(".", "", $directed_by)."','".str_replace(".", "", $casts)."','".$genre."','".$user_id."','".$user_ip."','".date("Y-m-d H:i:s")."','".date("Y-m-d H:i:s")."','".$post_title."','".$post_name."','".$post_content."','".$url_thumb."','".$movie_flv."','".$movie_iframe."','".$post_status."')"); 
				
				$genre_id = explode(",", $genre);
				$genre_for = mysql_fetch_array(mysql_query("SELECT * FROM gold_categories WHERE category_id='".$genre_id[0]."'"));
				
				$season = $_POST['season'];
				foreach ($season as $season_ke=>$season_value ) {
					$season_key = $season_ke+1;

					$episode_name = $_POST['episode_name_'.$season_key];
					$episodes_movie_flv = $_POST['episodes_movie_flv_'.$season_key];
					$episodes_movie_iframe = $_POST['episodes_movie_iframe_'.$season_key];
					foreach ($episode_name as $key=>$value ) {
						$key_id = $key+1;
						$insert = mysql_query("INSERT INTO `gold_episodes` (`movie_id`, `season_id`, `episode_name`, `movie_link`, `movie_iframe`) VALUES ('".$post_name."', '".$season_key."', '".$value."', '".$episodes_movie_flv[$key]."', '".$episodes_movie_iframe[$key]."')");
					}

				}

				//print success message. 
				header('Location: '.$root."/".$genre_for['name']."/".$post_name.'');
			}
			
		} else {
			$post_title = filter($_POST['title']);
			$category = filter($_POST['category']);
			header('Location: '.$root.'/submit/image/?error=1');
		}
	}

	if($_REQUEST['episode_id'] != '') {
		$select_episodes = mysql_query("SELECT * FROM gold_episodes WHERE id='".$_REQUEST['episode_id']."' LIMIT 1");
		while($episode = mysql_fetch_array($select_episodes)) {
	  			if($episode['movie_link'] != '') {
	  				echo '
	  					<div class="Gold_PLAYER" style="max-height: 470px;"></div>
						<script type="text/javascript">
							$(".Gold_PLAYER").goldplayer({
								src: "'.$episode['movie_link'].'"
							});
						</script>
					';
	  			} else {
	  				echo '
						<iframe src="'.$episode['movie_iframe'].'" frameborder="0" class="fixed_player"></iframe>
					';
	  			}
	  	}
	}

	if($_REQUEST['seasons_post_name'] != '') {
		$select_all_episodes = mysql_query("SELECT (@row:=@row+1) AS ROW_ID, e.* FROM gold_episodes e, (SELECT @row := 0) r WHERE movie_id='".$_REQUEST['seasons_post_name']."' GROUP BY season_id ORDER BY season_id ASC");
	  	while($all_episode = mysql_fetch_array($select_all_episodes)) {
			$episodes_list .= '<div class="episode-item clearfix" data-index="0">
							<div class="episode-number" style="text-align: center; width: 100%;">
								<span>'.$all_episode['ROW_ID'].'</span>
							</div>
							<a href="javascript:" onclick="show_season('.$all_episode['id'].');" class="tip" title=""></a>
						</div>';
	  	}
	  	echo $episodes_list;
	}

	if($_REQUEST['season_id'] != '') {
		$select_episodes = mysql_query("SELECT * FROM gold_episodes WHERE id='".$_REQUEST['season_id']."' LIMIT 1");
		while($episode = mysql_fetch_array($select_episodes)) {
	  			$select_all_episodes = mysql_query("SELECT (@row:=@row+1) AS ROW_ID, e.* FROM gold_episodes e, (SELECT @row := 0) r WHERE season_id='".$episode['season_id']."' AND movie_id='".$episode['movie_id']."' ORDER BY id ASC");
	  			while($all_episode = mysql_fetch_array($select_all_episodes)) {
	  				$episodes_list .= '<div class="episode-item clearfix" data-index="0">
							<div class="episode-number">
								<span>'.$all_episode['ROW_ID'].'</span>
							</div>
							<div class="episode-title">
								<span class="episode-name">'.$all_episode['episode_name'].'</span>
							</div>
							<a href="javascript:" onclick="show_episode('.$all_episode['id'].');" class="tip" title=""></a>
						</div>';
	  			}
	  			echo $episodes_list;
	  	}
	}
	
	if($_GET['GOLD'] == 'autocomplete') {
		$q = $_GET['term'];
		$tag_data = mysql_real_escape_string($q);
		$sql = "SELECT tag_name FROM gold_tags WHERE tag_name LIKE '%$tag_data%' ORDER BY tag_name";
		$result = mysql_query($sql) or die(mysql_error());
		$return = array();
		if($result) {
			while($row = mysql_fetch_array($result)) {
				$rows = array_push($return,array('label'=>$row['tag_name'],'value'=>$row['tag_name']));
			}
		}
		echo(json_encode($return));
	}
	
	// GOLD if($_POST['gold'] == '{POST VALUE}'))
	if($_POST['submit_feedback']) {
		$full_name = filter($_POST['full_name']);
		$email = filter($_POST['email']);
		$comments = nl2br($_POST['comments']);
	  	if($full_name && $email && $comments){
			//get todays date
			$todayis = date("l, F j, Y, g:i a") ;
			//set a title for the message
			$subject = "Message from Your Website";
			$body = 'From <b style="color: #EC3A39;">'.$full_name.'</b>, 
						<div style="padding: 40px 0px;">- '.$comments.'</div>
						<div>
							<b>Sender Details:</b><br>
							Full Name: <b><font color="#EC3A39">'.$full_name.'</font></b><br>
							Email: <b><font color="#EC3A39">'.$email.'</font></b><br>
							IP: <b><font color="#EC3A39">'.$_SERVER['HTTP_X_FORWARDED_FOR'].'</font></b>
						</div>';
			$headers = 'From: '.$email.'' . "\r\n" .
			    'Reply-To: '.$email.'' . "\r\n" .
				'Content-type: text/html; charset=utf-8' . "\r\n" .
			    'X-Mailer: PHP/' . phpversion();
			
			//put your email address here
			mail(set('gold_email'), $subject, $body, $headers);
			header('Location: '.$root.'/pages/feedback?success=1');
		} else {
			header('Location: '.$root.'/pages/feedback?error=1&full_name='.$full_name.'&email='.$email.'&comments='.$comments.'');
		}
	}
	
	if($_POST['gold'] == 'login') {
		session_start();
		$username = mysql_real_escape_string($_POST['name']);
		$password = md5(mysql_real_escape_string($_POST['password']));
		$q = mysql_query("SELECT * FROM gold_users WHERE user_username='".$username."' OR user_email='".$username."' AND user_active='1'");
		$num_row = mysql_num_rows($q);
		$row=mysql_fetch_assoc($q);
		if($username != '' && $password != '') {
			if($num_row == 1) {
				if($password == $row['user_password']) {
					echo 'true';
					mysql_query('UPDATE gold_users SET user_points = user_points + '.set('points_add_for_every_login').' WHERE user_id='.$row['user_id'].'');
					$_SESSION['user_username'] = $row['user_username'];
					$_SESSION['user_email'] = $row['user_email'];
					$_SESSION['user_id'] = $row['user_id'];
				} else {
					echo 'Wrong username or password';
				}
			} else {
					echo 'Wrong username or password';
				}
		} else {
			echo 'Wrong username or password';
		}
	}
	
	// GOLD if($_POST['gold'] == '{POST VALUE}'))
	if($_POST['gold'] == 'admin_menu') {
		$action 				= mysql_real_escape_string($_POST['action']);
		$updateRecordsArray 	= $_POST['recordsArray'];
		
		if ($action == "updateRecordsListings"){
			$listingCounter = 1;
			foreach ($updateRecordsArray as $recordIDValue) {
				$query = "UPDATE gold_menu SET menu_id = " . $listingCounter . " WHERE id = " . $recordIDValue;
				mysql_query($query) or die('Error, insert query failed');
				$listingCounter = $listingCounter + 1;
			}
				
			echo '<pre>';
			print_r($updateRecordsArray);
			echo '</pre>';
			echo 'If you refresh the page, you will see that records will stay just as you modified.';
		}
		elseif ($action == "main_sidebar_updateRecordsListings"){
			$listingCounter = 1;
			foreach ($_POST['MainSidebarArray'] as $recordIDValue) {
				$query = "UPDATE gold_blocks SET block_position = " . $listingCounter . " WHERE block_type='main' AND block_id = " . $recordIDValue;
				mysql_query($query) or die('Error, insert query failed');
				$listingCounter = $listingCounter + 1;
			}
				
			echo '<pre>';
			print_r($_POST['MainSidebarArray']);
			echo '</pre>';
			echo 'If you refresh the page, you will see that records will stay just as you modified.';
		}
		elseif ($action == "profile_sidebar_updateRecordsListings"){
			$listingCounter = 1;
			foreach ($_POST['ProfileSidebarArray'] as $recordIDValue) {
				$query = "UPDATE gold_blocks SET block_position = " . $listingCounter . " WHERE block_type='profile' AND block_id = " . $recordIDValue;
				mysql_query($query) or die('Error, insert query failed');
				$listingCounter = $listingCounter + 1;
			}
				
			echo '<pre>';
			print_r($_POST['ProfileSidebarArray']);
			echo '</pre>';
			echo 'If you refresh the page, you will see that records will stay just as you modified.';
		} elseif ($action == "post_sidebar_updateRecordsListings"){
			$listingCounter = 1;
			foreach ($_POST['PostSidebarArray'] as $recordIDValue) {
				$query = "UPDATE gold_blocks SET block_position = " . $listingCounter . " WHERE block_type='post' AND block_id = " . $recordIDValue;
				mysql_query($query) or die('Error, insert query failed');
				$listingCounter = $listingCounter + 1;
			}
				
			echo '<pre>';
			print_r($_POST['PostSidebarArray']);
			echo '</pre>';
			echo 'If you refresh the page, you will see that records will stay just as you modified.';
		}
	}
	
	// GOLD if($_POST['gold'] == '{POST VALUE}'))
	if($_POST['register_button']) {
		$username = filter(mysql_real_escape_string($_POST['signin-username']));
		$email = filter(mysql_real_escape_string($_POST['signin-email']));
		$password_extract = mysql_real_escape_string($_POST['signin-password']);
		$password = mysql_real_escape_string(md5($_POST['signin-password']));
		$confirmation_code = bin2hex(openssl_random_pseudo_bytes(15));
		
			$select_username = mysql_query("SELECT * FROM gold_users WHERE user_username='".$username."' LIMIT 1");
			$select_email = mysql_query("SELECT * FROM gold_users WHERE user_email='".$email."' LIMIT 1");
			if(mysql_num_rows($select_username) != '0') { header('Location: '.$root.'/register?error=1&email='.$email.'&error_username='.$username.'&password='.$password_extract); }
			if(mysql_num_rows($select_email) != '0') { header('Location: '.$root.'/register?error=1&error_email='.$email.'&username='.$username.'&password='.$password_extract); }
			$select = mysql_query("SELECT * FROM gold_users WHERE user_username='".$username."' AND user_email='".$email."' LIMIT 1");
			if(mysql_num_rows($select) != '0') { header('Location: '.$root.'/register?error=1&error_email='.$email.'&error_username='.$username.'&password='.$password_extract); }
	  	if(!$username == '' && !$email == '' && !$password == ''){
			if(mysql_num_rows($select_username) == '0' && mysql_num_rows($select_email) == '0') {
				$result = mysql_query("INSERT INTO gold_users ( user_login_ip, user_confirmation_code, user_active, user_created, user_create_ip, user_username, user_email ) VALUES 
									( '".$_SERVER['HTTP_X_FORWARDED_FOR']."', '".$confirmation_code."', '0', '".date("Y-m-d H:i:s")."', '".$_SERVER['HTTP_X_FORWARDED_FOR']."', '".@$username."', '".$email."' )"); 
				$check_row = mysql_fetch_array(mysql_query("SELECT * FROM gold_users WHERE user_username='".$username."' AND user_email='".$email."' LIMIT 1"));
				mysql_query('UPDATE gold_users SET user_points = user_points + '.set('points_add_for_all_users').' WHERE user_id='.$check_row['user_id'].'');
				$_SESSION['user_username'] = $check_row['user_username'];
				$_SESSION['user_email'] = $check_row['user_email'];
				$_SESSION['user_id'] = $check_row['user_id'];
				
				//get todays date
				$todayis = date("l, F j, Y, g:i a");
				//set a title for the message
				$subject = set('gold_email_template_register_title');
				$root_url = $root;
				$root_theme = $root_url.'/gold-skins/default';
				$template_data = set('gold_email_template_register');
				$old = array('{$root}', '{$skin}', '{$confirmation_code}', '{$username}', '{$email}', '{$password}', '{$fullname}');
				$new = array($root_url, $root_theme, $check_row['user_confirmation_code'], $check_row['user_username'], $check_row['user_email'], $password_extract, $check_row['user_fullname']);
				$body = str_replace($old, $new, $template_data);
				$headers = 'From: '.set('gold_email').'' . "\n" .
			    	'Reply-To: '.set('gold_email').'' . "\n" .
					'Content-type: text/html; charset=utf-8' . "\n" .
			    	'X-Mailer: PHP/' . phpversion();
				//put your email address here
				mail($email, $subject, $body, $headers);
				
				header('Location: '.$root.'/');
			}
		} else {
			$select = mysql_query("SELECT * FROM gold_users WHERE user_username='".$username."' AND user_email='".$email."' LIMIT 1");
			$select_username = mysql_query("SELECT * FROM gold_users WHERE user_username='".$username."' LIMIT 1");
			$select_email = mysql_query("SELECT * FROM gold_users WHERE user_email='".$email."' LIMIT 1");
			if(mysql_num_rows($select_username) == '0') { header('Location: '.$root.'/register?error=1&email='.$email.'&error_username='.$username.'&password='.$password_extract); }
			if(mysql_num_rows($select_email) != '0') { header('Location: '.$root.'/register?error=1&error_email='.$email.'&username='.$username.'&password='.$password_extract); }
			if(mysql_num_rows($select) != '0') {
				header('Location: '.$root.'/register?error=1&error_username='.$username.'&error_email='.$email.'&password='.$password_extract.'');
			} else {
				
			}
		}
	}
	
	function random_password( $length = 8 ) {
   		$chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789!@#$%^&*()_-=+;:,.?";
   		$password = substr( str_shuffle( $chars ), 0, $length );
    	return $password;
	}

	// GOLD if($_POST['gold'] == '{POST VALUE}'))
	if($_POST['forgot_button']) {
		$username = filter(mysql_real_escape_string($_POST['signin-username']));
		$password = random_password(10);
		if(!$username == ''){
			$check_row = mysql_fetch_array(mysql_query("SELECT * FROM gold_users WHERE user_username='".$username."' OR user_email='".$username."' LIMIT 1"));
			$email = $check_row['user_email'];
			if($check_row['user_id'] != '') {
				//get todays date
				$todayis = date("l, F j, Y, g:i a");
				//set a title for the message
				$subject = set('gold_email_template_forgot_title');
				$root_url = $root;
				$root_theme = $root_url.'/gold-skins/default';
				$template_data = set('gold_email_template_forgot');
				$old = array('{$root}', '{$skin}', '{$username}', '{$email}', '{$password}', '{$fullname}');
				$new = array($root_url, $root_theme, $check_row['user_username'], $check_row['user_email'], $password, $check_row['user_fullname']);
				$body = str_replace($old, $new, $template_data);
				$headers = 'From: '.set('gold_email').'' . "\r\n" .
			    	'Reply-To: '.set('gold_email').'' . "\r\n" .
					'Content-type: text/html; charset=utf-8' . "\r\n" .
			    	'X-Mailer: PHP/' . phpversion();
				//put your email address here
				mail($email, $subject, $body, $headers);
				
				$update = mysql_query("UPDATE gold_users SET user_password='".md5($password)."' WHERE user_username='".$username."' OR user_email='".$username."' LIMIT 1");
				
				header('Location: '.$root.'/forgot?action=sent');
			} else {
				header('Location: '.$root.'/forgot?error=1&error_username='.$username.'');
			}
		} else {
			$select = mysql_query("SELECT * FROM gold_users WHERE user_username='".$username."' OR user_email='".$username."' LIMIT 1");
			if(mysql_num_rows($select) != '0') {
				header('Location: '.$root.'/forgot?error=1&username='.$username.'');
			} else {
				header('Location: '.$root.'/forgot?error=1&username='.$username.'');
			}
		}
	}
	
	if($_POST['gold'] == 'vote_up') {
		if($_SESSION['user_id'] != '') {
			$id = $_POST['id'];
			$user_id = $_POST['user_id'];
			function GOLD_VOTES($id) { $gold_votes = array(); $q = "SELECT * FROM gold_votes WHERE vote_type='post' AND post_id = $id"; $r = mysql_query($q); if(mysql_num_rows($r)==1) { $row = mysql_fetch_assoc($r); $gold_votes[0] = $row['gold_votes']; } return $gold_votes; }
			function GET_GOLD_VOTES($id) { $query = "SELECT * FROM gold_votes WHERE vote_type='post' AND post_id = $id"; $result = mysql_query($query); $vote = mysql_num_rows($result); return $vote; }
			$current_votes = GOLD_VOTES($id);
			$votes_up = $current_votes[0]+1;
			$GOLD_QUERY = mysql_query("SELECT * FROM gold_votes WHERE vote_type='post' AND post_id='$id' AND user_id='$user_id'");
			if (mysql_num_rows($GOLD_QUERY)) { } else {
				$GOLD_INSERT_QUERY = mysql_query('INSERT INTO gold_votes (vote_type, post_id, user_id) VALUES ("post", "'.$id.'", "'.$user_id.'")');
			}
				$row = mysql_fetch_array(mysql_query("SELECT * FROM gold_posts WHERE post_id='".$id."' LIMIT 1"));
				mysql_query('UPDATE gold_users SET user_points = user_points + '.set('points_per_up_vote_on_your_media').' WHERE user_id='.$row['user_id'].'');
				mysql_query('UPDATE gold_users SET user_points = user_points + '.set('points_voting_up_a_media').' WHERE user_id='.$user_id.'');
			
			echo GET_GOLD_VOTES($id);
		} else {
			echo "<a href='".$root."/login'>Please Log in</a>";
		}
	}
	
	if($_POST['gold'] == 'vote_down') {
		if($_SESSION['user_id'] != '') {
			$id = $_POST['id'];
			$user_id = $_POST['user_id'];
			function GOLD_VOTES($id) { $gold_votes = array(); $q = "SELECT * FROM gold_votes WHERE vote_type='post' AND post_id = $id"; $r = mysql_query($q); if(mysql_num_rows($r)==1) { $row = mysql_fetch_assoc($r); $gold_votes[0] = $row['gold_votes']; } return $gold_votes; }
			function GET_GOLD_VOTES($id) { $query = "SELECT * FROM gold_votes WHERE vote_type='post' AND post_id = $id"; $result = mysql_query($query); $vote = mysql_num_rows($result); return $vote; }
			$current_votes = GOLD_VOTES($id);
			$votes_up = $current_votes[1]+1;
			$GOLD_QUERY = mysql_query("SELECT * FROM gold_votes WHERE vote_type='post' AND post_id='$id' AND user_id='$user_id'");
			if(mysql_num_rows($GOLD_QUERY)) {
				$GOLD_DELETE_QUERY = mysql_query('DELETE FROM gold_votes WHERE vote_type="post" AND post_id="'.$id.'" AND user_id="'.$user_id.'"');
			}
				$row = mysql_fetch_array(mysql_query("SELECT * FROM gold_posts WHERE post_id='".$id."' LIMIT 1"));
				mysql_query('UPDATE gold_users SET user_points = user_points + '.set('points_per_up_vote_on_your_media').' WHERE user_id='.$row['user_id'].'');
				mysql_query('UPDATE gold_users SET user_points = user_points + '.set('points_voting_up_a_media').' WHERE user_id='.$user_id.'');
			
			echo GET_GOLD_VOTES($id);
		} else {
			echo "<a href='".$root."/login'>Please Log in</a>";
		}
	}
	
	if($_POST['gold'] == 'flag') {
		$id = $_POST['id'];
		$user_id = $_POST['user_id'];
		$type = $_POST['type'];
		$user_ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
		if($type == 'post') {
			$GOLD_QUERY = mysql_query("SELECT * FROM gold_flags WHERE flag_type='post' AND post_id='$id' AND user_ip='$user_ip' AND user_id='$user_id'");
			if (mysql_num_rows($GOLD_QUERY)) { } else {
				$GOLD_INSERT_QUERY = mysql_query('INSERT INTO gold_flags (flag_type, post_id, user_ip, user_id) VALUES ("post", "'.$id.'", "'.$user_ip.'", "'.$user_id.'")');
			}
		} elseif($type == '') {
			$GOLD_QUERY = mysql_query("SELECT * FROM gold_flags WHERE flag_type='comment' AND post_id='$id' AND user_ip='$user_ip' AND user_id='$user_id'");
			if (mysql_num_rows($GOLD_QUERY)) { } else {
				$GOLD_INSERT_QUERY = mysql_query('INSERT INTO gold_flags (flag_type, post_id, user_ip, user_id) VALUES ("comment", "'.$id.'", "'.$user_ip.'", "'.$user_id.'")');
			}
		}
	}
	
	