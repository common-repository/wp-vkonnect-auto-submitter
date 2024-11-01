<?php

/* 
    Plugin Name:  		WP vKonnect Auto Submitter
    Plugin URI: 		http://vkonnect.com/
    Description: 		vKonnect automated submitter WordPress plugin is a one big platform to share your social media content to 50+ top social networks. vKonnect social auto bookmarking tool is really easy to use. 
    Author: 			vKonnect
    Version: 			1.0.3 
    Requires at least: 	3.0
    Tested up to: 		3.4.1
    Author URI: 		http://vkonnect.com/
    License: 			GPLv2 or later
    License URI: 		http://www.gnu.org/licenses/gpl-2.0.html
*/

/*  Copyright (c) 2013, vkonnect.com 
	All rights reserved.

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License, version 2, as 
    published by the Free Software Foundation.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

session_start();

   	include('include.php');
   
	global $wpdb;

    function vk_requires_wordpress_version() {
		global $wp_version;
		$wp_version;
		$plugin = plugin_basename( __FILE__ );
		$plugin_data = get_plugin_data( __FILE__, false );
		$require_wp = "3.0";
	 
		if ( version_compare( $wp_version, $require_wp, "<" ) ) {
			if( is_plugin_active($plugin) ) {
				deactivate_plugins( $plugin );
				wp_die( "<strong>".$plugin_data['Name']."</strong> requires <strong>WordPress ".$require_wp."</strong> or higher, and has been deactivated! Please upgrade WordPress and try again.<br /><br />Back to the WordPress <a href='".get_admin_url(null, 'plugins.php')."'>Plugins page</a>. Or you want to upgrade <a href='http://wordpress.org'> WordPress. </a>" );
			}
		}
	}
	add_action( 'admin_init', 'vk_requires_wordpress_version' );


    function vkonnect_adds_scripts_to_the_head() {
    	

    	//include java script and stylesheets files to the header
 
    	wp_enqueue_script('jQuery'); 
	
	    wp_register_script( 'vk_fancy_box_js_script', 	VK_URL . '/js/jquery.fancybox-1.3.4.js');
	    wp_register_style( 'vk_fancybox_css_style', 	VK_URL . '/js/jquery.fancybox-1.3.4.css' );
	    wp_register_style( 'vk_main_css_style', 		VK_URL . '/css/style.css');
	    wp_register_script( 'vk_custom_js', 			VK_URL . '/js/custom.js');
	    wp_register_script( 'vk_cookie_js', 			VK_URL . '/js/jquery.cookie.js');
	    wp_register_script( 'vk_tipsy_tooltip_js', 		VK_URL . '/js/jquery.tipsy.js');
	    wp_register_style( 'vk_tipsy_css_style', 		VK_URL . '/css/tipsy.css');

	  
	    wp_enqueue_script( 'vk_fancy_box_js_script' );
	    wp_enqueue_style( 'vk_fancybox_css_style' );
	    wp_enqueue_style( 'vk_main_css_style' );
	    wp_enqueue_script( 'vk_custom_js' );
	    wp_enqueue_script( 'vk_cookie_js' );

	    wp_enqueue_script( 'vk_tipsy_tooltip_js' );
	    wp_enqueue_style( 'vk_tipsy_css_style' );
 
	}
 
	add_action( 'init', 'vkonnect_adds_scripts_to_the_head' );

	//require('update-notifier.php');
	register_activation_hook(__FILE__,'vk_plugin_install');
	register_deactivation_hook(__FILE__ , 'vk_plugin_uninstall' );

	function vk_plugin_install() {
		global $wpdb;
	    //	CREATE ALL PLUGIN TABLES HERE..
	    if (function_exists('is_multisite') && is_multisite()) {

	        // check if it is a network activation - if so, run the activation function for each blog id
			if (isset($_GET['networkwide']) && ($_GET['networkwide'] == 1)) {
	            $old_blog = $wpdb->blogid;

	            // Get all blog ids
	            $blogids = $wpdb->get_col($wpdb->prepare("SELECT blog_id FROM $wpdb->blogs"));

	            foreach ($blogids as $blog_id) {
	                switch_to_blog($blog_id);
	                vk_plugin_create_db_table();
	            }
	        } else {
	            //	not admin network
	            switch_to_blog($old_blog);
	            vk_plugin_create_db_table();
	        }
	    } else {
	        //	not multisite
	        vk_plugin_create_db_table();
	    }
	}

	function vk_plugin_create_db_table($current_blog_id = NULL) {
	    global $wpdb;

	    //VK_TABLE_PREFIX
	    if ($current_blog_id) {
	        if ($current_blog_id == 1) {
	            $table_prefix = VK_TABLE_PREFIX;
	        } else {
	           echo $table_prefix = VK_TABLE_PREFIX . $current_blog_id . '_';
	        }
	    } else {
	        $table_prefix = VK_TABLE_PREFIX;
	    }
	    /* 		Create Table vk_user_info	 */
	    include('includes/db_table.php');
	}


	function vk_plugin_uninstall() {
		global $wpdb;

		session_unset();
		session_destroy();
                
        $old_blog = '';
                
	    //	DROP ALL PLUGIN TABLES HERE..
	    if (function_exists('is_multisite') && is_multisite()) {
	        if (isset($_GET['networkwide']) && ($_GET['networkwide'] == 1)) {
	            $old_blog = $wpdb->blogid;

	            $blogids = $wpdb->get_col($wpdb->prepare("SELECT blog_id FROM $wpdb->blogs"));
	            
	            foreach ($blogids as $blog_id) {
	                switch_to_blog($blog_id);
	                vk_plugin_drop_db_table();
	            }
	        } else {
	        	switch_to_blog($old_blog);
	            vk_plugin_drop_db_table();
	        }
	    } else {
	        vk_plugin_drop_db_table();
	    }

	}

	function vk_plugin_drop_db_table($current_blog_id = NULL) {
	    global $wpdb;

	    if ($current_blog_id) {
	        if ($current_blog_id == 1) {
	            $table_prefix = VK_TABLE_PREFIX;
	        } else {
	            $table_prefix = VK_TABLE_PREFIX . $current_blog_id . '_';
	        }
	    } else {
	        $table_prefix = VK_TABLE_PREFIX;
	    }
	    /* 		DROP Table vk_user_info	 */

	      include('includes/db_uninstall.php');
	}

	if ( is_admin() ) {
		add_action( 'admin_menu', 'vk_admin_menu' );

		// set the menu of the page 
		function vk_admin_menu() { 
			$page_title = "vKonnect setting";
			$menu_title = "vKonnect setting";
			$capability = 'manage_options';
			$menu_slug = __FILE__;
			$function = "vk_admin_page_setting";
			add_menu_page($page_title, $menu_title, $capability, $menu_slug, $function);
		}
	}

	function vk_admin_page_setting() {
		if ( !current_user_can('manage_options') ) {
       		wp_die('You do not have sufficient permissions to access this page.');
    	}

		include('includes/login_form.php');
	}

	add_action('admin_footer', 'footer_text');

	function footer_text() 
	{
		echo "<div id='wpfooter'>";
		echo "<p id='footer' style=\"text-align:center;font-weight:bold;font-size:14px;\">Created by vKonnect.com</p>";
		echo "</div>";
	} 


?>
