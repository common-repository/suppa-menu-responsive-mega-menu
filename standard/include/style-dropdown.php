<?php

	// SubMenu Type Mega Links
	// DropDown Links Font
	$this->add_font(
		array(
			'group_id'			=> 'style', 		
			'option_id'			=> 'submenu-dropdown-link_font', 		
			'title'				=> __( 'Links Typography' , 'suppa_menu' ), 		
			'desc'				=> __( 'Set the links typography' , 'suppa_menu' ), 		 		
			'font_size'			=> 16,	// Font Size
			'font_type'			=> 'px',	// Font Size Type
			'font_family'		=> "'Arial', 'Verdana' sans-serif",	// Font Family
			'font_style'		=> 'normal',	// Font Style
			'font_color'		=> '#c9c9c9',	// Font Color
		)
	);

	// DropDown Links color ( Hover )
	$this->add_colorpicker(
		array(
			'group_id'			=> 'style', 		
			'option_id'			=> 'submenu-dropdown-link_color_hover', 		
			'value'				=> '#ffffff',
			'title'				=> __( 'Links Color ( Hover )' , 'suppa_menu' ),
			'desc'				=> __( 'Set the links color when you hover over' , 'suppa_menu' )
		)
	);

	// DropDown Links Bottom Border color
	$this->add_colorpicker(
		array(
			'group_id'			=> 'style', 		
			'option_id'			=> 'submenu_dropdown_link_bg_hover', 		
			'value'				=> '#0b1b26',
			'title'				=> __( 'Links Background Color ( Hover )' , 'suppa_menu' ),
			'desc'				=> __( 'Set the links background color when user hover over' , 'suppa_menu' )
		)
	);

	// DropDown Links Bottom Border color
	$this->add_colorpicker(
		array(
			'group_id'			=> 'style', 		
			'option_id'			=> 'submenu_dropdown_link_border_color', 		
			'value'				=> '#193345',
			'title'				=> __( 'Links Bottom Border Color' , 'suppa_menu' ),
			'desc'				=> __( 'Set the links bottom border color' , 'suppa_menu' )
		)
	);


$this->add_text_input(
	array(
		'group_id'			=> 'style', 		
		'option_id'			=> 'submenu_dropdown_link_height', 		
		'value'				=> '40px',
		'title'				=> __( 'Links Height' , 'suppa_menu' ),
		'desc'				=> __( 'Set the title height' , 'suppa_menu' )
	)
);

// Links Padding 
echo 	'<div class="ctf_option_container ">
			<span class="ctf_option_title">'.__( 'Links Padding' , 'era_framework' ).'</span>';
			
			$this->add_text_input(
				array(
					'group_id'			=> 'style', 		
					'option_id'			=> 'submenu_dropdown_link_padding_left', 		
					'value'				=> '15px', 		 				
					'class'				=> 'ctf_option_gradient'
				),
				'ctf_container_gradient'
			);

			$this->add_text_input(
				array(
					'group_id'			=> 'style', 		
					'option_id'			=> 'submenu_dropdown_link_padding_right', 		
					'value'				=> '0px', 		
					'class'				=> 'ctf_option_gradient'
				),
				'ctf_container_gradient'
			);

			echo '<div class="clearfix"></div><span class="ctf_option_desc">'.__( 'Set the links padding ( Left , Right )' , 'era_framework' ).'</span>

		</div>';

	echo '<br/><br/>';


// Arrow Style
echo 	'<div class="ctf_option_container ">
			<span class="ctf_option_title">'.__( 'Arrow Style' , 'suppa_menu' ).'</span>';
			
			$this->add_text_input(
				array(
					'group_id'			=> 'style', 		
					'option_id'			=> 'dropdown-links-arrow_width', 		
					'value'				=> '14px', 		 				
				),
				'ctf_option_no_border'
			);

			$this->add_colorpicker(
				array(
					'group_id'			=> 'style', 		
					'option_id'			=> 'dropdown-links-arrow_color', 		
					'value'				=> '#c9c9c9', 		 				
				),
				'ctf_option_no_border'
			);

			$this->add_colorpicker(
				array(
					'group_id'			=> 'style', 		
					'option_id'			=> 'dropdown-links_arrow_color_hover', 		
					'value'				=> '#ffffff', 		 				
				),
				'ctf_option_no_border'
			);

			$this->add_text_input(
				array(
					'group_id'			=> 'style', 		
					'option_id'			=> 'dropdown-links-arrow_position_right', 		
					'value'				=> '10px', 		 				
				),
				'ctf_option_no_border'
			);

			$this->add_text_input(
				array(
					'group_id'			=> 'style', 		
					'option_id'			=> 'dropdown-links-arrow_position_top', 		
					'value'				=> '0px', 		 				
				),
				'ctf_option_no_border'
			);

			echo '<div class="clearfix"></div><span class="ctf_option_desc">'.__( 'Set the dropdown arrow style ( Size , Color , Color[Hover] , Margin Right , Margin Top )' , 'suppa_menu' ).'</span>

		</div>';