<?php

	global $wpdb;

	$db_user_table = "drop table if exists " . $table_prefix . VK_SETTING_USER;
 	$wpdb->query($db_user_table);

 	$db_counter_table = "drop table if exists " . $table_prefix . VK_POST_COUNTER;
 	$wpdb->query($db_counter_table);

 	$db_counter_table = "drop table if exists " . $table_prefix . VK_WORKSPACE_USER;
 	$wpdb->query($db_counter_table);


?>