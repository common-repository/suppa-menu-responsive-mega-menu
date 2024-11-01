<?php
/**
 * Submenu Type Links Style
 *
 * @package 	CTFramework
 * @author		Sabri Taieb ( codezag )
 * @copyright	Copyright (c) Sabri Taieb
 * @link		http://codetemp.com
 * @since		Version 1.0
 *
 */

// Column Margin Right
$this->add_text_input(
	array(
		'group_id'			=> 'style', 		
		'option_id'			=> 'submenu_column_right_margin', 		
		'value'				=> '12px',
		'title'				=> __( 'Column Right Margin' , 'suppa_menu' ),
		'desc'				=> __( 'Set the column right margin (px)' , 'suppa_menu' )
	)
);

// Title Link Font
$this->add_font(
	array(
		'group_id'			=> 'style', 		
		'option_id'			=> 'submenu-links-title_link_font', 		
		'title'				=> __( 'Title Typography' , 'suppa_menu' ), 		
		'desc'				=> __( 'Set the title typography' , 'suppa_menu' ), 		 		
		'font_size'			=> 18,	// Font Size
		'font_type'			=> 'px',	// Font Size Type
		'font_family'		=> "'Arial', 'Verdana' sans-serif",	// Font Family
		'font_style'		=> 'normal',	// Font Style
		'font_color'		=> '#c9c9c9',	// Font Color
	)
);

// Title Link color ( Hover )
$this->add_colorpicker(
	array(
		'group_id'			=> 'style', 		
		'option_id'			=> 'submenu-links-title_link_color_hover', 		
		'value'				=> '#ffffff',
		'title'				=> __( 'Title Color ( Hover )' , 'suppa_menu' ),
		'desc'				=> __( 'Set the title color when you hover over' , 'suppa_menu' )
	)
);

// Title Bottom Border color
$this->add_colorpicker(
	array(
		'group_id'			=> 'style', 		
		'option_id'			=> 'submenu-links-title_bottom_border_color', 		
		'value'				=> '#1d3c4d',
		'title'				=> __( 'Title Bottom Border Color' , 'suppa_menu' ),
		'desc'				=> __( 'Set the title bottom border color' , 'suppa_menu' )
	)
);

$this->add_text_input(
	array(
		'group_id'			=> 'style', 		
		'option_id'			=> 'submenu-links-title_height', 		
		'value'				=> '40px',
		'title'				=> __( 'Title Height' , 'suppa_menu' ),
		'desc'				=> __( 'Set the title height' , 'suppa_menu' )
	)
);

// Title Padding 
echo 	'<div class="ctf_option_container ">
			<span class="ctf_option_title">'.__( 'Title Padding' , 'suppa_menu' ).'</span>';
			
			$this->add_text_input(
				array(
					'group_id'			=> 'style', 		
					'option_id'			=> 'submenu-links-title_padding_left', 		
					'value'				=> '0px', 		 				
					'class'				=> 'ctf_option_gradient'
				),
				'ctf_container_gradient'
			);

			$this->add_text_input(
				array(
					'group_id'			=> 'style', 		
					'option_id'			=> 'submenu-links-title_padding_right', 		
					'value'				=> '0px', 		
					'class'				=> 'ctf_option_gradient'
				),
				'ctf_container_gradient'
			);

			echo '<div class="clearfix"></div><span class="ctf_option_desc">'.__( 'Set the title padding ( Left , Right )' , 'suppa_menu' ).'</span>

		</div>';

echo '<br/><br/>';

// SubMenu Type Mega Links
// Links Font
$this->add_font(
	array(
		'group_id'			=> 'style', 		
		'option_id'			=> 'submenu-links-links_font', 		
		'title'				=> __( 'Links Typography' , 'suppa_menu' ), 		
		'desc'				=> __( 'Set the links typography' , 'suppa_menu' ), 		 		
		'font_size'			=> 14,	// Font Size
		'font_type'			=> 'px',	// Font Size Type
		'font_family'		=> "'Arial', 'Verdana' sans-serif",	// Font Family
		'font_style'		=> 'normal',	// Font Style
		'font_color'		=> '#c9c9c9',	// Font Color
	)
);

// Links color ( Hover )
$this->add_colorpicker(
	array(
		'group_id'			=> 'style', 		
		'option_id'			=> 'submenu-links-links_color_hover', 		
		'value'				=> '#ffffff',
		'title'				=> __( 'Links Color ( Hover )' , 'suppa_menu' ),
		'desc'				=> __( 'Set the Links color when you hover over' , 'suppa_menu' )
	)
);

$this->add_text_input(
	array(
		'group_id'			=> 'style', 		
		'option_id'			=> 'submenu-links-links_height', 		
		'value'				=> '35px',
		'title'				=> __( 'Links Height' , 'suppa_menu' ),
		'desc'				=> __( 'Set the title height' , 'suppa_menu' )
	)
);

// Links Padding 
echo 	'<div class="ctf_option_container ">
			<span class="ctf_option_title">'.__( 'Links Padding' , 'suppa_menu' ).'</span>';
			
			$this->add_text_input(
				array(
					'group_id'			=> 'style', 		
					'option_id'			=> 'submenu-links-links_padding_left', 		
					'value'				=> '5px', 		 				
					'class'				=> 'ctf_option_gradient'
				),
				'ctf_container_gradient'
			);

			$this->add_text_input(
				array(
					'group_id'			=> 'style', 		
					'option_id'			=> 'submenu-links-links_padding_right', 		
					'value'				=> '5px', 		
					'class'				=> 'ctf_option_gradient'
				),
				'ctf_container_gradient'
			);

			echo '<div class="clearfix"></div><span class="ctf_option_desc">'.__( 'Set the links padding ( Left , Right )' , 'suppa_menu' ).'</span>

		</div>';