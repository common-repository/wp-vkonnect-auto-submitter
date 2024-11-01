<?php

/*
 * 	File for the General functions
 * 	Can use database calls but if large function with db hits then 
 * 	make seperate file to related functionality or go for PHP Class
 * 	
 * 	
 */


/* * ****************************************************************************************************************************
  DATABASE HIT FUNCTION
 * **************************************************************************************************************************** */

	function get_list_post_vk(){

		$args = array('posts_per_page' => -1, 'offset'=> 0, 'post_type' => 'post', 'suppress_filters'  => true);

		return get_posts($args);
	}

	function number_rows(){

		global $wpdb;

		$table_name = VK_TABLE_PREFIX . VK_SETTING_USER; //	create table name

		$wpdb->get_results("SELECT * FROM $table_name");
		return $wpdb->num_rows;
	}

	function fetch_result(){
		global $wpdb;

		$table_name = VK_TABLE_PREFIX . VK_SETTING_USER; //	create table name

		return $wpdb->get_results("SELECT * FROM $table_name", ARRAY_A);
	}

	function insert_data( $username, $password, $authenticationcode, $account_type ){

		global $wpdb;

		$table_name = VK_TABLE_PREFIX . VK_SETTING_USER; //	create table name

		return $result = $wpdb->insert(
								$table_name, 
								array(
									'username' => $username,
									'password' => $password,
									'authenticationtoken' => $authenticationcode,
									'account_type' => $account_type
									),
								array(
									'%s',
									'%s',
									'%s',
									'%s'
									)
								);

	}

	function update_data( $json_data, $postfieldid, $profilename, $id ) {

		global $wpdb;

		$table_name = VK_TABLE_PREFIX . VK_SETTING_USER;

		$wpdb->update( 
				$table_name, 
				array( 
					'active_services' => $json_data,	// json data
					'post_profile_id' => $postfieldid,
					'profile_name' => $profilename
				), 
				array( 'id' => $id ), 
				array( 
					'%s',
					'%d',
					'%s'
				), 
				array( '%d' ) 
			);

	}

// Script to test if the CURL extension is installed on this server

// Define function to test
function _is_curl_installed() {
    if  (in_array  ('curl', get_loaded_extensions())) {
        return true;
    }
    else {
        return false;
    }
}

function _isCurl(){
    return function_exists('curl_version');
}


function checkDuplicateRows($id){

		global $wpdb;

		$table_name = VK_TABLE_PREFIX . VK_WORKSPACE_USER; //	create table name

		$wpdb->get_results("SELECT * FROM $table_name WHERE post__profile_id = '$id'");
		return $wpdb->num_rows;
	}

function actionCreate( $postProfileID, $profileName, $authenticationcode, $account_type, $activate ){

		global $wpdb;

		$table_name = VK_TABLE_PREFIX . VK_WORKSPACE_USER; //	create table name
		
			return $result = $wpdb->insert(
									$table_name, 
									array(
										'post__profile_id' => $postProfileID,
										'profile_name' => $profileName,
										'autherntication_token' => $authenticationcode,
										'account_type' => $account_type,
										'activate' => $activate
										),
									array(
										'%d',
										'%s',
										'%s',
										'%s',
										'%d'
										)
									);

	}

	function update_activate_column($activate, $id){

		global $wpdb;

		$table_name = VK_TABLE_PREFIX . VK_WORKSPACE_USER;

		$wpdb->query("UPDATE $table_name SET activate='$activate' WHERE post__profile_id != '$id'");

	}

	function actionUpdate( $profileName, $activate, $postProfileID ) {

		global $wpdb;

		$table_name = VK_TABLE_PREFIX . VK_WORKSPACE_USER;

		return $wpdb->update( 
				$table_name, 
				array( 
					'profile_name' => $profileName,	// json data
					'activate' => $activate
				), 
				array( 'post__profile_id' => $postProfileID ), 
				array( 
					'%s',
					'%d'
				), 
				array( '%d' ) 
			);

	}

	function actionDelete( $postProfileID ) {

		global $wpdb;

		$table_name = VK_TABLE_PREFIX . VK_WORKSPACE_USER;

		$wpdb->query("DELETE FROM $table_name WHERE post__profile_id = '$postProfileID'");

	}

	function actionUpdateonDelete($activate, $postProfileID ) {

		global $wpdb;

		$table_name = VK_TABLE_PREFIX . VK_WORKSPACE_USER;

		return $wpdb->update( 
				$table_name, 
				array( 
					//'profile_name' => $profileName,	// json data
					'activate' => $activate
				), 
				array( 'post__profile_id' => $postProfileID ), 
				array( 
					//'%s',
					'%d'
				), 
				array( '%d' ) 
			);

	}

	function getAll() {

		global $wpdb;

		$table_name = VK_TABLE_PREFIX . VK_WORKSPACE_USER;

		return $wpdb->get_results("SELECT post__profile_id FROM $table_name", ARRAY_A);

	}