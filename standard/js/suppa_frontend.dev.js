/**
 * Run this script on jQuery no-conflict mode!
 *  
 */
if( suppa_js_settings.jquery_mode == 'noConflict_mode' )
{
	$suppa_jq_mode = jQuery.noConflict();
}


/**
 * ShowTime!
 * This run's when the DOM complete loading
 * 
 */
jQuery(document).ready(function($)
{
	
	/** Merge CSS/JS Custom JS **/
	var pass_settings = jQuery.extend({}, suppa_js_settings, suppa_css_settings);
	/** parse jquery_time to int **/
	if( pass_settings.jquery_time == "" ) { pass_settings.jquery_time = 0; }
	else { pass_settings.jquery_time = parseInt( pass_settings.jquery_time ); }
	

	/** Get Menu **/
	var $menu = jQuery('.suppaMenu_wrap');

	
	/** UnBind Events "click..." , custom menu may add some jquery events **/
	//$menu.find('ul, ul li.menu-item, ul li.menu-item > a' ).unbind().off();
	$menu.find('.suppa_menu, .suppa_dropdown_item_container > a').removeClass('menu-item menu-item-type-post_type menu-item-object-page menu-item-has-children');
	
	/** Remove Top Level Arrow If Submenu Empty **/
	/** Need To Execute First to Prevent Copy Content to RWD with Arrows not needed **/
	$menu.find('.suppa_menu').each(function(){
		var $this = jQuery(this);
		var $subm = $this.find('> .suppa_submenu');

		if( $subm == 0 || $subm.html() == '' )
		{
			$this.find('>a .era_suppa_arrow_box').remove();
			if( $subm.html() == '' )
			{
				$subm.remove();
			}
		}
	});

	/** Detect Retina Devices **/
	if (window.devicePixelRatio >= 2) 
	{
		pass_settings.retina_device = true;
		$menu.find('.suppa_upload_img').each(function(){
			var $this = jQuery(this);
			$this.attr('src', $this.attr('data-retina') );
		});
	}
	else
	{
		pass_settings.retina_device = false;
	}

	/** Resposive Web Design **/
	var $menu_container 	=	$menu.find('.suppaMenu'),
		$rwd_start_width 	= 	parseInt( pass_settings.rwd_start_width );

	/** Change the trigger to "click" if this device is using "Android" or "IOS" **/
	var $device_system = navigator.userAgent.toLowerCase();
	// Mobile : Windows 8 / Andoird / IOS
	if( window.navigator.msMaxTouchPoints || $device_system.match(/(iphone|ipod|ipad)/) || jQuery.suppa_browser )
	{
		if( pass_settings.rwd_enable == 'on' )
		{
			// Hide Menus
			$menu.find('.suppaMenu').css({'display':'none'});
			suppa_rwd_create_func( $menu , pass_settings );
			suppa_rwd_trigger_func( $menu , pass_settings );
		}
		else
		{
			/** Trigger Functions Call **/
			switch( pass_settings.jquery_trig )
			{
				case 'click' : 
					suppa_click_trigger_funcs( $menu , pass_settings );
				break;

				case 'hover' :
					suppa_hover_trigger_funcs( $menu , pass_settings , "hover" );
				break;

				case 'hover-intent' :
					suppa_hover_trigger_funcs( $menu , pass_settings , "hoverIntent" );
				break;
			}
		}
		
		/** Scroll Menu **/
		suppa_scrolling( $menu, pass_settings, 'mobile' );

		/** Close Submenu's when click outside **/
		suppa_close_menu_when_click_outside( $menu, pass_settings );
	}
	// Desktop / Laptop
	else
	{
		/** Trigger Functions Call **/
		switch( pass_settings.jquery_trig )
		{
			case 'click' : 
				suppa_click_trigger_funcs( $menu , pass_settings );
				
				// Close Submenu's when click outside
				suppa_close_menu_when_click_outside( $menu , pass_settings );
			break;

			case 'hover' :
				suppa_hover_trigger_funcs( $menu , pass_settings , "hover" );
			break;

			case 'hover-intent' :
				suppa_hover_trigger_funcs( $menu , pass_settings , "hoverIntent" );
			break;
		}

		// RWD for Desktops / Laptops
		suppa_rwd_for_desktops( $menu, pass_settings );

		/** Scroll Menu **/
		suppa_scrolling( $menu, pass_settings, 'desktop_laptop' );	
	}
	
	/** Box Layout **/
	suppa_box_layout( $menu, pass_settings );

	/** THIS MUST BE CALLED AFTER THE TRIGGER TO PREVENT STYLE ISSUE'S ON MOBILE DEVICES **/
	/** Style Adjust **/
	suppa_adjust_css( $menu, pass_settings );
	suppa_rwd_adjust_submenu_align( $menu, pass_settings );

});



/**
 * RWD For Desktops / Laptops
 * 
 * @param $menu the menu object
 * @param pass_settings Custom menu settings & style 
 * 
 */
function suppa_rwd_for_desktops( $menu, pass_settings )
{
	var $starting_width = parseInt( pass_settings.rwd_start_width );
	var $window_width   = jQuery(window).width();
	var $menu_to_hide_show = $menu.find('.suppaMenu');

	if( pass_settings.rwd_enable_desk == 'on' )
	{
		// Start RWD
		if( $menu.find('.suppaMenu_rwd_wrap').length == 0 )
		{
			suppa_rwd_create_func( $menu , pass_settings );
			pass_settings.rwd_text_t_mode = "click_both_mode";
			suppa_rwd_trigger_func( $menu , pass_settings );
		}

		var $rwd_menus = $menu.find('.suppaMenu_rwd_wrap');
			$rwd_menus.hide();

		if( $starting_width >= $window_width )
		{
			$menu_to_hide_show.hide();
			$rwd_menus.show();
		}

		// Resize
		jQuery(window).resize(function(){

			$window_width   = jQuery(window).width();

			if( $starting_width >= $window_width )
			{
				$menu_to_hide_show.hide();
				$rwd_menus.show();		
			}
			else
			{
				$menu_to_hide_show.show();
				$rwd_menus.hide();	
			}

		});
	}
}




/**
 * Box Layout
 * 
 * @param $menu the menu object
 * @param pass_settings Custom menu settings & style 
 * 
 */
function suppa_box_layout( $menu, pass_settings )
{
	/** Boxed **/
	if( pass_settings.box_layout == 'boxed_layout' )
	{
		$menu.attr('class', 'suppaMenu_wrap');
	}
	/** Wide **/
	else if( pass_settings.box_layout == 'wide_layout' )
	{
		$menu.attr('class', 'suppaMenu_wrap suppaMenu_wrap_wide_laout');
	}
}


/**
 * Scrolling Menu
 * 
 * @param $menu the menu object
 * @param pass_settings Custom menu settings & style 
 * 
 */
function suppa_scrolling( $menu, pass_settings, device )
{
	if( device == 'desktop_laptop' && pass_settings.scroll_enable == 'on' )
	{
		var topOff = parseInt( $menu.offset().top );
		jQuery(window).scroll(function(){
			var winTop = parseInt( jQuery(window).scrollTop() );
			if( winTop >= topOff )
			{
				$menu.css({ 'position' : 'fixed' , 'top' : '0px' , 'left' : '0px' , 'width' : jQuery(window).width()+'px' });
				$menu.children('.suppaMenu').css({ 'margin-top' : '0px' , 'margin-bottom' : '0px', 'margin-left' : 'auto' , 'margin-right' : 'auto' });
			}
			else 
			{
				$menu.css({ 'position' : 'relative' , 'top' : '' , 'left' : '' , 'width' : '100%' });
				$menu.children('.suppaMenu').css({ 'margin-top' : '' , 'margin-bottom' : '' });
			}
		});
	}
	else if( device == 'mobile' &&  pass_settings.scroll_enable_mob == 'on'  )
	{
		var topOff = parseInt( $menu.offset().top );
		jQuery(window).scroll(function(){
			var winTop = parseInt( jQuery(window).scrollTop() );
			if( winTop >= topOff )
			{
				$menu.css({ 'position' : 'fixed' , 'top' : '0px' , 'left' : '0px' , 'width' : jQuery(window).width()+'px' });
				$menu.children('.suppaMenu').css({ 'margin-top' : '0px' , 'margin-bottom' : '0px', 'margin-left' : 'auto' , 'margin-right' : 'auto' });
			}
			else 
			{
				$menu.css({ 'position' : 'relative' , 'top' : '' , 'left' : '' , 'width' : '100%' });
				$menu.children('.suppaMenu').css({ 'margin-top' : '' , 'margin-bottom' : '' });
			}
		});
	}
}



/**
 * Adjust Some Menu CSS
 * 
 * @param $menu the menu object
 * @param pass_settings Custom menu settings & style 
 * 
 */
function suppa_adjust_css( $menu, pass_settings )
{
	/** Adjust Submenus width **/
	$menu.find('.suppa_submenu_posts, .suppa_submenu_html, .suppa_submenu_columns_wrap').each(function(){
		var $this 		= jQuery( this );
		var this_w 		= $this.width();
		var menu_w 		= $menu.width();
		var to_width 	= menu_w - ( parseInt( pass_settings.submenu_border_right ) + parseInt( pass_settings.submenu_border_left ) ); 
		if( this_w >= menu_w  )
		{
			$this.width( to_width );
		}
	});

	/** Adjust Top Level Links Padding **/
	$menu.find('.suppa_menu_posts, .suppa_menu_dropdown, .suppa_menu_links, .suppa_menu_html, .suppa_menu_social').each(function(){
		var $this 			= jQuery( this ),
			$paddi_left 	= parseInt( $this.children('a').css('padding-left')  ),
			$paddi_right	= parseInt( $this.children('a').css('padding-right') ),
			$paddi;

		if( $paddi_left < $paddi_right )
			$paddi = $paddi_left;
		else
			$paddi = $paddi_right;

		if( $this.children('.suppa_submenu_posts, .suppa_submenu_html, .suppa_submenu_columns_wrap, .suppa_submenu').length == 0 )
		{
			$this.children('a').css({ 'padding-left' : $paddi+'px' , 'padding-right' : $paddi+'px' })
		}
	});
}


/**
 * Adjust Submenu Align for HTML/LINKS on RWD
 * 
 * @param $menu the menu object
 * @param pass_settings Custom menu settings & style 
 * 
 */
function suppa_rwd_adjust_submenu_align( $menu, pass_settings )
{
	$menu.find('.suppa_rwd_submenu_columns_wrap, .suppa_rwd_submenu_html').each(function(){
		jQuery(this).attr('style','width:100%;');
	});
}


/**
 * Close Submenus when user click outside the menu
 * 
 * @param $menu the menu object
 * @param pass_settings Custom menu settings & style 
 * 
 */
function suppa_close_menu_when_click_outside( $menu , pass_settings )
{

	//But not when the menu is clicked
	$menu.click( function(e){
		e.stopPropagation();
	});

	jQuery(document).on( 'click' , function(e)
	{
		var $top_links = $menu.find('.suppa_menu > a');

		$menu.find('*[data-preventclick="prevent"]')
				.attr('data-preventclick','')
				.removeClass( 'suppa_menu_class_hover' );

		$menu.find('.suppa_dropdown_item_container')
			.removeClass( 'suppa_menu_class_dropdown_levels_hover' );				

		// Hide All Submenu's
		switch ( pass_settings.jquery_anim )
		{
		case "none":
		  	$menu.find('.suppa_submenu').stop(true, true).hide( pass_settings.jquery_time );
			$menu.find('.suppa_rwd_menus_container').stop(true, true).hide( pass_settings.jquery_time );
			break;
		case "fade":
			$menu.find('.suppa_submenu').stop(true, true).fadeOut( pass_settings.jquery_time );
			$menu.find('.suppa_rwd_menus_container').stop(true, true).fadeOut( pass_settings.jquery_time );
			break;
		case "slide":
			$menu.find('.suppa_submenu').stop(true, true).slideUp( pass_settings.jquery_time );
			$menu.find('.suppa_rwd_menus_container').stop(true, true).slideUp( pass_settings.jquery_time );	
			break;
		default:
			$menu.find('.suppa_submenu').stop(true, true).hide( pass_settings.jquery_anim , {} , pass_settings.jquery_time );
			$menu.find('.suppa_rwd_menus_container').stop(true, true).hide( pass_settings.jquery_anim , {} , pass_settings.jquery_time );		
		}

   		// Default CSS Values
		/** CSS **/
		$top_links.css({ 'color' : '' , 'background-color' : '' });
		$top_links.children('.ctf_suppa_fa_box_top_arrow').css({'color' : '' });
	
	});
}


/**
 * Click Trigger Function
 * 
 * @param $menu the menu object
 * @param pass_settings Custom menu settings & style 
 * 
 */
function suppa_click_trigger_funcs( $menu , pass_settings )
{
	/*** Hover : Top Level Links ***/
	$menu.children('.suppaMenu').find('.suppa_menu').each(function(){
		var $this = jQuery(this);

		$this.click(function( event ){
			
			/** Lazy Load & Retina **/
			if( $this.is('.suppa_menu_posts') )
			{
				$this.find('.suppa_lazy_load').each(function(){
					var $cur_img = jQuery(this);
					$cur_img.removeClass('suppa_lazy_load');
					// Retina
					if( pass_settings.retina_device )
					{
						$cur_img.attr( 'src', $cur_img.attr('data-retina') );
					}
					else
					{
						$cur_img.attr( 'src', $cur_img.attr('data-original') );
					}
				});
			}

			// If No Submenu Found Don't Prevent
			if( $this.children('.suppa_submenu').length > 0 )
			{
				// Prevent First Click
				if( $this.attr('data-preventClick') != "prevent" )
				{
					$this.attr('data-preventClick' , 'prevent' );
					event.preventDefault();

					/** All items except this **/
					$this.siblings().attr('data-preventClick',''); 
					
					/** Hide SubMenu's **/
					$menu.children('.suppaMenu').find('.suppa_menu').each(function(){
						var $second = jQuery(this);

						switch ( pass_settings.jquery_anim )
						{
						case "none":
							$second.children('.suppa_submenu').stop(true, true).hide( pass_settings.jquery_time );
							break;
						case "fade":
							$second.children('.suppa_submenu').stop(true, true).fadeOut( pass_settings.jquery_time );
							break;
						case "slide":
							$second.children('.suppa_submenu').stop(true, true).slideUp( pass_settings.jquery_time );
							break;
						default:
							$second.children('.suppa_submenu').stop(true, true).hide(  pass_settings.jquery_anim , {} , pass_settings.jquery_time );
						}

					});

					/** CSS **/
					$this.addClass('suppa_menu_class_hover');
					$this.siblings().removeClass('suppa_menu_class_hover');

				}

				// Display Submenu
				switch ( pass_settings.jquery_anim )
				{
				case "none":
					$this.children('.suppa_submenu').stop(true, true).show( pass_settings.jquery_time );

					break;
				case "fade":
					$this.children('.suppa_submenu').stop(true, true).fadeIn( pass_settings.jquery_time );

					break;
				case "slide":
					$this.children('.suppa_submenu').stop(true, true).slideDown( pass_settings.jquery_time );

					break;
				default:
					$this.children('.suppa_submenu').stop(true, true).show( pass_settings.jquery_anim , {} , pass_settings.jquery_time );
				}

			}
			
		});

	});

	/*** DropDown Levels Show/Hide ***/
	$menu.find('.suppa_dropdown_item_container').each(function(){
		var $this = jQuery(this);
		$this.click(function(event){
			if( $this.children('.suppa_submenu').length != 0 )
			{
				// Prevent First Click
				if( $this.attr('data-preventClick') != "prevent" )
				{
					// Display Submenu
					switch ( pass_settings.jquery_anim )
					{
					case "none":
						$this.parent().find('.suppa_submenu').stop(true, true).hide( pass_settings.jquery_time );
						$this.children('.suppa_submenu').stop(true, true).show( pass_settings.jquery_time );
					
						break;
					case "fade":
						$this.parent().find('.suppa_submenu').stop(true, true).fadeOut( pass_settings.jquery_time );
						$this.children('.suppa_submenu').stop(true, true).fadeIn( pass_settings.jquery_time );
					
						break;
					case "slide":
						$this.parent().find('.suppa_submenu').stop(true, true).slideUp( pass_settings.jquery_time );
						$this.children('.suppa_submenu').stop(true, true).slideDown( pass_settings.jquery_time );
					
						break;
					default:
						$this.parent().find('.suppa_submenu').stop(true, true).hide( pass_settings.jquery_anim , {} , pass_settings.jquery_time );
						$this.children('.suppa_submenu').stop(true, true).show( pass_settings.jquery_anim , {} , pass_settings.jquery_time );
					}

					$this.attr('data-preventClick' , 'prevent' );
					event.preventDefault();

					/** All items except $this **/
					$this.siblings().attr('data-preventClick',''); 

					/** CSS **/
					$this.addClass('suppa_menu_class_dropdown_levels_hover');
					$this.siblings().removeClass('suppa_menu_class_dropdown_levels_hover');
				}
			}
		});
	});
}


/**
 * Hover & Hover-Intent Trigger Function
 * 
 * @param $menu the menu object
 * @param pass_settings Custom menu settings & style 
 * 
 */
function suppa_hover_trigger_funcs( $menu , pass_settings , hover_type )
{
	if( hover_type == "hover" )
	{
		/*** Hover : Top Level Links ***/
		$menu.children('.suppaMenu').find('.suppa_menu').each(function(){
			var $this = jQuery(this);

			$this.mouseenter(function(event){

				switch ( pass_settings.jquery_anim )
				{
				case "none":
					$this.children('.suppa_submenu').stop(true, true).show( pass_settings.jquery_time );
					break;
				case "fade":
					$this.children('.suppa_submenu').stop(true, true).fadeIn( pass_settings.jquery_time );
					break;
				case "slide":
					$this.children('.suppa_submenu').stop(true, true).slideDown( pass_settings.jquery_time );
					break;
				default:
					$this.children('.suppa_submenu').stop(true, true).show( pass_settings.jquery_anim , {} , pass_settings.jquery_time );
				}

				/** Lazy Load & Retina **/
				if( $this.is('.suppa_menu_posts') )
				{
					$this.find('.suppa_lazy_load').each(function(){
						var $cur_img = jQuery(this);
						$cur_img.removeClass('suppa_lazy_load');
						// Retina
						if( pass_settings.retina_device )
						{
							$cur_img.attr( 'src', $cur_img.attr('data-retina') );
						}
						else
						{
							$cur_img.attr( 'src', $cur_img.attr('data-original') );
						}
					});
				}
			});

			$this.mouseleave(function(event){

				switch ( pass_settings.jquery_anim )
				{
				case "none":
					$this.children('.suppa_submenu').stop(true, true).hide( pass_settings.jquery_time );
					break;
				case "fade":
					$this.children('.suppa_submenu').stop(true, true).fadeOut( pass_settings.jquery_time );
					break;
				case "slide":
					$this.children('.suppa_submenu').stop(true, true).slideUp( pass_settings.jquery_time );
					break;
				default:
					$this.children('.suppa_submenu').stop(true, true).hide( pass_settings.jquery_anim , {} , pass_settings.jquery_time );
				}

			});

		});

	}
	else
	{
		/*** HoverIntent : Top Level Links ***/
		$menu.children('.suppaMenu').find('.suppa_menu').each(function()
		{
			var $this = jQuery(this);

			$this.hoverIntent(function(event)
				{

					switch ( pass_settings.jquery_anim )
					{
					case "none":
						$this.children('.suppa_submenu').stop(true, true).show( pass_settings.jquery_time );
						break;
					case "fade":
						$this.children('.suppa_submenu').stop(true, true).fadeIn( pass_settings.jquery_time );
						break;
					case "slide":
						$this.children('.suppa_submenu').stop(true, true).slideDown( pass_settings.jquery_time );
						break;
					default:
						$this.children('.suppa_submenu').stop(true, true).show( pass_settings.jquery_anim , {} , pass_settings.jquery_time );
					}

					/** Lazy Load & Retina **/
					if( $this.is('.suppa_menu_posts') )
					{
						$this.find('.suppa_lazy_load').each(function(){
							var $cur_img = jQuery(this);
							$cur_img.removeClass('suppa_lazy_load');
							// Retina
							if( pass_settings.retina_device )
							{
								$cur_img.attr( 'src', $cur_img.attr('data-retina') );
							}
							else
							{
								$cur_img.attr( 'src', $cur_img.attr('data-original') );
							}
						});
					}
				},

				function()
				{

					switch ( pass_settings.jquery_anim )
					{
					case "none":
						$this.children('.suppa_submenu').stop(true, true).hide( pass_settings.jquery_time );
						break;
					case "fade":
						$this.children('.suppa_submenu').stop(true, true).fadeOut( pass_settings.jquery_time );
						break;
					case "slide":
						$this.children('.suppa_submenu').stop(true, true).slideUp( pass_settings.jquery_time );
						break;
					default:
						$this.children('.suppa_submenu').stop(true, true).hide( pass_settings.jquery_anim , {} , pass_settings.jquery_time );
					}
				}		
			);
		});
	}


	/*** DropDown ***/
	$menu.find('.suppa_menu_dropdown .suppa_dropdown_item_container').each(function(){

		var $this = jQuery(this);

		$this.mouseenter(function(){

			switch ( pass_settings.jquery_anim )
			{
			case "none":
				$this.children('.suppa_submenu').stop(true, true).show( pass_settings.jquery_time );
				break;
			case "fade":
				$this.children('.suppa_submenu').stop(true, true).fadeIn( pass_settings.jquery_time );
				break;
			case "slide":
				$this.children('.suppa_submenu').stop(true, true).slideDown( pass_settings.jquery_time );
				break;
			default:
				$this.children('.suppa_submenu').stop(true, true).show( pass_settings.jquery_anim , {} , pass_settings.jquery_time );
			}

		});

		$this.mouseleave(function(){

			switch ( pass_settings.jquery_anim )
			{
			case "none":
				$this.children('.suppa_submenu').stop(true, true).hide( pass_settings.jquery_time );
				break;
			case "fade":
				$this.children('.suppa_submenu').stop(true, true).fadeOut( pass_settings.jquery_time );
				break;
			case "slide":
				$this.children('.suppa_submenu').stop(true, true).slideUp( pass_settings.jquery_time );
				break;
			default:
				$this.children('.suppa_submenu').stop(true, true).hide( pass_settings.jquery_anim , {} , pass_settings.jquery_time );
			}

		});

	});

}


/** Responsive Web Design ( smartphones & Tablets )**/

/**
 * Create RWD New Menu, Then change the class's of Menu's & Submenu's
 * @param $menu the menu object
 * @param pass_settings Custom menu settings & style 
 * 
 */
function suppa_rwd_create_func( $menu, pass_settings )
{
	// Add RWD Container
	$menu.append('<div class="suppaMenu_rwd_wrap"></div>');

	// Clone the menus
	var $clone_menus = $menu.children('.suppaMenu').html();
	var $rwd = $menu.children('.suppaMenu_rwd_wrap');
	$rwd.append('<div class="suppa_rwd_top_button_container"><span class="suppa_rwd_button"><span aria-hidden="true" class="suppa-reorder"></span></span><span class="suppa_rwd_text">'+pass_settings.rwd_text+'</span></div>');
	$rwd.append('<div class="suppa_rwd_menus_container"></div>');
	$rwd.children('.suppa_rwd_menus_container').append($clone_menus);

	// Change Menus & Submenus Class's
	$rwd.find('.suppa_menu').each(function(){
		var $menu_class_attr = jQuery(this).attr('class');
		$menu_class_attr = $menu_class_attr.replace(/suppa_menu/g,'suppa_rwd_menu');
		jQuery(this).attr('class',$menu_class_attr);
	});
	$rwd.find('.suppa_submenu').each(function(){
		var $menu_class_attr = jQuery(this).attr('class');
		$menu_class_attr = $menu_class_attr.replace(/suppa_submenu/g,'suppa_rwd_submenu');
		jQuery(this).attr('class',$menu_class_attr);
	});
	$rwd.find('.era_suppa_arrow_box').each(function(){
		var $menu_class_attr = jQuery(this).attr('class');
		$menu_class_attr = $menu_class_attr.replace(/.+/g,'era_rwd_suppa_arrow_box');
		jQuery(this).attr('class',$menu_class_attr);
	});


	// If Menu width > Parent With & Responsive Enable
	if( $menu.children('.suppaMenu').width() >= $menu.parent().width() && pass_settings.rwd_enable == 'on' )
	{
		$menu.children('.suppaMenu').css({ 'width' : '100%' });
		$menu.find('.suppaMenu_rwd_wrap').css({ 'width' : '100%' });
	}

}



/**
 * RWD Trigger : 2 Technics
 * @param $menu the menu object
 * @param pass_settings Custom menu settings & style 
 * 
 */

function suppa_rwd_trigger_func( $menu, pass_settings )
{
	var $rwd = $menu.children('.suppaMenu_rwd_wrap');
	var $rwd_c = $rwd.children('.suppa_rwd_menus_container');

	$rwd.find('.suppa_rwd_top_button_container').on({
		'click': function( event )
		{
			event.preventDefault();

			switch ( pass_settings.jquery_anim )
			{
			case "none":
				$rwd_c.stop(true,true).toggle(pass_settings.jquery_time);
				break;
			case "fade":
				$rwd_c.stop(true,true).fadeToggle(pass_settings.jquery_time);
				break;
			case "slide":
				$rwd_c.stop(true,true).slideToggle(pass_settings.jquery_time);
				break;
			default:
				$rwd_c.stop(true,true).toggle(pass_settings.jquery_anim , {} , pass_settings.jquery_time);			
			}
		}
	});

	if( pass_settings.rwd_text_t_mode == 'click_link_mode' )
	{
		$rwd.find('.suppa_rwd_menu > a ').each(function(){
			var $this = jQuery(this);
			$this.on({ 
				'click' : function( event ){

					event.preventDefault();
					event.stopPropagation();

					if( $this.is('.era_rwd_suppa_submenu_box') )
					{

						switch ( pass_settings.jquery_anim )
						{
						case "none":
							$this.parent().children('.suppa_rwd_submenu').stop(true, true).hide(pass_settings.jquery_time);
							break;
						case "fade":
							$this.parent().children('.suppa_rwd_submenu').stop(true, true).fadeOut(pass_settings.jquery_time);
							break;
						case "slide":
							$this.parent().children('.suppa_rwd_submenu').stop(true, true).slideUp(pass_settings.jquery_time);	
							break;
						default:
							$this.parent().children('.suppa_rwd_submenu').stop(true, true).hide( pass_settings.jquery_anim , {} , pass_settings.jquery_time );
						}

						$this.children('.era_rwd_suppa_arrow_box').children('span').attr('class','suppa-caret-down');
						$this.removeClass('era_rwd_suppa_submenu_box');

					}
					else 
					{

						switch ( pass_settings.jquery_anim )
						{
						case "none":
							$this.parent().children('.suppa_rwd_submenu').show(pass_settings.jquery_time);
							break;
						case "fade":
							$this.parent().children('.suppa_rwd_submenu').fadeIn(pass_settings.jquery_time);
							break;
						case "slide":
							$this.parent().children('.suppa_rwd_submenu').slideDown(pass_settings.jquery_time);	
							break;
						default:
							$this.parent().children('.suppa_rwd_submenu').show( pass_settings.jquery_anim , {} , pass_settings.jquery_time );
						}

						/** Lazy Load & Retina **/
						if( $this.parent().is('.suppa_rwd_menu_posts') )
						{
							$this.parent().find('.suppa_lazy_load').each(function(){
								var $cur_img = jQuery(this);
								$cur_img.removeClass('suppa_lazy_load');
								// Retina
								if( pass_settings.retina_device )
								{
									$cur_img.attr( 'src', $cur_img.attr('data-retina') );
								}
								else
								{
									$cur_img.attr( 'src', $cur_img.attr('data-original') );
								}
							});
						}

						$this.children('.era_rwd_suppa_arrow_box').children('span').attr('class','suppa-caret-up');
						$this.addClass('era_rwd_suppa_submenu_box');
					
					}
			
				}
			});
		});
	}
	else if( pass_settings.rwd_text_t_mode == 'click_arrow_mode' )
	{
		$rwd.find('.suppa_rwd_menu > a > .era_rwd_suppa_arrow_box ').each(function(){
			var $this = jQuery(this);

			$this.on({ 
				'click' : function( event ){

					event.preventDefault();
					event.stopPropagation();
					if( $this.is('.era_rwd_suppa_arrow_box_open') )
					{
						switch ( pass_settings.jquery_anim )
						{
						case "none":
							$this.parent().parent().children('.suppa_rwd_submenu').stop(true, true).hide(pass_settings.jquery_time);
							break;
						case "fade":
							$this.parent().parent().children('.suppa_rwd_submenu').stop(true, true).fadeOut(pass_settings.jquery_time);
							break;
						case "slide":
							$this.parent().parent().children('.suppa_rwd_submenu').stop(true, true).slideUp(pass_settings.jquery_time);	
							break;
						default:
							$this.parent().parent().children('.suppa_rwd_submenu').stop(true, true).hide( pass_settings.jquery_anim , {} , pass_settings.jquery_time );
						}

						$this.children('span').attr('class','suppa-caret-down');
						$this.removeClass('era_rwd_suppa_arrow_box_open');

					}
					else 
					{
						switch ( pass_settings.jquery_anim )
						{
						case "none":
							$this.parent().parent().children('.suppa_rwd_submenu').show(pass_settings.jquery_time);
							break;
						case "fade":
							$this.parent().parent().children('.suppa_rwd_submenu').fadeIn(pass_settings.jquery_time);
							break;
						case "slide":
							$this.parent().parent().children('.suppa_rwd_submenu').slideDown(pass_settings.jquery_time);	
							break;
						default:
							$this.parent().parent().children('.suppa_rwd_submenu').show(pass_settings.jquery_anim , {} , pass_settings.jquery_time);
						}

						/** Lazy Load & Retina **/
						if( $this.parent().parent().is('.suppa_rwd_menu_posts') )
						{
							$this.parent().parent().find('.suppa_lazy_load').each(function(){
								var $cur_img = jQuery(this);
								$cur_img.removeClass('suppa_lazy_load');
								// Retina
								if( pass_settings.retina_device )
								{
									$cur_img.attr( 'src', $cur_img.attr('data-retina') );
								}
								else
								{
									$cur_img.attr( 'src', $cur_img.attr('data-original') );
								}
							});
						}

						$this.children('span').attr('class','suppa-caret-up');
						$this.addClass('era_rwd_suppa_arrow_box_open');
					
					}
			
				}
			});

		});
	}
	else // click_both_mode
	{
		// Link click
		$rwd.find('.suppa_rwd_menu > a ').each(function(){
			var $this = jQuery(this);
			$this.on({ 
				'click' : function( event ){

					if( !$this.is('.era_rwd_suppa_submenu_box') )
					{
						event.preventDefault();
						event.stopPropagation();

						switch ( pass_settings.jquery_anim )
						{
						case "none":
							$this.parent().children('.suppa_rwd_submenu').show(pass_settings.jquery_time);
							break;
						case "fade":
							$this.parent().children('.suppa_rwd_submenu').fadeIn(pass_settings.jquery_time);
							break;
						case "slide":
							$this.parent().children('.suppa_rwd_submenu').slideDown(pass_settings.jquery_time);	
							break;
						default:
							$this.parent().children('.suppa_rwd_submenu').show( pass_settings.jquery_anim , {} , pass_settings.jquery_time );
						}

						/** Lazy Load & Retina **/
						if( $this.parent().is('.suppa_rwd_menu_posts') )
						{
							$this.parent().find('.suppa_lazy_load').each(function(){
								var $cur_img = jQuery(this);
								$cur_img.removeClass('suppa_lazy_load');
								// Retina
								if( pass_settings.retina_device )
								{
									$cur_img.attr( 'src', $cur_img.attr('data-retina') );
								}
								else
								{
									$cur_img.attr( 'src', $cur_img.attr('data-original') );
								}
							});
						}

						$this.children('.era_rwd_suppa_arrow_box')
							.addClass('era_rwd_suppa_arrow_both_open')
							.children('span')
							.attr('class','suppa-caret-up');
						$this.addClass('era_rwd_suppa_submenu_box era_rwd_suppa_link_both_open');
					}
			
				}
			});
		});

		// Arrow click
		$rwd.find('.suppa_rwd_menu > a > .era_rwd_suppa_arrow_box ').each(function(){
			var $this = jQuery(this);

			$this.on({ 
				'click' : function( event ){

					if( $this.is('.era_rwd_suppa_arrow_both_open') )
					{
						event.preventDefault();
						event.stopPropagation();

						if( $this.parent().is('.era_rwd_suppa_submenu_box') )
						{

							switch ( pass_settings.jquery_anim )
							{
							case "none":
								$this.parent().parent().children('.suppa_rwd_submenu').stop(true, true).hide(pass_settings.jquery_time);
								break;
							case "fade":
								$this.parent().parent().children('.suppa_rwd_submenu').stop(true, true).fadeOut(pass_settings.jquery_time);
								break;
							case "slide":
								$this.parent().parent().children('.suppa_rwd_submenu').stop(true, true).slideUp(pass_settings.jquery_time);	
								break;
							default:
								$this.parent().parent().children('.suppa_rwd_submenu').stop(true, true).hide( pass_settings.jquery_anim , {} , pass_settings.jquery_time );
							}

							$this.removeClass('era_rwd_suppa_arrow_both_open')
									.children('span').attr('class','suppa-caret-down');
							$this.parent().removeClass('era_rwd_suppa_submenu_box era_rwd_suppa_link_both_open');
						}						
					}

			
				}
			});

		});
	}
}



/**
 * jQuery.suppa_browser (http://detectmobilebrowser.com/)
 *
 * jQuery.suppa_browser will be true if the browser is a mobile device
 *
 **/
(function(a){jQuery.suppa_browser=/android.+mobile|avantgo|bada\/|blackberry|blazer|compal|elaine|fennec|hiptop|iemobile|ip(hone|od)|iris|kindle|lge |maemo|midp|mmp|netfront|opera m(ob|in)i|palm( os)?|phone|p(ixi|re)\/|plucker|pocket|psp|symbian|treo|up\.(browser|link)|vodafone|wap|windows (ce|phone)|xda|xiino/i.test(a)||/1207|6310|6590|3gso|4thp|50[1-6]i|770s|802s|a wa|abac|ac(er|oo|s\-)|ai(ko|rn)|al(av|ca|co)|amoi|an(ex|ny|yw)|aptu|ar(ch|go)|as(te|us)|attw|au(di|\-m|r |s )|avan|be(ck|ll|nq)|bi(lb|rd)|bl(ac|az)|br(e|v)w|bumb|bw\-(n|u)|c55\/|capi|ccwa|cdm\-|cell|chtm|cldc|cmd\-|co(mp|nd)|craw|da(it|ll|ng)|dbte|dc\-s|devi|dica|dmob|do(c|p)o|ds(12|\-d)|el(49|ai)|em(l2|ul)|er(ic|k0)|esl8|ez([4-7]0|os|wa|ze)|fetc|fly(\-|_)|g1 u|g560|gene|gf\-5|g\-mo|go(\.w|od)|gr(ad|un)|haie|hcit|hd\-(m|p|t)|hei\-|hi(pt|ta)|hp( i|ip)|hs\-c|ht(c(\-| |_|a|g|p|s|t)|tp)|hu(aw|tc)|i\-(20|go|ma)|i230|iac( |\-|\/)|ibro|idea|ig01|ikom|im1k|inno|ipaq|iris|ja(t|v)a|jbro|jemu|jigs|kddi|keji|kgt( |\/)|klon|kpt |kwc\-|kyo(c|k)|le(no|xi)|lg( g|\/(k|l|u)|50|54|e\-|e\/|\-[a-w])|libw|lynx|m1\-w|m3ga|m50\/|ma(te|ui|xo)|mc(01|21|ca)|m\-cr|me(di|rc|ri)|mi(o8|oa|ts)|mmef|mo(01|02|bi|de|do|t(\-| |o|v)|zz)|mt(50|p1|v )|mwbp|mywa|n10[0-2]|n20[2-3]|n30(0|2)|n50(0|2|5)|n7(0(0|1)|10)|ne((c|m)\-|on|tf|wf|wg|wt)|nok(6|i)|nzph|o2im|op(ti|wv)|oran|owg1|p800|pan(a|d|t)|pdxg|pg(13|\-([1-8]|c))|phil|pire|pl(ay|uc)|pn\-2|po(ck|rt|se)|prox|psio|pt\-g|qa\-a|qc(07|12|21|32|60|\-[2-7]|i\-)|qtek|r380|r600|raks|rim9|ro(ve|zo)|s55\/|sa(ge|ma|mm|ms|ny|va)|sc(01|h\-|oo|p\-)|sdk\/|se(c(\-|0|1)|47|mc|nd|ri)|sgh\-|shar|sie(\-|m)|sk\-0|sl(45|id)|sm(al|ar|b3|it|t5)|so(ft|ny)|sp(01|h\-|v\-|v )|sy(01|mb)|t2(18|50)|t6(00|10|18)|ta(gt|lk)|tcl\-|tdg\-|tel(i|m)|tim\-|t\-mo|to(pl|sh)|ts(70|m\-|m3|m5)|tx\-9|up(\.b|g1|si)|utst|v400|v750|veri|vi(rg|te)|vk(40|5[0-3]|\-v)|vm40|voda|vulc|vx(52|53|60|61|70|80|81|83|85|98)|w3c(\-| )|webc|whit|wi(g |nc|nw)|wmlb|wonu|x700|xda(\-|2|g)|yas\-|your|zeto|zte\-/i.test(a.substr(0,4))})(navigator.userAgent||navigator.vendor||window.opera);