<?php
// --- GOLD MOVIES --- //

	define('GOLD_BASE', dirname(empty($_SERVER['SCRIPT_FILENAME']) ? __FILE__ : $_SERVER['SCRIPT_FILENAME']).'/');
	
class SkinFunctions {
	//All CMS template management related functions will be here.
    var $templateName='default';
    var $widgetPositions=array(); //array which holds widget positions and widget names
	var $pluginPositions=array(); //array which holds plugin positions and plugin names
	public function getCurrentTemplatePath()
	{
		return 'gold-skins/'.$this->templateName.'/';
	}
	public function LANG($title) {
		include(GOLD_BASE.'gold-app/gold-lang/'.$this->set('gold_lang').'.php');
		return $LANG[$title];
  	}
	public function skin()
	{
		$query = mysql_query("SELECT * FROM gold_settings WHERE set_name='gold_skin'");
		$row = mysql_fetch_array($query);
		return $row['set_content'];
	}
	//this will set template which we want to use	
	public function setTemplate($templateName)
	{
		$this->templateName=$templateName;
	}
	public function GOLD_ROOT() {
		if(GOLD_SUB_FOLDER != '') { $sub_folder = '/'.GOLD_SUB_FOLDER.'/';
			$root .= 'http://' . $_SERVER['SERVER_NAME'] . $sub_folder;
			return $root;
		} else {
			return 'http://' . $_SERVER['SERVER_NAME'] .'/';
		}
	}
	public function GOLD_REQUEST($request) {
		return $_REQUEST[''.$request.''];
	}
	public function GOLD_appOutput()
	{
		require_once(GOLD_BASE.'gold-app/gold-includes/GOLD-CLASS.php');
		$app = new GOLD_CONNECT();
		$this->GOLD_run();
	}
	public function query($sql){
        if($this->query = mysql_query($sql)){
            return $this->query;
        }else{
            $this->exception("Could not query the database!");
            return false;       
        }
    }
	public function GOLD_DB_INSERT($table_name, $form_data) {
        // retrieve the keys of the array (column titles)
        $fields = array_keys($form_data);
        // build the query
        $sql = "INSERT INTO ".$table_name."
        (`".implode('`,`', $fields)."`)
        VALUES('".implode("','", $form_data)."')";
        // run and return the query result resource
        return mysql_query($sql);
    }
    public function GOLD_DB_DELETE($table_name, $where_clause='') {
        // check for optional where clause
        $whereSQL = '';
        if(!empty($where_clause))
        {
            // check to see if the 'where' keyword exists
            if(substr(strtoupper(trim($where_clause)), 0, 5) != 'WHERE')
            {
                // not found, add keyword
                $whereSQL = " WHERE ".$where_clause;
            } else {
                $whereSQL = " ".trim($where_clause);
            }
        }
        // build the query
        $sql = "DELETE FROM ".$table_name.$whereSQL;
        
        // run and return the query result resource
        return mysql_query($sql);
    }
	public function GOLD_DB_UPDATE($table_name, $form_data, $where_clause='') {
    	// check for optional where clause
    	$whereSQL = '';
    	if(!empty($where_clause))
    	{
      		// check to see if the 'where' keyword exists
        	if(substr(strtoupper(trim($where_clause)), 0, 5) != 'WHERE')
        	{
        	    // not found, add key word
        	    $whereSQL = " WHERE ".$where_clause;
        	} else {
            	$whereSQL = " ".trim($where_clause);
        	}
    	}
    	// start the actual SQL statement
    	$sql = "UPDATE ".$table_name." SET ";
		
    	// loop and build the column /
    	$sets = array();
    	foreach($form_data as $column => $value)
    	{
     	    $sets[] = "`".$column."` = '".$value."'";
    	}
    	$sql .= implode(', ', $sets);
		
   		// append the where statement
    	$sql .= $whereSQL;
		
    	// run and return the query result
    	return mysql_query($sql);
	}
	public function widgetOutput($position='default')
    {
        if(empty($this->widgetPositions[$position]))
        {
			$q = mysql_query("SELECT * FROM gold_widgets WHERE widget_position='".$position."' AND widget_status = '1' ORDER BY widget_id DESC");
			while($row = mysql_fetch_assoc($q)){
				$widgets[] = $row['widget_title']; // Inside while loop
			}
            foreach($widgets as $widgetName) //display each widget
            {
                require_once(GOLD_BASE.'gold-app/gold-widgets/'.$widgetName.'/'.$widgetName.'.php');
                $widgetclass=ucfirst($widgetName).'Widget';
                $widget=new $widgetclass();
                $widget->run($widgetName);
            }
        }
    }
	public function pluginOutput($position='plugin')
    {
        if(empty($this->pluginPositions[$position]))
        {
			$q = mysql_query("SELECT * FROM gold_plugins WHERE plugin_position='".$position."' AND plugin_status = '1' ORDER BY plugin_id DESC");
			while($row = mysql_fetch_assoc($q)){
				$plugins[] = $row['plugin_title']; // Inside while loop
			}
            foreach($plugins as $pluginName) //display each widget
            {
				require_once(GOLD_BASE.'gold-app/gold-plugins/'.$pluginName.'/'.$pluginName.'.php');
				$pluginclass = $pluginName;
                $plugin=new $pluginclass();
                $plugin->run($pluginName);
            }
        }
    }
	public function poweredby()
	{
		return "<!-- Powered by Gold MOVIES - http://codecanyon.net/item/gold-movies/11371340?ref=ThemesGold -->
		";
	}
	public function widget($widget_title)
    {
		$q = mysql_query("SELECT * FROM gold_widgets WHERE widget_title='".$widget_title."' ORDER BY widget_id ASC LIMIT 1");
		while($row = mysql_fetch_assoc($q)){
				echo $row['widget_code']; // Inside while loop
		}
    }
	public function widget_echo($widget_title)
    {
		$q = mysql_query("SELECT * FROM gold_widgets WHERE widget_title='".$widget_title."' ORDER BY widget_id ASC LIMIT 1");
		while($row = mysql_fetch_assoc($q)){
				$GOLD_echo = $row['widget_code']; // Inside while loop
		}
		return $GOLD_echo;
    }
	public function menu($menu_name)
    {
		$q = mysql_query("SELECT * FROM gold_menu WHERE menu_name='".$menu_name."' ORDER BY id ASC LIMIT 1");
		while($row = mysql_fetch_assoc($q)){
				$GOLD_echo = $row['id']; // Inside while loop
		}
		return $GOLD_echo;
    }
	public function menu_status($menu_name)
    {
		$q = mysql_query("SELECT * FROM gold_menu WHERE menu_name='".$menu_name."' ORDER BY id ASC LIMIT 1");
		while($row = mysql_fetch_assoc($q)){
				if($row['menu_status'] == '0') { $GOLD_echo = "<a href='".$this->GOLD_ROOT().$_REQUEST['gold'].'/'.$_REQUEST['sub_gold'].'/active/menu/'.$row['menu_name']."' class='sort-right' style='color: #35BD00;'>Activate</a>"; }
				elseif($row['menu_status'] == '1') { $GOLD_echo = "<a href='".$this->GOLD_ROOT().$_REQUEST['gold'].'/'.$_REQUEST['sub_gold'].'/inactive/menu/'.$row['menu_name']."' class='sort-right'>Inactivate</a>"; }
		}
		return $GOLD_echo;
    }
	public function block($menu_name)
    {
		$q = mysql_query("SELECT * FROM gold_blocks WHERE block_type='main' AND block_name='".$menu_name."' ORDER BY block_id ASC LIMIT 1");
		while($row = mysql_fetch_assoc($q)){
				$GOLD_echo = $row['block_id']; // Inside while loop
		}
		return $GOLD_echo;
    }
	public function block_post($menu_name)
    {
		$q = mysql_query("SELECT * FROM gold_blocks WHERE block_type='post' AND block_name='".$menu_name."' ORDER BY block_id ASC LIMIT 1");
		while($row = mysql_fetch_assoc($q)){
				$GOLD_echo = $row['block_id']; // Inside while loop
		}
		return $GOLD_echo;
    }
	public function block_profile($menu_name)
    {
		$q = mysql_query("SELECT * FROM gold_blocks WHERE block_type='profile' AND block_name='".$menu_name."' ORDER BY block_id ASC LIMIT 1");
		while($row = mysql_fetch_assoc($q)){
				$GOLD_echo = $row['block_id']; // Inside while loop
		}
		return $GOLD_echo;
    }
	public function block_status($menu_name)
    {
		$q = mysql_query("SELECT * FROM gold_blocks WHERE block_name='".$menu_name."' ORDER BY block_id ASC LIMIT 1");
		while($row = mysql_fetch_assoc($q)){
				if($row['block_status'] == '0') { $GOLD_echo = "<a href='".$this->GOLD_ROOT().$_REQUEST['gold'].'/'.$_REQUEST['sub_gold'].'/active/main_sidebar/'.$row['block_name']."' class='sort-right' style='color: #35BD00;'>Activate</a>"; }
				elseif($row['block_status'] == '1') { $GOLD_echo = "<a href='".$this->GOLD_ROOT().$_REQUEST['gold'].'/'.$_REQUEST['sub_gold'].'/inactive/main_sidebar/'.$row['block_name']."' class='sort-right'>Inactivate</a>"; }
		}
		return $GOLD_echo;
    }
	public function block_post_status($menu_name)
    {
		$q = mysql_query("SELECT * FROM gold_blocks WHERE block_name='".$menu_name."' ORDER BY block_id ASC LIMIT 1");
		while($row = mysql_fetch_assoc($q)){
				if($row['block_status'] == '0') { $GOLD_echo = "<a href='".$this->GOLD_ROOT().$_REQUEST['gold'].'/'.$_REQUEST['sub_gold'].'/active/post_sidebar/'.$row['block_name']."' class='sort-right' style='color: #35BD00;'>Activate</a>"; }
				elseif($row['block_status'] == '1') { $GOLD_echo = "<a href='".$this->GOLD_ROOT().$_REQUEST['gold'].'/'.$_REQUEST['sub_gold'].'/inactive/post_sidebar/'.$row['block_name']."' class='sort-right'>Inactivate</a>"; }
		}
		return $GOLD_echo;
    }
	public function block_profile_status($menu_name)
    {
		$q = mysql_query("SELECT * FROM gold_blocks WHERE block_type='profile' AND block_name='".$menu_name."' ORDER BY block_id ASC LIMIT 1");
		while($row = mysql_fetch_assoc($q)){
				if($row['block_status'] == '0') { $GOLD_echo = "<a href='".$this->GOLD_ROOT().$_REQUEST['gold'].'/'.$_REQUEST['sub_gold'].'/active/profile_sidebar/'.$row['block_name']."' class='sort-right' style='color: #35BD00;'>Activate</a>"; }
				elseif($row['block_status'] == '1') { $GOLD_echo = "<a href='".$this->GOLD_ROOT().$_REQUEST['gold'].'/'.$_REQUEST['sub_gold'].'/inactive/profile_sidebar/'.$row['block_name']."' class='sort-right'>Inactivate</a>"; }
		}
		return $GOLD_echo;
    }
	public function set($set_name)
    {
		$q = mysql_query("SELECT * FROM gold_settings WHERE set_name='".$set_name."' LIMIT 1");
		while($row = mysql_fetch_assoc($q)){
				$GOLD_echo = $row['set_content']; // Inside while loop
		}
		return $GOLD_echo;
    }
	public function set_plugin($set_name)
    {
		$q = mysql_query("SELECT * FROM gold_plugins_settings WHERE set_name='".$set_name."' LIMIT 1");
		while($row = mysql_fetch_assoc($q)){
				$GOLD_echo = $row['set_content']; // Inside while loop
		}
		return $GOLD_echo;
    }
	public function prev_media($post_id)
    {
		$q = mysql_query("SELECT * FROM gold_posts WHERE post_id<'".$post_id."' and post_status='1' LIMIT 1");
		while($row = mysql_fetch_assoc($q)){
			$cat_sql = mysql_query("SELECT * FROM gold_categories WHERE category_id='".$row['category_id']."'");
			$cat = mysql_fetch_array($cat_sql);
			$GOLD_echo = $root.'/'.$cat['name'].'/'.$row['post_name']; // Inside while loop
		}
		return $GOLD_echo;
    }
	public function next_media($post_id)
    {
		$q = mysql_query("SELECT * FROM gold_posts WHERE post_id>'".$post_id."' and post_status='1' LIMIT 1");
		while($row = mysql_fetch_assoc($q)){
			$cat_sql = mysql_query("SELECT * FROM gold_categories WHERE category_id='".$row['category_id']."'");
			$cat = mysql_fetch_array($cat_sql);
			$GOLD_echo = $root.'/'.$cat['name'].'/'.$row['post_name']; // Inside while loop
		}
		return $GOLD_echo;
    }
	public function GOLD_show()
	{
   		$Gold_Query_Results = mysql_query("SELECT * FROM gold_settings WHERE set_name='gold_skin' AND set_content!='' LIMIT 1");
   		$Gold_Query_Show = mysql_num_rows($Gold_Query_Results);
		if(GOLD_HOSTNAME != '' && GOLD_USERNAME != '' && GOLD_DATABASE != '' && $Gold_Query_Show != '') {
			require ''.$this->getCurrentTemplatePath().'/gold-skin.php';
			$app = new GOLD_MEDIA();
			return $app->GOLD_html();
		} else {
			require GOLD_BASE.'gold-skins/default/gold-installer.php';
			require 'ControllerInstaller.php';
			$app = new GOLD_MEDIA_Installer();
			return $app->installer();
		}
	}
	public function GOLD_logged_in() {
	    if($_SESSION['user_id'] != '') {
			$GOLD_echo = $_SESSION['user_id'];
		}
		return $GOLD_echo;
	}
	public function GOLD_USER($user_id) {
	    $q = mysql_query("SELECT * FROM gold_users WHERE user_id='".$user_id."' LIMIT 1");
		$row = mysql_fetch_object($q);
		
		return $row;
	}
	public function time_ago($postedDateTime, $systemDateTime, $typeOfTime) {
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
			$timeCalc .= " seconds ago";
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
  // Levels and Points
  public function Level($points) {
        // Levels and Points
		$level_sql = mysql_query("SELECT * FROM gold_levels");
		$count = $points;
		$level_id = 1;
		while($level = mysql_fetch_array($level_sql)) {
			if ($count >= $level['level_points']) { $level_id = $level['level_id']; }
		}
    return $level_id;
  }
  // Levels and Points
  public function Group($user_group_id) {
        // Levels and Points
		$group_sql = mysql_query("SELECT * FROM gold_groups");
		$count = $user_group_id;
		while($group = mysql_fetch_array($group_sql)) {
			if ($count >= $group['group_id']) { $group_id = $group['group_name']; }
		}
    return $group_id;
  }
  // Uploaded Media
  public function Uploaded_Media($user_id) {
		$media_sql = mysql_query("SELECT * FROM gold_posts WHERE user_id='".$user_id."' AND post_status='1'");
    return mysql_num_rows($media_sql);
  }
  // Admin Uploaded Media
  public function Admin_Uploaded_Media() {
		$media_sql = mysql_query("SELECT * FROM gold_posts");
    return mysql_num_rows($media_sql);
  }
  // Admin Today Uploaded Media
  public function Admin_Today_Uploaded_Media() {
		$media_sql = mysql_query("SELECT * FROM gold_posts WHERE post_created='".date("Y-m-d h:i:s")."'");
    return mysql_num_rows($media_sql);
  }
  // Admin Registered Members
  public function Admin_Registered_Members() {
		$media_sql = mysql_query("SELECT * FROM gold_users");
    return mysql_num_rows($media_sql);
  }
  // Admin Today Registered Members
  public function Admin_Today_Registered_Members() {
		$media_sql = mysql_query("SELECT * FROM gold_users WHERE user_created='".date("Y-m-d h:i:s")."'");
    return mysql_num_rows($media_sql);
  }
  // Comments Number
  public function Comments_Num($user_id) {
		$media_sql = mysql_query("SELECT * FROM gold_comments WHERE comment_author='".$user_id."' AND comment_reply='0' AND comment_status='1'");
    return mysql_num_rows($media_sql);
  }
  // Comments Replies Number
  public function Comment_Replies_Num($user_id) {
		$media_sql = mysql_query("SELECT * FROM gold_comments WHERE comment_author='".$user_id."' AND comment_reply!='0' AND comment_status='1'");
    return mysql_num_rows($media_sql);
  }
  // Voted on Media
  public function Voted_on_Media($user_id) {
		$media_sql = mysql_query("SELECT * FROM gold_votes WHERE vote_type='post' AND user_id='".$user_id."'");
    return mysql_num_rows($media_sql);
  }
  // Voted on Comments
  public function Voted_on_Comments($user_id) {
		$media_sql = mysql_query("SELECT * FROM gold_votes WHERE vote_type='comment' AND user_id='".$user_id."'");
    return mysql_num_rows($media_sql);
  }
  // Received Votes
  public function Received_Votes($user_id) {
		$media_sql = mysql_query("SELECT posts.user_id, posts.post_status, posts.post_id, votes.vote_type, votes.user_id, votes.post_id FROM gold_posts posts, gold_votes votes WHERE posts.post_id=votes.post_id AND posts.user_id='".$user_id."' AND votes.user_id!='".$user_id."' AND votes.vote_type='post' AND posts.post_status='1'");
    return mysql_num_rows($media_sql);
  }
  // GOLD DB FUNCTIONS
  public function GOLD_index() {
		if (!isset($_REQUEST['content']) or !is_numeric($_REQUEST['content'])) { $content = 0; } else { $content = (int)$_REQUEST['content']; }		
		
		$page = (int) (!isset($_GET["page"]) ? 1 : $_GET["page"]);
		$limit = $this->set('gold_rows');
        $startpoint = ($page * $limit) - $limit;
		
        $statement = "gold_posts WHERE post_status='1'";
		
	    $q = mysql_query("SELECT * FROM $statement ORDER BY post_id DESC LIMIT $startpoint,$limit");
		
		$this->GOLD_box($q, $content, "index", $statement);
  }
  // GOLD DB FUNCTIONS
  public function GOLD_cat($name) {
    if($_REQUEST['sub_gold'] == '') {
		$this->GOLD_categories_page();
	} else {
		$page = (int) (!isset($_GET["page"]) ? 1 : $_GET["page"]);
		$limit = $this->set('gold_rows');
		$startpoint = ($page * $limit) - $limit;
		
		$cat = mysql_fetch_array(mysql_query("SELECT * FROM gold_categories WHERE name='".$name."' LIMIT 1"));
		
		$num = mysql_num_rows(mysql_query("SELECT * FROM gold_categories WHERE parent_id='".$cat['category_id']."'"));
		if($num != '0') {
			$q = mysql_query("SELECT p.*, c1.category_id, c1.name, c2.category_id, c2.name FROM gold_categories c1 LEFT JOIN gold_categories c2 ON c2.parent_id = c1.category_id INNER JOIN gold_posts p ON p.post_status='1' WHERE c1.parent_id = 0 AND p.category_id=c2.category_id OR p.category_id=c2.parent_id AND c2.parent_id LIKE '".$cat['category_id']."' AND p.category_id LIKE '".$cat['category_id']."' ORDER BY p.post_id DESC LIMIT ".$limit."");
		} else {
			$statement = "gold_posts WHERE FIND_IN_SET('".$cat['category_id']."', category_id) > 0 AND post_status='1'";
			$q = mysql_query("SELECT * FROM $statement ORDER BY post_id DESC LIMIT $startpoint, $limit");
		}
		$this->GOLD_box($q, $content, "cat", $statement);
	}
  }
  public function GOLD_producer($name) {
		$page = (int) (!isset($_GET["page"]) ? 1 : $_GET["page"]);
		$limit = $this->set('gold_rows');
		$startpoint = ($page * $limit) - $limit;
		
		$statement = "gold_posts WHERE directed_by LIKE '%".$name."%' AND post_status='1'";
		$q = mysql_query("SELECT * FROM $statement ORDER BY post_id DESC LIMIT $startpoint, $limit");
		
		$this->GOLD_box($q, $content, "producer", $statement);
  }
  public function GOLD_actor($name) {
		$page = (int) (!isset($_GET["page"]) ? 1 : $_GET["page"]);
		$limit = $this->set('gold_rows');
		$startpoint = ($page * $limit) - $limit;
		
		$statement = "gold_posts WHERE casts LIKE '%".$name."%' AND post_status='1'";
		$q = mysql_query("SELECT * FROM $statement ORDER BY post_id DESC LIMIT $startpoint, $limit");
		
		$this->GOLD_box($q, $content, "actor", $statement);
  }
  public function GOLD_year($name) {
		$page = (int) (!isset($_GET["page"]) ? 1 : $_GET["page"]);
		$limit = $this->set('gold_rows');
		$startpoint = ($page * $limit) - $limit;
		
		$statement = "gold_posts WHERE year='".$name."' AND post_status='1'";
		$q = mysql_query("SELECT * FROM $statement ORDER BY post_id DESC LIMIT $startpoint, $limit");
		
		$this->GOLD_box($q, $content, "year", $statement);
  }
  public function GOLD_sort($sort) {
    if($_REQUEST['sub2_gold'] == '') {
		$this->GOLD_sort_page($sort);
	} else {
		$category_sql = mysql_query("SELECT * FROM gold_categories WHERE name='".$_REQUEST['sub2_gold']."' LIMIT 1");
		$category = mysql_fetch_array($category_sql);
		$this->GOLD_sort_page_category($sort, $category['category_id']);
	}
  }
  public function GOLD_post($name) {
		$q = mysql_query("SELECT * FROM gold_posts WHERE post_name='".$name."' ORDER BY post_id LIMIT 1");
		$this->GOLD_full_post($q);
  }
  public function GOLD_movies($link) {
	$value = 'sdds';
	return $value;
  }
  public function GOLD_search_page($sub_gold, $sub2_gold) {
		if (!isset($_REQUEST['content']) or !is_numeric($_REQUEST['content'])) { $content = 0; } else { $content = (int)$_REQUEST['content']; }
		if($this->GOLD_REQUEST('sub_gold') == 'tag') {
				$q = mysql_query("SELECT t.*, COUNT(t.tag_name) AS tags_count FROM gold_tags t GROUP BY t.tag_name ORDER BY max(t.tag_id) DESC LIMIT 1000");
				$this->GOLD_tags_page($q);
		}
		elseif($this->GOLD_REQUEST('q') != '') {
			$page = (int) (!isset($_GET["page"]) ? 1 : $_GET["page"]);
			$limit = $this->set('gold_rows');
			$startpoint = ($page * $limit) - $limit;
			
			$statement = "*, MATCH(post_title, post_content) AGAINST('".$_REQUEST['q']."') AS post_id FROM gold_posts WHERE MATCH(post_title, post_content) AGAINST('".$_REQUEST['q']."')";
			$q = mysql_query("SELECT $statement ORDER BY post_id DESC LIMIT $startpoint, $limit");
			$this->GOLD_box($q, $content, "", $statement);
		}
  }
  public function GOLD_profile_page($username) {
		$q = mysql_query("SELECT * FROM gold_users WHERE user_username='".$username."' LIMIT 1");
		$this->GOLD_profile($q);
  }
  public function GOLD_pages_page($name) {
		$q = mysql_query("SELECT * FROM gold_pages WHERE name='".$name."' LIMIT 1");
		$this->GOLD_pages($q);
  }
  public function GOLD_confirm_page($code) {
		$update = mysql_query("UPDATE gold_users SET user_active='1' WHERE user_confirmation_code='".$code."'");
		header("location: ".$this->GOLD_ROOT()."");
  }
  public function GOLD_logout() {
		session_destroy();
		unset($_SESSION['user_id'], $_SESSION['user_username'], $_SESSION['user_username']);
		header("location: ".$this->GOLD_ROOT()."");
  }
  public function _NEW_USER($provider, $identifier, $display_name, $first_name, $last_name, $email, $profile_url) {
		$this->query("INSERT INTO gold_users ( user_type, user_identifier, user_active, user_created, user_create_ip, user_username, user_fullname, user_email, user_website ) VALUES 
		( '".$provider."', '".$identifier."', '1', '".date("Y-m-d H:i:s")."', '".$_SERVER['HTTP_X_FORWARDED_FOR']."', '".@$display_name."', '".$first_name." ".$last_name."', '".$email."', '".$profile_url."' ) ");
  }
  public function GOLD_run()
  {
	echo $this->pluginOutput('plugin');
    if(isset($_REQUEST['gold']))
    {
		$gold = empty($_REQUEST['gold']) ? 'index' : $_REQUEST['gold'];
		$select_user = mysql_query("SELECT * FROM gold_users WHERE user_id='".$_SESSION['user_id']."'");
		$row_user = mysql_fetch_array($select_user);

        switch($gold)
        {
          case 'index':$this->GOLD_index();break;
		  case 'sort':$this->GOLD_sort($_REQUEST['sub_gold']);break;
		  case 'genre':$this->GOLD_cat($_REQUEST['sub_gold']);break;
		  case 'producer':$this->GOLD_producer($_REQUEST['sub_gold']);break;
		  case 'year':$this->GOLD_year($_REQUEST['sub_gold']);break;
		  case 'movies':$this->GOLD_movies($_REQUEST['sub_gold']);break;
		  case 'actor':$this->GOLD_actor($_REQUEST['sub_gold']);break;
		  case 'pages':$this->GOLD_pages_page($_REQUEST['sub_gold']);break;
		  case 'post':$this->GOLD_post($_REQUEST['sub2_gold']);break;
		  case 'user':$this->GOLD_profile_page($_REQUEST['sub_gold']);break;
		  case 'search':$this->GOLD_search_page($_REQUEST['sub_gold'], $_REQUEST['sub2_gold']);break;
		  case 'top_users':$this->GOLD_top_users_page();break;
		  case 'submit':if($row_user['user_group'] == '1') { $this->GOLD_submit(); } else { $this->GOLD_login(); } break;
		  case 'admin':if($row_user['user_group'] == '1') { $this->GOLD_admin(); }else { $this->GOLD_login(); } break;
		  case 'logout':$this->GOLD_logout();break;
		  default:$this->GOLD_index();break;
        }    
    }
	else
    {
        $this->GOLD_index();
    }
    
  }
}
?>