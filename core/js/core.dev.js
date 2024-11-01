jQuery(document).ready(function(){


	/** Main Nav : Show/Hide pages **/
	jQuery('.codetemp_main_nav').on({
		click : function(){
			var $this = jQuery(this);
			var $target = $this.attr('href').replace( '#' , '' );

			jQuery('.codetemp_main_nav > li').removeClass('selected');
			$this.parent().addClass('selected');

			$this.parents('.codetemp_nav_pages_container').find('.codetemp_page').slideUp(200);
			$this.parents('.codetemp_nav_pages_container').find('#'+$target).slideDown(200);

			return false;
		}
	}, '> li > a' );
	/* second nav */
	jQuery('.codetemp_main_nav ul').on({
		click : function(){
			var $this = jQuery(this);
			var $target = $this.attr('href').replace( '#' , '' );

			jQuery('.codetemp_main_nav ul a').removeClass('selected');
			$this.addClass('selected');

			$this.parents('.codetemp_nav_pages_container').find('.codetemp_page').slideUp(200);
			$this.parents('.codetemp_nav_pages_container').find('#'+$target).slideDown(200);

			return false;
		}
	}, 'a' );


	/** Tab : Show / Hide **/
	jQuery('.codetemp_pages_container').on({
		click : function(){
			jQuery(this).parent().parent().parent().children('.codetemp_tab_inside').slideToggle(200);
		}
	}, '.codetemp_tab_display_icon' );


	/** Ace Editor **/
	var ace_editors = [];
	jQuery('.codetemp_pages_container').find('.ctf_option_ace_editor').each(function(i){
		var $this = jQuery(this);
		var $this_id = $this.attr('id');

		ace_editors[i] = ace.edit( $this_id + "_ace" );
		ace_editors[i].setTheme("ace/theme/monokai");
	});


	/** Ajax **/
	jQuery('.codetemp_settings_container').on({
		click : function(){

			var $this 				= jQuery(this);
			var $form 				= $this.parents('#codetemp_form');
			var $nonce 				= $form.find('#nonce').val();
			var $parent 			= $form.find('#codetemp_parent').val();
			var $group_id 			= $form.find('#codetemp_group_id').val();
			var $option_id 			= $form.find('#codetemp_option_id').val();
			var $wp_ajax_function 	= $form.find('#action').val();
			var $plugin_url 		= $form.find('#codetemp_plugin_url').val();
			var $data				= "";

			// Set Loading Image 
			var $html_ajax_img_load = $plugin_url + 'core/img/ajax-loader.gif'; 
			var $html_ajax_img_save = $plugin_url + 'core/img/success_icon.png'; 
			var $html_ajax_res = jQuery('.codetemp_ajax_response');

			$html_ajax_res.children('img').attr( 'src' , $html_ajax_img_load ); 
			$html_ajax_res.children('span').text('');
			$html_ajax_res.show();

			// Solve Tinymce issue
			if( typeof tinyMCE !== "undefined" )
				tinyMCE.triggerSave();		

			// Get Content From ACE Editors
			jQuery('.codetemp_pages_container').find('.ctf_option_ace_editor').each(function(i){
				jQuery(this).val( ace_editors[i].getSession().getValue() );
			});

			// Update or ADD Options ( all or by ID )
			if( $this.is('.codetemp_button_update_all') )
			{		
				// Search Groups
				var $groups = {}
				var $index ;

				// Add Groups
				for ($index = 0; $index < codetemp_groups.length; ++$index) 
				{
					$groups[codetemp_groups[$index]] = {}
				}

				// Add Options to their group
				$form.find('input[type="text"], input[type="hidden"], input[type="password"], input[type="radio"]:checked, input[type="checkbox"], textarea,select').each(function(){
				
					var $this = jQuery(this);
					var $name = $this.attr('name');
					var $Reg = new RegExp("_ctfsep_");
					if( $Reg.test( $name ) )
					{
						var $split = $name.split('_ctfsep_');
						$groups[$split[0]][$split[1]] = encodeURIComponent( $this.val() );
					}					

				});


				// Ajax Calls by Group
				for(var $gr in $groups ) 
				{
					var $data = "";
					for(var $op in $groups[$gr] ) 
					{
						$data = $data + "__ctfand__" + $op + "__ctfequal__" + $groups[$gr][$op];
					}

					$data = $data.substr(10);			

					if( $data != '' ){
						jQuery.ajax({
							type	:	'POST',
							url		:	ajaxurl,
							data	:	{
											action 				: $wp_ajax_function,
											ctf_request_type 	: 'update_all',
											ctf_group			: $gr,
											nonce 				: $nonce,
											data_string 		: $data,
									 },
							cache	: false,
							success	: function( response )
							{	
								$html_ajax_res.children('img').attr( 'src' , $html_ajax_img_save ); 
								$html_ajax_res.children('span').text( response );	

								setTimeout( function(){
									$html_ajax_res.fadeOut(200);
								}, 2000 );	
							},
							error	: function( )
							{
								$request_response = 'ajax_error';
							}
						});
					}
				}

				return false;
			}

			// Reset All Options
			else if( $this.is('.codetemp_button_reset_all') ) 
			{
				var r=confirm("Yes , Reset!");
				if (r==false)
				{
					$html_ajax_res.fadeOut(200) ;
					return false;
				}

				$all_data = 'nonce=' + $nonce + '&action=' + $wp_ajax_function + '&ctf_request_type=reset_all';

				jQuery.ajax({
					type	: 'POST',
					url		: ajaxurl,
					data	: $all_data,
					cache	: false,
					async	: false,
					success	: function( response )
					{							
						$html_ajax_res.children('img').attr( 'src' , $html_ajax_img_save ); 
						$html_ajax_res.children('span').text(response);	

						setTimeout( function(){
							$html_ajax_res.fadeOut(200);
						}, 1000 );

						setTimeout( function(){
							document.location = document.URL;
						},1200)
									
					},
					error	: function( response )
					{
						$html_ajax_res.children('img').attr( 'src' , $html_ajax_img_save ); 
						$html_ajax_res.children('span').text(response);	

						setTimeout( function(){
							$html_ajax_res.fadeOut(200);
						}, 1000 );
					}
				});
			}


			// Delete Group , by ID
			else if( $this.is('.codetemp_button_delete_group') ) 
			{
				
				$all_data = 'nonce=' + $nonce + '&action=' + $wp_ajax_function + "&codetemp_group_id=" + $group_id+ "&ctf_request_type=delete_group";

				jQuery.ajax({
					type	: 'POST',
					url		: ajaxurl,
					data	: $all_data,
					cache	: false,
					async	: false,
					success	: function( response )
					{							
						$html_ajax_res.children('img').attr( 'src' , $html_ajax_img_save ); 
						$html_ajax_res.children('span').text(response);	

						setTimeout( function(){
							$html_ajax_res.fadeOut(200);
						}, 1000 );
									
					},
					error	: function( response )
					{
						$html_ajax_res.children('img').attr( 'src' , $html_ajax_img_save ); 
						$html_ajax_res.children('span').text(response);	

						setTimeout( function(){
							$html_ajax_res.fadeOut(200);
						}, 1000 );
					}
				});

			}

			// Delete Option , by ID
			else if( $this.is('.codetemp_button_delete_option') ) 
			{
				$all_data = 'nonce=' + $nonce + '&action=' + $wp_ajax_function + "&codetemp_group_id=" + $group_id+ "&codetemp_option_id=" +$option_id+ "&request_type=delete_option";

				jQuery.ajax({
					type	: 'POST',
					url		: ajaxurl,
					data	: $all_data,
					cache	: false,
					async	: false,
					success	: function( response )
					{							
						$html_ajax_res.children('img').attr( 'src' , $html_ajax_img_save ); 
						$html_ajax_res.children('span').text(response);	

						setTimeout( function(){
							$html_ajax_res.fadeOut(200);
						}, 1000 );
									
					},
					error	: function( response )
					{
						$html_ajax_res.children('img').attr( 'src' , $html_ajax_img_save ); 
						$html_ajax_res.children('span').text(response);	

						setTimeout( function(){
							$html_ajax_res.fadeOut(200);
						}, 1000 );
					}
				});

			}

			return false;
		}
	}, '.codetemp_button_update_all, .codetemp_button_reset_all, .codetemp_button_delete_group, .codetemp_button_delete_option' );

	/** Checkbox **/
	jQuery('.codetemp_pages_container').find('input[type="checkbox"]').each(function(){
		var $this = jQuery(this);
		var $this_id = $this.attr('id');

		if( $this.val().toLowerCase() == 'on' )
		{
			jQuery('.codetemp_pages_container label[for="'+$this_id+'"]').css({ 'background-position' : 'left center' });
		}
		else
		{
			jQuery('.codetemp_pages_container label[for="'+$this_id+'"]').css({ 'background-position' : 'right center' });
		}
	});

	jQuery('.codetemp_pages_container').on({
		click : function()
		{
			var $this = jQuery(this);
			var $this_id = $this.attr('id');
			if( $this.val().toLowerCase() == 'on' )
			{
				$this.val('off');
				jQuery('.codetemp_pages_container label[for="'+$this_id+'"]').css({ 'background-position' : 'right center' });
			}
			else
			{
				$this.val('on');
				jQuery('.codetemp_pages_container label[for="'+$this_id+'"]').css({ 'background-position' : 'left center' });
			}

			return false;
		}
	}, '.ctf_option_checkbox' )



	/** Colorpicker **/
	jQuery('input.ctf_option_colorpicker').ColorPicker({
			onSubmit: function(hsb, hex, rgb, el) 
			{
				jQuery(el).val("#" + hex);
				jQuery(el).ColorPickerHide();
				jQuery(el).next().css({"background-color":"#"+hex});
				// this part is for font alert box
				
			},
			onBeforeShow: function () 
			{
				jQuery(this).ColorPickerSetColor(this.value);
			}
		})
		.bind('keyup', function()
		{
			jQuery(this).ColorPickerSetColor(this.value);
		}
	);


	/** Upload Images with Wordpress **/
	// WP 3.5+ 
	// Enqueue Media uploader scripts and environment [ wp_enqueue_media() ].
	// Strongly suggest to use this function on the admin_enqueue_scripts action hook. Using it on admin_init hook breaks it
	// How To : http://stackoverflow.com/questions/13847714/wordpress-3-5-custom-media-upload-for-your-theme-options
	// Don't Foooooooooooooooooorget to  array('jquery' , 'media-upload' , 'thickbox')  to the enqueue
	jQuery( '.codetemp_pages_container' ).on({
		click : function() 
		{					
		    var send_attachment_bkp = wp.media.editor.send.attachment;
		    var $this = jQuery(this);

		    wp.media.editor.send.attachment = function(props, attachment) {
		        $this.prev().val(attachment.url);
		        // back to first state
		        wp.media.editor.send.attachment = send_attachment_bkp;
		    }

		    wp.media.editor.open();

		    return false;     
		}  
	}, '.ctf_option_upload_button' );



	/** Font Style **/
	jQuery('.codetemp_pages_container').on({
		click : function()
		{
			var $this			= jQuery(this).parents('.ctf_option_container_font');
			var $box			= $this.find('.ctf_box_desc');
			var $font_size		= $this.find('.ctf_option_font_size').val();
			var $font_size_type	= $this.find('.ctf_option_font_size_type').val();
			var $font_family	= $this.find('.ctf_option_font_family').val();
			var $font_style		= $this.find('.ctf_option_font_family_style').val();
			var $font_color		= $this.find('.ctf_option_font_color').val();

			$box.parents('.ctf_box').slideDown(200);
			$box.css({ 'line-height' : '1.5em' , 'font-size' : $font_size+$font_size_type , 'font-family' : $font_family , 'color' : $font_color });

			if( $font_style == 'normal' || $font_style == 'italic' ) 
			{
				$box.css({ 'font-style' : $font_style , 'font-weight' : 'normal' });			
			}
			else if( $font_style == 'bold' ) 
			{
				$box.css({ 'font-style' : 'normal' , 'font-weight' : 'bold' });				}
			else 
			{
				$box.css({ 'font-style' : 'italic' , 'font-weight' : 'bold' });					
			}

			return	false;
		}
	}, '.ctf_option_font_demo' );


	/** BOX'S : Hide **/
	jQuery('.ctf_box').on({
		click : function(){
			jQuery(this).parents('.ctf_box').slideUp(200);
			return false;
		}
	}, '.ctf_box_close')

});