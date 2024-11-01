<?php

/** Responsive Web Design **/

// Enable RWD
$this->add_checkbox(
	array(
		'group_id'			=> 'settings', 
		'option_id'			=> 'settings-responsive_enable', 
		'title'				=> __( 'Enable Responsive Design on Mobile ' , 'suppa_menu' ), 	
		'desc'				=> __( 'Enable the responsive design mobile devices( IOS, Android ... )' , 'suppa_menu' ), 	
		'value'				=> 'off', 	
		'fetch'				=> 'yes',
	)
);

// Enable RWD
$this->add_checkbox(
	array(
		'group_id'			=> 'settings', 
		'option_id'			=> 'settings-responsive_enable_desktops', 
		'title'				=> __( 'Enable Responsive Design Desktops / Laptops' , 'suppa_menu' ), 	
		'desc'				=> __( 'Enable the responsive design on desktops and laptops' , 'suppa_menu' ), 	
		'value'				=> 'off', 	
		'fetch'				=> 'yes',
	)
);


// RWD Trigger Mode
$this->add_radio(
	array(
		'group_id'			=> 'settings', 
		'option_id'			=> 'settings-rwd_trigger_mode', 
		'title'				=> __( 'RWD Trigger Mode' , 'suppa_menu' ), 	
		'desc'				=> __( 'Click on link mode , User must hold on link to open it' , 'suppa_menu' ), 	
		'value'				=> 'click_link_mode',
		'select_options'	=> array(
									'Click on Link to Open Submenu'		=> 'click_link_mode',
									'Click on Arrow to Open Submenu'	=> 'click_arrow_mode',
									'Click on Link to Open Submenu and Arrow To close it'	=> 'click_both_mode',

								),
		'fetch'				=> 'yes',
	)
);

// RWD Width
$this->add_text_input(
	array(
		'group_id'			=> 'settings', 		
		'option_id'			=> 'settings_responsive_start_width', 		
		'value'				=> '960px',
		'title'				=> __( 'RWD Start Work When Device <= To This Width' , 'suppa_menu' ),
		'desc'				=> __( 'Set the width for RWD to start work' , 'suppa_menu' )
	)
);

// RWD Text
$this->add_text_input(
	array(
		'group_id'			=> 'settings', 		
		'option_id'			=> 'settings-responsive_text', 		
		'value'				=> 'Menu',
		'title'				=> __( 'Add Responsive Text ' , 'suppa_menu' ),
		'desc'				=> __( 'Add a text like "Responsive Menu"' , 'suppa_menu' )
	)
);