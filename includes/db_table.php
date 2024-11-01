<?php

	global $wpdb;

	require_once(ABSPATH . 'wp-admin/includes/upgrade.php');

	$db_user_table = "
	CREATE TABLE IF NOT EXISTS " . $table_prefix . VK_SETTING_USER . " (
		id  INT(11) NOT NULL AUTO_INCREMENT,
		username VARCHAR( 100 ) NOT NULL,
		password VARCHAR( 100 ) NOT NULL,
		authenticationtoken VARCHAR ( 1000 ),
		account_type VARCHAR( 100 ),
		active_services VARCHAR ( 1000000 ),
		post_profile_id INT ( 100 ),
		profile_name varchar(50),
		last_updated TIMESTAMP,
		PRIMARY KEY (id)
		);";

	dbDelta($db_user_table);

	$db_counter_table = " 
	CREATE TABLE IF NOT EXISTS " . $table_prefix . VK_POST_COUNTER . " (
		post_id int(11) NOT NULL AUTO_INCREMENT,
		counter int(100),
		PRIMARY KEY (post_id)
		);";

	dbDelta($db_counter_table);

	$db_workspace_table = " 
	CREATE TABLE IF NOT EXISTS " . $table_prefix . VK_WORKSPACE_USER . " (
		id int(11) NOT NULL AUTO_INCREMENT,
		post__profile_id int(100),
		profile_name varchar(100),
		autherntication_token varchar(100),
		account_type varchar(100),
		activate int(10),
		last_updated TIMESTAMP,
		PRIMARY KEY (id)
		);";

	dbDelta($db_workspace_table);
	
	
	

?>