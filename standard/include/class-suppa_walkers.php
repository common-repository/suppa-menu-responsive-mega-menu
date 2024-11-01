<?php 

/**
 * This file holds various classes and methods necessary to edit the wordpress menu.
 *
 * @package 	CTFramework
 * @author		Sabri Taieb ( codezag )
 * @copyright	Copyright (c) Sabri Taieb
 * @link		http://codetemp.com
 * @since		Version 1.0
 *
 */

/**
 * This class contains various methods necessary to create mega menus in the backend
 * @package CTFramework
 * 
 */
if( !class_exists( 'suppa_walkers' ) )
{
	class suppa_walkers
	{

		static $thumbnail_wdith;
		static $thumbnail_height;
		static $image_resize;

		/**
		 * Constructor
		 * @package CTFramework
		 *
		 */
		public function __construct( $thumbnail_wdith = '300px' , $thumbnail_height='150px' , $image_resize )
		{
			/** Variables **/
			self::$thumbnail_wdith = $thumbnail_wdith;
			self::$thumbnail_height = $thumbnail_height;
			self::$image_resize = $image_resize;

			/** Load style & javascript to admin : nav-menus.php only **/
			add_action('admin_menu', array($this,'load_menus_css') , 9);
			add_action( 'admin_print_styles-nav-menus.php', array( $this , 'load_menus_js' ) , 3000 ); 

			/** Replace the selected menu args **/
			add_filter( 'wp_nav_menu_args', array( $this,'replace_args'), 3000);

			/** add new options to the walker **/
			add_filter( 'wp_edit_nav_menu_walker', array( $this,'replace_backend_walker') , 3000 );

			/** save suppa menu new options **/
			add_action( 'wp_update_nav_menu_item', array( $this,'update_menu'), 101, 3);

			/** Add WP Edior & Font Awesome Widgets on the Footer **/
			add_action( 'admin_footer', array( $this , 'add_widgets' ) );

			/** Ajax : Save Menu Location **/
			add_action( 'wp_ajax_suppamenu_save_menu_location' , array( $this , 'save_menu_location' ) ); 
		
			add_action( 'admin_head', array( $this , 'add_accordion_metabox' ) );
		}


		/**
		 *
		 * Add JS & CSS only on nav-menus.php
		 * @package CTFramework
		 *
		 */
		function load_menus_css( )
		{
			if( basename( $_SERVER['PHP_SELF'] ) == "nav-menus.php" )
			{
				wp_enqueue_style ( 'suppa_menu_admin_menu_css', plugins_url('../css/' , __FILE__ ). 'suppa_admin_menus.css');
				wp_enqueue_style ( 'suppa_menu_admin_fontAwesome', plugins_url('../css/fontAwesome/' , __FILE__ ). 'style.css');
			}
		}
		function load_menus_js( ) 
		{
			if( basename( $_SERVER['PHP_SELF'] ) == "nav-menus.php" )
			{
				// WP 3.5+ 
				// Enqueue Media uploader scripts and environment [ wp_enqueue_media() ].
				// Strongly suggest to use this function on the admin_enqueue_scripts action hook. Using it on admin_init hook breaks it
				// How To : http://stackoverflow.com/questions/13847714/wordpress-3-5-custom-media-upload-for-your-theme-options
				// Don't Foooooooooooooooooorget to  array('jquery' , 'media-upload' , 'thickbox')  to the enqueue
				wp_enqueue_media();
				wp_enqueue_script( 'suppa_menu_admin_js' , plugins_url('../js/'  , __FILE__ ).'suppa_admin.js',  array('jquery' , 'media-upload' , 'thickbox' , 'jquery-ui-core' , 'jquery-ui-draggable' , 'jquery-ui-droppable' , 'jquery-ui-sortable' ), '1.0.0', true );
				wp_enqueue_script( 'suppa_menu_admin_tinymce_js' , plugins_url('../js/tinymce/'  , __FILE__ ).'tinymce.min.js',  array('suppa_menu_admin_js' ), false, true );	
			}
		}

		/**
		 *
		 * Add Select Location Meta Box
		 * @package CTFramework
		 *
		 */
		function add_accordion_metabox()
		{ 
			add_meta_box( 'nav-menu-theme-suppa-location', __( 'Select Menu Location' , 'suppa_menu' ), array( $this , 'display_select_location_meta_box' ) , 'nav-menus', 'side', 'high' );
		}


		/**
		 *
		 * Select Location Meta Box Render
		 * @package CTFramework
		 *
		 */
		function display_select_location_meta_box()
		{
			// Get Settings
			$settings = array();

			// Add Accordion Menu Locations
			$menu_locations = get_registered_nav_menus();
			$menus = get_terms('nav_menu');

			if( get_option('suppa_menu_settings') )
			{
				$settings = get_option('suppa_menu_settings');
			}

			echo '
			<p>
				<p>Select Location</p>
				<select id="suppa_menu_location_selected">
					<option value="none">'.__('Select Location','suppa_menu').'</option> 
				';
					foreach ($menu_locations as $key => $value)
					{
						if( $settings['location'] == $key )
						{
							echo '<option value="'.$key.'" selected="selected" >'.$value.'</option>'; 
						}
						else
						{
							echo '<option value="'.$key.'" >'.$value.'</option>'; 
						}
					}
			echo '
				</select>
			</p>			
			<p>
				<span>
					<input type="submit" class="button-primary" value="Save" id="admin_suppa_save_menu_location">
					<input type="hidden" value="'.wp_create_nonce("suppa_menu_location_nonce").'" id="admin_suppa_save_menu_location_nonce">

				</span>
			</p>
			<br/><br/>
			';

			/*echo '
			<p style="display:none;">
				<p>Export/Import Menu Items</p>
				<textarea id="suppa_import_export_menu_items"></textarea>
				<button class="button" id="suppa_export_menu_items" >Export</button>&nbsp;&nbsp;&nbsp;<button class="button" id="suppa_import_menu_items" >Import</button>
			</p>';*/

		}


		/**
		 *
		 * Add WP Edior & Font Awesome Widgets on the Footer
		 * @package CTFramework
		 *
		 */
		function add_widgets() 
		{
			global $fontAwesome;

			if( basename( $_SERVER['PHP_SELF'] ) == "nav-menus.php" )
			{
				// Add Widgets
				echo '
				<input type="hidden" id="admin_suppa_plugin_url" value="'.plugins_url( '../js/tinymce/' , __FILE__ ).'" />
				<div class="era_admin_widgets_container" >
					<div class="era_admin_widget_box suppa_wp_editor_container" >
						
						<div class="era_admin_widget_box_header">
							<span>WP Editor</span>
							<a>x</a>
						</div>

						<div class="era_admin_widget_box_inside" >

							';
							
							wp_editor( '', 'suppa_wp_editor_the_editor', $settings = array() );
							
							echo '							
							<div class="admin_suppa_clearfix"></div>
						</div>

						<div class="era_admin_widget_box_footer">
							<button class="era_admin_widgets_container_button admin_suppa_getContent_button">Add</button>
						</div>

					</div>
					';

					

				echo '
					<div class="era_admin_widget_box suppa_fontAwesome_container" >
						
						<div class="era_admin_widget_box_header">
							<span>Select an Icon</span>
							<a>x</a>
						</div>

						<div class="era_admin_widget_box_inside" >';

						foreach ( $fontAwesome as $icon ) 
						{
							echo '
								<span class="admin_suppa_fontAwesome_icon_box">
									<span aria-hidden="true" class="'.$icon.'"></span>
								</span>
							';

						}

				echo '
							<div class="admin_suppa_clearfix"></div>
						</div>

						<div class="era_admin_widget_box_footer">
							<button class="era_admin_widgets_container_button admin_suppa_addIcon_button">Add</button>
						</div>

					</div>
				</div>';
			}
		}


		/**
		 *
		 * Ajax : Save Menu Location
		 * @package CTFramework
		 *
		 */
		function save_menu_location()
		{
			check_ajax_referer( 'suppa_menu_location_nonce', 'nonce' );
			
			$location 	= $_POST['location'];

			if( get_option('suppa_menu_settings') )
			{
				update_option('suppa_menu_settings', array( 'location' => $location ) );
			}
			else 
			{
				add_option( 'suppa_menu_settings', array( 'location' => $location ) );
			}

			die( "Location Saved" );
		}


		/**
		 *
		 * Replace the selected menu args
		 * @package CTFramework
		 *
		 */
		function replace_args($args){
							
			if( get_option('suppa_menu_settings') )
			{
				$settings 	= get_option('suppa_menu_settings');
				$location 	= $settings['location'];
				//$menu_id 	= $settings['menu'];

				if( $args['theme_location'] == $location )
				{
					if( class_exists('suppa_menu_walker') )
					{
						$args['walker'] 				= new suppa_menu_walker();
						$args['container_class'] 		= ' suppaMenu_wrap';
						$args['menu_class']				= ' suppaMenu';
						$args['items_wrap']				= '<div id="%1$s" class="%2$s">%3$s</div>';
						$args['depth']					= 4;
					}
				}
			}

			/*
			echo '<pre>';
				print_r( $args );
			echo '<pre>';
			*/

			return $args;
		}


		/**
		 *
		 * Tells wordpress to use our backend walker instead of the default one
		 * @package CTFramework
		 *
		 */
		function replace_backend_walker($name)
		{
			return 'suppa_menu_backend_walker';
		}



		/*
		 * Save and Update the Custom Navigation Menu Item Properties by checking all $_POST vars with the name of $check
		 * @param int $menu_id
		 * @param int $menu_item_db
		 */
		function update_menu($menu_id, $menu_item_db)
		{
			$all_keys = array( 
								'menu_type' ,
								'dropdown_width',
								'logo_image',
								'logo_image_retina',
								'links_fullwidth',
								'links_width',
								'links_align',
								'links_column_width',
								'html_fullwidth',
								'html_width',
								'html_align',
								'posts_thumb_width',
								'posts_thumb_height',
								'html_content',
								'link_position',
								'link_icon_only',
								'link_icon_type',
								'link_icon_image',
								'link_icon_image_hover',
								'link_icon_image',
								'link_icon_fontawesome',
								'link_icon_fontawesome_size',
								'posts_taxonomy',
								'posts_category',
								'posts_number',
								'search_text',
								'logo_image_height',
								'logo_image_width',
								'link_icon_image_height',
								'link_icon_image_width'

							);

			foreach ( $all_keys as $key )
			{
				if(!isset($_POST['menu-item-suppa-'.$key][$menu_item_db]))
				{
					$_POST['menu-item-suppa-'.$key][$menu_item_db] = "";
				}

				$value = $_POST['menu-item-suppa-'.$key][$menu_item_db];
				update_post_meta( $menu_item_db, '_menu-item-suppa-'.$key, $value );
			}
		}
	}
}



if( !class_exists( 'suppa_menu_walker' ) )
{

	/**
	 * This walker is for the frontend
	 */
	class suppa_menu_walker extends Walker {
		/**
		 * @see Walker::$tree_type
		 * @var string
		 */
		var $tree_type = array( 'post_type', 'taxonomy', 'custom' );

		/**
		 * @see Walker::$db_fields
		 * @todo Decouple this.
		 * @var array
		 */
		var $db_fields = array( 'parent' => 'menu_item_parent', 'id' => 'db_id' );

		/**
		 * @var string $menu_type
		 */
		var $menu_type = '';

		/**
		 * @var string $menu_key
		 */
		var $menu_key = '_menu-item-suppa-';

		/**
		 * @var int $top_level_counter
		 */
		var $top_level_counter = 0;

		/**
		 * @var int $top_level_counter
		 */
		var $dropdown_first_level_conuter = 0;

		/**
		 * @var int $top_level_counter
		 */
		var $dropdown_second_level_conuter = 0;


		/**
		 * @var int $column
		 */
		var $column = 0;

		
		/**
		 * @var string $dropdown_width
		 */
		var $dropdown_width = "180px";


		/**
		 * @var string $dropdown_position
		 */
		var $dropdown_position = "left";


		/**
		 * @var string $dropdown_position
		 */
		var $links_column_width = "180px";


		/**
		 * @see Walker::start_lvl()
		 *
		 * @param string $output Passed by reference. Used to append additional content.
		 * @param int $depth Depth of page. Used for padding.
		 */
		function start_lvl(&$output, $depth = 0, $args = array()) 
		{
			// DropDown
			if( $this->menu_type == 'dropdown' )
			{
				if( $depth == 0 )
				{
					$output = str_replace("<span class=\"suppa_ar_arrow_down_".$this->top_level_counter."\"></span>", '<span class="era_suppa_arrow_box ctf_suppa_fa_box_top_arrow"><span aria-hidden="true" class="suppa-caret-down"></span></span>' , $output );
				}

				$css_left = '0px';
				if( $depth != 0 )
				{
					$css_left = $this->dropdown_width;
					
				}

				if( $depth == 1 )
				{
					$output = str_replace("<span class=\"suppa_ar_arrow_right_".$this->dropdown_first_level_conuter.'_'.$depth."\"></span>", '<span class="era_suppa_arrow_box"><span aria-hidden="true" class="suppa-caret-right"></span></span>' , $output );
				}

				if( $depth == 2 )
				{
					$output = str_replace("<span class=\"suppa_ar_arrow_right_".$this->dropdown_second_level_conuter.'_'.$depth."\"></span>", '<span class="era_suppa_arrow_box"><span aria-hidden="true" class="suppa-caret-right"></span></span>' , $output );
				}



				$output .= '<div class="suppa_submenu suppa_submenu_'.$depth.'" style="width:'.$this->dropdown_width.';'.$this->dropdown_position.':'.$css_left.';" >';
			}

		}

		/**
		 * @see Walker::end_lvl()
		 *
		 * @param string $output Passed by reference. Used to append additional content.
		 * @param int $depth Depth of page. Used for padding.
		 */
		function end_lvl(&$output, $depth = 0, $args = array()) 
		{	

			if( $this->menu_type == 'dropdown' )
			{
				$output .= '</div>';
			}
		}

		/**
		 * @see Walker::start_el()
		 *
		 * @param string $output Passed by reference. Used to append additional content.
		 * @param object $item Menu item data object.
		 * @param int $depth Depth of menu item. Used for padding.
		 * @param int $current_page Menu item ID.
		 * @param object $args
		 */
		function start_el(&$output, $item, $depth = 0, $args = array(), $current_object_id = 0) {
			global $wp_query;

			// Link Attributes
			$attributes  = ! empty( $item->attr_title ) ? ' title="'  . esc_attr( $item->attr_title ) .'"' : '';
			$attributes .= ! empty( $item->target )     ? ' target="' . esc_attr( $item->target     ) .'"' : '';
			$attributes .= ! empty( $item->xfn )        ? ' rel="'    . esc_attr( $item->xfn        ) .'"' : '';
			$attributes .= ! empty( $item->url )        ? ' href="'   . esc_attr( $item->url        ) .'"' : '';
			
			// Link Icon
			$icon_type 	= get_post_meta( $item->ID, $this->menu_key.'link_icon_type', true);
			$link_title	= $item->title;
			$icon 		= get_post_meta( $item->ID, $this->menu_key.'link_icon_image', true);
			$icon_hover = get_post_meta( $item->ID, $this->menu_key.'link_icon_image_hover', true);
			$icon_only 	= get_post_meta( $item->ID, $this->menu_key.'link_icon_only', true);
			$FA_icon 	= get_post_meta( $item->ID, $this->menu_key.'link_icon_fontawesome', true);
			$link_html 	= "";

			// Link Classes
			$class_names = '';
			$classes = empty( $item->classes ) ? array() : (array) $item->classes;
			$class_names = join( ' ', apply_filters( 'nav_menu_css_class', array_filter( $classes ), $item ) );
			$class_names = esc_attr( $class_names );

			// Item Description
			$description  = ! empty( $item->description ) ? '<br/><span class="suppa_item_desc">'.$item->description.'</span>' : '';

			// Item Icon
			if( $icon_type == "upload" )
			{
				if( $icon != "" )
				{
					$check_retina_icon = ( $icon_hover != "" ) ? $icon_hover : $icon;

					// If Only Icon , no Link Title
					if( $icon_only == "on" )
					{
						$link_html = '<img class="suppa_upload_img suppa_UP_icon_only" src="'.$icon.'" alt="'.$link_title.'" data-icon="'.$icon.'" data-retina="'.$check_retina_icon.'" >';

					}
					else
					{
						$link_html = '<img class="suppa_upload_img suppa_UP_icon" src="'.$icon.'" alt="'.$link_title.'" data-icon="'.$icon.'" data-retina="'.$check_retina_icon.'" ><span class="suppa_item_title">'.$link_title.'</span>';
					}

				}
				else
				{
					$link_html = '<span class="suppa_item_title">'.$link_title.'</span>';
				}
			}
			else if( $icon_type == "fontawesome" )
			{
				if( $FA_icon != "" )
				{
					// If Only Icon , no Link Title
					if( $icon_only == "on" )
					{
						$link_html = '<span class="ctf_suppa_fa_box suppa_FA_icon_only"><span aria-hidden="true" class="'.$FA_icon.'" ></span></span>';

					}
					else
					{
						$link_html = '<span class="ctf_suppa_fa_box suppa_FA_icon"><span aria-hidden="true" class="'.$FA_icon.'" ></span></span><span class="suppa_item_title">'.$link_title.'</span>';
					}
				}
				else
				{
					$link_html = '<span class="suppa_item_title">'.$link_title.'</span>';
				}
			}
			else
			{
					$link_html = '<span class="suppa_item_title">'.$link_title.'</span>';
			}
			
			//$link_html .= $description;

			// If Level 0
			if( $depth === 0 )
			{
				$this->top_level_counter += 1;
				$this->menu_type = get_post_meta( $item->ID, $this->menu_key.'menu_type', true);
				
				$this_item_position = get_post_meta( $item->ID, $this->menu_key.'link_position', true);
				$this_item_position_css = ' float:left; ';
				$this_item_position_class = ' suppa_menu_position_'.$this_item_position.' ';
				$class_names .= $this_item_position_class;

				if( $this_item_position == "right" )
				{
					$this_item_position_css = ' float:right !important; ';
				}


				// Dropdown
				if( 'dropdown' == $this->menu_type )
				{	
					$this->dropdown_width = get_post_meta( $item->ID, $this->menu_key.'dropdown_width', true);
					$this->dropdown_position = get_post_meta( $item->ID, $this->menu_key.'link_position', true);
					$item_output = '<div style="'.$this_item_position_css.'" class=" '.$class_names.' suppa_menu suppa_menu_dropdown suppa_menu_'.$this->top_level_counter.'" ><a '.$attributes.' >'.$link_html.'<span class="suppa_ar_arrow_down_'.$this->top_level_counter.'"></span></a>';
					$output .= apply_filters( 'walker_nav_menu_start_el', $item_output, $item, $depth, $args );
				}

				// Logo
				else if( 'logo' == $this->menu_type )
				{
					$logo 			= get_post_meta( $item->ID, $this->menu_key.'logo_image', true);
					$logo_retina 	= get_post_meta( $item->ID, $this->menu_key.'logo_image_retina', true);
					$logo_height 	= get_post_meta( $item->ID, $this->menu_key.'logo_image_height', true);
					$logo_width 	= get_post_meta( $item->ID, $this->menu_key.'logo_image_width', true);

					$link_html = "";

					if( $icon_type == "upload" )
					{
						if( isset( $icon ) and $icon != "" )
						{
							if( isset( $icon_hover ) and $icon_hover !="" )
							{
								if( isset( $icon_only ) and $icon_only == "on" )
								{
									$link_html = '<img src="'.$icon.'" alt="'.$link_title.'" data-icon="'.$icon.'" data-hover_icon="'.$icon_hover.'" >';
								}
							}
							else
							{
								if( isset( $icon_only ) and $icon_only == "on" )
								{
									$link_html = '<img src="'.$icon.'" alt="'.$link_title.'" data-icon="'.$icon.'" data-hover_icon="none" >';
								}
							}	
						}
					}
					else
					{
						if( isset( $FA_icon ) and $FA_icon != "" )
						{
							if( isset( $icon_only ) and $icon_only == "on" )
							{
								$link_html = '<span class="ctf_suppa_fa_box"><span aria-hidden="true" class="'.$FA_icon.'"></span></span>';
							}
						}
					}

					if( $link_html == "" )
					{
						$link_html = '<img src="'.$logo.'" alt="" data-old="'.$logo.'" data-new="'.$logo_retina.'" style="width:'.$logo_width.';height:'.$logo_height.';" />';
					}

					$output .= '<div style="'.$this_item_position_css.'" class="suppa_menu suppa_menu_logo suppa_menu_'.$this->top_level_counter.'">';	
					$output .= '<a '.$attributes.' >'.$link_html.'</a>';	
					$output .= '</div><!--Suppa Logo-->';			
				}

			}


			// Dropdown 
			if( 'dropdown' == $this->menu_type )
			{
				if( $depth == 1 )
				{
					$this->dropdown_first_level_conuter += 1;
					$item_output = '<div class="suppa_dropdown_item_container"><a class="'.$class_names.'" '.$attributes.' >'.$link_html.'<span class="suppa_ar_arrow_right_'.$this->dropdown_first_level_conuter.'_'.$depth.'"></span></a> ';
					$output .= apply_filters( 'walker_nav_menu_start_el', $item_output, $item, $depth, $args );
				}
				else if( $depth == 2 )
				{
					$this->dropdown_second_level_conuter += 1;
					$item_output = '<div class="suppa_dropdown_item_container"><a class="'.$class_names.'" '.$attributes.' >'.$link_html.'<span class="suppa_ar_arrow_right_'.$this->dropdown_second_level_conuter.'_'.$depth.'"></span></a> ';
					$output .= apply_filters( 'walker_nav_menu_start_el', $item_output, $item, $depth, $args );
				}	
				else if( $depth == 3 )
				{
					$item_output = '<div class="suppa_dropdown_item_container"><a class="'.$class_names.'" '.$attributes.' >'.$link_html.'</span></a> ';
					$output .= apply_filters( 'walker_nav_menu_start_el', $item_output, $item, $depth, $args );
				}
			}

		}

		/**
		 * @see Walker::end_el()
		 *
		 * @param string $output Passed by reference. Used to append additional content.
		 * @param object $item Page data object. Not used.
		 * @param int $depth Depth of page. Not Used.
		 */
		function end_el(&$output, $object, $depth = 0, $args = array() ) 
		{
			// Dropdown 
			if( 'dropdown' == $this->menu_type )
			{
				$output .= '</div>';
			}
			// Links
			if( 'links' == $this->menu_type )
			{
				if( $depth === 0 )
				{
					$output .= '</div></div><!--suppa_submenu_columns_wrap-->';
				}
				else if( $depth === 1 )
				{
					$output .= '</div><!--end column-->';
				}	
			}
		}// End Func


	}// End Class
}


if( !class_exists( 'suppa_menu_backend_walker' ) )
{
/**
 * @package CTFramework
 * @since 1.0
 * @uses Walker_Nav_Menu
 */
	class suppa_menu_backend_walker extends Walker_Nav_Menu
	{
		/**
		 * @see Walker_Nav_Menu::start_lvl()
		 * @since 3.0.0
		 *
		 * @param string $output Passed by reference.
		 * @param int $depth Depth of page.
		 */
		function start_lvl(&$output, $depth = 0, $args = array() ) {}

		/**
		 * @see Walker_Nav_Menu::end_lvl()
		 * @since 3.0.0
		 *
		 * @param string $output Passed by reference.
		 * @param int $depth Depth of page.
		 */
		function end_lvl(&$output, $depth = 0, $args = array() ) {}

		/**
		 * @see Walker::start_el()
		 * @since 3.0.0
		 *
		 * @param string $output Passed by reference. Used to append additional content.
		 * @param object $item Menu item data object.
		 * @param int $depth Depth of menu item. Used for padding.
		 * @param int $current_page Menu item ID.
		 * @param object $args
		 */
		function start_el(&$output, $item, $depth = 0, $args = array(), $id = 0) {
			global $_wp_nav_menu_max_depth;
			$_wp_nav_menu_max_depth = $depth > $_wp_nav_menu_max_depth ? $depth : $_wp_nav_menu_max_depth;

			$indent = ( $depth ) ? str_repeat( "\t", $depth ) : '';

			ob_start();
			$item_id = esc_attr( $item->ID );
			$removed_args = array(
				'action',
				'customlink-tab',
				'edit-menu-item',
				'menu-item',
				'page-tab',
				'_wpnonce',
			);

			$original_title = '';
			if ( 'taxonomy' == $item->type ) {
				$original_title = get_term_field( 'name', $item->object_id, $item->object, 'raw' );
			} elseif ( 'post_type' == $item->type ) {
				$original_object = get_post( $item->object_id );
				$original_title = $original_object->post_title;
			}

			$classes = array(
				'menu-item menu-item-depth-' . $depth,
				'menu-item-' . esc_attr( $item->object ),
				'menu-item-edit-' . ( ( isset( $_GET['edit-menu-item'] ) && $item_id == $_GET['edit-menu-item'] ) ? 'active' : 'inactive'),
			);

			$title = $item->title;

			if ( isset( $item->post_status ) && 'draft' == $item->post_status ) {
				$classes[] = 'pending';
				/* translators: %s: title of menu item in draft status */
				$title = sprintf( __('%s (Pending)','suppa_menu'), $item->title );
			}

			$title = empty( $item->label ) ? $title : $item->label;


			$depth_class = " suppa_menu_item ";

			?>

			<li  id="menu-item-<?php echo $item_id; ?>" class="<?php echo $depth_class; echo implode(' ', $classes ); ?>">

				<dl class="menu-item-bar">	
					
					<dt class="menu-item-handle" style="position:relative;" >
					
						<span class="item-title"><?php echo esc_html( $title ); ?></span>
						<span class="item-controls">

							<?php 
								$menu_type 	= get_post_meta( $item->ID, '_menu-item-suppa-menu_type', true); 
								if( $menu_type == "" )
								{
									$menu_type = 'dropdown';
								}

							?>
							<span class="item-type item-type-default"><?php echo "( ".esc_html( $menu_type )." )"; ?></span>

							<a class="item-edit" id="edit-<?php echo $item_id; ?>" title="<?php _e('Edit Menu Item','suppa_menu'); ?>" href="<?php
								echo ( isset( $_GET['edit-menu-item'] ) && $item_id == $_GET['edit-menu-item'] ) ? admin_url( 'nav-menus.php' ) : add_query_arg( 'edit-menu-item', $item_id, remove_query_arg( $removed_args, admin_url( 'nav-menus.php#menu-item-settings-' . $item_id ) ) );
							?>"><?php _e( 'Edit Menu Item','suppa_menu' ); ?></a>
						</span>
					</dt>
				</dl>

				<div class="menu-item-settings" id="menu-item-settings-<?php echo $item_id; ?>">
					<?php if( 'custom' == $item->type ) : ?>
						<p class="field-url description description-wide">
							<label for="edit-menu-item-url-<?php echo $item_id; ?>">
								<?php _e( 'URL' ,'suppa_menu' ); ?><br />
								<input type="text" id="edit-menu-item-url-<?php echo $item_id; ?>" class="widefat code edit-menu-item-url" name="menu-item-url[<?php echo $item_id; ?>]" value="<?php echo esc_attr( $item->url ); ?>" />
							</label>
						</p>
					<?php endif; ?>
					<p class="description description-thin description-label avia_label_desc_on_active">
						<label for="edit-menu-item-title-<?php echo $item_id; ?>">
						<span class='avia_default_label'><?php _e( 'Navigation Label','suppa_menu' ); ?></span>
							<br />
							<input type="text" id="edit-menu-item-title-<?php echo $item_id; ?>" class="widefat edit-menu-item-title" name="menu-item-title[<?php echo $item_id; ?>]" value="<?php echo esc_attr( $item->title ); ?>" />
						</label>
					</p>
					<p class="description description-thin description-title">
						<label for="edit-menu-item-attr-title-<?php echo $item_id; ?>">
							<?php _e( 'Title Attribute','suppa_menu' ); ?><br />
							<input type="text" id="edit-menu-item-attr-title-<?php echo $item_id; ?>" class="widefat edit-menu-item-attr-title" name="menu-item-attr-title[<?php echo $item_id; ?>]" value="<?php echo esc_attr( $item->post_excerpt ); ?>" />
						</label>
					</p>
					<p class="field-link-target description description-thin">
						<label for="edit-menu-item-target-<?php echo $item_id; ?>">
							<?php _e( 'link Target','suppa_menu' ); ?><br />
							<select id="edit-menu-item-target-<?php echo $item_id; ?>" class="widefat edit-menu-item-target" name="menu-item-target[<?php echo $item_id; ?>]">
								<option value="" <?php selected( $item->target, ''); ?>><?php _e('Same window or tab','suppa_menu'); ?></option>
								<option value="_blank" <?php selected( $item->target, '_blank'); ?>><?php _e('New window or tab','suppa_menu'); ?></option>
							</select>
						</label>
					</p>
					<p class="field-css-classes description description-thin">
						<label for="edit-menu-item-classes-<?php echo $item_id; ?>">
							<?php _e( 'CSS Classes (optional)','suppa_menu' ); ?><br />
							<input type="text" id="edit-menu-item-classes-<?php echo $item_id; ?>" class="widefat code edit-menu-item-classes" name="menu-item-classes[<?php echo $item_id; ?>]" value="<?php echo esc_attr( implode(' ', $item->classes ) ); ?>" />
						</label>
					</p>
					<p class="field-xfn description description-thin">
						<label for="edit-menu-item-xfn-<?php echo $item_id; ?>">
							<?php _e( 'link Relationship (XFN)' ,'suppa_menu'); ?><br />
							<input type="text" id="edit-menu-item-xfn-<?php echo $item_id; ?>" class="widefat code edit-menu-item-xfn" name="menu-item-xfn[<?php echo $item_id; ?>]" value="<?php echo esc_attr( $item->xfn ); ?>" />
						</label>
					</p>
					<p class="field-description description description-wide">
						<label for="edit-menu-item-description-<?php echo $item_id; ?>">
							<?php _e( 'Description' ,'suppa_menu'); ?><br />
							<textarea id="edit-menu-item-description-<?php echo $item_id; ?>" class="widefat edit-menu-item-description" rows="3" cols="20" name="menu-item-description[<?php echo $item_id; ?>]"><?php echo esc_html( $item->post_content ); ?></textarea>
						</label>
					</p>

					<!-- *************** Suppa Options *************** -->
					<div class="admin_suppa_clearfix"></div>
					
					<br/>

					<div class='admin_suppa_options'>

						<!-- *************** new item *************** -->
						<div class="admin_suppa_box admin_suppa_box_menu_type" >
							
							<?php
								// Menu Type
								$title 	= __( 'Choose the menu type' , 'suppa_menu' );
								$key 	= "menu-item-suppa-menu_type";				

							?>

							<div class="admin_suppa_box_header">
								<span><?php echo $title; ?> :</span>
								<a>+</a>
							</div>


							<div class="admin_suppa_box_container">


								<!-- Select Menu Type -->

								<label for="edit-<?php echo $key.'-'.$item_id.'_2'; ?>">
									<input type="radio" value="logo" id="edit-<?php echo $key.'-'.$item_id; ?>" class="suppa_menu_type <?php echo $key; ?>" name="<?php echo $key . "[". $item_id ."]";?>" <?php if($menu_type=="logo") echo "checked"; ?> /> &nbsp;&nbsp; Use as Logo<br/> 
								</label>
								<label for="edit-<?php echo $key.'-'.$item_id.'_1'; ?>">
									<input type="radio" value="dropdown" id="edit-<?php echo $key.'-'.$item_id; ?>" class="suppa_menu_type <?php echo $key; ?>" name="<?php echo $key . "[". $item_id ."]";?>" <?php if($menu_type=="dropdown") echo "checked"; ?> /> &nbsp;&nbsp; Use as DropDown<br/> 
								</label>
								<br/><br/>

								<!-- Menu Type : DropDown -->
								<div <?php if( 'dropdown' != $menu_type) echo "style='display:none;'"; ?> class="admin_suppa_box_option_inside admin_suppa_box_option_inside_dropdown">

									<?php
										// Width
										$title 	= __( 'Submenu Width' , 'suppa_menu' );
										$key 	= "menu-item-suppa-dropdown_width";
										$value 	= get_post_meta( $item->ID, '_'.$key, true);

										if($value == "") $value = "180px";
									?>

									<span class="fl" ><?php echo $title; ?></span> 
									<input type="text" value="<?php echo $value; ?>" id="edit-<?php echo $key.'-'.$item_id; ?>" class="fr <?php echo $key; ?>" name="<?php echo $key . "[". $item_id ."]";?>" />

									<div class="admin_suppa_clearfix"></div>

								</div>

								<!-- Menu Type : Logo -->
								<div <?php if( 'logo' != $menu_type) echo "style='display:none;'"; ?> class="admin_suppa_box_option_inside admin_suppa_box_option_inside_logo">
									
									<?php
										// Logo
										$title 	= __( 'Logo' , 'suppa_menu' );
										$key 	= "menu-item-suppa-logo_image";
										$value 	= get_post_meta( $item->ID, '_'.$key, true);
									?>

									<span class="fl" ><?php echo $title; ?></span> 
									<div class="fr">
										<input type="text" value="<?php echo $value; ?>" id="edit-<?php echo $key.'-'.$item_id; ?>" class="admin_suppa_upload_input <?php echo $key; ?>" name="<?php echo $key . "[". $item_id ."]";?>" /> &nbsp;&nbsp; 
										<button class="button-primary admin_suppa_upload">Upload</button> <br/> 
									</div>

									<div class="admin_suppa_clearfix"></div>

									<br/>
									<?php
										// Logo
										$title 	= __( 'Logo ( Retina )' , 'suppa_menu' );
										$key 	= "menu-item-suppa-logo_image_retina";
										$value 	= get_post_meta( $item->ID, '_'.$key, true);
									?>

									<span class="fl" ><?php echo $title; ?></span> 
									<div class="fr">
										<input type="text" value="<?php echo $value; ?>" id="edit-<?php echo $key.'-'.$item_id; ?>" class="admin_suppa_upload_input <?php echo $key; ?>" name="<?php echo $key . "[". $item_id ."]";?>" /> &nbsp;&nbsp; 
										<button class="admin_suppa_upload button-primary">Upload</button> <br/> 
									</div>

									<div class="admin_suppa_clearfix"></div>

									<br/>
									<?php
										// Logo
										$title 	= __( 'Width ( px )' , 'suppa_menu' );
										$key 	= "menu-item-suppa-logo_image_width";
										$value 	= get_post_meta( $item->ID, '_'.$key, true);
									?>

									<span class="fl" ><?php echo $title; ?></span> 
									<div class="fr">
										<input type="text" value="<?php echo $value; ?>" id="edit-<?php echo $key.'-'.$item_id; ?>" class=" <?php echo $key; ?>" name="<?php echo $key . "[". $item_id ."]";?>" />
									</div>

									<div class="admin_suppa_clearfix"></div>


									<br/>
									<?php
										// Logo
										$title 	= __( 'Height ( px )' , 'suppa_menu' );
										$key 	= "menu-item-suppa-logo_image_height";
										$value 	= get_post_meta( $item->ID, '_'.$key, true);
									?>

									<span class="fl" ><?php echo $title; ?></span> 
									<div class="fr">
										<input type="text" value="<?php echo $value; ?>" id="edit-<?php echo $key.'-'.$item_id; ?>" class=" <?php echo $key; ?>" name="<?php echo $key . "[". $item_id ."]";?>" />
									</div>

									<div class="admin_suppa_clearfix"></div>
								</div>

							</div>
						</div>
						<!-- ***************  end item *************** -->


						<!-- *************** new item *************** -->
						<br/>
						<div class="admin_suppa_box admin_suppa_box_link_settings" >

							<div class="admin_suppa_box_header">
								<span>Link Settings :</span>
								<a>+</a>
							</div>
							<div class="admin_suppa_box_container admin_suppa_box_container_settings">

								<div id="menu-item-suppa-link_position_container" >
									<?php
										// Position
										$title 	= __( 'Position' , 'suppa_menu' );
										$key 	= "menu-item-suppa-link_position";
										$value 	= get_post_meta( $item->ID, '_'.$key, true);

										if($value == "") $value = "left";
									?>

									<?php echo $title; ?> 
									<select id="edit-<?php echo $key.'-'.$item_id; ?>" class=" <?php echo $key; ?>" name="<?php echo $key . "[". $item_id ."]";?>" >
										<option <?php if( 'left' == $value ) echo 'selected="selected"'; ?> >left</option>
										<option <?php if( 'right' == $value ) echo 'selected="selected"'; ?> >right</option>
									</select>

									<div class="admin_suppa_clearfix"></div>

								</div>

								<br/>

								<label for="edit-<?php echo $key.'-'.$item_id; ?>">
									<?php
										// Use icon only
										$title 	= __( 'Use icon only' , 'suppa_menu' );
										$key 	= "menu-item-suppa-link_icon_only";
										$value 	= get_post_meta( $item->ID, '_'.$key, true);

										if( $value == "" ){
											$value = "off";
										}
									?>
									<?php echo $title; ?>
									<input <?php if( $value == 'on' ) echo 'checked'; ?> type="checkbox" value="<?php echo $value; ?>" id="edit-<?php echo $key.'-'.$item_id; ?>" class="fr suppa_use_icon_only <?php echo $key; ?>" name="<?php echo $key . "[". $item_id ."]";?>" /> 
								
								</label>

									<br/><br/>

								<label for="edit-<?php echo $key.'-'.$item_id; ?>" >
									<?php
										// Upload or Font Awesome
										$key 	= "menu-item-suppa-link_icon_type";
										$value 	= get_post_meta( $item->ID, '_'.$key, true);

										if($value == "") $value = "icon_type";
										$icon_type = $value;
									?>
									<select id="edit-<?php echo $key.'-'.$item_id; ?>" class=" <?php echo $key; ?>" name="<?php echo $key . "[". $item_id ."]";?>"  >
										<option value="icon_type" <?php if( 'icon_type' == $value ) echo 'selected="selected"' ?> >Icon Type</option>										
										<option value="upload" <?php if( 'upload' == $value ) echo 'selected="selected"' ?> >Upload new icon</option>
										<option value="fontawesome" <?php if( 'fontawesome' == $value ) echo 'selected="selected"' ?> >Font Awesome icon</option>
									</select>

									<div class="admin_suppa_clearfix"></div>

								</label>

								<div <?php if( $icon_type != 'upload' ) echo 'style="display:none;"' ; ?> class="admin_suppa_box_option_inside admin_suppa_box_option_inside_icon_upload">

									<?php
										// Use icon only
										$title 	= __( 'Icon' , 'suppa_menu' );
										$key 	= "menu-item-suppa-link_icon_image";
										$value 	= get_post_meta( $item->ID, '_'.$key, true);

										if($value == "") $value = "";
										$uploaded_icon = $value;
									?>
									<?php echo $title; ?>
									<div>
										<input type="text" value="<?php echo $value; ?>" id="edit-<?php echo $key.'-'.$item_id; ?>" class="admin_suppa_upload_input <?php echo $key; ?>" name="<?php echo $key . "[". $item_id ."]";?>" /> 
										
										<button class="admin_suppa_upload button-primary fr">Upload</button> <br/> 
									</div>

									<div class="admin_suppa_clearfix"></div>

									<br/>
									<?php
										// Use icon only
										$title 	= __( 'Icon (Retina)' , 'suppa_menu' );
										$key 	= "menu-item-suppa-link_icon_image_hover";
										$value 	= get_post_meta( $item->ID, '_'.$key, true);

										if($value == "") $value = "";
									?>
									<?php echo $title; ?>
									<div>
										<input type="text" value="<?php echo $value; ?>" id="edit-<?php echo $key.'-'.$item_id; ?>" class="admin_suppa_upload_input <?php echo $key; ?>" name="<?php echo $key . "[". $item_id ."]";?>" /> 
										
										<button class="admin_suppa_upload button-primary fr">Upload</button> <br/> 
									</div>
									<div class="admin_suppa_clearfix"></div>

								</div>

								<div <?php if( $icon_type != 'fontawesome' ) echo 'style="display:none;"' ; ?> class="admin_suppa_box_option_inside admin_suppa_box_option_inside_icon_fontawesome">

									<?php
										// Use icon only
										$title 	= __( 'Icon' , 'suppa_menu' );
										$key 	= "menu-item-suppa-link_icon_fontawesome";
										$value 	= get_post_meta( $item->ID, '_'.$key, true);

										if($value == "") $value = "";
										$value_icon = $value;
									?>
									<?php echo $title; ?>

									<button id="<?php echo $item->ID; ?>" class="admin_suppa_selectIcon_button button-primary fr">Select</button> 
									<br/>
									<br/>
									<input type="hidden" value="<?php echo $value_icon; ?>" id="edit-<?php echo $key.'-'.$item_id; ?>" class="fr admin_suppa_fontAwesome_icon_hidden-<?php echo $item->ID; ?> <?php echo $key; ?>" name="<?php echo $key . "[". $item_id ."]";?>" /> 

									<div class="admin_suppa_clearfix"></div>
									<br/>

									<div class="admin_suppa_clearfix"></div>

									<br/>

									<span class="admin_suppa_fontAwesome_icon_box_preview admin_suppa_fontAwesome_icon_box_preview-<?php echo $item->ID; ?>">
										<span style="font-size:20px; ?>;" aria-hidden="true" class="<?php echo $value_icon; ?>"></span>
									</span>

									<br/><br/>

								</div>

							</div>
						</div>
						
						<!-- ***************  end item *************** -->


					</div>

					<!-- *************** end Suppa Options *************** -->





					<div class="menu-item-actions description-wide submitbox">
						<?php if( 'custom' != $item->type ) : ?>
							<p class="link-to-original">
								<?php printf( __('Original: %s','suppa_menu'), '<a href="' . esc_attr( $item->url ) . '">' . esc_html( $original_title ) . '</a>' ); ?>
							</p>
						<?php endif; ?>
						<a class="item-delete submitdelete deletion" id="delete-<?php echo $item_id; ?>" href="<?php
						echo wp_nonce_url(
							add_query_arg(
								array(
									'action' => 'delete-menu-item',
									'menu-item' => $item_id,
								),
								remove_query_arg($removed_args, admin_url( 'nav-menus.php' ) )
							),
							'delete-menu_item_' . $item_id
						); ?>"><?php _e('Remove','suppa_menu'); ?></a> <span class="meta-sep"> | </span> <a class="item-cancel submitcancel" id="cancel-<?php echo $item_id; ?>" href="<?php	echo add_query_arg( array('edit-menu-item' => $item_id, 'cancel' => time()), remove_query_arg( $removed_args, admin_url( 'nav-menus.php' ) ) );
							?>#menu-item-settings-<?php echo $item_id; ?>">Cancel</a>
					</div>

					<input class="menu-item-data-db-id" type="hidden" name="menu-item-db-id[<?php echo $item_id; ?>]" value="<?php echo $item_id; ?>" />
					<input class="menu-item-data-object-id" type="hidden" name="menu-item-object-id[<?php echo $item_id; ?>]" value="<?php echo esc_attr( $item->object_id ); ?>" />
					<input class="menu-item-data-object" type="hidden" name="menu-item-object[<?php echo $item_id; ?>]" value="<?php echo esc_attr( $item->object ); ?>" />
					<input class="menu-item-data-parent-id" type="hidden" name="menu-item-parent-id[<?php echo $item_id; ?>]" value="<?php echo esc_attr( $item->menu_item_parent ); ?>" />
					<input class="menu-item-data-position" type="hidden" name="menu-item-position[<?php echo $item_id; ?>]" value="<?php echo esc_attr( $item->menu_order ); ?>" />
					<input class="menu-item-data-type" type="hidden" name="menu-item-type[<?php echo $item_id; ?>]" value="<?php echo esc_attr( $item->type ); ?>" />
				</div><!-- .menu-item-settings-->
				<ul class="menu-item-transport"></ul>
			<?php
			$output .= ob_get_clean();
		}
	}


}