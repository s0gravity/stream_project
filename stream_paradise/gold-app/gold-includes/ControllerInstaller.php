<?php

class Installer {
	public function installer() {

		if(isset($_POST['submit'])) {
			$required = array('database_host', 'database_name', 'database_user', 'admin_username', 'admin_email', 'admin_password');
			$url = 'http'.(isset($_SERVER['HTTPS'])?'s':'').'://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];

			// Detect if sub-folder
			$details = parse_url($url);
			foreach($details as $key => $value){
				if($key == 'path') $subfolder = str_replace('/', '', $value);
			}
			
			// Loop over field names, make sure each one exists and is not empty
			$error = false;
			foreach($required as $field) {
			  if (empty($_POST[$field])) {
			    $error = true;
			  }
			}

			if ($error) {
				$_POST['error'] = "All fields are required";
			} else {

			  	$import_posts = $_POST['import_posts'];

			  	$connection = mysql_connect($_POST['database_host'], $_POST['database_user'], $_POST['database_password']);                
		        $connect_db = mysql_select_db($_POST['database_name']);

		        if (!$connection) {
		            $error = "Database Connection Failed";
		            $_POST['error'] = $error;
		        } else if (!$connect_db) {
		            $error = "Database Connection Failed";
		            $_POST['error'] = $error;
		        } else {

						$file_db = "././gold-config.php";
			            $content_db = 
	"<?php

		define('GOLD_HOSTNAME', '".$_POST['database_host']."'); // Your Localhost - default: localhost
		define('GOLD_USERNAME', '".$_POST['database_user']."'); // Your DB Username
		define('GOLD_PASSWORD', '".$_POST['database_password']."'); // Your DB Password
		define('GOLD_DATABASE', '".$_POST['database_name']."'); // Your DB Name
		define('GOLD_ADMIN_USERNAME', '".$_POST['admin_username']."'); // Admin Username
		define('GOLD_ADMIN_PASSWORD', '".$_POST['admin_password']."'); // Admin Password
		define('GOLD_ADMIN_EMAIL', '".$_POST['admin_email']."'); // Admin Email
		define('GOLD_SUB_FOLDER', '".$subfolder."'); // If you uploaded files on sub-folder, please type sub-folder name

	?>";
			            file_put_contents($file_db, $content_db);

			            // Import SQL Queries
			            $this->ImportSQL($_POST['database_host'], $_POST['database_user'], $_POST['database_password'], $_POST['database_name'], $_POST['admin_username'], $_POST['admin_password'], $_POST['admin_email'], $_POST['import_posts']);

			            // Go to Admin with Session->Login()
						$_SESSION['user_username'] = $_POST['admin_username'];
						$_SESSION['user_email'] = $_POST['admin_email'];
						$_SESSION['user_id'] = '1';

						if($subfolder != '') { $_POST['error'] = "<a>Installation Successfully Complete!</a>"; } else { $_POST['error'] = "<a>Installation Successfully Complete!</a>"; }

		        }

			}
		}
	}
	public function form_input($return, $title, $type, $name, $value, $class) {
		if($return == 'title') {
			return "<span class='input-prepend'>".$title."</span>";
		}
		elseif($return == 'input') {

			if($_POST['submit'] != '') {
				$value = $_POST[$name];
			}

			return "<input ".$red_error[$name]." type='".$type."' name='".$name."' value='".$value."' class='".$class."'>";
		}
	}
	public function ImportSQL($database_host, $database_username, $database_password, $database_name, $admin_username, $admin_password, $admin_email, $import_posts) {
		// Database Connection
		$connection = mysql_connect($database_host, $database_username, $database_password);                
		$connect_db = mysql_select_db($database_name, $connection);

		// Import SQL Queries

		// *** gold_blocks *** //
		$this->gold_blocks('table');
		$this->gold_blocks('insert');

		// *** gold_categories *** //
		$this->gold_categories('table');
        //$this->gold_categories('insert');
		$this->gold_categories('insert2');

		// *** gold_flags *** //
		$this->gold_flags('table');

		// *** gold_groups *** //
		$this->gold_groups('table');
		$this->gold_groups('insert');

		// *** gold_pages *** //
		$this->gold_pages('table');
		$this->gold_pages('insert');

		// *** gold_plugins *** //
		$this->gold_plugins('table');
		$this->gold_plugins('insert');
		
		// *** gold_plugins_settings *** //
		$this->gold_plugins_settings('table');
		$this->gold_plugins_settings('insert');
		
		// *** gold_tags *** //
		$this->gold_tags('table');
		
		// *** gold_users *** //
		$this->gold_users('table');
		$this->gold_users('insert', $admin_username, $admin_password, $admin_email);
		
		// *** gold_widgets *** //
		$this->gold_widgets('table');
		$this->gold_widgets('insert');

		// *** gold_episodes *** //
		$this->gold_episodes('table');

        // *** gold_links *** //
        $this->gold_links('table');

		// *** gold_settings *** //
		$this->gold_settings('table');
		$this->gold_settings('insert');

		// *** gold_posts *** //
		$this->gold_posts('table');
		if($_POST['import_posts'] == '1') { $this->gold_posts('insert'); }

	}
	public function gold_blocks($type) {
		if($type == 'table') {
			return mysql_query("
				CREATE TABLE IF NOT EXISTS `gold_blocks` (
				  `block_id` int(11) NOT NULL AUTO_INCREMENT,
				  `block_type` varchar(100) NOT NULL,
				  `block_title` varchar(1000) NOT NULL,
				  `block_name` varchar(100) NOT NULL,
				  `block_position` bigint(20) NOT NULL,
				  `block_status` int(1) NOT NULL DEFAULT '1',
				  PRIMARY KEY (`block_id`)
				) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;
			");
		}
		if($type == 'insert') {
			return mysql_query("
				INSERT INTO `gold_blocks` (`block_id`, `block_type`, `block_title`, `block_name`, `block_position`, `block_status`) VALUES
				(1, 'main', 'TOP Media', 'top_media', 2, 1),
				(2, 'main', 'Facebook Box', 'facebook_box', 3, 1),
				(3, 'main', 'Twitter Box', 'twitter_box', 4, 1),
				(4, 'main', 'Sidebar Advert', 'sidebar_advert', 5, 1),
				(5, 'post', 'FB Comments', 'fb_comments', 1, 1),
				(6, 'main', 'Categories', 'categories', 1, 1);
			");
		}
	}
	static function gold_categories($type) {
		if($type == 'table') {
			return mysql_query("
				CREATE TABLE IF NOT EXISTS `gold_categories` (
				  `category_id` int(11) NOT NULL,
				  `parent_id` varchar(100),
				  `title` varchar(100) NOT NULL,
				  `name` varchar(100) NOT NULL,
				  `status` int(1) NOT NULL DEFAULT '1',
				  `type` varchar(10) NOT NULL,
				  PRIMARY KEY (`category_id`)
				) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;
			");
		}
		if($type == 'insert') {
			return mysql_query("
				INSERT INTO `gold_categories` (`category_id`, `parent_id`, `title`, `name`, `status`) VALUES
				(1, '', 'Action & Adventure', 'action-adventure', 1),
				(2, '', 'Cartoons', 'cartoons', 1),
				(3, '', 'Anime', 'anime', 1),
				(4, '', 'Arts & Culture', 'arts-culture', 1),
				(5, '', 'Classics', 'classics', 1),
				(6, '', 'Comedy', 'comedy', 1),
				(7, '', 'Documentaries', 'documentaries', 1),
				(8, '', 'Drama', 'drama', 1),
				(9, '', 'Family', 'family', 1),
				(10, '', 'Food', 'food', 1),
				(11, '', 'Horror & Suspense', 'horror-suspense', 1),
				(12, '', 'International', 'international', 1),
				(13, '', 'Kids', 'kids', 1),
				(14, '', 'Korean Drama', 'korean-drama', 1),
				(15, '', 'Latino', 'latino', 1),
				(16, '', 'Lifestyle', 'lifestyle', 1),
				(17, '', 'Music', 'music', 1),
				(18, '', 'Reality Shows', 'reality-shows', 1),
				(19, '', 'Science Fiction', 'science-fiction', 1),
				(20, '', 'Sports', 'sports', 1),
				(21, '', 'Video Games', 'video-games', 1),
				(22, '', 'New Year''s Eve', 'new-years-eve', 1);
			");
		}
        if($type == 'insert2') {
            return mysql_query("
				INSERT INTO `gold_categories` (`category_id`, `parent_id`, `title`, `name`, `status`) VALUES
				(10001, '', 'Films', 'Films', 1),
				(10002, '', 'Series', 'Series', 1),
				(10003, '', 'Animes', 'Animes', 1),
			");
        }
	}
	static function gold_flags($type) {
		if($type == 'table') {
			return mysql_query("
				CREATE TABLE IF NOT EXISTS `gold_flags` (
				  `flag_id` int(11) NOT NULL AUTO_INCREMENT,
				  `flag_type` varchar(100) NOT NULL,
				  `post_id` int(11) NOT NULL,
				  `user_ip` varchar(100) NOT NULL,
				  `user_id` int(11) NOT NULL,
				  `flag_status` int(1) NOT NULL DEFAULT '1',
				  PRIMARY KEY (`flag_id`)
				) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;
			");
		}
		if($type == 'insert') {
			return mysql_query("
				
			");
		}
	}
	static function gold_groups($type) {
		if($type == 'table') {
			return mysql_query("
				CREATE TABLE IF NOT EXISTS `gold_groups` (
				  `id` int(11) NOT NULL AUTO_INCREMENT,
				  `group_id` bigint(20) NOT NULL,
				  `group_name` varchar(100) NOT NULL,
				  PRIMARY KEY (`id`)
				) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;
			");
		}
		if($type == 'insert') {
			return mysql_query("
				INSERT INTO `gold_groups` (`id`, `group_id`, `group_name`) VALUES
				(1, 1, 'Administrator'),
				(2, 2, 'Moderator'),
				(3, 3, 'Member');
			");
		}
	}
	static function gold_menu($type) {
		if($type == 'table') {
			return mysql_query("
				CREATE TABLE IF NOT EXISTS `gold_menu` (
				  `id` int(11) NOT NULL AUTO_INCREMENT,
				  `menu_id` varchar(100) NOT NULL,
				  `menu_name` text NOT NULL,
				  `menu_status` int(1) NOT NULL DEFAULT '1',
				  PRIMARY KEY (`id`)
				) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;
			");
		}
		if($type == 'insert') {
			return mysql_query("
				INSERT INTO `gold_menu` (`id`, `menu_id`, `menu_name`, `menu_status`) VALUES
				(1, '1', 'Categories', 1),
				(2, '2', 'Hot', 1),
				(3, '4', 'Tags', 1),
				(4, '3', 'Top_users', 1),
				(5, '5', 'Pages', 1),
				(6, '6', 'Feedback', 1);
			");
		}
	}
	static function gold_pages($type) {
		if($type == 'table') {
			return mysql_query("
				CREATE TABLE IF NOT EXISTS `gold_pages` (
				  `page_id` int(11) NOT NULL AUTO_INCREMENT,
				  `name` varchar(500) NOT NULL,
				  `title` varchar(500) NOT NULL,
				  `content` text NOT NULL,
				  `status` int(1) NOT NULL DEFAULT '1',
				  PRIMARY KEY (`page_id`)
				) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;
			");
		}
		if($type == 'insert') {
			return mysql_query("
				INSERT INTO `gold_pages` (`page_id`, `name`, `title`, `content`, `status`) VALUES
				(1, 'about-us', 'About us', 'Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry''s standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book. It has survived not only five centuries, but also the leap into electronic typesetting, remaining essentially unchanged. It was popularised in the 1960s with the release of Letraset sheets containing Lorem Ipsum passages, and more recently with desktop publishing software like Aldus PageMaker including versions of Lorem Ipsum.', 1),
				(2, 'feedback', 'Feedback', '<form method=post id=contact_form action=../gold-app/gold-includes/GOLD.php><b class=success>Your message successfully sent!</b><p><label>Your Name <span class=req>*</span><em id=err_full_name class=error>required</em></label><input type=text id=full_name name=full_name class=text req_class></p><p><label>Email Address <span class=req>*</span><em id=err_email class=error>required</em></label><input type=text id=email name=email class=text req_class></p><p><label>Comments <span class=req>*</span><em id=err_comments class=error>required</em></label><textarea id=comments name=comments rows=5 cols=50 class=req_class style=outline: 0px; line-height: 25px; height: 160px; margin-top: 0px; margin-bottom: 12px;></textarea></p> <p class=button><input type=submit value=Submit name=submit_feedback id=contact_btn class=btn ajax_submit_btn style=padding: 6px 20px; margin: 10px 0px;border: none;></p></form><style>select, textarea, input[type=text], input[type=password], input[type=datetime], input[type=datetime-local], input[type=date], input[type=month], input[type=time], input[type=week], input[type=number], input[type=email], input[type=url], input[type=search], input[type=tel], input[type=color], .field { border: 2px solid rgb(177, 178, 179); } .wrap input:focus { border: 2px solid #EC3A39; } .wrap textarea:focus { border: 2px solid #EC3A39; }</style>', 1)
			");
		}
	}
	static function gold_plugins($type) {
		if($type == 'table') {
			return mysql_query("
				CREATE TABLE IF NOT EXISTS `gold_plugins` (
				  `plugin_id` int(11) NOT NULL AUTO_INCREMENT,
				  `plugin_title` varchar(1000) NOT NULL,
				  `plugin_position` varchar(1000) NOT NULL,
				  `plugin_status` int(1) NOT NULL DEFAULT '1',
				  PRIMARY KEY (`plugin_id`)
				) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;
			");
		}
		if($type == 'insert') {
			return mysql_query("
				INSERT INTO `gold_plugins` (`plugin_id`, `plugin_title`, `plugin_position`, `plugin_status`) VALUES
				(1, 'SocialLogin', 'plugin', 1);
			");
		}
	}
	static function gold_plugins_settings($type) {
		if($type == 'table') {
			return mysql_query("
				CREATE TABLE IF NOT EXISTS `gold_plugins_settings` (
				  `set_id` int(11) NOT NULL AUTO_INCREMENT,
				  `plugin_name` varchar(1000) NOT NULL,
				  `plugin_title` varchar(1000) NOT NULL,
				  `set_name` varchar(1000) NOT NULL,
				  `set_content` text NOT NULL,
				  `set_status` int(1) NOT NULL DEFAULT '1',
				  PRIMARY KEY (`set_id`)
				) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;
			");
		}
		if($type == 'insert') {
			return mysql_query("
				INSERT INTO `gold_plugins_settings` (`set_id`, `plugin_name`, `plugin_title`, `set_name`, `set_content`, `set_status`) VALUES
				(1, 'SocialLogin', 'Facebook ID', 'fb_id', '', 1),
				(2, 'SocialLogin', 'Facebook Secret', 'fb_secret', '', 1),
				(3, 'SocialLogin', 'Twitter Key', 'twitter_key', '', 1),
				(4, 'SocialLogin', 'Twitter Secret', 'twitter_secret', '', 1),
				(5, 'SocialLogin', 'Google ID', 'google_id', '', 1),
				(6, 'SocialLogin', 'Google Secret', 'google_secret', '', 1),
				(7, 'SocialLogin', 'LinkedIn Key', 'linkedin_key', '', 1),
				(8, 'SocialLogin', 'LinkedIn Secret', 'linkedin_secret', '', 1),
				(9, 'SocialLogin', 'Live ID', 'live_id', '', 1),
				(10, 'SocialLogin', 'Live Secret', 'live_secret', '', 1),
				(11, 'SocialLogin', 'Tumblr ID', 'tumblr_id', '', 1),
				(12, 'SocialLogin', 'Tumblr Secret', 'tumblr_secret', '', 1);
			");
		}
	}
	static function gold_tags($type) {
		if($type == 'table') {
			return mysql_query("
				CREATE TABLE IF NOT EXISTS `gold_tags` (
				  `tag_id` int(11) NOT NULL AUTO_INCREMENT,
				  `tag_name` varchar(100) NOT NULL,
				  `post_id` int(11) NOT NULL,
				  PRIMARY KEY (`tag_id`)
				) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;
			");
		}
		if($type == 'insert') {
			return mysql_query("
				
			");
		}
	}
	static function gold_users($type, $admin_username, $admin_password, $admin_email) {
		if($type == 'table') {
			return mysql_query("
				CREATE TABLE IF NOT EXISTS `gold_users` (
				  `user_id` int(11) NOT NULL AUTO_INCREMENT,
				  `user_type` varchar(100) NOT NULL DEFAULT '',
				  `user_identifier` varchar(100) NOT NULL,
				  `user_loggedin` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
				  `user_login_ip` varchar(100) NOT NULL,
				  `user_confirmation_code` varchar(100) CHARACTER SET ascii NOT NULL,
				  `user_active` int(1) NOT NULL DEFAULT '1',
				  `user_created` datetime NOT NULL,
				  `user_create_ip` varchar(100) NOT NULL,
				  `user_username` varchar(500) NOT NULL,
				  `user_fullname` varchar(500) NOT NULL,
				  `user_email` varchar(500) NOT NULL,
				  `user_password` varchar(100) NOT NULL,
				  `user_location` varchar(100) NOT NULL,
				  `user_website` varchar(100) NOT NULL,
				  `user_about` text NOT NULL,
				  `user_avatar` varchar(500) NOT NULL DEFAULT 'avatar.png',
				  `user_cover` varchar(1000) NOT NULL DEFAULT 'default.jpg',
				  `user_points` varchar(5000) NOT NULL DEFAULT '0',
				  `user_group` enum('1','2','3') NOT NULL DEFAULT '3',
				  PRIMARY KEY (`user_id`)
				) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;
			");
		}
		if($type == 'insert') {
			return mysql_query("
				INSERT INTO `gold_users` (`user_id`, `user_type`, `user_identifier`, `user_loggedin`, `user_login_ip`, `user_confirmation_code`, `user_active`, `user_created`, `user_create_ip`, `user_username`, `user_fullname`, `user_email`, `user_password`, `user_location`, `user_website`, `user_about`, `user_avatar`, `user_cover`, `user_points`, `user_group`) VALUES
				(1, '', '', '', '0', '', 1, '', '0', '".$admin_username."', 'Admin Fullname', '".$admin_email."', '".md5($admin_password)."', 'USA', '', 'I am web-developer.', 'avatar.png', 'default.jpg', '0', '1'),
				(2, '', '', '', '0', '', 1, '', '0', 'john', 'John Piterson', 'john@site.com', '".md5($admin_password)."', 'USA', 'http://Piterson.com', 'I am Piterson ! People!', 'avatar.png', 'default.jpg', '179', '3');
			");
		}
	}
	static function gold_widgets($type) {
		if($type == 'table') {
			return mysql_query("
				CREATE TABLE IF NOT EXISTS `gold_widgets` (
				  `widget_id` int(11) NOT NULL AUTO_INCREMENT,
				  `widget_title` varchar(500) NOT NULL,
				  `widget_status` int(1) NOT NULL DEFAULT '1',
				  `widget_code` text NOT NULL,
				  PRIMARY KEY (`widget_id`)
				) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;
			");
		}
		if($type == 'insert') {
			return mysql_query("
				INSERT INTO `gold_widgets` (`widget_id`, `widget_title`, `widget_status`, `widget_code`) VALUES
				(1, 'CenterAdvert', 1, ''),
				(2, 'SidebarAdvert', 1, ''),
				(3, 'PostAdvert', 1, ''),
				(4, 'Analytics', 1, ''),
				(5, 'FacebookBox', 1, 'https://www.facebook.com/envato'),
				(6, 'TwitterBox', 1, 'https://twitter.com/envato'),
				(7, 'TMDB_API_KEY', 1, ''),
				(8, 'disqus', '1', '');
			");
		}
	}
	static function gold_episodes($type) {
		if($type == 'table') {
			return mysql_query("
				CREATE TABLE IF NOT EXISTS `gold_episodes` (
				  `id` int(11) NOT NULL,
				  `movie_id` mediumtext NOT NULL,
				  `post_id` int(10) unsigned NOT NULL,
				  `season_id` int(10) unsigned,
				  `episode_name` varchar(10000) NOT NULL,
				  `movie_link` mediumtext NOT NULL,
				  `movie_iframe` mediumtext NOT NULL,
				  PRIMARY KEY (`id`)
				) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;
			");
		}
		if($type == 'insert') {
			return mysql_query("
				
			");
		}
	}
    static function gold_links($type) {
        if($type == 'table') {
            return mysql_query("
				CREATE TABLE IF NOT EXISTS `gold_links` (
				  `id` int(11) NOT NULL,
				  `post_id` int(10) unsigned NOT NULL,
				  `season_id` int(10) unsigned,
				  `episode_id` int(10) unsigned,
				  `player` varchar(100),
				  `version` varchar(100),
				  `quality` varchar(100),
				  `link` mediumtext NOT NULL,
				  PRIMARY KEY (`id`)
				) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;
			");
        }
        if($type == 'insert') {
            return mysql_query("
				
			");
        }
    }
	static function gold_posts($type) {
		if($type == 'table') {
			return mysql_query("
				CREATE TABLE IF NOT EXISTS `gold_posts` (
				  `post_id` int(10) unsigned NOT NULL,
				  `post_type` int(1) NOT NULL DEFAULT '0',
				  `category_id` varchar(1000) NOT NULL,
				  `language` varchar(1000) NOT NULL DEFAULT 'English',
				  `imdb` varchar(1000) NOT NULL,
				  `year` varchar(1000) NOT NULL,
				  `directed_by` varchar(1000) NOT NULL,
				  `casts` mediumtext NOT NULL,
				  `user_id` int(10) unsigned NOT NULL,
				  `user_ip` varchar(100) NOT NULL,
				  `post_views` int(10) unsigned NOT NULL DEFAULT '0',
				  `post_flags` tinyint(4) NOT NULL DEFAULT '0',
				  `post_created` datetime NOT NULL,
				  `post_updated` datetime NOT NULL,
				  `post_title` varchar(1000) NOT NULL,
				  `post_name` varchar(1000) NOT NULL,
				  `post_content` text NOT NULL,
				  `post_thumb` varchar(1000) NOT NULL,
				  `movie_flv` varchar(1000),
				  `movie_iframe` varchar(1000),
				  `post_tags` varchar(1000) NOT NULL,
				  `post_status` int(10) unsigned NOT NULL DEFAULT '1',
				  `post_msa` varchar(1000) NOT NULL,
				  PRIMARY KEY (`post_id`),
				  FULLTEXT KEY `post_title` (`post_title`,`post_content`)
				) ENGINE=MyISAM  DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC AUTO_INCREMENT=1 ;
			");
		}
		if($type == 'insert') {

			return mysql_query("
				INSERT INTO `gold_posts` (`post_id`, `post_type`, `category_id`, `language`, `imdb`, `year`, `directed_by`, `casts`, `user_id`, `user_ip`, `post_views`, `post_flags`, `post_created`, `post_updated`, `post_title`, `post_name`, `post_content`, `post_thumb`, `movie_flv`, `movie_iframe`, `post_tags`, `post_status`) VALUES
(1, 0, '8,', 'English', '7.0', '2010', 'David Fincher', 'Jesse Eisenberg, Andrew Garfield, Justin Timberlake, Rooney Mara, Bryan Barter, Armie Hammer', 1, '', 17, 0, '2015-05-04 19:37:03', '2015-05-04 19:37:03', 'The Social Network', 'the-social-network', 'On a fall night in 2003, Harvard undergrad and computer programming genius Mark Zuckerberg sits down at his computer and heatedly begins working on a new idea. In a fury of blogging and programming, what begins in his dorm room as a small site among friends soon becomes a global social network and a revolution in communication. A mere six years and 500 million friends later, Mark Zuckerberg is the youngest billionaire in history... but for this entrepreneur, success leads to both personal and legal complications.', 'the-social-network.jpg', '', 'https://www.youtube.com/embed/2RB3edZyeYw', '', 1),
(2, 0, '1,8,', 'English', '7.6', '2001', 'Peter Jackson', 'Elijah Wood, Ian McKellen, Viggo Mortensen, Liv Tyler, Orlando Bloom, Sean Bean', 1, '', 3, 0, '2015-05-04 19:51:05', '2015-05-04 19:51:05', 'The Lord of the Rings: The Fellowship of the Ring', 'the-lord-of-the-rings-the-fellowship-of-the-ring', 'Young hobbit Frodo Baggins, after inheriting a mysterious ring from his uncle Bilbo, must leave his home in order to keep it from falling into the hands of its evil creator. Along the way, a fellowship is formed to protect the ringbearer and make sure that the ring arrives at its final destination: Mt. Doom, the only place where it can be destroyed.', 'the-lord-of-the-rings-the-fellowship-of-the-ring.jpg', '', 'https://www.youtube.com/embed/z_WZxJpHzEE', '', 1),
(3, 0, '1,19,', 'English', '7.7', '2014', 'Bryan Singer', 'Patrick Stewart, James McAvoy, Michael Fassbender', 1, '', 4, 0, '2015-05-04 20:05:03', '2015-05-04 20:05:03', 'X-Men: Days of Future Past', 'x-men-days-of-future-past', 'The ultimate X-Men ensemble fights a war for the survival of the species across two time periods in X-MEN: DAYS OF FUTURE PAST. The beloved characters from the original “X-Men” film trilogy join forces with their younger selves from “X-Men: First Class,” in an epic battle that must change the past – to save our future. ', 'x-men-days-of-future-past.jpg', '', 'https://www.youtube.com/embed/gsjtg7m1MMM', '', 1),
(4, 0, '1,', 'English', '6.6', '2012', 'Sam Mendes', 'Daniel Craig, Judi Dench, Javier Bardem', 1, '', 3, 0, '2015-05-04 20:14:21', '2015-05-04 20:14:21', 'Skyfall', 'skyfall', 'When Bond''s latest assignment goes gravely wrong and agents around the world are exposed, MI6 is attacked forcing M to relocate the agency. These events cause her authority and position to be challenged by Gareth Mallory (Ralph Fiennes), the new Chairman of the Intelligence and Security Committee. With MI6 now compromised from both inside and out, M is left with one ally she can trust: Bond. 007 takes to the shadows - aided only by field agent, Eve (Naomie Harris) - following a trail to the mysterious Silva (Javier Bardem), whose lethal and hidden motives have yet to reveal themselves.', 'skyfall.jpg', '', 'https://www.youtube.com/embed/6kw1UVovByw', '', 1),
(5, 0, '1,19,', 'English', '7.5', '2013', 'JJ Abrams', 'Alex Kurtzman, Roberto Orci, Damon Lindelof', 1, '', 3, 0, '2015-05-04 20:17:17', '2015-05-04 20:17:17', 'Star Trek Into Darkness', 'star-trek-into-darkness', 'When the crew of the Enterprise is called back home, they find an unstoppable force of terror from within their own organization has detonated the fleet and everything it stands for, leaving our world in a state of crisis. With a personal score to settle, Captain Kirk leads a manhunt to a war-zone world to capture a one man weapon of mass destruction. As our heroes are propelled into an epic chess game of life and death, love will be challenged, friendships will be torn apart, and sacrifices must be made for the only family Kirk has left: his crew.', 'star-trek-into-darkness.jpg', '', 'https://www.youtube.com/embed/MPLogYa9Q0o', '', 1),
(6, 0, '1,19,', 'English', '6.1', '2009', 'Michael Bay', 'Kevin Dunn, Shia LaBeouf, Megan Fox', 1, '', 3, 0, '2015-05-04 20:18:33', '2015-05-04 20:18:33', 'Transformers: Revenge of the Fallen', 'transformers-revenge-of-the-fallen', 'Sam Witwicky leaves the Autobots behind for a normal life. But when his mind is filled with cryptic symbols, the Decepticons target him and he is dragged back into the Transformers'' war.', 'transformers-revenge-of-the-fallen.jpg', '', 'https://www.youtube.com/embed/qSQ2xcjalHI', '', 1),
(7, 0, '6,8,', 'English', '7.9', '2013', 'Martin Scorsese', 'Leonardo DiCaprio, Jonah Hill, Margot Robbie', 1, '', 3, 0, '2015-05-04 20:20:15', '2015-05-04 20:20:15', 'The Wolf of Wall Street', 'the-wolf-of-wall-street', 'A New York stockbroker refuses to cooperate in a large securities fraud case involving corruption on Wall Street, corporate banking world and mob infiltration. Based on Jordan Belfort''s autobiography.', 'the-wolf-of-wall-street.jpg', '', 'https://www.youtube.com/embed/iszwuX1AK6A', '', 1),
(8, 0, '3,6,9,', 'English', '7.3', '2010', 'Lee Unkrich', 'Ned Beatty, Tom Hanks, Tim Allen', 1, '', 4, 0, '2015-05-04 20:21:48', '2015-05-04 20:21:48', 'Toy Story 3', 'toy-story-3', 'Woody, Buzz, and the rest of Andy''s toys haven''t been played with in years. With Andy about to go to college, the gang find themselves accidentally left at a nefarious day care center. The toys must band together to escape and return home to Andy.', 'toy-story-3.jpg', '', 'https://www.youtube.com/embed/TNMpa5yBf5o', '', 1),
(9, 0, '1,9,', 'English', '7.4', '2011', 'David Yates', 'John Hurt, Daniel Radcliffe, Rupert Grint', 1, '', 3, 0, '2015-05-04 20:23:07', '2015-05-04 20:23:07', 'Harry Potter and the Deathly Hallows: Part 2 ', 'harry-potter-and-the-deathly-hallows-part-2', 'In the second installment of the two-part conclusion, Harry and his best friends, Ron and Hermione, continue their quest to vanquish the evil Voldemort once and for all. Just as things begin to look hopeless for the young wizards, Harry discovers a trio of magical objects that endow him with powers to rival Voldemort''s formidable skills.', 'harry-potter-and-the-deathly-hallows-part-2.jpg', '', 'https://www.youtube.com/embed/I_kDb-pRCds', '', 1),
(10, 0, '1,', 'English', '6.7', '2014', 'Marc Webb', 'Andrew Garfield, Emma Stone, Jamie Foxx', 1, '', 3, 0, '2015-05-04 20:24:38', '2015-05-04 20:24:38', 'The Amazing Spider-Man 2', 'the-amazing-spider-man-2', 'For Peter Parker, life is busy. Between taking out the bad guys as Spider-Man and spending time with the person he loves, Gwen Stacy, high school graduation cannot come quickly enough. Peter has not forgotten about the promise he made to Gwen’s father to protect her by staying away, but that is a promise he cannot keep. Things will change for Peter when a new villain, Electro, emerges, an old friend, Harry Osborn, returns, and Peter uncovers new clues about his past.', 'the-amazing-spider-man-2.jpg', '', 'https://www.youtube.com/embed/DlM2CWNTQ84', '', 1),
(11, 0, '1,3,6,9,', 'English', '7.7', '2014', 'Phil Lord, Chris Miller', 'Chris Pratt, Elizabeth Banks, Will Ferrell', 1, '', 3, 0, '2015-05-04 20:25:57', '2015-05-04 20:25:57', 'The Lego Movie', 'the-lego-movie', 'An ordinary Lego mini-figure, mistakenly thought to be the extraordinary MasterBuilder, is recruited to join a quest to stop an evil Lego tyrant from gluing the universe together.', 'the-lego-movie.jpg', '', 'https://www.youtube.com/embed/gLvmVnKT4cc', '', 1),
(12, 0, '8,', 'English', '8.2', '1994', 'Frank Darabont', 'Morgan Freeman, Tim Robbins, Bob Gunton', 1, '', 3, 0, '2015-05-04 20:27:29', '2015-05-04 20:27:29', 'The Shawshank Redemption', 'the-shawshank-redemption', 'Framed in the 1940s for the double murder of his wife and her lover, upstanding banker Andy Dufresne begins a new life at the Shawshank prison, where he puts his accounting skills to work for an amoral warden. During his long stretch in prison, Dufresne comes to be admired by the other inmates -- including an older prisoner named Red -- for his integrity and unquenchable sense of hope.', 'the-shawshank-redemption.jpg', '', 'https://www.youtube.com/embed/WawU4ouldxU', '', 1),
(13, 0, '1,8,19,', 'English', '6.6', '2013', 'Neill Blomkamp', 'Matt Damon, Jodie Foster, Sharlto Copley', 1, '', 3, 0, '2015-05-04 20:28:52', '2015-05-04 20:28:52', 'Elysium', 'elysium', 'In the year 2159, two classes of people exist: the very wealthy who live on a pristine man-made space station called Elysium, and the rest, who live on an overpopulated, ruined Earth. Secretary Rhodes (Jodie Foster), a hard line government ofﬁcial, will stop at nothing to enforce anti-immigration laws and preserve the luxurious lifestyle of the citizens of Elysium. That doesn’t stop the people of Earth from trying to get in, by any means they can. When unlucky Max (Matt Damon) is backed into a corner, he agrees to take on a daunting mission that, if successful, will not only save his life, but could bring equality to these polarized worlds.', 'elysium.jpg', '', 'https://www.youtube.com/embed/oIBtePb-dGY', '', 1),
(14, 0, '1,3,6,9,', 'English', '7.3', '2009', 'Pete Docter, Bob Peterson', 'Edward Asner, Christopher Plummer, Jordan Nagai', 1, '', 3, 0, '2015-05-04 20:30:27', '2015-05-04 20:30:27', 'Up', 'up', 'After a lifetime of dreaming of traveling the world, 78-year-old homebody Carl flies away on an unbelievable adventure with Russell, an 8-year-old Wilderness Explorer, unexpectedly in tow. Together, the unlikely pair embarks on a thrilling odyssey full of jungle beasts and rough terrain. ', 'up.jpg', '', 'https://www.youtube.com/embed/YOOIK0baLvM', '', 1),
(15, 0, '1,6,', 'English', '7.1', '2010', 'Edgar Wright', 'Michael Cera, Mary Elizabeth, Winstead Kieran Culkin', 1, '', 4, 0, '2015-05-04 20:32:05', '2015-05-04 20:32:05', 'Scott Pilgrim vs. the World', 'scott-pilgrim-vs-the-world', 'Scott Pilgrim is a film adaptation of the critically acclaimed, award-winning series of graphic novels of the same name by Canadian cartoonist Bryan Lee O’Malley. Scott Pilgrim is a 23 year old Canadian slacker and wannabe rockstar who falls in love with an American delivery girl, Ramona V. Flowers, and must defeat her seven evil exes to be able to date her.', 'scott-pilgrim-vs-the-world.jpg', '', 'https://www.youtube.com/embed/7wd5KEaOtm4', '', 1),
(16, 0, '1,19,', 'English', '6.6', '2010', 'Jon Favreau', 'Scarlett Johansson,  Robert Downey, Gwyneth Paltrow', 1, '', 3, 0, '2015-05-04 20:33:32', '2015-05-04 20:33:32', 'Iron Man 2', 'iron-man-2', 'Now that his Super Hero secret has been revealed, Tony Stark''s life is more intense than ever. Everyone wants in on the Iron Man technology, whether for power or profit... But for Ivan Vanko, it''s revenge! Tony must once again suit up and face his most dangerous enemy yet, but not without a few new allies of his own.', 'iron-man-2.jpg', '', 'https://www.youtube.com/embed/FNQowwwwYa0', '', 1),
(17, 0, '1,8,', 'English', '7.0', '2012', 'Ang Lee', 'Suraj Sharma, Irrfan Khan, Ayush Tandon', 1, '', 3, 0, '2015-05-04 20:34:45', '2015-05-04 20:34:45', 'Life of Pi', 'life-of-pi', 'The story of an Indian boy named Pi, a zookeeper''s son who finds himself in the company of a hyena, zebra, orangutan, and a Bengal tiger after a shipwreck sets them adrift in the Pacific Ocean.', 'life-of-pi.jpg', '', 'https://www.youtube.com/embed/j9Hjrs6WQ8M', '', 1),
(18, 0, '1,', 'English', '6.6', '2006', 'Gore Verbinski', 'Johnny Depp, Orlando Bloom, Keira Knightley', 1, '', 3, 0, '2015-05-04 20:36:13', '2015-05-04 20:36:13', 'Pirates of the Caribbean: Dead Man''s Chest ', 'pirates-of-the-caribbean-dead-mans-chest', 'The high-seas adventures of happy-go-lucky troublemaker Captain Jack Sparrow, young Will Turner and headstrong beauty Elizabeth Swann continues as Sparrow works his way out of a blood debt with the ghostly Davey Jones, he also attempts to avoid eternal damnation.', 'pirates-of-the-caribbean-dead-mans-chest.jpg', '', 'https://www.youtube.com/embed/wXCs8qDWEMk', '', 1),
(19, 0, '6,', 'English', '6.2', '2012', 'Seth MacFarlane', 'Sam Jones, Mark Wahlberg, Mila Kunis', 1, '', 3, 0, '2015-05-04 20:37:49', '2015-05-04 20:37:49', 'Ted', 'ted', 'Family Guy creator Seth MacFarlane brings his boundary-pushing brand of humor to the big screen for the first time as writer, director and voice star of Ted. In the live action/CG-animated comedy, he tells the story of John Bennett, a grown man who must deal with the cherished teddy bear who came to life as the result of a childhood wish, and has refused to leave his side ever since.', 'ted.jpg', '', 'https://www.youtube.com/embed/9fbo_pQvU7M', '', 1),
(20, 0, '1,', 'English', '6.5', '2011', 'Brad Bird', 'Tom Cruise, Jeremy Renner, Simon Pegg', 1, '', 3, 0, '2015-05-04 20:39:37', '2015-05-04 20:39:37', 'Mission: Impossible - Ghost Protocol', 'mission-impossible-ghost-protocol', 'In the 4th installment of the Mission Impossible series, Ethan Hunt (Cruise) and his team are racing against time to track down a dangerous terrorist named Hendricks (Nyqvist), who has gained access to Russian nuclear launch codes and is planning a strike on the United States. An attempt to stop him ends in an explosion causing severe destruction to the Kremlin and the IMF to be implicated in the bombing, forcing the President to disavow them. No longer being aided by the government, Ethan and his team chase Hendricks around the globe, although they might still be too late to stop a disaster.', 'mission-impossible-ghost-protocol.jpg', '', 'https://www.youtube.com/embed/V0LQnQSrC-g', '', 1),
(21, 0, '1,8,12,', 'English', '7.7', '2014', 'Dan Gilroy', 'Bill Paxton, Riz Ahmed, Jake Gyllenhaal', 1, '', 3, 0, '2015-05-04 20:43:16', '2015-05-04 20:43:16', 'Nightcrawler', 'nightcrawler', 'A driven young man (Gyllenhaal) stumbles upon the underground world of L.A. freelance crime journalism. When Lou Bloom, desperate for work, muscles into the world of L.A. crime journalism, he blurs the line between observer and participant to become the star of his own story. Aiding him in his effort is Nina, a TV-news veteran.', 'nightcrawler.jpg', '', 'https://www.youtube.com/embed/1lEdwqwOttg', '', 1),
(22, 0, '8,', 'English', '6.3', '2008', 'Robert Luketic', 'Jim Sturgess, Kevin Spacey, Kate Bosworth', 1, '', 3, 0, '2015-05-04 20:44:53', '2015-05-04 20:44:53', '21', '21', 'Ben Campbell is a young, highly intelligent, student at M.I.T. in Boston who strives to succeed. Wanting a scholarship to transfer to Harvard School of Medicine with the desire to become a doctor, Ben learns that he cannot afford the $300,000 for the four to five years of schooling as he comes from a poor, working-class background. But one evening, Ben is introduced by his unorthodox math professor Micky Rosa into a small but secretive club of five. Students Jill, Choi, Kianna, and Fisher, who are being trained by Professor Rosa of the skill of card counting at blackjack.', '21.jpg', '', 'https://www.youtube.com/embed/2v9z9EACSnU', '', 1),
(23, 0, '1,8,', 'English', '7.2', '2013', 'Louis Leterrier', 'Morgan Freeman, Jesse Eisenberg, Mark Ruffalo', 1, '', 4, 0, '2015-05-04 20:46:13', '2015-05-04 20:46:13', 'Now You See Me', 'now-you-see-me', 'An FBI agent and an Interpol detective track a team of illusionists who pull off bank heists during their performances and reward their audiences with the money.', 'now-you-see-me.jpg', '', 'https://www.youtube.com/embed/VXuQDNdbd5E', '', 1);
			");
		}
	}
	static function gold_settings($type) {
		if($type == 'table') {
			return mysql_query("
				CREATE TABLE IF NOT EXISTS `gold_settings` (
				  `set_id` int(11) NOT NULL AUTO_INCREMENT,
				  `set_name` varchar(1000) NOT NULL,
				  `set_content` text NOT NULL,
				  PRIMARY KEY (`set_id`)
				) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=27 ;
			");
		}
		if($type == 'insert') {
			return mysql_query("
				INSERT INTO `gold_settings` (`set_id`, `set_name`, `set_content`) VALUES
				(1, 'gold_email', 'soufiane.chakroun@gmail.com'),
				(2, 'gold_rows', '25'),
				(3, 'gold_logo', 'logo.png'),
				(4, 'gold_email_template_register_title', 'Registration'),
				(5, 'gold_email_template_register', '<style type=text/css>    /* CLIENT-SPECIFIC STYLES */    #outlook a{padding:0;} /* Force Outlook to provide a view in browser message */    .ReadMsgBody{width:100%;} .ExternalClass{width:100%;} /* Force Hotmail to display emails at full width */    .ExternalClass, .ExternalClass p, .ExternalClass span, .ExternalClass font, .ExternalClass td, .ExternalClass div {line-height: 100%;} /* Force Hotmail to display normal line spacing */    body, table, td, a{-webkit-text-size-adjust:100%; -ms-text-size-adjust:100%;} /* Prevent WebKit and Windows mobile changing default text sizes */    table, td{mso-table-lspace:0pt; mso-table-rspace:0pt;} /* Remove spacing between tables in Outlook 2007 and up */    img{-ms-interpolation-mode:bicubic;} /* Allow smoother rendering of resized image in Internet Explorer */    /* RESET STYLES */    body{margin:0; padding:0;}    img{border:0; height:auto; line-height:100%; outline:none; text-decoration:none;}    table{border-collapse:collapse !important;}    body{height:100% !important; margin:0; padding:0; width:100% !important;}    /* iOS BLUE LINKS */    .appleBody a {color:#68440a; text-decoration: none;}    .appleFooter a {color:#999999; text-decoration: none;}    /* MOBILE STYLES */    @media screen and (max-width: 525px) {        /* ALLOWS FOR FLUID TABLES */        table[class=wrapper]{          width:100% !important;        }        /* ADJUSTS LAYOUT OF LOGO IMAGE */        td[class=logo]{          text-align: left;          padding: 20px 0 20px 0 !important;        }        td[class=logo] img{          margin:0 auto!important;        }        /* USE THESE CLASSES TO HIDE CONTENT ON MOBILE */        td[class=mobile-hide]{          display:none;}        img[class=mobile-hide]{          display: none !important;        }        img[class=img-max]{          max-width: 100% !important;          height:auto !important;        }        /* FULL-WIDTH TABLES */        table[class=responsive-table]{          width:100%!important;        }        /* UTILITY CLASSES FOR ADJUSTING PADDING ON MOBILE */        td[class=padding]{          padding: 10px 5% 15px 5% !important;        }        td[class=padding-copy]{          padding: 10px 5% 10px 5% !important;          text-align: center;        }        td[class=padding-meta]{          padding: 30px 5% 0px 5% !important;          text-align: center;        }        td[class=no-pad]{          padding: 0 0 20px 0 !important;        }        td[class=no-padding]{          padding: 0 !important;        }        td[class=section-padding]{          padding: 50px 15px 50px 15px !important;        }        td[class=section-padding-bottom-image]{          padding: 50px 15px 0 15px !important;        }        /* ADJUST BUTTONS ON MOBILE */        td[class=mobile-wrapper]{            padding: 10px 5% 15px 5% !important;        }        table[class=mobile-button-container]{            margin:0 auto;            width:100% !important;        }        a[class=mobile-button]{            width:80% !important;            padding: 15px !important;            border: 0 !important;            font-size: 16px !important;        }    }</style><!-- HEADER --><table border=0 cellpadding=0 cellspacing=0 width=100%>    <tbody><tr>        <td bgcolor=#ffffff>            <div align=center style=padding: 0px 15px 0px 15px;>                <table border=0 cellpadding=0 cellspacing=0 width=500 class=wrapper>                    <!-- LOGO/PREHEADER TEXT -->                    <tbody><tr>                        <td style=padding: 20px 0px 30px 0px; class=logo>                            <table border=0 cellpadding=0 cellspacing=0 width=100%>                                <tbody><tr>                                    <td width=160 align=left style=    border-radius: 40px;    background-color: #DF0000;><a href={root} target=_blank><img alt=Logo src={skin}/images/logo.png style=display: block; font-family: Helvetica, Arial, sans-serif; color: #666666; font-size: 16px; border=0></a></td>                                    <td bgcolor=#ffffff width=400 align=right class=mobile-hide>                                        <table border=0 cellpadding=0 cellspacing=0>                                            <tbody><tr>                                                <td align=right style=padding: 0 0 5px 0; font-size: 14px; font-family: Arial, sans-serif; color: #666666; text-decoration: none;><span style=color: #666666; text-decoration: none;>Gold MEDIA - MEDIA SHARE SCRIPT<br>Create Media Website.</span></td>                                            </tr>                                        </tbody></table>                                    </td>                                </tr>                            </tbody></table>                        </td>                    </tr>                </tbody></table>            </div>        </td>    </tr></tbody></table><!-- ONE COLUMN SECTION --><table border=0 cellpadding=0 cellspacing=0 width=100%>    <tbody><tr>        <td bgcolor=#ffffff align=center style=padding: 70px 15px 70px 15px; class=section-padding>            <table border=0 cellpadding=0 cellspacing=0 width=500 class=responsive-table>                <tbody><tr>                    <td>                        <table width=100% border=0 cellspacing=0 cellpadding=0>                            <tbody><tr>                                <td>                                    <!-- HERO IMAGE -->                                    <table width=100% border=0 cellspacing=0 cellpadding=0>                                        <tbody>                                             <tr>                                                  <td class=padding-copy>                                                      <table width=100% border=0 cellspacing=0 cellpadding=0>                                                          <tbody><tr>                                                              <td>                                                                  <a href={root} target=_blank><img src={skin}/images/responsive-email.jpg width=500 height=200 border=0 style=display: block; padding: 0; color: #666666; text-decoration: none; font-family: Helvetica, arial, sans-serif; font-size: 16px; width: 500px; height: 200px; class=img-max></a>                                                              </td>                                                            </tr>                                                        </tbody></table>                                                  </td>                                              </tr>                                        </tbody>                                    </table>                                </td>                            </tr>                            <tr>                                <td>                                    <!-- COPY -->                                    <table width=100% border=0 cellspacing=0 cellpadding=0>                                        <tbody><tr>                                            <td align=center style=font-size: 25px; font-family: Helvetica, Arial, sans-serif; color: #333333; padding-top: 30px; class=padding-copy>Registration Complete</td>                                        </tr>                                        <tr>                                            <td align=center style=padding: 20px 0 0 0; font-size: 16px; line-height: 25px; font-family: Helvetica, Arial, sans-serif; color: #666666; class=padding-copy>Thanks for registering on our media website. We will be happy, if you will be our website active member.</td>                                        </tr>                                    </tbody></table>                                </td>                            </tr>                            <tr>                                <td>                                    <!-- BULLETPROOF BUTTON -->                                    <table width=100% border=0 cellspacing=0 cellpadding=0 class=mobile-button-container>                                        <tbody><tr>                                            <td align=center style=padding: 25px 0 0 0; class=padding-copy>                                                <table border=0 cellspacing=0 cellpadding=0 class=responsive-table>                                                    <tbody><tr>                                                        <td align=center><a href={root}/confirm/{confirmation_code}/ target=_blank style=font-size: 16px; font-family: Helvetica, Arial, sans-serif; font-weight: normal; color: #ffffff; text-decoration: none; background-color: #EC3A39; border-top: 15px solid #EC3A39; border-bottom: 15px solid #EC3A39; border-left: 25px solid #EC3A39; border-right: 25px solid #EC3A39; border-radius: 3px; -webkit-border-radius: 3px; -moz-border-radius: 3px; display: inline-block; class=mobile-button>Confirm Registration →</a></td>                                                    </tr>                                                </tbody></table>                                            </td>                                        </tr>                                    </tbody></table>                                </td>                            </tr>                        </tbody></table>                    </td>                </tr>            </tbody></table>        </td>    </tr></tbody></table><!-- FOOTER --><table border=0 cellpadding=0 cellspacing=0 width=100%>    <tbody><tr>        <td bgcolor=#ffffff align=center>            <table width=100% border=0 cellspacing=0 cellpadding=0 align=center>                <tbody><tr>                    <td style=padding: 20px 0px 20px 0px;>                        <!-- UNSUBSCRIBE COPY -->                        <table width=500 border=0 cellspacing=0 cellpadding=0 align=center class=responsive-table>                            <tbody><tr>                                <td align=center valign=middle style=font-size: 12px; line-height: 18px; font-family: Helvetica, Arial, sans-serif; color:#666666;>                                    <span class=appleFooter style=color:#666666;>Gold MEDIA - MEDIA SHARE SCRIPT</span><br>                                </td>                            </tr>                        </tbody></table>                    </td>                </tr>            </tbody></table>        </td>    </tr></tbody></table>'),
				(6, 'gold_email_template_forgot_title', 'Forgot Password'),
				(7, 'gold_email_template_forgot', '<style type=text/css>    /* CLIENT-SPECIFIC STYLES */    #outlook a{padding:0;} /* Force Outlook to provide a view in browser message */    .ReadMsgBody{width:100%;} .ExternalClass{width:100%;} /* Force Hotmail to display emails at full width */    .ExternalClass, .ExternalClass p, .ExternalClass span, .ExternalClass font, .ExternalClass td, .ExternalClass div {line-height: 100%;} /* Force Hotmail to display normal line spacing */    body, table, td, a{-webkit-text-size-adjust:100%; -ms-text-size-adjust:100%;} /* Prevent WebKit and Windows mobile changing default text sizes */    table, td{mso-table-lspace:0pt; mso-table-rspace:0pt;} /* Remove spacing between tables in Outlook 2007 and up */    img{-ms-interpolation-mode:bicubic;} /* Allow smoother rendering of resized image in Internet Explorer */    /* RESET STYLES */    body{margin:0; padding:0;}    img{border:0; height:auto; line-height:100%; outline:none; text-decoration:none;}    table{border-collapse:collapse !important;}    body{height:100% !important; margin:0; padding:0; width:100% !important;}    /* iOS BLUE LINKS */    .appleBody a {color:#68440a; text-decoration: none;}    .appleFooter a {color:#999999; text-decoration: none;}    /* MOBILE STYLES */    @media screen and (max-width: 525px) {        /* ALLOWS FOR FLUID TABLES */        table[class=wrapper]{          width:100% !important;        }        /* ADJUSTS LAYOUT OF LOGO IMAGE */        td[class=logo]{          text-align: left;          padding: 20px 0 20px 0 !important;        }        td[class=logo] img{          margin:0 auto!important;        }        /* USE THESE CLASSES TO HIDE CONTENT ON MOBILE */        td[class=mobile-hide]{          display:none;}        img[class=mobile-hide]{          display: none !important;        }        img[class=img-max]{          max-width: 100% !important;          height:auto !important;        }        /* FULL-WIDTH TABLES */        table[class=responsive-table]{          width:100%!important;        }        /* UTILITY CLASSES FOR ADJUSTING PADDING ON MOBILE */        td[class=padding]{          padding: 10px 5% 15px 5% !important;        }        td[class=padding-copy]{          padding: 10px 5% 10px 5% !important;          text-align: center;        }        td[class=padding-meta]{          padding: 30px 5% 0px 5% !important;          text-align: center;        }        td[class=no-pad]{          padding: 0 0 20px 0 !important;        }        td[class=no-padding]{          padding: 0 !important;        }        td[class=section-padding]{          padding: 50px 15px 50px 15px !important;        }        td[class=section-padding-bottom-image]{          padding: 50px 15px 0 15px !important;        }        /* ADJUST BUTTONS ON MOBILE */        td[class=mobile-wrapper]{            padding: 10px 5% 15px 5% !important;        }        table[class=mobile-button-container]{            margin:0 auto;            width:100% !important;        }        a[class=mobile-button]{            width:80% !important;            padding: 15px !important;            border: 0 !important;            font-size: 16px !important;        }    }</style><!-- HEADER --><table border=0 cellpadding=0 cellspacing=0 width=100%>    <tbody><tr>        <td bgcolor=#ffffff>            <div align=center style=padding: 0px 15px 0px 15px;>                <table border=0 cellpadding=0 cellspacing=0 width=500 class=wrapper>                    <!-- LOGO/PREHEADER TEXT -->                    <tbody><tr>                        <td style=padding: 20px 0px 30px 0px; class=logo>                            <table border=0 cellpadding=0 cellspacing=0 width=100%>                                <tbody><tr>                                    <td width=160 align=left style=    border-radius: 40px;    background-color: #DF0000;><a href={root} target=_blank><img alt=Logo src={skin}/images/logo.png style=display: block; font-family: Helvetica, Arial, sans-serif; color: #666666; font-size: 16px; border=0></a></td>                                    <td bgcolor=#ffffff width=400 align=right class=mobile-hide>                                        <table border=0 cellpadding=0 cellspacing=0>                                            <tbody><tr>                                                <td align=right style=padding: 0 0 5px 0; font-size: 14px; font-family: Arial, sans-serif; color: #666666; text-decoration: none;><span style=color: #666666; text-decoration: none;>Gold MEDIA - MEDIA SHARE SCRIPT<br>Create Media Website.</span></td>                                            </tr>                                        </tbody></table>                                    </td>                                </tr>                            </tbody></table>                        </td>                    </tr>                </tbody></table>            </div>        </td>    </tr></tbody></table><!-- ONE COLUMN SECTION --><table border=0 cellpadding=0 cellspacing=0 width=100%>    <tbody><tr>        <td bgcolor=#ffffff align=center style=padding: 70px 15px 70px 15px; class=section-padding>            <table border=0 cellpadding=0 cellspacing=0 width=500 class=responsive-table>                <tbody><tr>                    <td>                        <table width=100% border=0 cellspacing=0 cellpadding=0>                            <tbody><tr>                                <td>                                    <!-- HERO IMAGE -->                                    <table width=100% border=0 cellspacing=0 cellpadding=0>                                        <tbody>                                             <tr>                                                  <td class=padding-copy>                                                      <table width=100% border=0 cellspacing=0 cellpadding=0>                                                          <tbody><tr>                                                              <td>                                                                  <a href={root} target=_blank><img src={skin}/images/responsive-email.jpg width=500 height=200 border=0 style=display: block; padding: 0; color: #666666; text-decoration: none; font-family: Helvetica, arial, sans-serif; font-size: 16px; width: 500px; height: 200px; class=img-max></a>                                                              </td>                                                            </tr>                                                        </tbody></table>                                                  </td>                                              </tr>                                        </tbody>                                    </table>                                </td>                            </tr>                            <tr>                                <td>                                    <!-- COPY -->                                    <table width=100% border=0 cellspacing=0 cellpadding=0>                                        <tbody><tr>                                            <td align=center style=font-size: 25px; font-family: Helvetica, Arial, sans-serif; color: #333333; padding-top: 30px; class=padding-copy>Password Reset</td>                                        </tr>                                        <tr>                                            <td align=center style=padding: 20px 0 0 0; font-size: 16px; line-height: 25px; font-family: Helvetica, Arial, sans-serif; color: #666666; class=padding-copy>Thanks for password reseting on our media website. We will be happy, if you will be our website active member.</td>                                        </tr>                                    </tbody></table>                                </td>                            </tr>                            <tr>                                <td>                                    <!-- BULLETPROOF BUTTON -->                                    <table width=100% border=0 cellspacing=0 cellpadding=0 class=mobile-button-container>                                        <tbody><tr>                                            <td align=center style=padding: 25px 0 0 0; class=padding-copy>                                                <table border=0 cellspacing=0 cellpadding=0 class=responsive-table>                                                    <tbody><tr>                                                        <td align=center><a target=_blank style=font-size: 16px; font-family: Helvetica, Arial, sans-serif; font-weight: normal; color: #ffffff; text-decoration: none; background-color: #EC3A39; border-top: 15px solid #EC3A39; border-bottom: 15px solid #EC3A39; border-left: 25px solid #EC3A39; border-right: 25px solid #EC3A39; border-radius: 3px; -webkit-border-radius: 3px; -moz-border-radius: 3px; display: inline-block; class=mobile-button>Your Temporary Password: <b>{password}</b></a></td>                                                    </tr>                                                </tbody></table>                                            </td>                                        </tr>                                    </tbody></table>                                </td>                            </tr>                        </tbody></table>                    </td>                </tr>            </tbody></table>        </td>    </tr></tbody></table><!-- FOOTER --><table border=0 cellpadding=0 cellspacing=0 width=100%>    <tbody><tr>        <td bgcolor=#ffffff align=center>            <table width=100% border=0 cellspacing=0 cellpadding=0 align=center>                <tbody><tr>                    <td style=padding: 20px 0px 20px 0px;>                        <!-- UNSUBSCRIBE COPY -->                        <table width=500 border=0 cellspacing=0 cellpadding=0 align=center class=responsive-table>                            <tbody><tr>                                <td align=center valign=middle style=font-size: 12px; line-height: 18px; font-family: Helvetica, Arial, sans-serif; color:#666666;>                                    <span class=appleFooter style=color:#666666;>Gold MEDIA - MEDIA SHARE SCRIPT</span><br>                                </td>                            </tr>                        </tbody></table>                    </td>                </tr>            </tbody></table>        </td>    </tr></tbody></table>'),
				(8, 'gold_website_title', 'Films, Series, Animes - STREAM PARADISE'),
				(9, 'gold_website_description', 'Regardez des films/series/animes en ligne - STREAM PARADISE'),
				(10, 'gold_website_keywords', 'Films, streaming, HD, Films en ligne, series, animes, STREAM PARADISE'),
				(11, 'gold_max_related_media', '5'),
				(12, 'gold_max_tags', '20'),
				(13, 'gold_skin', 'custom1'),
				(14, 'gold_lang', 'en'),
				(15, 'points_posting_a_media', '2'),
				(16, 'points_per_up_vote_on_your_media', '1'),
				(17, 'points_per_down_vote_on_your_media', '1'),
				(18, 'points_posting_a_comment', '4'),
				(19, 'points_per_up_vote_on_your_comment', '2'),
				(20, 'points_per_down_vote_on_your_comment', '2'),
				(21, 'points_voting_up_a_media', '1'),
				(22, 'points_voting_down_a_media', '1'),
				(23, 'points_voting_up_a_comment', '1'),
				(24, 'points_voting_down_a_comment', '1'),
				(25, 'points_add_for_all_users', '100'),
				(26, 'points_add_for_every_login', '5'),
				(27, 'gold_slider', '0'),
				(28, 'gold_infobox', '0'),
				(29, 'gold_boxtype', '1');
			");
		}
	}
}