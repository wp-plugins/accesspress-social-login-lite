<?php defined('ABSPATH') or die("No script kiddies please!"); ?>
<?php
$options = get_option(APSL_SETTINGS);

$redirect_to = isset($_REQUEST['redirect_to']) ? $_REQUEST['redirect_to'] : '';

$encoded_url = urlencode($redirect_to);
?>
<div class='apsl-login-networks theme-<?php echo $options['apsl_icon_theme']; ?> clearfix'>
    <span class='apsl-login-new-text'><?php echo $options['apsl_title_text_field']; ?></span>
    <?php if (isset($_REQUEST['error']) || isset($_REQUEST['denied'])) { ?>
        <div class='apsl-error'>
            <?php _e('You have Access Denied. Please authorize the app to login.', APSL_TEXT_DOMAIN); ?>
        </div>
    <?php } ?>

    <div class='social-networks'>
        <?php foreach ($options['network_ordering'] as $key => $value): ?>
            <?php if ($options["apsl_{$value}_settings"]["apsl_{$value}_enable"] === 'enable') { ?>
                <a href="<?php wp_login_url() ?>?apsl_login_id=<?php echo $value; ?>_login<?php
                if ($encoded_url) {
                    echo "&state=" . base64_encode("redirect_to=$encoded_url");
                }
                ?>" title='<?php _e('Login with', APSL_TEXT_DOMAIN);
           echo ' ' . $value;
           ?>' >
                    <div class="apsl-icon-block icon-<?php echo $value; ?> clearfix">
                        <i class="fa fa-<?php echo $value; ?>"></i>
                        <span class="apsl-login-text"><?php _e('Login', APSL_TEXT_DOMAIN); ?></span>
                        <span class="apsl-long-login-text"><?php _e('Login with', APSL_TEXT_DOMAIN); ?><?php echo ' ' . $value; ?></span>
                    </div>
                </a>
    <?php } ?>
<?php endforeach; ?>
    </div>
</div>