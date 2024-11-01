<?php

/**
 * Top Level Links Style
 *
 * @package 	CTFramework
 * @author		Sabri Taieb ( codezag )
 * @copyright	Copyright (c) Sabri Taieb
 * @link		http://codetemp.com
 * @since		Version 1.0
 *
 */

/* Top Links Style */

$this->add_font(
	array(
		'group_id'			=> 'style', 		// Group to save this option on
		'option_id'			=> 'top_level_font', 		// Option ID
		'title'				=> __( 'Top Links Typography' , 'suppa_menu' ), 		// Title
		'desc'				=> __( 'Set the top links typography' , 'suppa_menu' ), 		// Description or Help
		'font_size'			=> 15,	// Font Size
		'font_type'			=> 'px',	// Font Size Type
		'font_family'		=> "'Arial', 'Verdana' sans-serif",	// Font Family
		'font_style'		=> 'normal',	// Font Style
		'font_color'		=> '#c9c9c9',	// Font Color
		'fetch'				=> 'yes',	// Fetch Database
	)
);

// Color ( Hover )
$this->add_colorpicker(
	array(
		'group_id'			=> 'style', 		
		'option_id'			=> 'top_level_links_color_hover', 		
		'value'				=> '#ffffff',
		'title'				=> __( 'Color ( Hover )' , 'suppa_menu' ),
		'desc'				=> __( 'Set the top links color when you hover over' , 'suppa_menu' )
	)
);

// Background ( Hover )
$this->add_colorpicker(
	array(
		'group_id'			=> 'style', 		
		'option_id'			=> 'top_level_bg_hover', 		
		'value'				=> '#03111c',
		'title'				=> __( 'Background ( Hover )' , 'suppa_menu' ),
		'desc'				=> __( 'Set the top links background color when you hover over' , 'suppa_menu' )
	)
);

// Padding
echo 	'<div class="ctf_option_container ">
			<span class="ctf_option_title">'.__( 'Padding' , 'suppa_menu' ).'</span>';
			
			$this->add_text_input(
				array(
					'group_id'			=> 'style', 		
					'option_id'			=> 'top_level_padding_left', 		
					'value'				=> '15px', 		 				
					'class'				=> 'ctf_option_gradient'
				),
				'ctf_container_gradient'
			);

			$this->add_text_input(
				array(
					'group_id'			=> 'style', 		
					'option_id'			=> 'top_level_padding_right', 		
					'value'				=> '25px', 		
					'class'				=> 'ctf_option_gradient'
				),
				'ctf_container_gradient'
			);

			echo '<div class="clearfix"></div><span class="ctf_option_desc">'.__( 'Set the padding ( Left, Right )' , 'suppa_menu' ).'</span>

		</div>';

// Top Link Border color
$this->add_colorpicker(
	array(
		'group_id'			=> 'style', 		
		'option_id'			=> 'top-links-border_color', 		
		'value'				=> 'transparent',
		'title'				=> __( 'Top Link Border color' , 'suppa_menu' ),
		'desc'				=> __( 'Set the top links border color' , 'suppa_menu' )
	)
);

// Arrow Style
echo 	'<div class="ctf_option_container ">
			<span class="ctf_option_title">'.__( 'Arrow Style' , 'suppa_menu' ).'</span>';
			
			$this->add_text_input(
				array(
					'group_id'			=> 'style', 		
					'option_id'			=> 'top-links-arrow_width', 		
					'value'				=> '14px', 		 				
				),
				'ctf_option_no_border'
			);

			$this->add_colorpicker(
				array(
					'group_id'			=> 'style', 		
					'option_id'			=> 'top-links-arrow_color', 		
					'value'				=> '#c9c9c9', 		 				
				),
				'ctf_option_no_border'
			);

			$this->add_colorpicker(
				array(
					'group_id'			=> 'style', 		
					'option_id'			=> 'top-links-arrow_color_hover', 		
					'value'				=> '#ffffff', 		 				
				),
				'ctf_option_no_border'
			);

			$this->add_text_input(
				array(
					'group_id'			=> 'style', 		
					'option_id'			=> 'top-links-arrow_position_right', 		
					'value'				=> '5px', 		 				
				),
				'ctf_option_no_border'
			);

			$this->add_text_input(
				array(
					'group_id'			=> 'style', 		
					'option_id'			=> 'top-links-arrow_position_top', 		
					'value'				=> '13px', 		 				
				),
				'ctf_option_no_border'
			);

			echo '<div class="clearfix"></div><span class="ctf_option_desc">'.__( 'Set the arrow style ( Size , Color , Color[Hover] , Margin Right , Margin Top )' , 'suppa_menu' ).'</span>

		</div>';