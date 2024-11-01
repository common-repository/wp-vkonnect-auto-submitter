<?php
/*
 * 
 *	Constant only which are list bellow, there is also an othe file
 *	Database Table Names, Folder, Site related
 *	
 *	Note: Please use related files for DEFINES.
 */
	

	/*	START		Database	START		*/
	global $wpdb;
	global $current_blog_id;

	// Database other defines
	$vk_table_prefix = $wpdb->prefix;
	define('VK_TABLE_PREFIX', $vk_table_prefix);

	/*	ENDS		Datase Tables	ENDS		*/




	/* Verion of plugin */
	define('VKONNECT_VERSION', '1.0.2');

	/*	 END 	version plugin 	*/




	/*	 CURL url defines 	*/
	define('VKONNECT_HOST', 'http://54.83.21.153');
	
	define('GET_ACCESS_TOKEN', VKONNECT_HOST . '/MobileClient/UserServiceJson.asmx/VK_Json_GetUserAccesToken');
	define('GET_POST_PROFILES', VKONNECT_HOST . '/MobileClient/PostProfilesServiceJson.asmx/VK_JSON_GetPostProfiles');
	//define('GET_PROFILE_SUBSCRIPTION_DETAIL_BRIEF', VKONNECT_HOST . '/MobileClient/PostProfilesServiceJson.asmx/VK_JSON_GetProfileSubscriptionsDetailBrief');
	define('GET_PROFILE_SUBSCRIPTION_DETAIL_BRIEF', VKONNECT_HOST . '/MobileClient/PostProfilesServiceJson.asmx/VK_JSON_GetProfileSubscriptionsBrief');
	define('SAVE_POST_CONTENTS', VKONNECT_HOST . '/MobileClient/PublishServiceJson.asmx/VK_JSON_SavePostContents');
	define('IS_PAYMENT', VKONNECT_HOST.'/MobileClient/UserServiceJson.asmx/VK_Json_IsPaymentRequired');
	/*	 ENDS 	CURL url 	ENDS 	*/




	// Tables
	define('VK_SETTING_USER', 'vkonnect_user_info');
	define('VK_POST_COUNTER', 'vk_counter');
	define('VK_WORKSPACE_USER', 'vk_workspace_user');

	/* 	ENDS		Datase Tables	ENDS		 */




	/*	STARTS		FOLDERS DEFINES	STARTS		*/
	$siteurl = get_option('siteurl');

	define('VK_SITE_URL', $siteurl . 'wp-admin/options-general.php?page=__FILE__');
	define('VK_FOLDER', dirname(plugin_basename(__FILE__)));
	define('VK_URL', $siteurl.'/wp-content/plugins/' . VK_FOLDER);
	define('VK_FILE_PATH', dirname(__FILE__));
	define('VK_DIR_NAME', basename(VK_FILE_PATH));
	//echo VK_FILE_PATH;

	/*	ENDS		FOLDERS DEFINES	ENDS		*/

	
?>