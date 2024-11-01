<?php

/*
Plugin Name: Suppa Menu (Free)
Plugin URI: http://codetemp.com
Description: Enhanced WordPress Menus, Responsive, Full Customizable . Please read the <a href="#">Guide</a>
Version: 1.0.0
Author: Sabri Taieb 
Author URI: http://codetemp.com
License: You should have purchased a license from http://codecanyon.net/
Copyright 2013  Sabri Taieb , Codetemp http://codetemp.com
*/

/** Defined **/
$suppa_settings = array
				(
					// Plugin Settings
					'plugin_id'			=> 'CTF_suppa_menu', // Don't ever ever ever change it
					'version'			=> '1.0.0',
					'guide'				=> 'http://cloud.codetemp.com/guides/suppa_menu',
					'support_forum'		=> 'http://support.codetemp.com/',
					'debug'				=> false, // disable this ( false ) , will boost up the menu
					'image_resize'		=> false, // disable this ( false ) , will boost up the menu
					'css_js_to_files'	=> true, // disable this ( false ) , will boost up the menu
					'plugin_url'		=> plugins_url( '' , __FILE__ ) . '/' ,
					'icon_url'			=> plugins_url( '' , __FILE__ ) . '/standard/img/icon.png' ,
					
					// Add Menu Page , Submenu Page Settins
					'menu_type'			=> 'menu_page',				// menu_page or submenu_page
					'page_title'		=> 'Suppa Menu Settings' ,	// page_title
					'menu_title'		=> 'Suppa Menu' ,		// menu_title
					'capability'		=> 'manage_options'	,		// capability

					// Framework Settings
					'framework_version'	=> '2.0.0',

					// Database Settings
					'groups'			=> array('style','settings') // Don't ever ever ever change it
				);

/** Files Required **/
require_once("core/class-all_fonts.php");
require_once('core/class-get_categories.php');
require_once('core/ctf_options.class.php');
require_once('core/ctf_setup.class.php');
require_once('core/array-fontAwesome.php');
require_once('standard/include/class-suppa_walkers.php');

/** Create [PLUGIN_NAME] CLASS **/
class codetemp_suppa_menu extends ctf_setup {

	/** Variables **/
	public $project_settings;

	public function __construct( $project_settings = array() )
	{	
		$this->project_settings = $project_settings;

		/** Localisation ( Translation ) **/
		add_action('init', array( $this , 'translation_action') );

		/** This must be first , generate plugin_id **/
		parent::__construct();

		/** -------------------------------------------------------------------------------- **/

		/** Add Support For WP 3+ Menus **/
		if( isset( $this->groups_db_offline['settings']['settings-theme_implement'] ) && $this->groups_db_offline['settings']['settings-theme_implement'] == 'on' )
		{
			register_nav_menus( array(
				'suppa_menu_location' => 'Suppa Menu Location'
			) );
		}

		/** -------------------------------------------------------------------------------- **/

		/** Start Mega Menu walkers **/
		$thumbnail_wdith = '300px';
		$thumbnail_height = '200px';
		if( isset($this->groups_db_offline['style']['submenu-posts-post_width'], $this->groups_db_offline['style']['submenu-posts-post_height']) )
		{
			$thumbnail_wdith 	= $this->groups_db_offline['style']['submenu-posts-post_width'];
			$thumbnail_height 	= $this->groups_db_offline['style']['submenu-posts-post_height'];
		}
		new suppa_walkers( $thumbnail_wdith, $thumbnail_height, $this->project_settings['image_resize'] );
		/** Swith To Suppa Walker Axtion **/
		add_action( 'wp_ajax_suppa_switch_menu_walker' , array( $this , 'switch_menu_walker' ) );

		/** -------------------------------------------------------------------------------- **/

		/** Admin : Load CSS & JS **/
		add_action( 'admin_enqueue_scripts' , array( $this , 'backend_css_js' ) );

		/** -------------------------------------------------------------------------------- **/

		/** Front-End : Load CSS & JS **/
		add_action( 'wp_enqueue_scripts' , array( $this , 'frontend_head_style' ) , 100 );

		/** Front-End : Load CSS & JS **/
		add_action( 'wp_enqueue_scripts' , array( $this , 'frontend_footer_scripts' ) , 400 );

		/** Front-End : Display **/		

		/** -------------------------------------------------------------------------------- **/

		/** Import Data **/
		$this->database_import();
	}


	public function translation_action()
	{
		load_plugin_textdomain('suppa_menu', false, basename( dirname( __FILE__ ) ) . '/languages' );
	}


	public function switch_menu_walker()
	{
		if ( ! current_user_can( 'edit_theme_options' ) )
		die('-1');

		check_ajax_referer( 'add-menu_item', 'menu-settings-column-nonce' );
	
		require_once ABSPATH . 'wp-admin/includes/nav-menu.php';
	
		$item_ids = wp_save_nav_menu_items( 0, $_POST['menu-item'] );
		if ( is_wp_error( $item_ids ) )
			die('-1');
	
		foreach ( (array) $item_ids as $menu_item_id ) {
			$menu_obj = get_post( $menu_item_id );
			if ( ! empty( $menu_obj->ID ) ) {
				$menu_obj = wp_setup_nav_menu_item( $menu_obj );
				$menu_obj->label = $menu_obj->title; // don't show "(pending)" in ajax-added items
				$menu_items[] = $menu_obj;
			}
		}
	
		if ( ! empty( $menu_items ) ) {
			$args = array(
				'after' => '',
				'before' => '',
				'link_after' => '',
				'link_before' => '',
				'walker' => new suppa_menu_backend_walker,
			);
			echo walk_nav_menu_tree( $menu_items, 0, (object) $args );
		}
		
		die('end');
	}
	

	/**	
	 *
	 * Build & Display Settings Page .
	 *
	 * ( this function change with every new plugin or theme )
	 * 
	 */
	public function display_admin_page()
	{
		// Anouncements 
		$this->add_box(
			array(
					'title' => 'Suppa Menu Pro &nbsp;&nbsp;&nbsp;<a href="http://labs.codetemp.com/suppa_menu/" class="codetemp_button codetemp_button_upgrade">Upgrade</a>',
					'desc'	=> '<br/><br/>
								Upgrade to <strong>[ Suppa Menu Pro ]</strong> to add support for:
								<br/><br/>
								* Latest Mega Links "Menu Type"<br/>
								* Latest Posts "Menu Type"<br/>
								* HTML & Shortcodes "Menu Type"<br/>
								* Search Form "Menu Type"<br/>
								* Latest Custom Post Type/Taxonomy "Menu Type"<br/>
								* Social Media "Menu Type"<br/>
								* Customization for the new "Menu Types"<br/>
								* Support<br/><br/>
								<br/>
								Find out more at <a href="#">http://labs.codetemp.com/suppa_menu/</a>
							'
				)
		);
	
		// Header
		$header_desc 	= 'Suppa Menu ' . $this->project_settings['version'] . '<br/>Framework ' . $this->project_settings['framework_version'];
		$html_id 		= 'suppa_menu';
		echo $this->get_html_header( $header_desc , $html_id );

		// Nav & Pages 
		echo '<div class="codetemp_nav_pages_container">';

		// NAV (Main)
		echo $this->get_html_nav( array( 
									__( '<span class="icon ct-wrench"></span>Settings' , 'suppa_menu' )		=> array(
																												__( 'jQuery', 'domain-name' ),
																												__( 'Responsive', 'domain-name' ),
																												__( 'Sticky Menu', 'domain-name' ),
																												__( 'Suppport WP Menus', 'domain-name' ),
																			),
									__( '<span class="icon ct-magic"></span>Style' , 'suppa_menu' )			=> array(
																												__( 'General', 'domain-name' ),
																												__( 'Logo', 'domain-name' ),
																												__( 'Top Level Links', 'domain-name' ),
																												__( 'Current Top Level Link', 'domain-name' ),
																												__( 'Top Level Icons', 'domain-name' ),
																												__( 'SubMenu General', 'domain-name' ),
																												__( 'SubMenu DropDown', 'domain-name' ),
																												__( 'SubMenu Dropdown Icons', 'domain-name' ),																																																											
																												__( 'Responsive W.D', 'domain-name' ),
																												__( 'Responsive W.D Icons', 'domain-name' ),
																			) ,
								) 
		);

		// Pages
		echo '		<!-- Pages Container -->
					<div class="codetemp_pages_container fl">';
					?>
						<!-- Page 1 -->
						<div class="codetemp_page" id="codetemp_page_1">
							<?php require_once('standard/include/settings-general.php') ?>							
						</div><!--page_1-->	

						<!-- Page 1 -->
						<div class="codetemp_page" id="codetemp_page_1_1">
							<?php require_once('standard/include/settings-jquery.php') ?>							
						</div><!--page_1-->	

						<!-- Page 1 -->
						<div class="codetemp_page" id="codetemp_page_1_2">
							<?php require_once('standard/include/settings-rwd.php') ?>							
						</div><!--page_1-->	

						<!-- Page 1 -->
						<div class="codetemp_page" id="codetemp_page_1_3">
							<?php require_once('standard/include/settings-sticky.php') ?>							
						</div><!--page_1-->	

						<!-- Page 1 -->
						<div class="codetemp_page" id="codetemp_page_1_4">
							<?php require_once('standard/include/settings-theme_implementation.php') ?>							
						</div><!--page_1-->	

						<!-- Page 2 -->
						<div class="codetemp_page" id="codetemp_page_2">
							<?php require_once('standard/include/style.php') ?>
						</div>

						<!-- Page 2.1 -->
						<div class="codetemp_page" id="codetemp_page_2_1">
							<?php require_once('standard/include/style-general.php') ?>
						</div>

						<!-- Page 2.1 -->
						<div class="codetemp_page" id="codetemp_page_2_2">
							<?php require_once('standard/include/style-logo.php') ?>
						</div>

						<!-- Page 2.1 -->
						<div class="codetemp_page" id="codetemp_page_2_3">
							<?php require_once('standard/include/style-top_level.php') ?>
						</div>

						<!-- Page 2.1 -->
						<div class="codetemp_page" id="codetemp_page_2_4">
							<?php require_once('standard/include/style-current_top_link.php') ?>
						</div>

						<!-- Page 2.1 -->
						<div class="codetemp_page" id="codetemp_page_2_5">
							<?php require_once('standard/include/style-top_level_icons.php') ?>
						</div>

						<!-- Page 2.1 -->
						<div class="codetemp_page" id="codetemp_page_2_6">
							<?php require_once('standard/include/style-submenu_general.php') ?>
						</div>

						<!-- Page 2.1 -->
						<div class="codetemp_page" id="codetemp_page_2_7">
							<?php require_once('standard/include/style-dropdown.php') ?>
						</div>

						<!-- Page 2.1 -->
						<div class="codetemp_page" id="codetemp_page_2_8">
							<?php require_once('standard/include/style-dropdown_icons.php') ?>
						</div>

						<!-- Page 2.1 -->
						<div class="codetemp_page" id="codetemp_page_2_9">
							<?php require_once('standard/include/style-rwd.php') ?>
						</div>

						<!-- Page 2.1 -->
						<div class="codetemp_page" id="codetemp_page_2_10">
							<?php require_once('standard/include/style-rwd_icons.php') ?>
						</div>
					<?php
		echo		'</div><!--codetemp_pages_container-->
				
					<div class="clearfix"></div>

			  </div><!--codetemp_nav_pages_container-->';

		// Footer
		echo $this->get_html_footer();

	}



	/**
	 *
	 *	Load Admin : CSS & JS
	 *
	 */
	public function backend_css_js($hook)
	{
		if( 'toplevel_page_CTF_suppa_menu' == $hook )
		{
			wp_enqueue_style('suppa_admin_menus_style', $this->project_settings['plugin_url'] . '/standard/css/suppa_admin_framework.css' );
		}

		if( 'nav-menus.php' == $hook )
		{
			wp_enqueue_style('suppa_admin_menus_style', $this->project_settings['plugin_url'] . '/standard/css/suppa_admin_menus.css' );
			wp_enqueue_style('suppa_admin_menus_script', $this->project_settings['plugin_url'] . '/standard/js/suppa_admin.js' , array( 'jquery' ) );
		}
	}


	/**
	 *
	 *	Front-End : Head CSS
	 *
	 */
	public function frontend_head_style()
	{
		/** Main Style **/
		wp_enqueue_style('suppa_frontend_style' , $this->project_settings['plugin_url'] . '/standard/css/suppa_frontend_style.css', false , $this->project_settings['version'] , 'screen' );
	
		/** Custom Style **/
		$style_file = get_option('suppa_user_style_file');
		wp_enqueue_style('suppa_frontend_custom_style' , $style_file , false , $this->project_settings['version'] , 'screen');

		/** Font Awesome **/
		wp_enqueue_style( 'suppa_frontend_fontAwesome' , $this->project_settings['plugin_url'] . '/standard/css/fontAwesome/style.css' , false , $this->project_settings['version'] , 'screen' );
		echo '
			<!--[if IE 7]>
			  <link rel="stylesheet" href="' . $this->project_settings['plugin_url'] . '/standard/css/fontAwesome/font-awesome-ie7.min.css">
			<![endif]-->
		';	
	}


	/**
	 *
	 *	Front-End : Footer Scripts
	 *
	 */
	public function frontend_footer_scripts()
	{
		$css_file = get_option('suppa_css_settings_file');
		$js_file = get_option('suppa_js_settings_file');

		wp_enqueue_script('suppa_css_settings_file' , $css_file , array() , $this->project_settings['version'] , true );
		wp_enqueue_script('suppa_js_settings_file' , $js_file , array() , $this->project_settings['version'] , true );
		wp_enqueue_script('suppa_frontend_script' , $this->project_settings['plugin_url'] . '/standard/js/suppa_frontend.min.js' , array('jquery' , 'hoverIntent' , 'suppa_js_settings_file' , 'suppa_css_settings_file', 'jquery-ui-core' , 'jquery-effects-core', 'jquery-effects-explode', 'jquery-effects-drop', 'jquery-effects-pulsate' ) , $this->project_settings['version'] , true );

	}


	/**
	 *
	 *	Save Style to File & DB
	 *
	 */
	public function save_settings_to_files( $settings_array )
	{
		$db_group_settings = $settings_array;

		// Save JS Settings
		// Responsive Design Enable or Not
		$responsive_enable = 'off';
		if( $db_group_settings['settings-responsive_enable'] == 'on' )
		{
			$responsive_enable = 'on';
		}

		// jQuery Mode 
		$jquery_mode = 'off';
		if( $db_group_settings['settings-jquery_enable'] == 'on' )
		{
			$jquery_mode = 'on';
		}

		/** Suppa JS Parameters **/
		$suppa_settings = "
		suppa_js_settings = new Object();

		suppa_js_settings.jquery_mode 		= '".$db_group_settings['settings-jquery_enable']."';
		suppa_js_settings.jquery_trig 		= '".$db_group_settings['settings-jquery_trigger']."';
		suppa_js_settings.jquery_anim 		= '".$db_group_settings['settings-jquery_animation']."';
		suppa_js_settings.jquery_time 		= '".$db_group_settings['settings-jquery_animation_time']."';
		suppa_js_settings.rwd_enable 		= '".$db_group_settings['settings-responsive_enable']."';
		suppa_js_settings.rwd_enable_desk	= '".$db_group_settings['settings-responsive_enable_desktops']."';


		suppa_js_settings.rwd_start_width	= '".$db_group_settings['settings_responsive_start_width']."';
		suppa_js_settings.rwd_text			= '".$db_group_settings['settings-responsive_text']."';
		suppa_js_settings.rwd_text_t_mode 	= '".$db_group_settings['settings-rwd_trigger_mode']."';
		suppa_js_settings.box_layout		= '".$db_group_settings['menu-layout']."';
		suppa_js_settings.scroll_enable		= '".$db_group_settings['settings-sticky_enable']."';
		suppa_js_settings.scroll_enable_mob	= '".$db_group_settings['settings-sticky_mobile_enable']."';

		";

		// If Upload Dir is Writable
		// Add All the Custom Style on a CSS-File
		$upload_dir = wp_upload_dir();
		if( is_writable($upload_dir['basedir']) && $this->project_settings['css_js_to_files'] )
		{
			// Create the CSS File
			$my_file = $upload_dir['basedir'].'/suppa_js_settings.js';
			$handle = @fopen($my_file, 'w');
			fwrite($handle, $suppa_settings);
			fclose($handle);
			update_option('suppa_js_settings_file' , $upload_dir['baseurl'].'/suppa_js_settings.js' );
		}
		else 
		{
			update_option('suppa_js_settings_file' , 'none' );
			die( 'Upload Folder Must Be Writable' );
		}
	}


	/**
	 *
	 *	Save Style to File & DB
	 *
	 */
	public function save_style_to_files( $style_array )
	{
		$style = $style_array;

		// Menu Background Image 
		$menu_background = '';
		if( isset( $style['menu_bg_bg_image'] ) and $style['menu_bg_bg_image'] != '' )
		{
			$menu_background 	= 'background-image:url(\''. $style['menu_bg_bg_image'].'\') !important; 
								    background-repeat:'.$style['menu_bg_bg_repeat'].'; 
									background-attachment:'.$style['menu_bg_bg_attachment'].'; 
									background-position:'.$style['menu_bg_bg_position'].';  
									';
		}

		// SubMenu Background Image 
		$submenu_background_image = '';
		if( isset( $style['submenu-bg_bg_image'] ) and $style['submenu-bg_bg_image'] != '' )
		{
			$submenu_background_image = 'background-image:url(\''. $style['submenu-bg_bg_image'].'\') !important; 
										 background-repeat:'.$style['submenu-bg_bg_repeat'].'; 
										 background-attachment:'.$style['submenu-bg_bg_attachment'].'; 
										 background-position:'.$style['submenu-bg_bg_position'].';  
										';
		}

		// Search Text
		$search_text_font_style = '';
		if( isset(  $style['submenu-search-text_font_font_family_style'] ) )
		{
			if( $style['submenu-search-text_font_font_family_style'] == 'normal' or $style['submenu-search-text_font_font_family_style'] == 'italic' )
			{
				$search_text_font_style = 'font-style:'.$style['submenu-search-text_font_font_family_style'].';';
			}
			else if( $style['submenu-search-text_font_font_family_style'] == 'bold' )
			{
				$search_text_font_style = 'font-weight:bold;';
			}
			else
			{
				$search_text_font_style = 'font-weight:bold;font-style:italic;';
			}
		}

		$custom_css = 
		'
			/** ----------------------------------------------------------------
			 ******** General Style
			 ---------------------------------------------------------------- **/	

			.suppaMenu_wrap {
				z-index:'.$style['menu_z_index'].';
				height:'.$style['menu_height'].' !important;
			}

			.suppaMenu_wrap_wide_laout {
				background-color:'.$style['menu_bg_bg_color'].';
				
				/* Borders */
				border-top: '.$style['menu_border_top_size'].' solid '.$style['menu_border_top_color'].';
				border-right: '.$style['menu_border_right_size'].' solid '.$style['menu_border_right_color'].';;
				border-bottom: '.$style['menu_border_bottom_size'].' solid '.$style['menu_border_bottom_color'].';;
				border-left: '.$style['menu_border_left_size'].' solid '.$style['menu_border_left_color'].';;

				/* CSS3 Gradient */ 
				background-image: -webkit-linear-gradient(top, '.$style['menu_bg_gradient_from'].', '.$style['menu_bg_gradient_to'].') ;
				background-image: -moz-linear-gradient(top, '.$style['menu_bg_gradient_from'].', '.$style['menu_bg_gradient_to'].') ;
				background-image: -o-linear-gradient(top, '.$style['menu_bg_gradient_from'].', '.$style['menu_bg_gradient_to'].') ;
				background-image: -ms-linear-gradient(top, '.$style['menu_bg_gradient_from'].', '.$style['menu_bg_gradient_to'].') ;
				background-image: linear-gradient(top, '.$style['menu_bg_gradient_from'].', '.$style['menu_bg_gradient_to'].') ;	
			
				'.$menu_background.'
			}

			.suppaMenu_wrap_wideSubmenu_laout {
				background-color:'.$style['menu_bg_bg_color'].';
				
				/* Borders */
				border-top: '.$style['menu_border_top_size'].' solid '.$style['menu_border_top_color'].';
				border-right: '.$style['menu_border_right_size'].' solid '.$style['menu_border_right_color'].';;
				border-bottom: '.$style['menu_border_bottom_size'].' solid '.$style['menu_border_bottom_color'].';;
				border-left: '.$style['menu_border_left_size'].' solid '.$style['menu_border_left_color'].';;

				/* CSS3 Gradient */ 
				background-image: -webkit-linear-gradient(top, '.$style['menu_bg_gradient_from'].', '.$style['menu_bg_gradient_to'].') ;
				background-image: -moz-linear-gradient(top, '.$style['menu_bg_gradient_from'].', '.$style['menu_bg_gradient_to'].') ;
				background-image: -o-linear-gradient(top, '.$style['menu_bg_gradient_from'].', '.$style['menu_bg_gradient_to'].') ;
				background-image: -ms-linear-gradient(top, '.$style['menu_bg_gradient_from'].', '.$style['menu_bg_gradient_to'].') ;
				background-image: linear-gradient(top, '.$style['menu_bg_gradient_from'].', '.$style['menu_bg_gradient_to'].') ;	
				
				'.$menu_background.'
			}

			.suppaMenu {
				z-index:'.$style['menu_z_index'].';
				width:'.$style['menu_width'].' !important;
				height:'.$style['menu_height'].' !important;

				background-color:'.$style['menu_bg_bg_color'].';
				
				/* Borders */
				border-top: '.$style['menu_border_top_size'].' solid '.$style['menu_border_top_color'].';
				border-right: '.$style['menu_border_right_size'].' solid '.$style['menu_border_right_color'].';;
				border-bottom: '.$style['menu_border_bottom_size'].' solid '.$style['menu_border_bottom_color'].';;
				border-left: '.$style['menu_border_left_size'].' solid '.$style['menu_border_left_color'].';;

				/* CSS3 Gradient */ 
				background-image: -webkit-linear-gradient(top, '.$style['menu_bg_gradient_from'].', '.$style['menu_bg_gradient_to'].') ;
				background-image: -moz-linear-gradient(top, '.$style['menu_bg_gradient_from'].', '.$style['menu_bg_gradient_to'].') ;
				background-image: -o-linear-gradient(top, '.$style['menu_bg_gradient_from'].', '.$style['menu_bg_gradient_to'].') ;
				background-image: -ms-linear-gradient(top, '.$style['menu_bg_gradient_from'].', '.$style['menu_bg_gradient_to'].') ;
				background-image: linear-gradient(top, '.$style['menu_bg_gradient_from'].', '.$style['menu_bg_gradient_to'].') ;

				'.$menu_background.'

				/* CSS3 Box Shadow */
				-moz-box-shadow   : 0px 0px '.$style['menu_boxshadow_blur'].' '.$style['menu_boxshadow_distance'].' '.$style['menu_boxshadow_color'].';
				-webkit-box-shadow: 0px 0px '.$style['menu_boxshadow_blur'].' '.$style['menu_boxshadow_distance'].' '.$style['menu_boxshadow_color'].';
				box-shadow        : 0px 0px '.$style['menu_boxshadow_blur'].' '.$style['menu_boxshadow_distance'].' '.$style['menu_boxshadow_color'].';
			
				/* CSS3 Border Radius */
				-webkit-border-radius: '.$style['menu_borderradius_top_left'].' '.$style['menu_borderradius_top_right'].' '.$style['menu_borderradius_bottom_right'].' '.$style['menu_borderradius_bottom_left'].'; 
				-moz-border-radius: '.$style['menu_borderradius_top_left'].' '.$style['menu_borderradius_top_right'].' '.$style['menu_borderradius_bottom_right'].' '.$style['menu_borderradius_bottom_left'].'; 
				border-radius: '.$style['menu_borderradius_top_left'].' '.$style['menu_borderradius_top_right'].' '.$style['menu_borderradius_bottom_right'].' '.$style['menu_borderradius_bottom_left'].'; 


				/* Prevent background color leak outs */
				-webkit-background-clip: padding-box; 
				-moz-background-clip:    padding; 
				background-clip:         padding-box;

				/* CSS3 Fixes For IE */ 
				behavior: url('.$this->project_settings['plugin_url'].'/standard/css/pie/PIE.php);
			}


			/** ----------------------------------------------------------------
			 ******** Logo Style
			 ---------------------------------------------------------------- **/	

			.suppa_menu_logo{
				padding : '.$style['title-padding_top'].' '.$style['title-padding_right'].' 0px '.$style['title-padding_left'].' !important;
			}


			/** ----------------------------------------------------------------
			 ******** Top Level Links Style
			 ---------------------------------------------------------------- **/	

			.suppa_menu > a{
				font-size:'.$style['top_level_font_font_size'].$style['top_level_font_font_size_type'].' !important;
				font-family:'.$style['top_level_font_font_family'].' !important;
				
				'.$style['top_level_font_font_family_style'].'

				line-height:'.$style['menu_height'].' !important;
				min-height:'.$style['menu_height'].' !important;
				color:'.$style['top_level_font_font_color'].';
			}

			.suppa_menu_posts,
			.suppa_menu_search,
			.suppa_menu_social {
				height:'.$style['menu_height'].' !important;
			}

			.suppa_menu_dropdown > a,
			.suppa_menu_links > a,
			.suppa_menu_posts > a,
			.suppa_menu_html > a {

				padding-left:'.$style['top_level_padding_left'].';
				padding-right:'.$style['top_level_padding_right'].';
				border-color:'.$style['top-links-border_color'].' !important;
			}

			/** ----------------------------------------------------------------
			 ******** Top Level Links on [HOVER] Style
			 ---------------------------------------------------------------- **/	

			.suppa_menu:hover > a{
				background-color:'.$style['top_level_bg_hover'].' !important;
				color:'.$style['top_level_links_color_hover'].' !important;
			}
			.suppa_menu:hover > a .suppa_item_title {
				color:'.$style['top_level_links_color_hover'].' !important;
			}

			/** Needed for suppa_frontend.js **/
			.suppa_menu_class_hover {
				background-color:'.$style['top_level_bg_hover'].' !important;
				color:'.$style['top_level_links_color_hover'].' !important;
			}
			.suppa_menu_class_hover > a .suppa_item_title {
				color:'.$style['top_level_links_color_hover'].' !important;
			}

			/* boder right or left */
			.suppa_menu_position_left,
			.suppa_menu_position_left > a{
				border-right:1px solid '.$style['top-links-border_color'].';
			}
			.suppa_menu_position_right,
			.suppa_menu_position_right > a{
				border-left:1px solid '.$style['top-links-border_color'].';
			}

			/** ----------------------------------------------------------------
			 ******** Top Level Arrow Style
			 ---------------------------------------------------------------- **/				

			.suppa_menu > a .ctf_suppa_fa_box_top_arrow {
					font-size:'.$style['top-links-arrow_width'].' !important;
					top:'.$style['top-links-arrow_position_top'].' !important;
					right:'.$style['top-links-arrow_position_right'].' !important;
					
					/* color/bg/border */
					color:'.$style['top-links-arrow_color'].';
			}
			.suppa_menu:hover > a .ctf_suppa_fa_box_top_arrow {
				color:'.$style['top-links-arrow_color_hover'].' !important;
			}
			/** Needed for suppa_frontend.js **/
			.suppa_menu_class_hover > a .ctf_suppa_fa_box_top_arrow,
			.suppa_menu_class_hover > a .ctf_suppa_fa_box{
				color:'.$style['top-links-arrow_color_hover'].' !important;
			}			

			/** ----------------------------------------------------------------
			 ******** Current Top Level Style
			 ---------------------------------------------------------------- **/				

			.suppaMenu .current-menu-item > a .suppa_item_title,
			.suppaMenu a.current-menu-ancestor,
			.suppaMenu .current-menu-item > a,
			.suppaMenu .current-menu-ancestor > a,
			.suppaMenu .current-menu-item > a .ctf_suppa_fa_box,
			.suppaMenu .current-menu-ancestor > a .ctf_suppa_fa_box{
				color:'.$style['top-links-current_color'].';
			}
			.suppaMenu a.current-menu-item,
			.suppaMenu a.current-menu-ancestor,
			.suppaMenu .current-menu-item > a,
			.suppaMenu .current-menu-ancestor > a{
				background-color:'.$style['top-links-current_bg'].';
			}

			.suppaMenu .current-menu-item > a .era_suppa_arrow_box span,
			.suppaMenu a.current-menu-item .era_suppa_arrow_box span {
				color:'.$style['top-links-current_arrow_color'].';
			}

			/** ----------------------------------------------------------------
			 ******** Top Level, RWD Main Links + RWD DropDown Icons Style
			 ---------------------------------------------------------------- **/

			/** F.Awesome Icons **/

			.suppa_menu_dropdown > a .suppa_FA_icon,
			.suppa_menu_links > a .suppa_FA_icon,
			.suppa_menu_posts > a .suppa_FA_icon,
			.suppa_menu_html > a .suppa_FA_icon{
				font-size:'.$style['fontawesome_icons_size'].' !important;
				padding-top: '.$style['top-links-fontawesome_icon_margin_top'].' !important;
				padding-right: '.$style['top-links-fontawesome_icon_margin_right'].' !important;
			}

			/** F.Awesome Only Icons **/
			.suppa_menu_dropdown > a .suppa_FA_icon_only,
			.suppa_menu_links > a .suppa_FA_icon_only,
			.suppa_menu_posts > a .suppa_FA_icon_only,
			.suppa_menu_html > a .suppa_FA_icon_only{
				font-size:'.$style['fontawesome_icons_size'].' !important;
				padding-top: '.$style['top-links-only_icon_margin_top'].' !important;
				padding-right: '.$style['top-links-only_icon_margin_right'].' !important;
			}

			/** Uploaded Icons **/
			.suppa_menu_dropdown > a .suppa_UP_icon,
			.suppa_menu_links > a .suppa_UP_icon,
			.suppa_menu_posts > a .suppa_UP_icon,
			.suppa_menu_html > a .suppa_UP_icon{
				width : '.$style['uploaded_icons_width'].' !important; 
				height : '.$style['uploaded_icons_height'].' !important; 
				padding-top: '.$style['top-links-normal_icon_margin_top'].' !important;
				padding-right: '.$style['top-links-normal_icon_margin_right'].' !important;
			}

			/** Uploaded Only Icons **/
			.suppa_menu_dropdown > a .suppa_UP_icon_only,
			.suppa_menu_links > a .suppa_UP_icon_only,
			.suppa_menu_posts > a .suppa_UP_icon_only,
			.suppa_menu_html > a .suppa_UP_icon_only {
				width : '.$style['uploaded_icons_width'].' !important; 
				height : '.$style['uploaded_icons_height'].' !important; 
				padding-top: '.$style['top-links-only_icon_margin_top'].' !important;
				padding-right: '.$style['top-links-only_icon_margin_right'].' !important;
			}

			/** ----------------------------------------------------------------
			 ******** General Submenu
			 ---------------------------------------------------------------- **/
			
			.suppa_submenu {
				
				top:'.$style['submenu-position_top'].' !important;

				/* color/bg/border */
				background-color:'.$style['submenu-bg_bg_color'].';
								
				border-top: '.$style['submenu-border-top_size'].' solid '.$style['submenu-border-top_color'].';
				border-right: '.$style['submenu-border-right_size'].' solid '.$style['submenu-border-right_color'].';
				border-bottom: '.$style['submenu-border-bottom_size'].' solid '.$style['submenu-border-bottom_color'].';
				border-left: '.$style['submenu-border-left_size'].' solid '.$style['submenu-border-left_color'].';

				/* CSS3 Gradient */ 
				background-image: -webkit-linear-gradient(top, '.$style['submenu-bg-gradient_from'].', '.$style['submenu-bg-gradient_to'].') ;
				background-image: -moz-linear-gradient(top, '.$style['submenu-bg-gradient_from'].', '.$style['submenu-bg-gradient_to'].') ;
				background-image: -o-linear-gradient(top, '.$style['submenu-bg-gradient_from'].', '.$style['submenu-bg-gradient_to'].') ;
				background-image: -ms-linear-gradient(top, '.$style['submenu-bg-gradient_from'].', '.$style['submenu-bg-gradient_to'].') ;
				background-image: linear-gradient(top, '.$style['submenu-bg-gradient_from'].', '.$style['submenu-bg-gradient_to'].') ;

				'.$submenu_background_image.'

				/* CSS3 Box Shadow */
				-moz-box-shadow   : 0px 0px '.$style['submenu-boxshadow_blur'].' '.$style['submenu-boxshadow_distance'].' '.$style['submenu-boxshadow_color'].';
				-webkit-box-shadow: 0px 0px '.$style['submenu-boxshadow_blur'].' '.$style['submenu-boxshadow_distance'].' '.$style['submenu-boxshadow_color'].';
				box-shadow        : 0px 0px '.$style['submenu-boxshadow_blur'].' '.$style['submenu-boxshadow_distance'].' '.$style['submenu-boxshadow_color'].';
			
				/* CSS3 Border Radius */
				-webkit-border-radius: '.$style['submenu-borderradius_top_left'].' '.$style['submenu-borderradius_top_right'].' '.$style['submenu-borderradius_bottom_right'].' '.$style['submenu-borderradius_bottom_left'].'; 
				-moz-border-radius: '.$style['submenu-borderradius_top_left'].' '.$style['submenu-borderradius_top_right'].' '.$style['submenu-borderradius_bottom_right'].' '.$style['submenu-borderradius_bottom_left'].'; 
				border-radius: '.$style['submenu-borderradius_top_left'].' '.$style['submenu-borderradius_top_right'].' '.$style['submenu-borderradius_bottom_right'].' '.$style['submenu-borderradius_bottom_left'].'; 

			    /* Prevent background color leak outs */
			    -webkit-background-clip: padding-box; 
			    -moz-background-clip:    padding; 
			    background-clip:         padding-box;

			    /* CSS3 Fixes For IE */ 
			    behavior: url('.$this->project_settings['plugin_url'].'/standard/css/pie/PIE.php);
			
			}


			/** ----------------------------------------------------------------
			 ******** Submenu DropDown Style
			 ---------------------------------------------------------------- **/	

			.suppa_menu_dropdown > .suppa_submenu a {
				font-size:'.$style['submenu-dropdown-link_font_font_size'].$style['submenu-dropdown-link_font_font_size_type'].' !important;
				font-family:'.$style['submenu-dropdown-link_font_font_family'].' !important;
				
				'.$style['submenu-dropdown-link_font_font_family_style'].'

				color:'.$style['submenu-dropdown-link_font_font_color'].';
				border-bottom:1px solid '.$style['submenu_dropdown_link_border_color'].';

				padding: 0px '.$style['submenu_dropdown_link_padding_right'].' 0px '.$style['submenu_dropdown_link_padding_left'].' !important;

				height : '.$style['submenu_dropdown_link_height'].' !important ;
				line-height : '.$style['submenu_dropdown_link_height'].' !important ;

			}
			.suppa_menu_dropdown > .suppa_submenu div:hover > a ,
			.suppa_menu_dropdown > .suppa_submenu a:hover {
				color:'.$style['submenu-dropdown-link_color_hover'].';
				background-color:'.$style['submenu_dropdown_link_bg_hover'].';
			}
			
			/** Needed for suppa_frontend.js **/
			.suppa_menu_class_dropdown_levels_hover > a {
				color:'.$style['submenu-dropdown-link_color_hover'].' !important;
				background-color:'.$style['submenu_dropdown_link_bg_hover'].' !important;
			}

			/** ----------------------------------------------------------------
			 ******** Submenu DropDown Icons Style
			 ---------------------------------------------------------------- **/	

			/** F.Awesome Icons **/
			.suppa_menu_dropdown .suppa_submenu .suppa_FA_icon {
				font-size:'.$style['submenu_dropdown_links_fontawesome_icons_size'].' !important;
				padding-top: '.$style['submenu_dropdown_links_fontawesome_icon_margin_top'].' !important;
				padding-right: '.$style['submenu_dropdown_links_fontawesome_icon_margin_right'].' !important;
			}

			/** F.Awesome Only Icons **/
			.suppa_menu_dropdown .suppa_submenu .suppa_FA_icon_only {
				font-size:'.$style['submenu_dropdown_links_fontawesome_icons_size'].' !important;
				padding-top: '.$style['submenu_dropdown_links_only_icon_margin_top'].' !important;
				padding-right: '.$style['submenu_dropdown_links_only_icon_margin_right'].' !important;
			}
			.suppa_menu_class_hover .suppa_FA_icon_only span{
				color:'.$style['top_level_links_color_hover'].' !important;
			}

			/** Uploaded Icons **/
			.suppa_menu_dropdown .suppa_submenu .suppa_UP_icon {
				width : '.$style['submenu_dropdown_links_uploaded_icons_width'].' !important; 
				height : '.$style['submenu_dropdown_links_uploaded_icons_height'].' !important; 
				padding-top: '.$style['submenu_dropdown_links_normal_icon_margin_top'].' !important;
				padding-right: '.$style['submenu_dropdown_links_normal_icon_margin_right'].' !important;
			}

			/** Uploaded Only Icons **/
			.suppa_menu_dropdown .suppa_submenu .suppa_UP_icon_only {
				width : '.$style['submenu_dropdown_links_uploaded_icons_width'].' !important; 
				height : '.$style['submenu_dropdown_links_uploaded_icons_height'].' !important; 
				padding-top: '.$style['submenu_dropdown_links_only_icon_margin_top'].' !important;
				padding-right: '.$style['submenu_dropdown_links_only_icon_margin_right'].' !important;
			}

			/** ----------------------------------------------------------------
			 ******** Submenu DropDown Arrow Style
			 ---------------------------------------------------------------- **/	

			.suppa_menu_dropdown .suppa_submenu a .era_suppa_arrow_box {
				top:'.$style['dropdown-links-arrow_position_top'].' !important;
				right:'.$style['dropdown-links-arrow_position_right'].' !important;
			}
			.suppa_menu_dropdown .suppa_submenu a .era_suppa_arrow_box span {
				font-size:'.$style['dropdown-links-arrow_width'].' !important;
				/* color/bg/border */
				color:'.$style['dropdown-links-arrow_color'].';
			}
			.suppa_menu_dropdown .suppa_dropdown_item_container:hover > a .era_suppa_arrow_box span {
				color:'.$style['dropdown-links_arrow_color_hover'].' !important;
			}

			/** Needed for suppa_frontend.js **/
			.suppa_menu_class_dropdown_levels_hover > a .era_suppa_arrow_box span{
				color:'.$style['dropdown-links_arrow_color_hover'].' !important;
			}

			 
			/** ----------------------------------------------------------------
			 ******** Responsive Web Design Style
			 ---------------------------------------------------------------- **/	
			
			.suppaMenu_rwd_wrap{
				background-color:'.$style['menu_bg_bg_color'].';
				
				/* Borders */
				border-top: '.$style['menu_border_top_size'].' solid '.$style['menu_border_top_color'].';
				border-right: '.$style['menu_border_right_size'].' solid '.$style['menu_border_right_color'].';;
				border-bottom: '.$style['menu_border_bottom_size'].' solid '.$style['menu_border_bottom_color'].';;
				border-left: '.$style['menu_border_left_size'].' solid '.$style['menu_border_left_color'].';;

				/* CSS3 Gradient */ 
				background-image: -webkit-linear-gradient(top, '.$style['menu_bg_gradient_from'].', '.$style['menu_bg_gradient_to'].') ;
				background-image: -moz-linear-gradient(top, '.$style['menu_bg_gradient_from'].', '.$style['menu_bg_gradient_to'].') ;
				background-image: -o-linear-gradient(top, '.$style['menu_bg_gradient_from'].', '.$style['menu_bg_gradient_to'].') ;
				background-image: -ms-linear-gradient(top, '.$style['menu_bg_gradient_from'].', '.$style['menu_bg_gradient_to'].') ;
				background-image: linear-gradient(top, '.$style['menu_bg_gradient_from'].', '.$style['menu_bg_gradient_to'].') ;	
				
				'.$menu_background.'
			}

			.suppa_rwd_menus_container {
				/* color/bg/border */
				background-color:'.$style['submenu-bg_bg_color'].';
								
				border-top: '.$style['submenu-border-top_size'].' solid '.$style['submenu-border-top_color'].';
				border-right: '.$style['submenu-border-right_size'].' solid '.$style['submenu-border-right_color'].';
				border-bottom: '.$style['submenu-border-bottom_size'].' solid '.$style['submenu-border-bottom_color'].';
				border-left: '.$style['submenu-border-left_size'].' solid '.$style['submenu-border-left_color'].';

				/* CSS3 Gradient */ 
				background-image: -webkit-linear-gradient(top, '.$style['submenu-bg-gradient_from'].', '.$style['submenu-bg-gradient_to'].') ;
				background-image: -moz-linear-gradient(top, '.$style['submenu-bg-gradient_from'].', '.$style['submenu-bg-gradient_to'].') ;
				background-image: -o-linear-gradient(top, '.$style['submenu-bg-gradient_from'].', '.$style['submenu-bg-gradient_to'].') ;
				background-image: -ms-linear-gradient(top, '.$style['submenu-bg-gradient_from'].', '.$style['submenu-bg-gradient_to'].') ;
				background-image: linear-gradient(top, '.$style['submenu-bg-gradient_from'].', '.$style['submenu-bg-gradient_to'].') ;

				'.$submenu_background_image.'
			}

			.suppa_rwd_menu > a,
			.suppa_rwd_submenu > .suppa_dropdown_item_container > a {
				height:'.$style['rwd_main_links_height'].' !important;
				line-height:'.$style['rwd_main_links_height'].' !important;

				font-size:'.$style['rwd_main_links_font_font_size'].$style['rwd_main_links_font_font_size_type'].' !important;
				font-family:'.$style['rwd_main_links_font_font_family'].' !important;
				
				'.$style['rwd_main_links_font_font_family_style'].'

				color :'.$style['rwd_main_links_font_font_color'].';
				border-bottom:1px solid '.$style['rwd_main_links_bottom_border_color'].' !important;
				background-color:'.$style['rwd_main_links_bg'].';
			}

				.suppa_rwd_menu > a:first-child{
					border-top:1px solid '.$style['rwd_main_links_bottom_border_color'].' !important;
				}
				.suppa_rwd_menu_search,
				.suppa_rwd_submenu_posts,
				.suppa_rwd_submenu_html,
				suppa_rwd_submenu_columns_wrap{
					border-bottom:1px solid '.$style['rwd_main_links_bottom_border_color'].' !important;
				}

				.suppa_rwd_menu > a{
					padding-left:'.$style['rwd_main_links_left_margin'].' !important;
				}

				.suppa_rwd_menu:hover > a,
				.suppa_rwd_menu .suppa_dropdown_item_container a:hover {
					color :'.$style['rwd-main_links_color_hover'].' !important;
					background-color :'.$style['rwd_main_links_bg_hover'].' !important;
				
				}

				.suppa_rwd_button,
				.suppa_rwd_text {
					line-height:'.$style['menu_height'].' !important;
				}

				.suppa_rwd_button {
					padding-right:'.$style['rwd_3bars_icon_right_margin'].' !important;
					padding-left:'.$style['rwd_3bars_icon_right_margin'].' !important;
					line-height:'.$style['menu_height'].' !important;
				}
				.suppa_rwd_button,
				.suppa_rwd_button span{
					font-size:'.$style['rwd_3bars_icon_size'].' !important;
					color:'.$style['rwd_3bars_icon_color'].' !important;
				}
				.suppa_rwd_text{
					font-size:'.$style['rwd_text_font_font_size'].$style['rwd_text_font_font_size_type'].' !important;
					font-family:'.$style['rwd_text_font_font_family'].' !important;
					
					'.$style['rwd_text_font_font_family_style'].'

					color :'.$style['rwd_text_font_font_color'].' !important;
					padding: 0px '.$style['rwd_text_left_margin'].' !important;
					line-height:'.$style['menu_height'].' !important;

				}


				/* Arrow */
				.suppa_rwd_menu > a .era_rwd_suppa_arrow_box {
						font-size:'.$style['rwd_main_links_arrow_width'].' !important;
						
						/* color/bg/border */
						color:'.$style['rwd_main_links_arrow_color'].';
				}
				.suppa_rwd_menu:hover > a .era_rwd_suppa_arrow_box {
					color:'.$style['rwd_main_links_arrow_color_hover'].' !important;
				}

				/* RWD Trigger Both */
				.era_rwd_suppa_arrow_both_open{
					color :'.$style['rwd_main_links_font_font_color'].' !important;
					background-color:'.$style['rwd_main_links_bg'].' !important;
				}
				.era_rwd_suppa_link_both_open{
					color :'.$style['rwd-main_links_color_hover'].' !important;
					background-color :'.$style['rwd_main_links_bg_hover'].' !important;
				}

				/* Submenu */
				.suppa_rwd_submenu_posts,
				.suppa_rwd_submenu_html,
				.suppa_rwd_submenu_columns_wrap	{

					/* color/bg/border */
					background-color:'.$style['submenu-bg_bg_color'].';
										
					border-top: '.$style['submenu-border-top_size'].' solid '.$style['submenu-border-top_color'].';
					border-right: '.$style['submenu-border-right_size'].' solid '.$style['submenu-border-right_color'].';
					border-bottom: '.$style['submenu-border-bottom_size'].' solid '.$style['submenu-border-bottom_color'].';
					border-left: '.$style['submenu-border-left_size'].' solid '.$style['submenu-border-left_color'].';

					/* CSS3 Gradient */ 
					background-image: -webkit-linear-gradient(top, '.$style['submenu-bg-gradient_from'].', '.$style['submenu-bg-gradient_to'].') ;
					background-image: -moz-linear-gradient(top, '.$style['submenu-bg-gradient_from'].', '.$style['submenu-bg-gradient_to'].') ;
					background-image: -o-linear-gradient(top, '.$style['submenu-bg-gradient_from'].', '.$style['submenu-bg-gradient_to'].') ;
					background-image: -ms-linear-gradient(top, '.$style['submenu-bg-gradient_from'].', '.$style['submenu-bg-gradient_to'].') ;
					background-image: linear-gradient(top, '.$style['submenu-bg-gradient_from'].', '.$style['submenu-bg-gradient_to'].') ;

					'.$submenu_background_image.'

					padding-top:'.$style['rwd_submenu_padding_top'].' !important;
					padding-right:'.$style['rwd_submenu_padding_right'].' !important;
					padding-bottom:'.$style['rwd_submenu_padding_bottom'].' !important;
					padding-left:'.$style['rwd_submenu_padding_left'].' !important;

				    /* CSS3 Fixes For IE */ 
			    	behavior: url('.$this->project_settings['plugin_url'].'standard/css/pie/PIE.php);
				}

			/** ----------------------------------------------------------------
			 ******** RWD Icons Style
			 ---------------------------------------------------------------- **/	

			/** F.Awesome Icons **/
			.suppa_rwd_menu > a .suppa_FA_icon,
			.suppa_rwd_menu .suppa_dropdown_item_container .suppa_FA_icon
			 {
				font-size:'.$style['rwd_links_fontawesome_icons_size'].' !important;
				padding-top: '.$style['rwd_links_fontawesome_icon_margin_top'].' !important;
				padding-right: '.$style['rwd_links_fontawesome_icon_margin_right'].' !important;
			}

			/** F.Awesome Only Icons **/
			.suppa_rwd_menu > a .suppa_FA_icon_only,
			.suppa_rwd_menu .suppa_dropdown_item_container .suppa_FA_icon_only
			 {
				font-size:'.$style['rwd_links_fontawesome_icons_size'].' !important;
				padding-top: '.$style['rwd_links_only_icon_margin_top'].' !important;
				padding-right: '.$style['rwd_links_only_icon_margin_right'].' !important;
			}

			/** Uploaded Icons **/
			.suppa_rwd_menu > a .suppa_UP_icon,
			.suppa_rwd_menu .suppa_dropdown_item_container a .suppa_UP_icon
			 {
				width : '.$style['rwd_links_uploaded_icons_width'].' !important; 
				height : '.$style['rwd_links_uploaded_icons_height'].' !important; 
				padding-top: '.$style['rwd_links_normal_icon_margin_top'].' !important;
				padding-right: '.$style['rwd_links_normal_icon_margin_right'].' !important;
			}

			/** Uploaded Only Icons **/
			.suppa_rwd_menu > a .suppa_UP_icon_only,
			.suppa_rwd_menu .suppa_dropdown_item_container a .suppa_UP_icon_only
			 {
				width : '.$style['rwd_links_uploaded_icons_width'].' !important; 
				height : '.$style['rwd_links_uploaded_icons_height'].' !important; 
				padding-top: '.$style['rwd_links_only_icon_margin_top'].' !important;
				padding-right: '.$style['rwd_links_only_icon_margin_right'].' !important;
			}
		';

		// Custom Style ( from ace editor on settings page )
		$custom_css .= $style['custom-css'];

		// If Upload Dir is Writable & css_js_to_files === true
		// Add All the Custom Style on a CSS-File
		$upload_dir = wp_upload_dir();
		if( is_writable($upload_dir['basedir']) && $this->project_settings['css_js_to_files'] )
		{
			// Create the CSS File
			$my_file = $upload_dir['basedir'].'/suppa_user_style.css';
			$handle = @fopen($my_file, 'w');
			fwrite($handle, $custom_css);
			fclose($handle);

			//Save File Link To DB
			update_option('suppa_user_style_file' , $upload_dir['baseurl'].'/suppa_user_style.css' );
		}
		else 
		{
			update_option('suppa_user_style_file' , 'none' );
			die( 'Upload Folder Must Be Writable' );
		}

		/** Suppa Style Settings **/
		$suppa_css_settings = "
		suppa_css_settings = new Object();

		suppa_css_settings.topLinks_c  				= '".$style['top_level_font_font_color']."';
		suppa_css_settings.topLinks_bgH 			= '".$style['top_level_bg_hover']."';
		suppa_css_settings.topLinks_cH 				= '".$style['top_level_links_color_hover']."';
		suppa_css_settings.topLinks_arrowColor 		= '".$style['top-links-arrow_color']."';
		suppa_css_settings.topLinks_arrowColorH		= '".$style['top-links-arrow_color_hover']."';
		suppa_css_settings.submenu_border_right 	= '".$style['submenu-border-right_size']."';
		suppa_css_settings.submenu_border_left 		= '".$style['submenu-border-left_size']."';
		suppa_css_settings.dropdown_sub_link_c  	= '".$style['submenu-dropdown-link_color_hover']."';
		suppa_css_settings.dropdown_sub_link_bg 	= '".$style['submenu_dropdown_link_bg_hover']."';
		suppa_css_settings.dropdown_sub_link_arr	= '".$style['dropdown-links_arrow_color_hover']."'; 

		suppa_css_settings.rwd_link_c 				= '".$style['rwd-main_links_color_hover']."'; 
		suppa_css_settings.rwd_link_bg 				= '".$style['rwd_main_links_bg_hover']."'; 
		suppa_css_settings.rwd_link_arrow 			= '".$style['rwd_main_links_arrow_color_hover']."'; 

		";

		// If Upload Dir is Writable
		// Add All the Custom Style on a CSS-File
		$upload_dir = wp_upload_dir();
		if( is_writable($upload_dir['basedir']) && $this->project_settings['css_js_to_files'] )
		{
			// Create the CSS File
			$my_file = $upload_dir['basedir'].'/suppa_css_settings.js';
			$handle = @fopen($my_file, 'w');
			fwrite($handle, $suppa_css_settings);
			fclose($handle);
			update_option('suppa_css_settings_file' , $upload_dir['baseurl'].'/suppa_css_settings.js' );
		}
		else 
		{
			update_option('suppa_css_settings_file' , 'none' );
			die( 'Upload Folder Must Be Writable' );
		}
	}


}// end class


/** Show Time **/
$suppa_menu_start = new codetemp_suppa_menu ( $suppa_settings );

/** Theme Implementation for WP 3+ Menus **/
if( !function_exists('suppa_implement') )
{
	function suppa_implement()
	{
		$args = array();

		$args['walker'] 				= new suppa_menu_walker();
		$args['container_class'] 		= ' suppaMenu_wrap';
		$args['menu_class']				= ' suppaMenu';
		$args['items_wrap']				= '<div id="%1$s" class="%2$s">%3$s</div>';
		$args['depth']					= 4;
		$args['theme_location']			= 'suppa_menu_location';

		wp_nav_menu( $args );
	}
} 
?>