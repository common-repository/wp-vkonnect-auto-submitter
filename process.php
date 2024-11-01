<?php

$url = explode('wp-content', $_SERVER['SCRIPT_FILENAME']);

require_once( $url[0] . '/wp-load.php' );
include_once('include.php');

			
$table_name = VK_TABLE_PREFIX . VK_SETTING_USER;
$table_workspace_name = VK_TABLE_PREFIX.VK_WORKSPACE_USER;

if (!strstr($_SERVER['REQUEST_URI'],'process.php')){
	header('HTTP/1.0 404 Not Found');
	return;
}

else{

if (!_is_curl_installed()) {
	$message['status'] = 0;
  	$message['message'] = "Please Activate cURL to LogIn";;
  	//$message['curl_status'] = "installed"; 
  	echo json_encode($message);

} 
else{
	if (!_iscurl()){
		$message['status'] = 0;
  		$message['message'] = "cURL is NOT Enabled on this server";;
  		echo json_encode($message);
	}
	else{

if( isset($_REQUEST['cmd']) ){
	$scure_url = htmlspecialchars( $_REQUEST['cmd'] );
	switch ( $scure_url ) {
		
		// in this case get user access token from the vkonnect api
		case 'LoginUser':

		$message = array();
		$message['status'] = 0;
		$message['authencode'] = '';
		$message['message'] = '';
		$message['services'] = '';
		$message['content'] = '';
		
		$username = trim($_POST['username']);
		$password = trim($_POST['password']);

		if( !empty($username) && !empty($password) ){

			$password_hash = md5($password);

			// Get cURL resource
			$curl_resource = curl_init();

			// Set some options - we are passing in a useragent too here
			curl_setopt_array($curl_resource, array(
			    CURLOPT_RETURNTRANSFER => 1,
			    CURLOPT_URL => GET_ACCESS_TOKEN,
			    CURLOPT_POST => 1,
			));
			curl_setopt($curl_resource, CURLOPT_POSTFIELDS, "UserName=".$username."&Password=".$password);

			// execute curl request recieve data in json format
			$server_output = curl_exec ($curl_resource); 

			if( $server_output ){

				// decode json data in object form
				$return_object = json_decode($server_output);
			//print_r($return_object);
				if($return_object){
				// get AuthenticationToken of the user of vkonnect
				if( $return_object->AuthenticationToken ){
					if($return_object->IsEmailVarified){
						$message['status'] = 1;
						$message['authencode'] = $return_object->AuthenticationToken;
						$authenticationcode = $return_object->AuthenticationToken;
						$account_type = $return_object->AccountType;
						$_SESSION['accountType'] = $account_type;
						$_SESSION['user'] = $username;
								
						$count_rows = number_rows( );

						if( $count_rows == 0 ){

							$result = insert_data( $username, $password_hash, $authenticationcode, $account_type );

							if( $result ){
								$message['status'] = 1;
								$message['message'] = "You are <b> logged in</b> successfully.";
							}
						}
						else{
							$field = fetch_result();
							$id = $field[0]['id'];
							$usql = $wpdb->prepare("UPDATE $table_name SET username = '$username', password ='$password_hash', authenticationtoken = '$authenticationcode' WHERE id='$id'");
		        			$wpdb->query($usql);
		        			$message['message'] = "Updated successfully.";
						}
					}
					else{
						$message['status'] = 0;
						$message['message'] = "Email is not verified";
					}
				}
				else{
					$message['status'] = 0;
					$message['authencode'] = '';
					$message['message'] = "Invalid Username or Password";
				}
			}
			else{
				$message['status'] = 0;
				$message['authencode'] = '';
				$message['message'] = "CURL Error: " . curl_error($curl_resource);
			}

			}
			else{
				$message['status'] = 0;
				$message['authencode'] = '';
				$message['message'] = "CURL Error: " . curl_error($curl_resource);
			}
		}
		else{
			$message['status'] = 0;
			$message['message'] = "Please enter Username or Password!";
		}

		// data send back to the url in json format
		echo json_encode($message);

		// close the curl request
		curl_close($curl_resource);

		break;

		// in this case GetPostProfiles, Active Workspaces And Services Detailed of the user of vkonnect
		case 'AuthenticationToken':
		
		$authenticationToken = $_POST['authenticationToken'];

		$curl_resource = curl_init();

		// Set some options - we are passing in a useragent too here
		curl_setopt_array($curl_resource, array(
		    CURLOPT_RETURNTRANSFER => 1,
		    CURLOPT_URL => GET_POST_PROFILES,
		    CURLOPT_POST => 1,
		));

		// Get Post Profiles Of User And Active Workspaces By This Request
		curl_setopt($curl_resource, CURLOPT_POSTFIELDS, "UserID=".$authenticationToken);

		$server_output = curl_exec($curl_resource);

		if( $server_output ){

			$return_object = json_decode($server_output, true);

			$SubscriptionTypeID = 0;

			$curl_resource2 = curl_init();

			// Set some options - Get Services Detailed
			curl_setopt_array($curl_resource2, array(
				    CURLOPT_RETURNTRANSFER => 1,
				    CURLOPT_URL => GET_PROFILE_SUBSCRIPTION_DETAIL_BRIEF,
				    CURLOPT_POST => 1,
				));

			//if($return_object){
				//for ($i=0; $i<count($return_object); $i++) {
					$numberRows = checkDuplicateRows($return_object[0]['PostProfileId']);
					
					if($numberRows == 0){
						actionCreate($return_object[0]['PostProfileId'], $return_object[0]['ProfileName'], $authenticationToken, $_SESSION['accountType'], 1);
					}
					else{
						$result = actionUpdate($return_object[$i]['ProfileName'], $return_object[0]['PostProfileId']);
					}
				//}
			//}

			// Get Services Detailed By This CURL Set Option By Default It Will Display First One
			curl_setopt($curl_resource2, CURLOPT_POSTFIELDS, "UserID=".$authenticationToken."&PostProfileID=".$return_object[0]['PostProfileId']."&SubscriptionTypeID=".$SubscriptionTypeID);
			
			$server_output2 = curl_exec($curl_resource2);
		
			//if( $return_object[0]['PostProfileId'] ){
			if( $server_output2 ){

				$fields = fetch_result();

				$id = $fields[0]['id'];

				update_data( $server_output2, $return_object[0]['PostProfileId'], $return_object[0]['ProfileName'], $id );

				//$return_object2 = json_decode($server_output2, true);
				//print_r($return_object2);
				
				//echo $SubscriptionId = $return_object2[0]['SubscriptionId'];
	
				//if($SubscriptionId ){

				$message['status'] = 1;
				$message['message'] = "Connected Sucessfully to <b>vKonnect.com.</b>";
				$message['content'] = $return_object;
				$_SESSION['totalWorkspace'] = count($return_object);
				$_SESSION['postProfileID'] = $return_object[0]['PostProfileId'];
				$_SESSION['postProfileName'] = $return_object[0]['ProfileName'];
				/*}
				else{
					$message['status'] = 0;
					$message['message'] = "Please check your credentials its not correct.";
				}*/
			}
			else{
				$message['status'] = 0;
				$message['PostProfileId'] = '';
				$message['message'] = "CURL Error: " . curl_error($curl_resource2);
			}
					
			curl_close($curl_resource2);
		//}
		}
		else{
			$message['status'] = 0;
			$message['PostProfileId'] = '';
			$message['message'] = "CURL Error: " . curl_error($curl_resource);
		}
		echo json_encode($message);
		
		curl_close($curl_resource);

		break;

		// in this case delete all the saved data from the table
		case 'Disconnect':

		$result = $wpdb->query("DELETE FROM $table_name");

		if( $result ){
			session_unset();
			session_destroy();
			unset($_COOKIE['PHPSESSID']);
			$result1 = $wpdb->query("DELETE FROM wp_vk_counter");
			$result2 = $wpdb->query("DELETE FROM wp_vk_workspace_user");
			$message['status'] = 1;
			$message['message'] = "You are <b>Disconnected!</b>";
		}
		
		echo json_encode($message);

		break;

		// By This Case Pass The Posts Content To The Vkonnect Server
		case 'SavePostContents':

		global $wpdb;
		
		if(isset($_POST['social_chk']) && !empty($_POST['social_chk'])){

			$rows = fetch_result();

			$authenticationcode = $rows[0]['authenticationtoken'];
			$postprofileid = $rows[0]['post_profile_id'];
			$list_active_services = $rows[0]['active_services'];
			$PostingCategoryID = 0;
			//echo $authenticationcode;

			// initialize the curl
			$curl_resource1 = curl_init();

			// Set some options - we are passing in a useragent too here
			curl_setopt_array($curl_resource1, array(
			    CURLOPT_RETURNTRANSFER => 1,
			    CURLOPT_URL => IS_PAYMENT,
			    CURLOPT_POST => 1,
			));

			//Through This CURL Request Pass Data To Vkonnet Server
			curl_setopt($curl_resource1, CURLOPT_POSTFIELDS, "UserID=".$authenticationcode);

			// execute the curl request
			$server_output1 = curl_exec ($curl_resource1);
			//print_r($server_output1);
			//echo $server_output1;die;
			if($server_output1 == 'false'){
				//echo 'hello';die;
				$social_chk = $_POST['social_chk'];

			if($social_chk != ''){
				foreach($social_chk as $key => $val){

						$ProfileSubscriptionIds = $val;
				}
			}
			else{
				$message['status'] = 0;
				$message['message'] = "Please select at least one <b>service</b>";
			}

			$publish_id = $_POST['publish_id'];

			$key_id = explode( '_', $publish_id );

			$post_id = $key_id[1];

			$queried_post = get_post($post_id);

			$post_tag = wp_get_post_tags( $post_id, array( 'fields' => 'names' ) );

			$tags = implode(",", $post_tag);

			$post_id = $queried_post->ID;

			$counter = 0;


			$table_name = VK_TABLE_PREFIX . VK_POST_COUNTER; //	create table name

			$wpdb->get_results("SELECT * FROM $table_name WHERE post_id = '$post_id'");

			$count_rows = $wpdb->num_rows;

			if( $count_rows == 0 ){

				$result = $wpdb->insert(
										$table_name, 
										array(
											'post_id' => $post_id,
											'counter' => $counter + 1
											),
										array(
											'%d',
											'%d'
											)
										);
			}
			else{

				$sql = $wpdb->prepare("UPDATE $table_name SET counter = counter + 1 WHERE post_id = '$post_id'");
				$result = $wpdb->query($sql);
			}

			
			$PostingCategoryID = 0;
			$ScheduleOptionID = 1;
			$ScheduleDateTime2 = '';
			$ScheduleDateTime = round(microtime(true) * 1000);


			// create the array for data posting in json to vkonnect	
			$array_json = array(
				'Title'			=> $queried_post->post_title,
				'Description'	=> $queried_post->post_content,
				'Url'			=> $queried_post->guid,
				'ProfileSubscriptionIds' => $ProfileSubscriptionIds,
				'PostingCategoryID' => $PostingCategoryID,
				'Tags' => $tags,
				'ScheduleDateTime' => $ScheduleDateTime,
				'ScheduleDateTime2' => $ScheduleDateTime2,
				'ScheduleOptionID' => $ScheduleOptionID

				); 

			// encode and pass data in json format to Vkonnect Server
			$data_json_format = json_encode($array_json);

			// initialize the curl
			$curl_resource = curl_init();

			// Set some options - we are passing in a useragent too here
			curl_setopt_array($curl_resource, array(
			    CURLOPT_RETURNTRANSFER => 1,
			    CURLOPT_URL => SAVE_POST_CONTENTS,
			    CURLOPT_POST => 1,
			));

			//Through This CURL Request Pass Data To Vkonnet Server
			curl_setopt($curl_resource, CURLOPT_POSTFIELDS, "UserID=".$authenticationcode."&PostProfileID=".$postprofileid."&postJobContent_json=".$data_json_format);

			// execute the curl request
			$server_output = curl_exec ($curl_resource);
			//print_r($server_output);
			if( $server_output ){
				
				// count the number of rows in table
				$field = $wpdb->get_results("SELECT * FROM $table_name WHERE post_id = '$post_id'");
				$number_rows = $wpdb->num_rows;
				if($number_rows > 0){
					$message['status'] = 1;
					$message['message'] = $server_output;
				}
			}
			else{

				$message['status'] = 0;
				$message['message'] = "CURL Error: " . curl_error($curl_resource);
			}

			// close curl request
			curl_close($curl_resource);
				
			}
			else if($server_output1 == 'true'){
				//echo "hello1";die;
				$message['status'] = 2;
				$message['message'] = "Please pay your dues!";
			}
			else{
				$message['status'] = 0;
				$message['message'] = "CURL Error: " . curl_error($curl_resource1);
			}

			// close curl request
			curl_close($curl_resource1);

		}
		else{
			$message['status'] = 0;
			$message['message'] = "Please select at least one Social Profile!";
		}
		
		// return the response in json
		echo json_encode($message);

		break;

		// in this case check services update if any changes in Vkonnect Server by the user
		// For example if user add or delete service this switch case check updation of any
		case 'UpdateServices':

		global $wpdb;

		// fetch the data fro the database
		$fields = fetch_result();

		//count the numbers of rows in the table
		$count_rows = number_rows();

		if( $count_rows > 0 ){

			$authenticationcode = $fields[0]['authenticationtoken'];
			$postprofileid = $fields[0]['post_profile_id'];
			$postprofilename = $fields[0]['profile_name'];
			$id = $fields[0]['id'];
			$SubscriptionTypeID = 0;

			$curl_resource = curl_init();

			// Set some options - we are passing in a useragent too here
			curl_setopt_array($curl_resource, array(
			    CURLOPT_RETURNTRANSFER => 1,
			    CURLOPT_URL => GET_PROFILE_SUBSCRIPTION_DETAIL_BRIEF,	// get the brief detail of user active services
			    CURLOPT_POST => 1,
			));
			curl_setopt($curl_resource, CURLOPT_POSTFIELDS, "UserID=".$authenticationcode."&PostProfileID=".$postprofileid."&SubscriptionTypeID=".$SubscriptionTypeID);

			$server_output = curl_exec($curl_resource);

			if( $server_output ){
				$return_object = json_decode($server_output);

				update_data( $server_output, $postprofileid, $postprofilename, $id );

				$ul = '';
				// Show List Of Services In Well Formatted Style And Pass The Reponse To The Respected Request
				// Of Ajax
				if($return_object){
					foreach( $return_object as $wokspaceKey => $workspaceVal ){
						$disbaled = '';
						$title = '';
		            	if($workspaceVal->CredentialStatus == 'Invalid'){
		            		$disbaled = 'disabled';
		            		$title = 'Invalid Social Profile';
		            	}

		            	if(strpos($workspaceVal->SubscriptionName, '.') !== false){
		            		$subscriptionName = str_replace('.', '-', $workspaceVal->SubscriptionName);
		            	}
		            	else{
		            		$subscriptionName = str_replace(' ', '', $workspaceVal->SubscriptionName);
		            	}

		                $ul .= '<span class="col_a21">
		                <span class="service_name_lh30" title="'.$title.'">
		                <span class="chkbx">
		                    <input title="" '.$disbaled.' type="checkbox" class="chk check_service" name="user_ids[]" 
		                        value="'.$workspaceVal->ProfileSubscriptionID.'">
		                </span>
		                <span class="report_icon_30x30 r30_'.$subscriptionName.'">
		                </span>'.substr($workspaceVal->ProfileSubscriptionDesc, 0, 34).'</span></span>';
		            }
		        }
		        else{
		        	$ul = "<br /><span style='padding-left:10px;'>Please <b>Activate</b> at least one <b>Social Profiles</b> to the <b><a target='_blank' href='https://vkonnect.com/'>vkonnect.com!</a></span></b><br /><br />";   
		        }

				$message['status'] = 1;
				$message['message'] = "Status Updated Successfully.";
				$message['services'] = $ul;
		
			}
			else{
				$message['status'] = 0;
				$message['message'] = "CURL Error: " . curl_error($curl_resource);

			}
		}

		echo json_encode($message);

		curl_close($curl_resource);

		break;

		case 'GetWorkspaces':

		global $wpdb;

		// fetch the data fro the database
		$fields = fetch_result();

		//count the numbers of rows in the table
		$count_rows = number_rows();

		if( $count_rows > 0 ){


			$authenticationcode = $fields[0]['authenticationtoken'];

			$curl_resource = curl_init();

			// Set some options - we are passing in a useragent too here
			curl_setopt_array($curl_resource, array(
			    CURLOPT_RETURNTRANSFER => 1,
			    CURLOPT_URL => GET_POST_PROFILES,
			    CURLOPT_POST => 1,
			));
			curl_setopt($curl_resource, CURLOPT_POSTFIELDS, "UserID=".$authenticationcode);

			$server_output = curl_exec($curl_resource);
			//echo '<pre>';

			//print_r($server_output);

			if( $server_output ){

				$return_object = json_decode($server_output, true);
 		
				$rows = getAll();

		
				//print_r($rows);
				//if($_SESSION['accountType'] == 'Pro'){
					if($return_object){
						$remainig_post_profile_id = '';
						if($rows){
							//echo "hello1";
							for ($i=0; $i<count($rows); $i++) {
								$array1[] = isset($rows[$i]['post__profile_id'])?$rows[$i]['post__profile_id']:'';
								$array2[] = isset($return_object[$i]['PostProfileId'])?$return_object[$i]['PostProfileId']:'';
						
								$remainig_post_profile_id = array_diff($array1, $array2);
								//print_r($remainig_post_profile_id);
							}
						}
						else{
							//echo "hello";
							$array2[] = isset($return_object[0]['PostProfileId'])?$return_object[0]['PostProfileId']:'';
							//$array2[] = isset($return_object[0]['PostProfileId'])?$return_object[0]['PostProfileId']:'';
							$remainig_post_profile_id = $array2;
						}
							//print_r($array2);
					}
					if(isset($remainig_post_profile_id) && !empty($remainig_post_profile_id)){

						//echo "<pre>";
					//print_r($remainig_post_profile_id);
					
						foreach($remainig_post_profile_id as $idKey => $idVal){
							if($count_rows > 0 && $fields[0]['post_profile_id'] == $idVal){
								//$message['pleaseSelect'] = "please select";
								//echo "hello";
								$authenticationcode = $fields[0]['authenticationtoken'];
								//$postprofileid = $fields[0]['post_profile_id'];
								$id = $fields[0]['id'];
								$SubscriptionTypeID = 0;

								$curl_resource2 = curl_init();

								// Set some options - we are passing in a useragent too here
								curl_setopt_array($curl_resource2, array(
								    CURLOPT_RETURNTRANSFER => 1,
								    CURLOPT_URL => GET_PROFILE_SUBSCRIPTION_DETAIL_BRIEF,	// get the brief detail of user active services
								    CURLOPT_POST => 1,
								));
								curl_setopt($curl_resource2, CURLOPT_POSTFIELDS, "UserID=".$authenticationcode."&PostProfileID=".$array2[0]."&SubscriptionTypeID=".$SubscriptionTypeID);
								$server_output2 = curl_exec($curl_resource2);
								//print_r($server_output2);die;

								$ul = '';
									if( $server_output2 ){
									$return_object2 = json_decode($server_output2);

									//print_r($return_object2);
									if($return_object2){
										foreach( $return_object2 as $wokspaceKey => $workspaceVal ){
											$disbaled = '';
											$title = '';
							            	if($workspaceVal->CredentialStatus == 'Invalid'){
							            		$disbaled = 'disabled';
							            		$title = 'Invalid Social Profile';
							            	}

							            	if(strpos($workspaceVal->SubscriptionName, '.') !== false){
							            		$subscriptionName = str_replace('.', '-', $workspaceVal->SubscriptionName);
							            	}
							            	else{
							            		$subscriptionName = str_replace(' ', '', $workspaceVal->SubscriptionName);
							            	}

						                	$ul .= '<span class="col_a21">
						                	<span class="service_name_lh30" title="'.$title.'">
						               		<span class="chkbx">
						                    	<input title="" '.$disbaled.' type="checkbox" class="chk check_service" name="user_ids[]" 
						                        value="'.$workspaceVal->ProfileSubscriptionID.'">
						                	</span>
						                	<span class="report_icon_30x30 r30_'.$subscriptionName.'">
						                	</span>'.substr($workspaceVal->ProfileSubscriptionDesc, 0, 34).'</span></span>';
						            }

						        	}
						        	else{
						        		$ul = "<br /><span style='padding-left:10px;'>Please <b>Activate</b> at least one <b>Social Profiles</b> to the <b><a target='_blank' href='https://vkonnect.com'>vkonnect.com!</a></span></b><br /><br />";   
						        	}

						        	$result2 = actionUpdateonDelete($activate=1, $array2[0] );
									//if($result2){
									//	update_activate_column($activate=0, $array2[0]);
									//}

						        	update_data( $server_output2, $array2[0], $id );

						        	$message['services'] = $ul;
						        	unset($_SESSION['postProfileID']);
						        	unset($_SESSION['postProfileName']);
						        	$_SESSION['postProfileID'] = $array2[0];
						        	$_SESSION['postProfileName'] = $return_object[0]['ProfileName'];
								}
								else{
									$message['status'] = 0;
									$message['message'] = "CURL Error: " . curl_error($curl_resource);
								}
								curl_close($curl_resource2);

							}
							actionDelete($idVal);	
						}
						
					}	 
				$message['status'] = 1;
				$message['content'] = $return_object;
				$message['totalWorkspace'] = count($return_object);
			
				if(isset($_SESSION['postProfileID']) && !empty($_SESSION['postProfileID']) && isset($_SESSION['postProfileName']) && !empty($_SESSION['postProfileName'])){
					$message['sessionProfileID'] = $_SESSION['postProfileID'];
					$message['sessionProfileName'] = $_SESSION['postProfileName'];

				}
			}
			else{
				$message['status'] = 0;
				$message['message'] = "CURL Error: " . curl_error($curl_resource);
			}
		}

		echo json_encode($message);

		curl_close($curl_resource);

		break;

		case 'GetServicesByWorkspace':

		global $wpdb;

		$postProfileID = $_POST['selectedPostProfileID'];
		$postProfileName = $_POST['selectedPostProfileName'];
		$_SESSION['postProfileID'] = $postProfileID;
		$_SESSION['postProfileName'] = $postProfileName;
		//$chooseWorkspace = $_POST['profileName'];
		
		// fetch the data fro the database
		$fields = fetch_result();

		//count the numbers of rows in the table
		$count_rows = number_rows();

		if( $count_rows > 0 ){
			//print_r($fields)

			$authenticationcode = $fields[0]['authenticationtoken'];
			//$postprofileid = $fields[0]['post_profile_id'];
			$id = $fields[0]['id'];
			$SubscriptionTypeID = 0;

			$curl_resource = curl_init();

			// Set some options - we are passing in a useragent too here
			curl_setopt_array($curl_resource, array(
			    CURLOPT_RETURNTRANSFER => 1,
			    CURLOPT_URL => GET_PROFILE_SUBSCRIPTION_DETAIL_BRIEF,	// get the brief detail of user active services
			    CURLOPT_POST => 1,
			));
			curl_setopt($curl_resource, CURLOPT_POSTFIELDS, "UserID=".$authenticationcode."&PostProfileID=".$postProfileID."&SubscriptionTypeID=".$SubscriptionTypeID);
			$server_output = curl_exec($curl_resource);
			//print_r($server_output);
			$ul = '';

			if( $server_output ){
				$numberRows = checkDuplicateRows($postProfileID);
				if($numberRows == 0){
					$result = actionCreate($postProfileID, $postProfileName, $authenticationcode, $_SESSION['accountType'], 1);
				
				}
				else{
					$result = actionUpdate($postProfileName, 1, $postProfileID);
				}

				if($result){
					$activate = 0;
					update_activate_column($activate, $postProfileID);
				}

				$return_object = json_decode($server_output);
				if($return_object){
					foreach( $return_object as $wokspaceKey => $workspaceVal ){
						$disbaled = '';
						$title = '';
		            	if($workspaceVal->CredentialStatus == 'Invalid'){
		            		$disbaled = 'disabled';
		            		$title = 'Invalid Social Profile';
		            	}

		            	if(strpos($workspaceVal->SubscriptionName, '.') !== false){
		            		$subscriptionName = str_replace('.', '-', $workspaceVal->SubscriptionName);
		            	}
		            	else{
		            		$subscriptionName = str_replace(' ', '', $workspaceVal->SubscriptionName);
		            	}

		                $ul .= '<span class="col_a21">
		                <span class="service_name_lh30" title="'.$title.'">
		                <span class="chkbx">
		                    <input title="" '.$disbaled.' type="checkbox" class="chk check_service" name="user_ids[]" 
		                        value="'.$workspaceVal->ProfileSubscriptionID.'">
		                </span>
		                <span class="report_icon_30x30 r30_'.$subscriptionName.'">
		                </span>'.substr($workspaceVal->ProfileSubscriptionDesc, 0, 34).'</span></span>';
		            }

		        }
		        else{
		        	$ul = "<br /><span style='padding-left:10px;'>Please <b>Activate</b> at least one <b>Social Profiles</b> to the <b><a target='_blank' href='https://vkonnect.com'>vkonnect.com!</a></span></b><br /><br />";   
		        }

				update_data( $server_output, $postProfileID, $postProfileName, $id );

				$message['status'] = 1;
				$message['message'] = "Get All Workspaces Successfully.";
				$message['postProfileID'] = $postProfileID;
				$message['services'] = $ul;
		
			}
			else{
				$message['status'] = 0;
				$message['message'] = "CURL Error: " . curl_error($curl_resource);

			}
		}

		echo json_encode($message);

		curl_close($curl_resource);

		break;
	}

	}
}

}

}
?>