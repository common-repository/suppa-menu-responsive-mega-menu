<?php
/**
 * Logo Style
 *
 * @package 	CTFramework
 * @author		Sabri Taieb ( codezag )
 * @copyright	Copyright (c) Sabri Taieb
 * @link		http://codetemp.com
 * @since		Version 1.0
 *
 */

// Logo Padding
echo 	'<div class="ctf_option_container ">
			<span class="ctf_option_title">'.__( 'Logo Padding' , 'suppa_menu' ).'</span>';
			
			$this->add_text_input(
				array(
					'group_id'			=> 'style', 		
					'option_id'			=> 'title-padding_top', 		
					'value'				=> '0px', 		 				
					'class'				=> 'ctf_option_box_shadow'
				),
				'ctf_container_box_shadow'
			);

			$this->add_text_input(
				array(
					'group_id'			=> 'style', 		
					'option_id'			=> 'title-padding_left', 		
					'value'				=> '0px', 		 				
					'class'				=> 'ctf_option_box_shadow'
				),
				'ctf_container_box_shadow'
			);

			$this->add_text_input(
				array(
					'group_id'			=> 'style', 		
					'option_id'			=> 'title-padding_right', 		
					'value'				=> '0px', 		
					'class'				=> 'ctf_option_box_shadow'
				),
				'ctf_container_box_shadow'
			);

			echo '<div class="clearfix"></div><span class="ctf_option_desc">'.__( 'Set the padding ( Top , Left , Right )' , 'suppa_menu' ).'</span>

		</div>';