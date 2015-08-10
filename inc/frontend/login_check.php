<?php defined( 'ABSPATH' ) or die( "No script kiddies please!" ); ?>
<?php
if( !class_exists( 'APSL_Lite_Login_Check_Class' ) ){
	class APSL_Lite_Login_Check_Class{

//constructor
function __construct(){
	if(isset($_GET['apsl_login_id'])){
		$exploder=explode( '_', $_GET['apsl_login_id'] );
		switch($exploder[0]){
			case 'facebook':
				//include( APSL_PLUGIN_DIR.'facebook/src/facebook.php' );
				if (version_compare(PHP_VERSION, '5.4.0', '<')) {
				   echo _e('The Facebook SDK requires PHP version 5.4 or higher. Please notify about this error to site admin.', APSL_TEXT_DOMAIN );
					die();
				}
				$this->onFacebookLogin();
				break;

			case 'twitter':
			    if( !class_exists( 'TwitterOAuth' ) ){
				include( APSL_PLUGIN_DIR.'twitter/OAuth.php' );
				include( APSL_PLUGIN_DIR.'twitter/twitteroauth.php' );
                }
				$this->onTwitterLogin();
				break;

			case 'google':
				include( APSL_PLUGIN_DIR.'google/Client.php' );
				include( APSL_PLUGIN_DIR.'google/Service/Plus.php' );
				$this->onGoogleLogin();
				break;
		}
	}
}

//for facebook login
function onFacebookLogin(){
	$response = new stdClass();
	$result = $this->facebookLogin($response);
	if(isset($result->status) == 'SUCCESS'){
		$row = $this->getUserByMail( $result->email );
		$options = get_option( APSL_SETTINGS );
		
		if(!$row){
			$this->creatUser($result->username, $result->email);
			$row = $this->getUserByMail( $result->email);
			update_user_meta($row->ID, 'email', $result->email);
			update_user_meta($row->ID, 'first_name', $result->first_name);
			update_user_meta($row->ID, 'last_name', $result->last_name);
			update_user_meta($row->ID, 'deuid', $result->deuid);
			update_user_meta($row->ID, 'deutype', $result->deutype);
			update_user_meta($row->ID, 'deuimage', $result->deuimage);
			update_user_meta($row->ID, 'description', $result->about);
			update_user_meta($row->ID, 'sex', $result->gender);
			wp_update_user( array ('ID' => $row->ID, 'display_name' => $result->first_name.' '.$result->last_name, 'role'=>$options['apsl_user_role'], 'user_url' => $result->url) ) ;
		}
		$this->loginUser($row->ID);
	}
}



	function facebookLogin(){
			$request 	= $_REQUEST;
			$site 		= $this->siteUrl();
			$callBackUrl= $this->callBackUrl();
			$response 	= new stdClass();
			$return_user_details = new stdClass();
			$exploder=explode('_', $_GET['apsl_login_id']);
			$action		= $exploder[1];
			$options = get_option( APSL_SETTINGS );
			$config = array(
							  'app_id' 					=> $options['apsl_facebook_settings']['apsl_facebook_app_id'],
							  'app_secret' 				=> $options['apsl_facebook_settings']['apsl_facebook_app_secret'],
							  'default_graph_version' 	=> 'v2.4'
					  	  );
			
			include( APSL_PLUGIN_DIR.'facebook/autoload.php' );
			$fb = new Facebook\Facebook($config);
			
			$encoded_url = isset($_GET['redirect_to']) ? $_GET['redirect_to'] : '';

			if(isset($encoded_url) && $encoded_url !=''){
				$callback =$callBackUrl.'apsl_login_id'.'=facebook_check&redirect_to='.$encoded_url;
			}else{
				$callback =$callBackUrl.'apsl_login_id'.'=facebook_check';
			}

			if ($action == 'login'){
					// Well looks like we are a fresh dude, login to Facebook!
				    $helper = $fb->getRedirectLoginHelper();
				    $permissions = array('email', 'public_profile'); // optional
				    $loginUrl = $helper->getLoginUrl($callback, $permissions);
				    $this->redirect($loginUrl);
			}else{
					if(isset($_REQUEST['error'])){
					$response->status 		= 'ERROR';
					$response->error_code 	= 2;
					$response->error_message= 'INVALID AUTHORIZATION';
					return $response;
					die();
					}
					if (isset($_REQUEST['code'])) {
					        $helper = $fb->getRedirectLoginHelper();
					        try {
					            $accessToken = $helper->getAccessToken();
					        } catch(Facebook\Exceptions\FacebookResponseException $e) {
					              
					              // When Graph returns an error
					              echo 'Graph returned an error: ' . $e->getMessage();
					              exit;
					        } catch(Facebook\Exceptions\FacebookSDKException $e) {
					              
					              // When validation fails or other local issues
					              echo 'Facebook SDK returned an error: ' . $e->getMessage();
					            exit;
					        }

				        	if (isset($accessToken)) {
				              	// Logged in!
				              	$_SESSION['facebook_access_token'] = (string) $accessToken;
							  	$fb->setDefaultAccessToken($accessToken);

								try {
								  $response = $fb->get('/me?fields=email,name, first_name, last_name, gender, link, about, address, bio, birthday, education, hometown, is_verified, languages, location, website');
								  $userNode = $response->getGraphUser();
								} catch(Facebook\Exceptions\FacebookResponseException $e) {
								  // When Graph returns an error
								  echo 'Graph returned an error: ' . $e->getMessage();
								  exit;
								} catch(Facebook\Exceptions\FacebookSDKException $e) {
								  // When validation fails or other local issues
								  echo 'Facebook SDK returned an error: ' . $e->getMessage();
								  exit;
								}

						        $user_profile = $this->accessProtected($userNode, 'items');
					          	if($user_profile!=null){
									$return_user_details->status 		= 'SUCCESS';
									$return_user_details->deuid		= $user_profile['id'];
									$return_user_details->deutype		= 'facebook';
									$return_user_details->first_name	= $user_profile['first_name'];
									$return_user_details->last_name	= $user_profile['last_name'];
									$return_user_details->email		= $user_profile['email'];
									$return_user_details->username		= $user_profile['email'];
									$return_user_details->gender 		= $user_profile['gender'];
									$return_user_details->url 			= $user_profile['link'];
									$return_user_details->about 		= ''; //facebook doesn't return user about details.
									$headers = get_headers('https://graph.facebook.com/'.$user_profile['id'].'/picture',1);
									
									// just a precaution, check whether the header isset...
									if(isset($headers['Location'])) {
										$return_user_details->deuimage = $headers['Location']; // string
									} else {
										$return_user_details->deuimage = false; // nothing there? .. weird, but okay!
									}
									$return_user_details->error_message = '';
									}else{
										$return_user_details->status 		= 'ERROR';
										$return_user_details->error_code 	= 2;
										$return_user_details->error_message= 'INVALID AUTHORIZATION';
									}
				        	}
					} else {
					    // Well looks like we are a fresh dude, login to Facebook!
					    $helper = $fb->getRedirectLoginHelper();
					    $permissions = array('email', 'public_profile'); // optional
					    $loginUrl = $helper->getLoginUrl($callback, $permissions);
					    $this->redirect($loginUrl);
					}

			}
			return $return_user_details;
	}


//for twitter login
function onTwitterLogin(){
	$result = $this->twitterLogin();
	if(isset($result->status) == 'SUCCESS'){
		$row = $this->getUserByMail( $result->email);
		$options = get_option( APSL_SETTINGS );
		if(!$row){
			$this->creatUser($result->username, $result->email);
			$row = $this->getUserByMail( $result->email);
			update_user_meta($row->ID, 'email', $result->email);
			update_user_meta($row->ID, 'first_name', $result->first_name);
			update_user_meta($row->ID, 'last_name', $result->last_name);
			update_user_meta($row->ID, 'deuid', $result->deuid);
			update_user_meta($row->ID, 'deutype', $result->deutype);
			update_user_meta($row->ID, 'deuimage', $result->deuimage);
			update_user_meta($row->ID, 'description', $result->about);
			wp_update_user( array ('ID' => $row->ID, 'display_name' => $result->first_name.' '.$result->last_name, 'role'=>$options['apsl_user_role'], 'user_url' => $result->url) ) ;
		}
		$this->loginUser($row->ID);
	}
}

	function twitterLogin(){
		$request 	= $_REQUEST;
		$site 		= $this->siteUrl();
		$callBackUrl= $this->callBackUrl();
		$response 	= new stdClass();
		$exploder=explode('_', $_GET['apsl_login_id']);
		$action		= $exploder[1];
		@session_start();
		$options = get_option( APSL_SETTINGS );
		if ($action == 'login'){
			// Get identity from user and redirect browser to OpenID Server
			if(!isset($request['oauth_token']) || $request['oauth_token']==''){
				$twitterObj 	= new TwitterOAuth($options['apsl_twitter_settings']['apsl_twitter_api_key'], $options['apsl_twitter_settings']['apsl_twitter_api_secret'] );
				$encoded_url = isset($_GET['redirect_to']) ? $_GET['redirect_to'] : '';
				if(isset($encoded_url) && $encoded_url !=''){
					$callback =$callBackUrl.'apsl_login_id'.'=twitter_check&redirect_to='.$encoded_url;
				}else{
					$callback =$callBackUrl.'apsl_login_id'.'=twitter_check';
				}

				$request_token 	= $twitterObj->getRequestToken($callback);
				$_SESSION['oauth_twitter'] = array();
				
				/* Save temporary credentials to session. */
				$_SESSION['oauth_twitter']['oauth_token'] = $token = $request_token['oauth_token'];
				$_SESSION['oauth_twitter']['oauth_token_secret'] = $request_token['oauth_token_secret'];
				
				/* If last connection failed don't display authorization link. */
				switch ($twitterObj->http_code) {
					case 200:
						try{
							$url = $twitterObj->getAuthorizeUrl($token);
							$this->redirect($url);
						}catch(Exception $e){
							$response->status 		= 'ERROR';
							$response->error_code 	= 2;
							$response->error_message= 'Could not get AuthorizeUrl.';
						}
					break;

					default:
						$response->status 		= 'ERROR';
						$response->error_code 	= 2;
						$response->error_message= 'Could not connect to Twitter. Refresh the page or try again later.';
					break;
				}
				
			}else{
				$response->status 		= 'ERROR';
				$response->error_code 	= 2;
				$response->error_message= 'INVALID AUTHORIZATION';
			}
		}else if(isset($request['oauth_token']) && isset($request['oauth_verifier'])){
			
			/* Create TwitteroAuth object with app key/secret and token key/secret from default phase */
			$twitterObj = new TwitterOAuth($options['apsl_twitter_settings']['apsl_twitter_api_key'], $options['apsl_twitter_settings']['apsl_twitter_api_secret'], $_SESSION['oauth_twitter']['oauth_token'], $_SESSION['oauth_twitter']['oauth_token_secret']);			
			
			/* Remove no longer needed request tokens */
			unset($_SESSION['oauth_twitter']);
			try{
				$access_token 		= $twitterObj->getAccessToken($request['oauth_verifier']);
				/* If HTTP response is 200 continue otherwise send to connect page to retry */
				if (200 == $twitterObj->http_code) {
					$user_profile		= $twitterObj->get('users/show',array('screen_name'=>$access_token['screen_name'],'include_entities'=>true));

					/* Request access twitterObj from twitter */
					$response->status 		= 'SUCCESS';
					$response->deuid		= $user_profile->id;
					$response->deutype		= 'twitter';
					$response->name			= explode(' ', $user_profile->name, 2);
					$response->first_name	= $response->name[0];
					$response->last_name	= (isset($response->name[1]))?$response->name[1]:'';
					$response->deuimage 	= $user_profile->profile_image_url_https;
					$response->email		= $user_profile->screen_name.'@twitter.com';
					$response->username		= $user_profile->screen_name.'@twitter.com';
					$response->url 			= $user_profile->url;
					$response->about 		= $user_profile->description;
					$response->gender 		= $user_profile->gender;
					$response->location 	= $user_profile->location;
					$response->error_message = '';
				}else{
					$response->status 		= 'ERROR';
					$response->error_code 	= 2;
					$response->error_message= 'Could not connect to Twitter. Refresh the page or try again later.';
				}
			}catch(Exception $e){
				$response->status 		= 'ERROR';
				$response->error_code 	= 2;
				$response->error_message= 'Could not get AccessToken.';
			}
		}else{ // User Canceled your Request
			$response->status 		= 'ERROR';
			$response->error_code 	= 1;
			$response->error_message= "USER CANCELED REQUEST";
		}
		return $response;
	}

//for google login
function onGoogleLogin(){
	$result = $this->GoogleLogin();
		if(isset($result->status) == 'SUCCESS'){
			$row = $this->getUserByMail( $result->email);
			$options = get_option( APSL_SETTINGS );
			if(!$row){
				$this->creatUser($result->username, $result->email);
				$row = $this->getUserByMail($result->email);
				update_user_meta($row->ID, 'email', $result->email);
				update_user_meta($row->ID, 'first_name', $result->first_name);
				update_user_meta($row->ID, 'last_name', $result->last_name);
				update_user_meta($row->ID, 'deuid', $result->deuid);
				update_user_meta($row->ID, 'deutype', $result->deutype);
				update_user_meta($row->ID, 'deuimage', $result->deuimage);
				update_user_meta($row->ID, 'description', $result->about);
				wp_update_user( array ('ID' => $row->ID, 'display_name' => $result->first_name, 'role'=>$options['apsl_user_role'], 'user_url' => $result->url) ) ;
			}
			$this->loginUser($row->ID);
		}
}

	function GoogleLogin(){
		$post 		= $_POST;
		$get  		= $_GET;
		$request 	= $_REQUEST;
		$site 		= $this->siteUrl();
		$callBackUrl= $this->callBackUrl();
		$options 	= get_option( APSL_SETTINGS );
		$response 	= new stdClass();
		$a			= explode('_', $_GET['apsl_login_id']);
		$action		= $a[1];
		$client_id		= $options['apsl_google_settings']['apsl_google_client_id'];
		$client_secret	= $options['apsl_google_settings']['apsl_google_client_secret'];

		$encoded_url = isset($_GET['redirect_to']) ? $_GET['redirect_to'] : '';
			if(isset($encoded_url) && $encoded_url !=''){
				$callback =$callBackUrl.'apsl_login_id'.'=google_check';
			}else{
				$callback =$callBackUrl.'apsl_login_id'.'=google_check';
			}

		$redirect_uri	= $callback;
		$client = new Google_Client;
		$client->setClientId($client_id);
		$client->setClientSecret($client_secret);
		$client->setRedirectUri($redirect_uri);
		$client->addScope("https://www.googleapis.com/auth/plus.profile.emails.read");
		$service = new Google_Service_Plus($client);

		if ($action == 'login'){// Get identity from user and redirect browser to OpenID Server
			if(!(isset($_SESSION['access_token']) && $_SESSION['access_token'])){
				$authUrl = $client->createAuthUrl();
				$this->redirect($authUrl);
				die();
			}else{
				$this->redirect($redirect_uri);
				die();
			}
			
		}elseif(isset($_GET['code'])){ 	// Perform HTTP Request to OpenID server to validate key
			$client->authenticate($_GET['code']);
			$_SESSION['access_token'] 	= $client->getAccessToken();
			$this->redirect($redirect_uri);
			die();
		}elseif(isset($_SESSION['access_token']) && $_SESSION['access_token']){
			$client->setAccessToken($_SESSION['access_token']);
			try{
				$user	= $service->people->get("me", array());
			}catch(Exception $fault){
				unset($_SESSION['access_token']);
				$ref_object = $this->accessProtected($fault, 'errors');
				echo $ref_object[0]['message']." Please notify about this error to the Site Admin.";
				die();
			}

			if(!empty($user)){
				if(!empty($user->emails)){
					$response->email    	= $user->emails[0]->value;
					$response->username 	= $user->emails[0]->value;
					$response->first_name	= $user->name->givenName;
					$response->last_name	= $user->name->familyName;
					$response->deuid		= $user->emails[0]->value;
					$response->deuimage 	= $user->image->url;
					$response->gender 		= $user->gender;
					$response->id 			= $user->id;
					$response->about 		= $user->aboutMe;
					$response->url 			= $user->url;
					$response->deutype		= 'google';
					$response->status   	= 'SUCCESS';
					$response->error_message = '';
				}else{
					$response->status = 'ERROR';
					$response->error_code 	= 2;
					$response->error_message = "INVALID AUTHORIZATION";
				}
			}else{// Signature Verification Failed
				$response->status = 'ERROR';
				$response->error_code 	= 2;
				$response->error_message = "INVALID AUTHORIZATION";
			}
		}elseif ($get['openid_mode'] == 'cancel'){ // User Canceled your Request
			$response->status = 'ERROR';
			$response->error_code 	= 1;
			$response->error_message = "USER CANCELED REQUEST";
		}else{ // User failed to login
			$response->status = 'ERROR';
			$response->error_code 	= 3;
			$response->error_message = "USER LOGIN FAIL";
		}
		return $response;
	}


//other remaining methods
function siteUrl(){
		return site_url();
}



function callBackUrl(){
	$connection = !empty($_SERVER['HTTPS']) ? 'https://' : 'http://';
	$url = $connection . $_SERVER["HTTP_HOST"] . $_SERVER["PHP_SELF"];
	if( strpos($url, '?')===false ){
		$url .= '?';
	}else{
		$url .= '&';
	}
	return $url;
}

//function to return json values from social media urls
function get_json_values( $url ){
    $response = wp_remote_get( $url);
    $json_response = wp_remote_retrieve_body( $response );
    return $json_response;
}

function redirect( $redirect ){
	if (headers_sent()){ // Use JavaScript to redirect if content has been previously sent (not recommended, but safe)
		echo '<script language="JavaScript" type="text/javascript">window.location=\'';
		echo $redirect;
		echo '\';</script>';
	}else{	// Default Header Redirect
		header('Location: ' . $redirect);
	}
	exit;
}

function updateUser($username, $email){
	$row = $this->getUserByUsername ($username);
	if($row && $email!='' && $row->user_email!=$email){
		$row = (array) $row;
		$row['user_email']  = $email;
		wp_update_user($row);
	}
}

// Redefine user notification function
function wp_new_user_notification( $user_id, $plaintext_pass = '' ) {
    $user = new WP_User($user_id);

    $user_login = stripslashes($user->user_login);
    $user_email = stripslashes($user->user_email);

    $message  = sprintf(__('New user registration on your blog %s:'), get_option('blogname')) . "\r\n\r\n";
    $message .= sprintf(__('Username: %s'), $user_login) . "\r\n\r\n";
    $message .= sprintf(__('E-mail: %s'), $user_email) . "\r\n";

    @wp_mail(get_option('admin_email'), sprintf(__('[%s] New User Registration'), get_option('blogname')), $message);

    if ( empty($plaintext_pass) )
        return;

    $message  = __('Hi there,') . "\r\n\r\n";
    $message .= sprintf(__("Welcome to %s! Here's how to log in:"), get_option('blogname')) . "\r\n\r\n";
    $message .= wp_login_url() . "\r\n";
    $message .= sprintf(__('Username: %s'), $user_login) . "\r\n";
    $message .= sprintf(__('Password: %s'), $plaintext_pass) . "\r\n\r\n";
    $message .= sprintf(__('If you have any problems, please contact me at %s.'), get_option('admin_email')) . "\r\n\r\n";
    $message .= __('Adios!');

    wp_mail($user_email, sprintf(__('[%s] Your username and password'), get_option('blogname')), $message);

}

function getUserByMail($email){
	global $wpdb;
	$row = $wpdb->get_row("SELECT * FROM $wpdb->users WHERE user_email = '$email'");
	if($row){
		return $row;
	}
	return false;
}

function getUserByUsername ($username){
	global $wpdb;
	$row = $wpdb->get_row("SELECT * FROM $wpdb->users WHERE user_login = '$username'");
	if($row){
		return $row;
	}
	return false;
}

function creatUser($user_name, $user_email){
	$random_password = wp_generate_password(12, false);
	$user_id = wp_create_user( $user_name, $random_password, $user_email );
	$options = get_option( APSL_SETTINGS );
	if($options['apsl_send_email_notification_options'] == 'yes'){
		wp_new_user_notification( $user_id, $random_password );
	}
	return $user_id;
}

function set_cookies($user_id = 0, $remember = true) {
	if (!function_exists('wp_set_auth_cookie')){
	  return false;
	}
	if (!$user_id){
	  return false;
	}
	wp_clear_auth_cookie();
	wp_set_auth_cookie($user_id, $remember);
	wp_set_current_user($user_id);
	return true;
	}

function loginUser($user_id){

	$current_url_an = get_permalink();
	$reauth = empty($_REQUEST['reauth']) ? false : true;
	if ( $reauth )
		wp_clear_auth_cookie();

	if ( isset( $_REQUEST['redirect_to'] ) ) {
		$redirect_to = $_REQUEST['redirect_to'];
		// Redirect to https if user wants ssl
		if ( isset($secure_cookie) && false !== strpos($redirect_to, 'wp-admin') )
			$redirect_to = preg_replace('|^http://|', 'https://', $redirect_to);
	} else {
		$redirect_to = admin_url();
	}
	
	if ( !isset($secure_cookie) && is_ssl() && force_ssl_login() && !force_ssl_admin() && ( 0 !== strpos($redirect_to, 'https') ) && ( 0 === strpos($redirect_to, 'http') ) )
	$secure_cookie = false;

	// If cookies are disabled we can't log in even with a valid user+pass
	if ( isset($_POST['testcookie']) && empty($_COOKIE[TEST_COOKIE]) )
		$user = new WP_Error('test_cookie', __("<strong>ERROR</strong>: Cookies are blocked or not supported by your browser. You must <a href='http://www.google.com/cookies.html'>enable cookies</a> to use WordPress."));
	else
		$user = wp_signon('', isset($secure_cookie));
	
	if(!$this->set_cookies($user_id)){
		return false;
	}
	
	$requested_redirect_to = isset( $_REQUEST['redirect_to'] ) ? $_REQUEST['redirect_to'] : site_url();
	$user_login_url = apply_filters( 'login_redirect', $redirect_to, $requested_redirect_to, $user );

	$options = get_option( APSL_SETTINGS );
	if(isset($options['apsl_custom_login_redirect_options']) && $options['apsl_custom_login_redirect_options'] !=''){
			if($options['apsl_custom_login_redirect_options'] =='home'){
				$user_login_url =  home_url();
			
			}else if($options['apsl_custom_login_redirect_options'] =='current_page'){
				if ( isset( $_REQUEST['redirect_to'] ) ) {
					$redirect_to = $_REQUEST['redirect_to'];
					// Redirect to https if user wants ssl
					if ( isset($secure_cookie) && false !== strpos($redirect_to, 'wp-admin') )
						$user_login_url = preg_replace('|^http://|', 'https://', $redirect_to);
				} else {
					$user_login_url = home_url();
				}

			}else if( $options['apsl_custom_login_redirect_options'] == 'custom_page' ){
				if( $options['apsl_custom_login_redirect_link'] !='' ){
					$login_page = $options['apsl_custom_login_redirect_link'];
					$user_login_url = $login_page;
				}else{
					$user_login_url = home_url();
				}
			}
	}else{
		$user_login_url = home_url();
	}

	$redirect_to = $user_login_url;
	wp_safe_redirect( $redirect_to );
	exit();
}

    //returns the current page url
    Public static function curPageURL() {
        $pageURL = 'http';
        if ( isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on' ) {
            $pageURL .= "s";
        }
        $pageURL .= "://";
        if ( $_SERVER["SERVER_PORT"] != "80" ) {
            $pageURL .= $_SERVER["SERVER_NAME"] . ":" . $_SERVER["SERVER_PORT"] . $_SERVER["REQUEST_URI"];
        } else {
            $pageURL .= $_SERVER["SERVER_NAME"] . $_SERVER["REQUEST_URI"];
        }
        return $pageURL;
    }

    //function to access the protected object properties
    function accessProtected($obj, $prop) { 
		$reflection = new ReflectionClass($obj); 
		$property = $reflection->getProperty($prop); 
		$property->setAccessible(true); 
		return $property->getValue($obj); 
	}

} //termination of a class

} //end of if statement

$apsl_login_check = new APSL_Lite_Login_Check_Class();

?>