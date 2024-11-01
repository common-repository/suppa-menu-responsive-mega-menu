<?php

if( !class_exists('ctf_setup') )
{
	/** CLASS **/
	class ctf_setup extends ctf_options 
	{
		/**
		 *
		 *	Construct
		 *
		 */
		function __construct()
		{
			/** Create Groups **/
			$this->create_groups();

			/** Add : Admin Page **/
			add_action( 'admin_menu' , array( $this , 'add_admin_page' ) );

			/** Admin : Laod CSS **/
			add_action( 'admin_enqueue_scripts' , array( $this , 'core_admin_css' ) , 0 );

			/** Admin : Laod JS **/
			add_action( 'admin_enqueue_scripts' , array( $this , 'core_admin_js' ) , 0 );
			
			/** AJAX **/
			add_action( 'wp_ajax_' . $this->project_settings['plugin_id'] . '_update_options' , array( $this , 'ajax_update_options' ) );
			add_action( 'wp_ajax_' . $this->project_settings['plugin_id'] . '_database_import' , array( $this , 'database_import' ) );

			/** Front End : Load Google Fonts **/
			add_action( 'wp_head' , array( $this , 'load_frontend_google_fonts' ) );

			/** Action : Save Style&Settings To Files **/
			add_action( $this->project_settings['plugin_id'] . '_save_style_to_files' , array( $this , 'save_style_to_files' ) );
			add_action( $this->project_settings['plugin_id'] . '_save_settings_to_files' , array( $this , 'save_settings_to_files' ) );

		}


		/**
		 *
		 *	Add : Admin Page
		 *
		 */
		function add_admin_page( )
		{
			add_menu_page( 
				$this->project_settings['page_title'] 	, 		// page_title
				$this->project_settings['menu_title'] 	, 		// menu_title 
				$this->project_settings['capability'] 	, 		// capability 
				$this->project_settings['plugin_id']	, 		// menu_slug 
				array( $this, 'display_admin_page' )	, 		// function 
				$this->project_settings['icon_url']  			// icon_url 
			);
			// Add Export & Import Sub-Menu Page
			add_submenu_page( 
				$this->project_settings['plugin_id']											,		// parent_slug
				$this->project_settings['page_title'].__(' - Backup Settings' , 'suppa_menu') 	,		// page_title
				__('Backup Settings' , 'suppa_menu') 											,		// menu_title
				$this->project_settings['capability'] 											,		// capability
				$this->project_settings['plugin_id'] . '_export_import' 						,		// menu_slug
				array( $this, 'database_export' )												// function 
			);
		}



		/**
		 *
		 *	Admin : Export All DB To TEXT File
		 *
		 */
		public function database_export()
		{
			$export = '';

			foreach ( $this->groups_db_offline as $gr_id => $options ) 
			{
				foreach ( $options as $op_id => $op_val) 
				{
					$export .= $gr_id . '_ctfoption_' . $op_id . '_ctfval_' . $op_val . '_ctfnewoption_';
				}
			}

			// Create Export File
			$upload_dir			= wp_upload_dir();
			$export_file 		= $upload_dir['basedir'] . '/' . $this->project_settings['plugin_id'] . '_export.txt';
			$export_file_url 	= $upload_dir['baseurl'] . '/' . $this->project_settings['plugin_id'] . '_export.txt';

			$handle = 	@fopen($export_file, 'w') or die('Cannot open file:  '.$export_file);
						@fwrite($handle, $export);
						@fclose($handle);

			echo '
				<form id="codetemp_import_db" enctype="multipart/form-data" action="" method="post" >
							
					<div class="codetemp_pages_container">
						<div class="codetemp_pages_container_export">
							<h3>Export Settings</h3>
							<br/>
							Download the settings file<br/><br/>
							<a href="'.$export_file_url.'" class="codetemp_button" download >Download</a>
						</div>

						<div class="codetemp_pages_container_import">
							<h3>Import Settings</h3>
							<br/>
							Upload the settings file<br/><br/>
							<input type="file" name="ctf_import_file" id="ctf_import_file">
							<br/><br/>
							<input type="submit" value="Upload" name="ctf_import_data" class="codetemp_button" />
						</div>
					</div>

				</form>
			';
		}


		/**
		 *
		 *	Admin : Import Data to db
		 *
		 */
		public function database_import()
		{
			if( isset( $_POST['ctf_import_data'] ) )
			{
				$upload_dir 		= wp_upload_dir();
				$export_file 		= $upload_dir['basedir'] . '/' . $this->project_settings['plugin_id'] . '_import.txt';
				$export_file_url 	= $upload_dir['baseurl'] . '/' . $this->project_settings['plugin_id'] . '_import.txt';

				if( file_exists($export_file) )
				{
					unlink($export_file);
					move_uploaded_file( $_FILES["ctf_import_file"]["tmp_name"] , $export_file );
				}
				else 
				{
					move_uploaded_file( $_FILES["ctf_import_file"]["tmp_name"] , $export_file );
				}

				$handle 		= 	@fopen($export_file, 'r');
				$import_data 	= 	@fread($handle,filesize($export_file));
									@fclose($handle);

				// Create Data Structure 
				$import_array = explode( "_ctfnewoption_" , $import_data );
				$new_db_groups = array();
				foreach ( $import_array as $option ) 
				{
					if( $option != '' )
					{
						$op 		= explode( "_ctfoption_" , $option );
						$group_id 	= $op[0];
						$op 		= explode( "_ctfval_" , $op[1] );
						$op_id 		= $op[0];
						$op_val 	= $op[1];
						$new_db_groups[$group_id][$op_id] = $op_val;
					}
				}

				// Save imported Data to db
				foreach ( $this->project_settings['groups'] as $group ) 
				{
					if( isset( $new_db_groups[$group] ) )
					update_option( $this->project_settings['plugin_id'] . '__group__' . $group , $new_db_groups[$group]  );
				}

				echo '<span class="codetemp_ajax_response codetemp_ajax_response_backup">
							<span>'.__('Data Imported','suppa_menu').'</span>
							<div class="clearfix"></div>
						</span>
						<script type="text/javascript">
						setTimeout(function(){
							jQuery(".codetemp_ajax_response_backup").fadeOut(200);
						},1500);
						</script>
						';
			}
		}



		/**
		 *
		 *	Admin : Load CSS
		 *
		 */
		public function core_admin_css( $hook )
		{
			if( 'toplevel_page_CTF_suppa_menu' == $hook or
				'suppa-menu_page_CTF_suppa_menu_export_import' == $hook )
			{

				wp_enqueue_style( 'codetemp-core-admin-style' , $this->project_settings['plugin_url'] . 'core/css/core.dev.css' , false , $this->project_settings['framework_version'] , 'screen' );

				//Load Google Fonts
				$all_fonts = "";
				$i = 0;
				$font_found = false;
				foreach ( $this->fonts as $font_name => $font_css ) 
				{
					$font_found = false;
					foreach ( $this->not_google_fonts as $google_font) 
					{
						if( $google_font == $font_name ) $font_found = true;
					}
					if( !$font_found )
					{
						$i += 1;
						$all_fonts .= $font_name."|";
					}
					
				}
				if( $i != 0 )
				{
					wp_enqueue_style( 'codetemp-core-admin-google-fonts' , 'http://fonts.googleapis.com/css?family='.$all_fonts );
				}

				// Color picker style
				wp_enqueue_style( 'codetemp-core-admin-colorpicker-style' , $this->project_settings['plugin_url'] . 'core/js/colorpicker/css/colorpicker.css' , false , $this->project_settings['framework_version'] , 'screen' );
				
				// Font-Awesome Icons Load 
				wp_enqueue_style( 'codetemp-core-admin-font_awesome' , $this->project_settings['plugin_url'] . 'core/css/codetempIcons/style.css' , false , $this->project_settings['framework_version'] , 'screen' );
				echo '
					<!--[if IE 7]>
					  <link rel="stylesheet" href="' . $this->project_settings['plugin_url'] . 'core/css/codetempIcons/ie7/ie7.css">
					<![endif]-->
				';
			}
		}



		/**
		 *
		 *	Admin : Load JS
		 *
		 */
		public function core_admin_js( $hook )
		{
			if( 'toplevel_page_CTF_suppa_menu' == $hook or 
				'suppa-menu_page_CTF_suppa_menu_export_import' == $hook )
			{
				// WP 3.5+ 
				// Enqueue Media uploader scripts and environment [ wp_enqueue_media() ].
				// Strongly suggest to use this function on the admin_enqueue_scripts action hook. Using it on admin_init hook breaks it
				// How To : http://stackoverflow.com/questions/13847714/wordpress-3-5-custom-media-upload-for-your-theme-options
				// Don't Foooooooooooooooooorget to  array('jquery' , 'media-upload' , 'thickbox')  to the enqueue
				wp_enqueue_media();
				
				wp_enqueue_script( 'codetemp-core-admin-settings' , $this->project_settings['plugin_url'] . 'core/js/core.dev.js' , array('jquery' , 'media-upload' , 'thickbox' , 'jquery-ui-core' , 'jquery-ui-draggable' , 'jquery-ui-droppable' , 'jquery-ui-sortable' ) , $this->project_settings['framework_version'] , false );
				wp_enqueue_script( 'codetemp-core-admin-ace-script' , $this->project_settings['plugin_url']  .'core/js/ace/ace.js' , array( 'codetemp-core-admin-settings' ) , $this->project_settings['framework_version'] , false );
				wp_enqueue_script( 'codetemp-core-admin-colorpicker-script' , $this->project_settings['plugin_url'] . 'core/js/colorpicker/js/colorpicker.js' , array( 'codetemp-core-admin-settings' ) , $this->project_settings['framework_version'] , false );
				wp_localize_script( 'codetemp-core-admin-settings', 'codetemp_groups', $this->project_settings['groups'] );	
			}
		}


		/* 
		 * 
		 * Get full url
		 * http://snipplr.com/view.php?codeview&id=2734 
		 *
		 */
		public function get_current_full_url()
		{
			$s = empty($_SERVER["HTTPS"]) ? '' : ($_SERVER["HTTPS"] == "on") ? "s" : "";
			$protocol = substr(strtolower($_SERVER["SERVER_PROTOCOL"]), 0, strpos(strtolower($_SERVER["SERVER_PROTOCOL"]), "/")) . $s;
			$port = ($_SERVER["SERVER_PORT"] == "80") ? "" : (":".$_SERVER["SERVER_PORT"]);
			return $protocol . "://" . $_SERVER['SERVER_NAME'] . $port . $_SERVER['REQUEST_URI'];
		}



		/**
		 *
		 *	Get HTML Header
		 *
		 */
		public function get_html_header( $header_desc = "" , $html_id = "" )
		{
			$html = '<form id="codetemp_form" >

						<span class="codetemp_ajax_response" style="display:none;">
							<img src="' . $this->project_settings['plugin_url'] . 'core/img/ajax-loader.gif" alt="" />
							<span></span>
							<div class="clearfix"></div>
						</span>

						<input type="hidden" name="nonce" id="nonce" value="' . wp_create_nonce( $this->project_settings['plugin_id'] ) . '"/>
						<input type="hidden" name="action" id="action" value="' . $this->project_settings['plugin_id'] . '_update_options' . '" />
						
						<input type="hidden" name="codetemp_group_id" id="codetemp_group_id" value="0" />
						<input type="hidden" name="codetemp_option_id" id="codetemp_option_id" value="menu-title" />
						<input type="hidden" name="codetemp_plugin_url" id="codetemp_plugin_url" value="' . $this->project_settings['plugin_url'] . '" />

					 <div class="codetemp_settings_container" id="'.$html_id.'" >';

			// Header
			$html .= '
						<div class="codetemp_header" >
							<img src="' . $this->project_settings['plugin_url'] . 'core/img/codetemp.png" alt="" class="codetemp_header_logo fl" />
							<h3 class="fl">Code Templates</h3>
							<span class="codetemp_header_desc fr" >'.$header_desc.'</span>
							<div class="clearfix"></div>
						</div><!--codetemp_header-->
					
						<div class="codetemp_bread">
						
							<a href="' . $this->project_settings['guide'] . '" class="fl">
								<img src="' . $this->project_settings['plugin_url'] . 'core/img/rounded_guide.png" alt="" class="fl" />
								<span class="fl" >'.__( 'Guide' , 'suppa_menu' ).'</span>
							</a>

							'.$this->get_html_button( 'update_all' , __( 'Save Settings' , 'suppa_menu' ) , 'fr' ).'

							<div class="clearfix"></div>
						
						</div><!--codetemp_bread-->';
					
			return $html;
		}



		/**
		 *
		 *	Get HTML Footer
		 *
		 */
		public function get_html_footer()
		{

			$html ='	<div class="codetemp_bread">'
							.$this->get_html_button( 'update_all' , __( 'Save Settings' , 'suppa_menu' ) , 'fr' )
							.$this->get_html_button( 'reset_all' , __( 'Reset Settings' , 'suppa_menu' ) , 'fl' ).
							'<div class="clearfix"></div>
						</div><!--codetemp_bread-->';

			$html .=' 	<div class="codetemp_footer">
						
							<ul class="codetemp_footer_social fr">
								<li><a href="https://twitter.com/codetemplates" title="Follow on Twitter" ><img src="' . $this->project_settings['plugin_url'] . 'core/img/rounded_twitter.png" alt="Follow on facebook" width="24" height="24" /></a></li>
							</ul>
						
							<span>'.date('Y').' &copy; Build By CTFramework , <a href="http://codetemp.com">Codetemp.com</a></span>
					
							<div class="clearfix" ></div>
						</div>
						</div><!--codetemp_settings_container-->
					</form>
					';

			return $html;
		}



		/**
		 *
		 *	Get HTML NAV (Main) 
		 * 
		 * @param $nav Array
		 *
		 */
		public function get_html_nav( $nav = array() )
		{
			$html = '
						<!-- Main NAV -->
						<ul class="codetemp_main_nav fl"> ';

			$i = 0;
			foreach ( $nav as $key => $value ) 
			{
				$i++;
				$isSelected = $i == 1 ? ' class="selected" ' : '';
				$html .= '<li '.$isSelected.'>
								<a href="#codetemp_page_'.$i.'" >'.$key.'</a>
								';
				$j = 0;
				$value = array_filter($value);
				if( !empty($value) ) $html .= '<ul>';

					foreach ( $value as $key_2 )
					{
						$j++;
						$html .= '<li><a href="#codetemp_page_'.$i.'_'.$j.'" >'.$key_2.'</a></li>';
					}
				
				if( !empty($value) ) $html .= '</ul>';

				$html .= '
						</li>';
			}
			$html .='	</ul><!--codetemp_main_nav-->';

			return $html;
		}


		/**
		 *
		 *	Get HTML Buttons 
		 * 
		 * @param $type String ( update_all , reset_all , delete_group , delete_option )
		 *
		 */
		public function get_html_button( $type = 'update_all' , $text = 'Save All Settings' , $special_class = '' , $id ='' )
		{
			return  '<button class="codetemp_button codetemp_button_'.$type.' '.$special_class.'" id="'.$id.'" >'.$text.'</button>';	}


	}// end class

}// end if