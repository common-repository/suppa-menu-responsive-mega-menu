<?php


	// Load Skins & Custom CSS
	$suppa_layout = array(
		'Boxed'						=>	'boxed_layout',
		'Wide'						=>	'wide_layout',
	);

	$this->add_select(
		array(
			'group_id'			=> 'settings', 
			'option_id'			=> 'menu-layout',	 		
			'title'				=> __( 'Choose Your Layout Style' , 'suppa_menu' ) , 		
			'desc'				=> __( 'Choose Your Layout Style' , 'suppa_menu' ), 		
			'select_options'	=> $suppa_layout, 
			'value'				=> 'boxed_layout', 	
			'container'			=> 'yes', 			
			'fetch'				=> 'yes',	
		)
	);

