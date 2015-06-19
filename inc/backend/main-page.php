<div class="wrap"><div class='apsl-outer-wrapper'>

<div class="apsl-setting-header clearfix">

        <div class="apsl-headerlogo">
         <div class="logo-wrap">  <img src="<?php echo APSL_IMAGE_DIR; ?>/logo.png" alt="<?php esc_attr_e('AccessPress Social Login Lite', APSL_TEXT_DOMAIN ); ?>" /></div>
        	<div class="logo-content"><?php esc_attr_e('AccessPress Social Login Lite', APSL_TEXT_DOMAIN ); ?><br />
        	<span class='plugin-version'><?php _e('version '.APSL_VERSION, APSL_TEXT_DOMAIN ); ?></span></div>
        </div>

        <div class="apsl-right-header-block">
        <div class="apsl-header-icons">
            <p>Follow us for new updates</p>
            <div class="apsl-social-bttns">
                <iframe src="//www.facebook.com/plugins/like.php?href=https%3A%2F%2Fwww.facebook.com%2Fpages%2FAccessPress-Themes%2F1396595907277967&amp;width&amp;layout=button&amp;action=like&amp;show_faces=false&amp;share=false&amp;height=35&amp;appId=1411139805828592" scrolling="no" frameborder="0" style="border:none; overflow:hidden; height:20px; width:50px " allowtransparency="true"></iframe>
                &nbsp;&nbsp;
                <iframe id="twitter-widget-0" scrolling="no" frameborder="0" allowtransparency="true" src="http://platform.twitter.com/widgets/follow_button.5f46501ecfda1c3e1c05dd3e24875611.en.html#_=1421918256492&amp;dnt=true&amp;id=twitter-widget-0&amp;lang=en&amp;screen_name=apthemes&amp;show_count=false&amp;show_screen_name=true&amp;size=m" class="twitter-follow-button twitter-follow-button" title="Twitter Follow Button" data-twttr-rendered="true" style="width: 126px; height: 20px;"></iframe>
                <script>
                !function (d, s, id) {
                        var js, fjs = d.getElementsByTagName(s)[0];
                        if (!d.getElementById(id)) {
                            js = d.createElement(s);
                            js.id = id;
                            js.src = "//platform.twitter.com/widgets.js";
                            fjs.parentNode.insertBefore(js, fjs);
                        }
                    }(document, "script", "twitter-wjs");
                </script>

            </div>
        </div>
        
    </div>
    <div class="clear"></div>
    </div>
    <div class="clear"></div>

<?php $options = get_option( APSL_SETTINGS );
 //$this->print_array($options);
?>

<?php

if(isset($_SESSION['apsl_message'])){ ?>
<div class="apsl-message">
<p><?php 
	echo $_SESSION['apsl_message'];
	unset($_SESSION['apsl_message']); 
	?></p>
</div>
<?php } ?>
	<div class='apsl-networks'>
		<div class='apsl-network-options'>
			<form method="post" action="<?php echo admin_url() . 'admin-post.php' ?>">
			<input type="hidden" name="action" value="apsl_save_options"/>
			<div class='apsl-settings-tabs-wrapper clearfix'>
				<ul class='apsl-tab-wrapper-fix clearfix'>
					<li><a href='javascript: void(0);' id='apsl-networks-settings' class='apsl-settings-tab apsl-active-tab' ><?php _e('Network settings', APSL_TEXT_DOMAIN ) ?></a></li>
					<li><a href='javascript: void(0);' id='apsl-theme-settings' class='apsl-settings-tab' ><?php _e('Other settings', APSL_TEXT_DOMAIN ) ?></a></li>
					<li><a href='javascript: void(0);' id='apsl-how-to-use' class='apsl-settings-tab' ><?php _e('How to use', APSL_TEXT_DOMAIN ) ?></a></li>
					<li><a href='javascript: void(0);' id='apsl-about' class='apsl-settings-tab' ><?php _e('About', APSL_TEXT_DOMAIN ) ?></a></li>
				</ul>
			</div>
			<div class="clear"></div>
			<div class='apsl-setting-tabs-wrapper'>
				<div class='apsl-tab-contents' id='tab-apsl-networks-settings'>
					
					<div class='network-settings'>
						<?php foreach($options['network_ordering'] as $key=>$value):  ?>
							<?php switch($value){
								 case 'facebook': ?>
								<div class='apsl-settings apsl-facebook-settings'>
								<!-- Facebook Settings -->
									<div class='apsl-label'><?php _e( "Facebook", APSL_TEXT_DOMAIN ); ?><span class='apsl_show_hide' id='apsl_show_hide_<?php echo $value; ?>'><i class="fa fa-caret-down"></i></span> </div>
									<div class='apsl_network_settings_wrapper' id='apsl_network_settings_<?php echo $value; ?>' style='display:none'>
										<div class='apsl-enable-disable'>
										<label><?php _e('Enable?', APSL_TEXT_DOMAIN ); ?></label>
										<input type='hidden' name='network_ordering[]' value='facebook' />
										<input type="checkbox" id='aspl-facbook-enable' value='enable' name='apsl_facebook_settings[apsl_facebook_enable]' <?php checked( 'enable', $options['apsl_facebook_settings']['apsl_facebook_enable'] ); ?>  />
										</div>
										<div class='apsl-app-id-wrapper'>
										<label><?php _e( 'App ID:', APSL_TEXT_DOMAIN ); ?></label><input type='text' id='apsl-facebook-app-id' name='apsl_facebook_settings[apsl_facebook_app_id]' value='<?php if(isset($options['apsl_facebook_settings']['apsl_facebook_app_id'])){ echo $options['apsl_facebook_settings']['apsl_facebook_app_id']; } ?>' />
										</div>
										<div class='apsl-app-secret-wrapper'>
										<label><?php _e( 'App Secret:', APSL_TEXT_DOMAIN ); ?></label><input type='text' id='apsl-facebook-app-secret' name='apsl_facebook_settings[apsl_facebook_app_secret]' value='<?php if(isset($options['apsl_facebook_settings']['apsl_facebook_app_secret'])){ echo $options['apsl_facebook_settings']['apsl_facebook_app_secret']; } ?>' />
										</div>
										<div class='apsl-info'>
											<span class='apsl-info-note'><?php _e('Note:', APSL_TEXT_DOMAIN ); ?></span> <br />
											<span class='apsl-info-content'>You need to create a new facebook API Applitation to setup facebook login. Please follow the instructions to create new app.</span>
											<br />
											<ul class='apsl-info-lists'>
												<li>Go to <a href='https://developers.facebook.com/apps' target='_blank'>https://developers.facebook.com/apps</a>.</li>
												<li>Click on 'Add a New App' button. A popup will open. Then choose website.</li>
												<li>Add the required informations and don't forget to make your app live. This is very important otherwise your app will not work for all users.</li>
												<li>Then Click the "Create App" button and follow the instructions, your new app will be created. </li>
												<li>Copy and Paste "App ID" and "App Secret" here.</li>
												<li>Site url: <?php echo site_url(); ?></li>
											</ul>
										</div>
									</div>
								</div>
								<?php break; ?>
								
								<?php case 'twitter': ?>
								 <div class='apsl-settings apsl-twitter-settings'>
								 <!-- Twitter Settings -->
								 	<div class='apsl-label'><?php _e( "Twitter", APSL_TEXT_DOMAIN ); ?> <span class='apsl_show_hide' id='apsl_show_hide_<?php echo $value; ?>'><i class="fa fa-caret-down"></i></span> </div>
								 	<div class='apsl_network_settings_wrapper' id='apsl_network_settings_<?php echo $value; ?>' style='display:none'>
									 	<div class='apsl-enable-disable'> 
									 	<label><?php _e('Enable?', APSL_TEXT_DOMAIN ); ?></label>
									 		<input type="checkbox" id='aspl-twitter-enable' value='enable' name='apsl_twitter_settings[apsl_twitter_enable]' <?php checked( 'enable', $options['apsl_twitter_settings']['apsl_twitter_enable'] ); ?>  />
									 	</div>

									 	<div class='apsl-app-id-wrapper'>
									 	<label><?php _e( 'Consumer Key (API Key):', APSL_TEXT_DOMAIN ); ?></label><input type='text' id='apsl-twitter-app-id' name='apsl_twitter_settings[apsl_twitter_api_key]' value='<?php if(isset($options['apsl_twitter_settings']['apsl_twitter_api_key'])){ echo $options['apsl_twitter_settings']['apsl_twitter_api_key']; } ?>' />
									 	</div>

									 	<div class='apsl-app-secret-wrapper'>
									 	<label><?php _e( 'Consumer Secret (API Secret):', APSL_TEXT_DOMAIN ); ?></label><input type='text' id='apsl-twitter-app-secret' name='apsl_twitter_settings[apsl_twitter_api_secret]' value='<?php if(isset($options['apsl_twitter_settings']['apsl_twitter_api_secret'])){ echo $options['apsl_twitter_settings']['apsl_twitter_api_secret']; } ?>' />
									 	</div>

									 	<input type='hidden' name='network_ordering[]' value='twitter' />
									 	<div class='apsl-info'>
									 		<span class='apsl-info-note'><?php _e('Note:', APSL_TEXT_DOMAIN ); ?> <br /> </span>
									 		<span class='apsl-info-content'>You need to create new twitter API application to setup the twitter login. Please follow the instructions to create new app.</span>
									 		<ul class='apsl-info-lists'>
										 		<li>Go to <a href='https://apps.twitter.com/' target='_blank'>https://apps.twitter.com/</a></li>
										 		<li>Click on Create New App button. A new application details form will appear. Please fill up the application details and click on "create your twitter application" button.</li>
										 		<li>Please note that before creating twiiter API application, You must add your mobile phone to your Twitter profile.</li>
										 		<li>After successful creation of the app. Please go to "Keys and Access Tokens" tabs and get Consumer key(API Key) and Consumer secret(API secret).</li>
										 		<li>Website: <?php echo site_url(); ?></li>
										 		<li>Callback URL: <?php echo site_url(); ?></li>
									 		</ul>

									 	</div>
								  	</div>	
								 </div>
								 <?php break;

								 case 'google': ?>
								 <div class='apsl-settings apsl-google-settings'>
								 <!-- Google Settings -->
								 	<div class='apsl-label'><?php _e( "Google", APSL_TEXT_DOMAIN ); ?> <span class='apsl_show_hide' id='apsl_show_hide_<?php echo $value; ?>'><i class="fa fa-caret-down"></i></span> </div>
								 	<div class='apsl_network_settings_wrapper' id='apsl_network_settings_<?php echo $value; ?>' style='display:none'>
									 	<div class='apsl-enable-disable'> 
									 	<label><?php _e('Enable?', APSL_TEXT_DOMAIN ); ?></label>
									 		<input type="checkbox" id='aspl-google-enable' value='enable' name='apsl_google_settings[apsl_google_enable]' <?php checked( 'enable', $options['apsl_google_settings']['apsl_google_enable'] ); ?>  />
									 	</div>
									 	<div class='apsl-app-id-wrapper'>
									 		<label><?php _e( 'Client ID:', APSL_TEXT_DOMAIN ); ?></label><input type='text' id='apsl-google-client-id' name='apsl_google_settings[apsl_google_client_id]' value='<?php if(isset($options['apsl_google_settings']['apsl_google_client_id'])){ echo $options['apsl_google_settings']['apsl_google_client_id']; } ?>' />
									 	</div>
									 	<div class='apsl-app-secret-wrapper'>
									 		<label><?php _e( 'Client Secret:', APSL_TEXT_DOMAIN ); ?></label><input type='text' id='apsl-google-client-secret' name='apsl_google_settings[apsl_google_client_secret]' value='<?php if(isset($options['apsl_google_settings']['apsl_google_client_secret'])){ echo $options['apsl_google_settings']['apsl_google_client_secret']; } ?>' />
									 	</div>
									 	<input type='hidden' name='network_ordering[]' value='google' />
									 	<div class='apsl-info'>
									 		<span class='apsl-info-note'><?php _e('Note:', APSL_TEXT_DOMAIN ); ?></span> <br />
									 		<span class='apsl-info-content'>You need to create new google API application to setup the google login. Please follow the instructions to create new application.</span>
									 		<ul class='apsl-info-lists'>
										 		<li>Go to <a href='https://console.developers.google.com/project' target='_blank'>https://console.developers.google.com/project.</a> </li>
										 		<li>Click on "Create Project" button.</li>
										 		<li>A pop up will appear.</li>
										 		<li>Please enter Project name and click on "Create" button.</li>
										 		<li>Now click on APIs & Auth > Consent screen and fill up the required credentials.</li>
										 		<li>Now click on APIs & Auth > Credentials.</li>
										 		<li>Now click on "Create new Client ID" button and fill up the required credentials.</li>
										 		<li>Get the client ID and client secret.</li>
										 		<li>Now go to APIs > Social APIs > Google+ API and enable it. It is very important to enable it for the google login to work.</li>
										 		<li>Rediret uri setup:<br />
										 			Please use <?php echo site_url(); ?>/wp-login.php?apsl_login_id=google_check - for wordpress login page.<br />
										 		<?php echo site_url(); ?>/index.php?apsl_login_id=google_check - if you have used the shortcode or widget in frontend.
										 		</li>
									 		</ul>
									 	</div>
								 	</div>	
								 </div>
								 <?php break; ?>

								 <?php default:
								 echo "should not reach here";
								 break;

								} ?> 
						<?php endforeach; ?>
					</div>	
				</div>

				<div class='apsl-tab-contents' id='tab-apsl-theme-settings' style="display:none">
					
						<div class='apsl-settings'>
							<div class='apsl-enable-disable-opt'>
								<div class="apsl-label"><?php _e('Social Login', APSL_TEXT_DOMAIN ); ?> <span class='apsl_show_hide'><i class="fa fa-caret-down"></i></span> </div>
								<div class='apsl_network_settings_wrapper' style='display:none'>
									<p class="social-login">
									<span><?php _e('Enable social login?', APSL_TEXT_DOMAIN ); ?></span>
			        				<input type='radio' id='apsl_enable_plugin' name='apsl_enable_disable_plugin' value='yes' <?php checked( $options['apsl_enable_disable_plugin'], 'yes', 'true' ); ?> /> <label for='apsl_enable_plugin'>Yes</label>
			        				<input type='radio' id='apsl_disable_plugin' name='apsl_enable_disable_plugin' value='no' <?php checked( $options['apsl_enable_disable_plugin'], 'no', 'true' ); ?> /> <label for='apsl_disable_plugin'>No</label>
			        				</p>
		        				</div>
	        				</div>
        				</div>

        				<div class='apsl-settings'>
							<div class='apsl-display-options'>
								<div class="apsl-label"><?php _e('Display options', APSL_TEXT_DOMAIN ); ?> <span class='apsl_show_hide'><i class="fa fa-caret-down"></i></span></div>
								<div class='apsl_network_settings_wrapper' style='display:none'>
									<p><?php _e('Please choose the options where you want to display the social login form.', APSL_TEXT_DOMAIN ); ?></p>
									<p><input type="checkbox" id="apsl_login_form" value="login_form" name="apsl_display_options[]" <?php if (in_array("login_form", $options['apsl_display_options'])) { echo "checked='checked'"; } ?> ><label for="apsl_login_form"><?php _e( 'Login Form', APSL_TEXT_DOMAIN ); ?> </label></p>
									<p><input type="checkbox" id="apsl_register_form" value="register_form" name="apsl_display_options[]" <?php if (in_array("register_form", $options['apsl_display_options'])) { echo "checked='checked'"; } ?> ><label for="apsl_register_form"><?php _e( 'Register Form', APSL_TEXT_DOMAIN ); ?> </label></p>
									<p><input type="checkbox" id="apsl_comment_form" value="comment_form" name="apsl_display_options[]" <?php if (in_array("comment_form", $options['apsl_display_options'])) { echo "checked='checked'"; } ?> ><label for="apsl_comment_form"><?php _e( 'Comments', APSL_TEXT_DOMAIN ); ?> </label></p>
								</div>	
							</div>
						</div>

					<div class='apsl-settings'>
						<div class='apsl-themes-wrapper'>
							<div class="apsl-label"><?php _e('Available icon themes', APSL_TEXT_DOMAIN ); ?> <span class='apsl_show_hide'><i class="fa fa-caret-down"></i></span> </div>
							<div class='apsl_network_settings_wrapper' style='display:none'>
								<?php for($i=1; $i<=4; $i++): ?>
									<div class='apsl-theme apsl-theme-<?php echo $i; ?>'>
										<label><input type="radio" id="apsl-theme-<?php echo $i; ?>" value="<?php echo $i; ?>" class="apsl-theme apsl-png-theme" name="apsl_icon_theme" <?php checked( $i, $options['apsl_icon_theme'] ); ?> >
										<span><?php _e('Theme '.$i, APSL_TEXT_DOMAIN ); ?></span></label>
										<div class="apsl-theme-previewbox">
				                            <img src="<?php echo APSL_IMAGE_DIR; ?>/preview-<?php echo $i; ?>.jpg" alt="theme preview">
				                        </div>
		                        	</div>
		                    <?php endfor; ?>
		                    </div>
						</div>
					</div>

					<div class='apsl-settings'>
						<div class='apsl-text-settings'>
							<div class="apsl-label"><?php _e('Text Settings', APSL_TEXT_DOMAIN ); ?> <span class='apsl_show_hide'><i class="fa fa-caret-down"></i></span> </div>
								<div class='apsl_network_settings_wrapper' style='display:none'>
									<p class='apsl-title-text-field'>
										<span><?php _e('Login text:', APSL_TEXT_DOMAIN ); ?></span> <input type='text' name='apsl_title_text_field' id='apsl-title-text' value='<?php if(isset($options['apsl_title_text_field']) && $options['apsl_title_text_field'] !=''){ echo $options['apsl_title_text_field']; } ?>' />
									</p>
								</div>	
						</div>
					</div>

				</div>


				<!-- how to use section -->
				<div class='apsl-tab-contents' id='tab-apsl-how-to-use' style="display:none">
					<?php include(APSL_PLUGIN_DIR.'inc/backend/how-to-use.php'); ?>
				</div>

				<!-- about section -->
				<div class='apsl-tab-contents' id='tab-apsl-about' style="display:none">
					<?php include(APSL_PLUGIN_DIR.'inc/backend/about.php'); ?>
				</div>
				
				<!-- Save settings Button -->
				<div class='apsl-save-settings'>
					<?php wp_nonce_field( 'apsl_nonce_save_settings', 'apsl_settings_action' ); ?>
					<input type='submit' class='apsl-submit-settings primary-button' name='apsl_save_settings' value='<?php _e('Save settings', APSL_TEXT_DOMAIN ); ?>' />
				</div>

				<div class='apsl-restore-settings'>
	 				<?php $nonce = wp_create_nonce( 'apsl-restore-default-settings-nonce' ); ?>
         			<a href="<?php echo admin_url().'admin-post.php?action=apsl_restore_default_settings&_wpnonce='.$nonce;?>" onclick="return confirm('<?php _e( 'Are you sure you want to restore default settings?',APSL_TEXT_DOMAIN ); ?>')"><input type="button" value="Restore Default Settings" class="apsl-reset-button button primary-button"/></a>
				</div>
					
			</div>
		</div>
	</div>
</div>
</div>