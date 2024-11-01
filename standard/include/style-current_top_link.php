<?php

// Current Top Link color
$this->add_colorpicker(
	array(
		'group_id'			=> 'style', 		
		'option_id'			=> 'top-links-current_color', 		
		'value'				=> '#ffffff',
		'title'				=> __( 'Current Top Link color' , 'suppa_menu' ),
		'desc'				=> __( 'Set the current top links color' , 'suppa_menu' )
	)
);

// Current Top Link Background 
$this->add_colorpicker(
	array(
		'group_id'			=> 'style', 		
		'option_id'			=> 'top-links-current_bg', 		
		'value'				=> '#2b2b2b',
		'title'				=> __( 'Current Top Link Background Color' , 'suppa_menu' ),
		'desc'				=> __( 'Set the current top links background color' , 'suppa_menu' )
	)
);

// Current Top Link Arrow Color 
$this->add_colorpicker(
	array(
		'group_id'			=> 'style', 		
		'option_id'			=> 'top-links-current_arrow_color', 		
		'value'				=> '#ffffff',
		'title'				=> __( 'Current Top Link Arrow Color ' , 'suppa_menu' ),
		'desc'				=> __( 'Set the current top links arrow color' , 'suppa_menu' )
	)
);