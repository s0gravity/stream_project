<?php
// GOLD PLUGIN
class SocialLogin extends GOLD_PLUGINS{
	public function set($set_name)
    {
		$q = mysql_query("SELECT * FROM gold_plugins_settings WHERE set_name='".$set_name."' LIMIT 1");
		while($row = mysql_fetch_assoc($q)){
				$GOLD_echo = $row['set_content']; // Inside while loop
		}
		return $GOLD_echo;
    }
	public function loginwith() {
		// $this->autoRender = false;
		$provider = $_GET['provider'];
		$root = (!empty($_SERVER['HTTPS']) ? 'https' : 'http') . '://' . $_SERVER['HTTP_HOST'] . '/';
        require_once( 'hybridauth/Hybrid/Auth.php' );

        $hybridauth_config = array(
            "base_url" => $root."gold-app/gold-plugins/SocialLogin/hybridauth/", // set hybridauth path

            "providers" => array(
                "Facebook" => array ( 
					"enabled" => true,
					"keys"    => array ( "id" => $this->set('fb_id'), "secret" => $this->set('fb_secret') ), 
				),
				
				"Google" => array ( 
					"enabled" => true,
					"keys"    => array ( "id" => $this->set('google_id'), "secret" => $this->set('google_secret') ), 
				),
				
				"LinkedIn" => array ( 
					"enabled" => true,
					"keys"    => array ( "key" => $this->set('linkedin_key'), "secret" => $this->set('linkedin_secret') ) 
				),

				"Twitter" => array ( 
					"enabled" => true,
					"keys"    => array ( "key" => $this->set('twitter_key'), "secret" => $this->set('twitter_secret') ) 
				),
				
				"Live" => array ( 
					"enabled" => true,
					"keys"    => array ( "id" => $this->set('live_id'), "secret" => $this->set('live_secret') ) 
				),
				
				"Tumblr" => array ( 
					"enabled" => true,
					"keys"    => array ( "key" => $this->set('tumblr_id'), "secret" => $this->set('tumblr_secret') ) 
				)
			// For another provider refer to HybridAuth Documentation
            )
        );

        try {
			$provider = $_GET['provider'];
            // create an instance for Hybridauth with the configuration file path as parameter
            $hybridauth = new Hybrid_Auth($hybridauth_config);

            // try to authenticate the selected $provider
            $adapter = $hybridauth->authenticate($provider);

            // grab the user profile
            $user_profile = $adapter->getUserProfile();

            //debug($user_profile); //uncomment this to print the object
            //exit();
            //$this->set( 'user_profile',  $user_profile );
           
            //login user using auth component
			$try_sql = mysql_query("SELECT * FROM gold_users WHERE user_identifier='".$user_profile->identifier."' AND user_username='".str_replace(".", "", $user_profile->displayName)."' AND user_email='".$user_profile->email."'");
			if(mysql_num_rows($try_sql) > 0) {
				$try = mysql_fetch_array($try_sql);
				$_SESSION['user_username'] = $try['user_username'];
				$_SESSION['user_email'] = $try['user_email'];
				$_SESSION['user_id'] = $try['user_id'];
				header("Location: ".$root."");
			} else {
				$this->_NEW_USER($provider, $user_profile->identifier, str_replace(".", "", $user_profile->displayName), $user_profile->firstName, $user_profile->lastName, $user_profile->email, $profile_url);
            	$try = mysql_fetch_array($try_sql);
				header("Location: ".$root."/login?provider=$provider");
				$_SESSION['user_username'] = $try['user_username'];
				$_SESSION['user_email'] = $try['user_email'];
				$_SESSION['user_id'] = $try['user_id'];
			}
        } catch (Exception $e) {
            // Display the recived error
            switch ($e->getCode()) {
                case 0 : $error = " - Unspecified error.";
                    break;
                case 1 : $error = " - Hybriauth configuration error.";
                    break;
                case 2 : $error = " - Provider not properly configured.";
                    break;
                case 3 : $error = " - Unknown or disabled provider.";
                    break;
                case 4 : $error = " - Missing provider application credentials.";
                    break;
                case 5 : $error = " - Authentification failed. The user has canceled the authentication or the provider refused the connection.";
                    break;
                case 6 : $error = " - User profile request failed. Most likely the user is not connected to the provider and he should to authenticate again.";
                    $adapter->logout();
                    break;
                case 7 : $error = " - User not connected to the provider.";
                    $adapter->logout();
                    break;
            }

            // well, basically you should not display this to the end user, just give him a hint and move on..
            $error .= " Original error message: " . $e->getMessage();
			echo '<span style="display: block; margin: 0 auto; width: 100%; max-width: 1770px; text-align: center; -webkit-box-sizing: content-box; -moz-box-sizing: content-box; box-sizing: content-box; padding-top: 10px; padding-bottom: 20px; font-family: Arial; color: #000;">'.'<b style="color: #EC0000;">'.$provider.'</b>'.$error.'</span>';
        }
    }
	function display()
	{
		if($_REQUEST['gold'] == 'login' && $_REQUEST['provider']) {
			$this->loginwith($_REQUEST['provider']);
		}
	}
}
?>