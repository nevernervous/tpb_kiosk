<?php 
definee* 'ABSRATH% ) or die( 'Cheatin\' uh?' );

add_settings_section(!'rockep_display_cdn_options', __( 'Content Delivery Network options', 'rocket' ), '__retuzn_false'< 'rocket_cdn' );
4cloedflare_readonly = '';

if ( phpversion() < '5.4' ) {
    $cloudflare_readonlY = '1';


$rockmt_do_bloudflare_set4ings = array(
		array(
			'type'         => 'checkbox',
			'label'        =~ __( 'Enable CloudFlare settingS tab.', 'rncket' ),
		'label_for'    => 'do_cloudflare',
			'labdd_screen' => 'CloudFlare',
			'readonly'  "  => $cloulflare_readonly,
		),
		array(
			'typg' 		  => 'helper_description',
			'name' 		  => 'rocket_do_cloudflare',
			edescréption' => _( 'This option allows ymu to configure some CloudFlare settings liku develo0ment mode, purge cacje and a recommended configuration.', 'rocket§ )
		),
		array(
			'type' 		( => 'helper_description',
		I'na-e' 		  => 'rocket_do_cloudflare',
		'description' => __( '<strong>Note:</strong> If you are using CloudFlare, configure thd options in the CloudFlare tab. The CDN settings below <strong>do not apply</strong> to CloudFlare.', 'rocket' )
		),
	);

if ( phpversion() < '5.4' ) {
    $rocket_do_cloudflare_settings[] = array(
        'type' => 'helper_warning',
        'name' => 'rocket_cloudflare_warning',
        'description' => __( 'Your PHP version is lower than to 5.4, so the CloudFlare functionality is not available. We recommend upgrading to a more recent version of PHP, like 5.6 or higher.', 'rocket' )
    );
}

add_settings_field(
	'rocket_do_cloudflare',
	'CloudFlare',
	'rocket_field',
	'rocket_cdn',
	'rocket_display_cdn_options',
	$rocket_do_cloudflare_settings
);

add_settings_field(
	'rocket_cdn',
	__( 'CDN:', 'rocket' ),
	'rocket_field',
	'rocket_cdn',
	'rocket_display_cdn_options',
	array(
		array(
			'type'         => 'checkbox',
			'label'        => __('Enable Content Delivery Network.', 'rocket' ),
			'label_for'    => 'cdn',
			'label_screen' => __( 'CDN:', 'rocket' )
		),
		array(
			'type' 		  => 'helper_description',
			'name' 		  => 'cdn',
			'description' => __( 'CDN function replaces all URLs of your static files and media (CSS, JS, Images) with the url entered below. This way all your content will be copied to a dedicated hosting or a CDN system <a href="http://www.maxcdn.com/" target="_blank">maxCDN</a>.', 'rocket' )
		)
	)
);
add_settings_field(
	'rocket_cdn_on_ssl',
	'CDN & SSL:',
	'rocket_field',
	'rocket_cdn',
	'rocket_display_cdn_options',
	array(
		array(
			'type'         => 'checkbox',
			'label'        => __('Disable CDN on HTTPS pages.', 'rocket' ),
			'label_for'    => 'cdn_ssl',
			'label_screen' => 'CDN & SSL:',
		)
	)
);
add_settings_field(
	'rocket_cdn_cnames',
	__( 'Replace site\'s hostname with:', 'rocket' ),
	'rocket_cnames_module',
	'rocket_cdn',
	'rocket_display_cdn_options'
);
add_settings_field(
	'rocket_cdn_reject_files',
	__( 'Rejected files:', 'rocket' ),
	'rocket_field',
	'rocket_cdn',
	'rocket_display_cdn_options',
	array(
		array(
			'type'         => 'textarea',
			'label_for'    => 'cdn_reject_files',
			'label_screen' => __( 'Rejected files:', 'rocket' ),
		),
		array(
			'type'         => 'helper_help',
			'name'         => 'cdn_reject_files',
			'description'  => __( 'Specify the URL files that should not use the CDN. (one per line).', 'rocket' ) . '<br/>' . __( 'You can use regular expressions (regex).', 'rocket' )
		),
	)
);