<?php
class Facebook extends loginBySocialID{
	static $loginBy = 'deLoginByFacebook';

function loginByOpenID(){
		$post 		= $_POST;
		$get  		= $_GET;
		$request 	= $_REQUEST;
		$site 		= $this->siteUrl();
		$callBackUrl= $this->callBackUrl();
		$response 	= new stdClass();
		$a			= explode('_',$this->get_var(parent::$loginKey));
		$action		= $a[1];
		$options 	= $this->getOptions();
		$config = array(
			  'appId' => $options['facebook_key'],
			  'secret' => $options['facebook_secret']
		  );

		$facebook = new Facebook($config);
		if ($action == 'login'){
				$loginUrl = $facebook->getLoginUrl(array('redirect_uri'=>$callBackUrl.parent::$loginKey.'='.self::$loginBy.'_check', 'scope'=>'email'));
			$this->redirect($loginUrl);
			exit(); 
		}else{
			echo "jay";
			die();
			$user = $facebook->getUser();
			if ($user){
			  	try {// Proceed knowing you have a logged in user who's authenticated.
					$user_profile = $facebook->api('/me');
			  	} catch (FacebookApiException $e) {
					error_log($e);
					$user = null;
			  	}
			}
			if($user!=null){
				$response->status 		= 'SUCCESS';
				$response->deuid		= $user_profile['id'];
				$response->deutype		= 'facebook';
				$response->first_name	= $user_profile['first_name'];
				$response->last_name	= $user_profile['last_name'];
				$response->email		= $user_profile['email'];
				$response->username		= $user_profile['email'];
				////['id'].'@facebook';
				$headers = get_headers('https://graph.facebook.com/'.$user_profile['id'].'/picture',1);
				// just a precaution, check whether the header isset...
				if(isset($headers['Location'])) {
					$response->deuimage = $headers['Location']; // string
				} else {
					$response->deuimage = false; // nothing there? .. weird, but okay!
				}
				$response->error_message = '';
			}else{
				$response->status 		= 'ERROR';
				$response->error_code 	= 2;
				$response->error_message= 'INVALID AUTHORIZATION';
			}
		}
		return $response;
	}

	function onLogin(){
		$response = new stdClass();
		$result = $this->loginByOpenID($response);
		if($result->status == 'SUCCESS'){
			$row = $this->getUserByMail( $result->email);
			if(!$row){
				$this->creatUser($result->username, $result->email);
				$row = $this->getUserByMail( $result->email);
				update_user_meta($row->ID, 'email', $result->email);
				update_user_meta($row->ID, 'first_name', $result->first_name);
				update_user_meta($row->ID, 'last_name', $result->last_name);
				update_user_meta($row->ID, 'deuid', $result->deuid);
				update_user_meta($row->ID, 'deutype', $result->deutype);
				update_user_meta($row->ID, 'deuimage', $result->deuimage);
				wp_update_user( array ('ID' => $row->ID, 'display_name' => $result->first_name.' '.$result->last_name) ) ;
			}
			//var_dump($row);
			$this->loginUser($row->ID);			
		}
	}
}

?>