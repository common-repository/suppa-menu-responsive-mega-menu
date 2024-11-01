<?php

// FontAwesome Icons Size
$this->add_text_input(
	array(
		'group_id'			=> 'style', 		
		'option_id'			=> 'submenu_dropdown_links_fontawesome_icons_size', 		
		'title'				=> __( 'FontAwesome Icons Size' , 'suppa_menu' ), 		
		'desc'				=> __( 'Set the FontAwesome icons size ( px )' , 'suppa_menu' ), 		
		'value'				=> '14px', 		
	)
);

// FontAwesome Icon Margin
echo 	'<div class="ctf_option_container ">
			<span class="ctf_option_title">'.__( 'FontAwesome Icon Margin' , 'suppa_menu' ).'</span>';
			
			$this->add_text_input(
				array(
					'group_id'			=> 'style', 		
					'option_id'			=> 'submenu_dropdown_links_fontawesome_icon_margin_top', 		
					'value'				=> '13px', 		 				
					'class'				=> 'ctf_option_gradient'
				),
				'ctf_container_gradient'
			);

			$this->add_text_input(
				array(
					'group_id'			=> 'style', 		
					'option_id'			=> 'submenu_dropdown_links_fontawesome_icon_margin_right', 		
					'value'				=> '10px', 		
					'class'				=> 'ctf_option_gradient'
				),
				'ctf_container_gradient'
			);

			echo '<div class="clearfix"></div><span class="ctf_option_desc">'.__( 'Set the font awesome icon margin ( Top , Right )' , 'suppa_menu' ).'</span>

		</div>';

// Uploaded Icons width
$this->add_text_input(
	array(
		'group_id'			=> 'style', 		
		'option_id'			=> 'submenu_dropdown_links_uploaded_icons_width', 		
		'title'				=> __( 'Uploaded Icons width' , 'suppa_menu' ), 		
		'desc'				=> __( 'Set the Uploaded icons width ( px )' , 'suppa_menu' ), 		
		'value'				=> '14px', 		
	)
);
// Uploaded Icons height
$this->add_text_input(
	array(
		'group_id'			=> 'style', 		
		'option_id'			=> 'submenu_dropdown_links_uploaded_icons_height', 		
		'title'				=> __( 'Uploaded Icons height' , 'suppa_menu' ), 		
		'desc'				=> __( 'Set the Uploaded icons height ( px )' , 'suppa_menu' ), 		
		'value'				=> '14px', 		
	)
);
// Uploaded Icon Margin
echo 	'<div class="ctf_option_container ">
			<span class="ctf_option_title">'.__( 'Uploaded Icon Margin' , 'suppa_menu' ).'</span>';
			
			$this->add_text_input(
				array(
					'group_id'			=> 'style', 		
					'option_id'			=> 'submenu_dropdown_links_normal_icon_margin_top', 		
					'value'				=> '15px', 		 				
					'class'				=> 'ctf_option_gradient'
				),
				'ctf_container_gradient'
			);

			$this->add_text_input(
				array(
					'group_id'			=> 'style', 		
					'option_id'			=> 'submenu_dropdown_links_normal_icon_margin_right', 		
					'value'				=> '10px', 		
					'class'				=> 'ctf_option_gradient'
				),
				'ctf_container_gradient'
			);

			echo '<div class="clearfix"></div><span class="ctf_option_desc">'.__( 'Set the uploaded icon margin ( Top , Right )' , 'suppa_menu' ).'</span>

		</div>';

// Only Icon Margin
echo 	'<div class="ctf_option_container ">
			<span class="ctf_option_title">'.__( 'Only Icons Margin' , 'suppa_menu' ).'</span>';
			
			$this->add_text_input(
				array(
					'group_id'			=> 'style', 		
					'option_id'			=> 'submenu_dropdown_links_only_icon_margin_top', 		
					'value'				=> '15px', 		 				
					'class'				=> 'ctf_option_gradient'
				),
				'ctf_container_gradient'
			);

			$this->add_text_input(
				array(
					'group_id'			=> 'style', 		
					'option_id'			=> 'submenu_dropdown_links_only_icon_margin_right', 		
					'value'				=> '10px', 		
					'class'				=> 'ctf_option_gradient'
				),
				'ctf_container_gradient'
			);

			echo '<div class="clearfix"></div><span class="ctf_option_desc">'.__( 'This options is when you check "Only Icon" ( Margin Top , Margin Right )' , 'suppa_menu' ).'</span>

		</div>';