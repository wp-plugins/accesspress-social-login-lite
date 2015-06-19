<?php $options = get_option( APSL_SETTINGS ); ?>

<div class='apsl-login-networks theme-<?php echo $options['apsl_icon_theme']; ?> clearfix'>
	<span class='apsl-login-new-text'><?php echo $options['apsl_title_text_field']; ?></span>	
	<?php if( isset($_REQUEST['error']) || isset($_REQUEST['denied']) ){ ?> 
	<div class='apsl-error'>
		<?php _e('You have Access Denied. Please authorize the app to login.', APSL_TEXT_DOMAIN ); ?>
	</div>
	<?php } ?>

	<div class='social-networks'>
	<?php foreach($options['network_ordering'] as $key=>$value): ?>
	<?php	if($options["apsl_{$value}_settings"]["apsl_{$value}_enable"]==='enable'){ ?>
	 <a href="?apsl_login_id=<?php echo $value; ?>_login" title='<?php _e('Login with', APSL_TEXT_DOMAIN ); echo ' '.$value; ?>' >
	 <div class="apsl-icon-block icon-<?php echo $value; ?> clearfix">
		<i class="fa fa-<?php echo $value; ?>"></i>
	 </div>
	 </a>
		<?php } ?>
	<?php endforeach; ?>
	</div>
</div>
