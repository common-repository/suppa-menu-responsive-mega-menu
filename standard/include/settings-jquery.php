<?php


// Selec jQuery Mode
$this->add_checkbox(
	array(
		'group_id'			=> 'settings', 
		'option_id'			=> 'settings-jquery_enable', 
		'title'				=> __( 'Enable jQuery on noConflict Mode' , 'suppa_menu' ), 	
		'desc'				=> __( 'Enable jquery on noConflict mode ' , 'suppa_menu' ), 	
		'value'				=> 'off', 	
		'fetch'				=> 'yes',
	)
);

// jQuery Trigger
$this->add_select(
	array(
		'group_id'			=> 'settings', 
		'option_id'			=> 'settings-jquery_trigger',	 		
		'title'				=> __( 'jQuery Trigger' , 'suppa_menu' ) , 		
		'desc'				=> __( 'set the jquery trigger' , 'suppa_menu' ), 		
		'select_options'	=> array( 'hover' => 'hover' , 'click' => 'click' , 'hover-intent' => 'hover-intent' ), 
		'value'				=> 'click', 
	)
);

// jQuery Animation
$this->add_select(
	array(
		'group_id'			=> 'settings', 
		'option_id'			=> 'settings-jquery_animation',	 		
		'title'				=> __( 'jQuery Animation' , 'suppa_menu' ) , 		
		'desc'				=> __( 'set the jquery animation' , 'suppa_menu' ), 		
		'select_options'	=> array( 
								'none' => 'none' , 
								'fade' => 'fade' , 
								'slide' => 'slide' , 
								'explode' => 'explode',
								'drop' => 'drop' ,
								'pulsate' => 'pulsate' ,
								), 
		'value'				=> 'none', 
	)
);

// Animation Time
$this->add_text_input(
	array(
		'group_id'			=> 'settings', 		
		'option_id'			=> 'settings-jquery_animation_time', 		
		'value'				=> '500',
		'title'				=> __( 'Animation Time' , 'suppa_menu' ),
		'desc'				=> __( 'set the jquery animation Time ( by millisecond )' , 'suppa_menu' )
	)
);