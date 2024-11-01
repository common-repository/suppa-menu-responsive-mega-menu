/**
 * Admin Script
 *
 * @package 	CTFramework
 * @author		Sabri Taieb ( codezag )
 * @copyright	Copyright (c) Sabri Taieb
 * @link		http://codetemp.com
 * @since		Version 1.0
 *
 */

(function($) {

	var suppa_menu = {

		// Delete Menu Types if item is not depth === 0
		eventOnClickMenuSave : function() {
			var locs = '',
			menuName = $('#menu-name'),
			menuNameVal = menuName.val();
			// Cancel and warn if invalid menu name
			if( !menuNameVal || menuNameVal == menuName.attr('title') || !menuNameVal.replace(/\s+/, '') ) {
				menuName.parent().addClass('form-invalid');
				return false;
			}
			// Copy menu theme locations
			$('#nav-menu-theme-locations select').each(function() {
				locs += '<input type="hidden" name="' + this.name + '" value="' + $(this).val() + '" />';
			});
			$('#update-nav-menu').append( locs );
			// Update menu item position data
			api.menuList.find('.menu-item-data-position').val( function(index) { return index + 1; } );
			window.onbeforeunload = null;

			// Delete Menu types for depth > 0
			api.menuList.find('.suppa_menu_item').each(function(){
				var c = $(this);
				var id = c.attr('id').split('enu-item-')[1];

				if( !c.is('.menu-item-depth-0') )
				{
					c.find('.admin_suppa_box_menu_type').remove();
					c.find('label[for="edit-menu-item-suppa-link_position-'+id+'"]').remove();
				}
			});

			return true;
		},

		/** Switch Menu Backend with Ajax **/
		addItemToMenu : function(menuItem, processMethod, callback) {
			var menu = jQuery('#menu').val(),
				nonce = jQuery('#menu-settings-column-nonce').val();

			processMethod = processMethod || function(){};
			callback = callback || function(){};

			params = {
				'action': 'suppa_switch_menu_walker',
				'menu': menu,
				'menu-settings-column-nonce': nonce,
				'menu-item': menuItem
			};

			jQuery.post( ajaxurl, params, function(menuMarkup) {
				var ins = jQuery('#menu-instructions');
				processMethod(menuMarkup, params);
				if( ! ins.hasClass('menu-instructions-inactive') && ins.siblings().length )
					ins.addClass('menu-instructions-inactive');
				callback();
			});
		},

		/** Export Menu Items **/
		export_items : function()
		{
			jQuery( document ).on('click', '#suppa_export_menu_items', 
				function(event)
				{
					event.preventDefault();

					var $this 	 = jQuery(this);
					var $content = jQuery('#menu-to-edit').html();
					jQuery('#suppa_import_export_menu_items').val($content);
				}
			);
		},

		/** Import Menu Items **/
		import_items : function()
		{
			jQuery( document ).on('click', '#suppa_import_menu_items', 
				function(event)
				{
					event.preventDefault();

					var $this 	 = jQuery(this);
					var $content = jQuery('#suppa_import_export_menu_items').val();
					if( $content != '' )
					{
						jQuery('#menu-to-edit').html($content);
					}
				}
			);
		},

		/** Show / Hide Options Container **/
		options_container : function()
		{
			jQuery( document ).on('click', '.admin_suppa_box_header a', 
				function()
				{
					var $this 	= jQuery(this);
					if( $this.text() == '+' )
					{
						$this.parents('.admin_suppa_box').find('.admin_suppa_box_container').slideDown(80);
						$this.text('-');
					}
					else 
					{
						$this.parents('.admin_suppa_box').find('.admin_suppa_box_container').slideUp(80);
						$this.text('+');
					}
				}
			);
		},

		/** Show / Hide Menu type options **/
		menu_type_options : function()
		{
			jQuery( document ).on('click', '.suppa_menu_type', 
				function()
				{
					var $this 	= jQuery(this);
					var type 	= $this.val();
					$this.parents('.suppa_menu_item').find('.menu-item-handle .item-type').text('( '+type+' )');
					$this.parents('.admin_suppa_box_container').find('.admin_suppa_box_option_inside').slideUp(80);
					$this.parents('.admin_suppa_box_container').find('.admin_suppa_box_option_inside_'+type).slideDown(80);
				}
			);
		},

		/** Upload Images with Wordpress ( wp 3.5.2+ ) **/
		upload_images : function()
		{
			jQuery( document ).on('click', '.admin_suppa_upload', 
				function()
					{

				    var send_attachment_bkp = wp.media.editor.send.attachment;
				    var $this = jQuery(this);

				    wp.media.editor.send.attachment = function(props, attachment) {
				        $this.parent().find('.admin_suppa_upload_input').val(attachment.url);
				        // back to first state
				        wp.media.editor.send.attachment = send_attachment_bkp;
				    }

				    wp.media.editor.open();

				    return false;     
				}
			);
		},

		/** Show / Hide More Options When Disable FullWidth **/
		fullwidth_checkbox_click : function()
		{
			jQuery( document ).on('click', '.admin_suppa_fullwidth_checkbox', 
				function()
				{
					var $this = jQuery(this);
					if( $this.is(':checked') )
					{
						$this.val('on');
						$this.parents('.admin_suppa_box_option_inside').find('.admin_suppa_fullwidth_div').hide();
					}
					else
					{
						$this.val('off');
						$this.parents('.admin_suppa_box_option_inside').find('.admin_suppa_fullwidth_div').show();
					}
				}
			);
		},

		/** Show / Hide FullWidth More Options When Page Load  **/
		fullwidth_checkbox : function()
		{
			jQuery('.admin_suppa_fullwidth_checkbox').each(function(){
				var $this = jQuery(this);
				if( $this.is(':checked') )
				{
					$this.parents('.admin_suppa_box_option_inside').find('.admin_suppa_fullwidth_div').hide();
				}
				else
				{
					$this.parents('.admin_suppa_box_option_inside').find('.admin_suppa_fullwidth_div').show();
				}
			});
		},

		/** Add Tinymce Editor **/
		wp_editor_set_content : function()
		{
			jQuery( document ).on('click', '.admin_suppa_edit_button', 
				function()
				{
					var $this = jQuery(this);
					var $id 	= $this.attr('id');
					
					var $textarea 	= $this.parent().parent().find('textarea');
					var $content 	= $textarea.val();

					jQuery('.admin_suppa_getContent_button').attr('id', $id);

					tinyMCE.get('suppa_wp_editor_the_editor').setContent( $content, {format : 'raw'});

					jQuery('.era_admin_widgets_container').fadeIn(100);
					
					jQuery('.suppa_wp_editor_container').fadeIn(100);

					return false;
				}
			);
		},


		/** Get Content from WordPress Editor **/
		wp_editor_get_content : function()
		{
			jQuery( document ).on('click', '.admin_suppa_getContent_button', 
				function()
				{
					var $this 	= jQuery(this);
					var $id 	= $this.attr('id');
					var $textarea 	= jQuery('#edit-menu-item-suppa-html_content-'+$id);

					var content;
					var editor = tinyMCE.get('suppa_wp_editor_the_editor');
					if (editor) {
					    // Ok, the active tab is Visual
					    content = editor.getContent();
					} else {
					    // The active tab is HTML, so just query the textarea
					    content = $('#'+'suppa_wp_editor_the_editor').val();
					}

					$textarea.val( content );

					jQuery('.era_admin_widgets_container').fadeOut(100);
					jQuery('.suppa_wp_editor_container').fadeOut(100);

					return false;
				}
			);
		},


		/** Show Front Awesome Widget **/
		show_fontAwesome_widget : function()
		{
			jQuery( document ).on('click', '.admin_suppa_selectIcon_button', 
				function()
				{
					var $this = jQuery(this);
					var $id 	= $this.attr('id');

					jQuery('.admin_suppa_addIcon_button').attr('id', $id);

					jQuery('.era_admin_widgets_container').fadeIn(100);
					jQuery('.suppa_fontAwesome_container').fadeIn(100);

					return false;
				}
			);
		},


		/** Add Font Awesome Icon to the Hidden Input **/
		add_fontAwesome_icon : function()
		{
			jQuery( document ).on('click', '.admin_suppa_addIcon_button', 
				function()
				{
					var $this 	= jQuery(this);
					var $id 	= $this.attr('id');
					var $icon 	= jQuery('.suppa_fontAwesome_container').find('.selected').find('span').attr('class');

					jQuery('.admin_suppa_fontAwesome_icon_hidden-'+$id).val( $icon );
					jQuery('.admin_suppa_fontAwesome_icon_box_preview-'+$id).children('span').attr('class',$icon);
					jQuery('.era_admin_widgets_container').fadeOut(100);
					jQuery('.suppa_fontAwesome_container').fadeOut(100);

					return false;
				}
			);
		},

		/** Select Font Awesome Icon **/
		select_fontAwesome_icon : function()
		{
			jQuery( document ).on('click', '.admin_suppa_fontAwesome_icon_box',
				function(){
					var $this 	= jQuery(this);

					jQuery('.admin_suppa_fontAwesome_icon_box').removeClass('selected')
					$this.addClass('selected');

					return false;
				}
			);
		},

		/** Close Widget Container **/
		close_widget_container : function()
		{
			jQuery( document ).on('click', '.era_admin_widget_box_header a',
				function(){

					jQuery('.era_admin_widgets_container').fadeOut(100);
					jQuery('.era_admin_widget_box').fadeOut(100);

					return false;
				}
			);
		},

		/** Upload or Font Awesome **/
		upload_or_fontAwesome : function()
		{
			jQuery( document ).on('change', '.menu-item-suppa-link_icon_type',
				function()
				{
					var $selected = jQuery(this).find('option:selected').val();
					if( $selected == 'upload' )
					{
						jQuery('.admin_suppa_box_option_inside_icon_upload').fadeIn(80);
						jQuery('.admin_suppa_box_option_inside_icon_fontawesome').fadeOut(80);
					}
					else if( $selected == 'fontawesome' )
					{
						jQuery('.admin_suppa_box_option_inside_icon_upload').fadeOut(80);
						jQuery('.admin_suppa_box_option_inside_icon_fontawesome').fadeIn(80);
					}
					else
					{
						
					}
					return false;
				}
			);
		},

		// Ajax : Save Menu Location
		ajax_save_location : function()
		{
			jQuery( document ).on('click', '#admin_suppa_save_menu_location',
				function()
				{
					var $data = {
						action 		: 'suppamenu_save_menu_location',
						nonce 		: jQuery('#admin_suppa_save_menu_location_nonce').val(),
						location 	: jQuery("#suppa_menu_location_selected option:selected").val(),
						//menu 		: jQuery('#suppa_menu_menu_selected option:selected').val()
					};
					jQuery.post(ajaxurl, $data, function(response) {
						
						/** Remove Alert **/
						jQuery('body').append('<div class="suppa_location_saved">Location Saved</div>');
						setTimeout( function(){
							jQuery('.suppa_location_saved').remove();
						},2000);
					});
					return false;
				}
			);
		},


		// Add Taxonomy : when select the category
		select_taxonomy : function()
		{
			jQuery( document ).on('change', '.menu-item-suppa-posts_category',
				function()
				{				
					var $this = jQuery(this);
					var $taxonomy = jQuery(this).find('option:selected').attr('data-taxonomy');
					$this.parent().parent().find('.suppa_taxonomy').val( $taxonomy );
					return false;
				}
			);
		},


		// Use Icon Only Checkbox
		icon_only_checkbox : function()
		{
			jQuery( document ).on('click', '.suppa_use_icon_only',
				function()
				{
					var $this = jQuery(this);
					
					if( $this.is(':checked') )
					{
						$this.val('on');
					}
					else 
					{
						$this.val('off');
					}
				}
			);
		},

		
	};// End Object

	suppa_menu.menu_type_options();
	suppa_menu.options_container();
	
	suppa_menu.upload_images();

	suppa_menu.fullwidth_checkbox();
	suppa_menu.fullwidth_checkbox_click();
	
	// Widgets 
	suppa_menu.wp_editor_set_content();
	suppa_menu.wp_editor_get_content();
	suppa_menu.show_fontAwesome_widget();
	suppa_menu.select_fontAwesome_icon();
	suppa_menu.add_fontAwesome_icon();
	suppa_menu.close_widget_container();

	suppa_menu.upload_or_fontAwesome();
	suppa_menu.ajax_save_location();

	suppa_menu.select_taxonomy();
	suppa_menu.icon_only_checkbox();

	/** Export / Import Menu Items **/
	suppa_menu.export_items();
	suppa_menu.import_items();

	/** Switch Menu Backend with Ajax **/
	if(typeof wpNavMenu != 'undefined')
	{ 
		// Add Our Item to Menu 
		wpNavMenu.addItemToMenu = suppa_menu.addItemToMenu; 

		// Delete Menu Types if item is not depth === 0
		wpNavMenu.eventOnClickMenuSave = suppa_menu.eventOnClickMenuSave;

		api = wpNavMenu;
	}	


})(jQuery);