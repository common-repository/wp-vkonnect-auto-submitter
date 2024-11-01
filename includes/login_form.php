<?php

	global $wpdb;

	$table_name = VK_TABLE_PREFIX . VK_SETTING_USER;
	$table_workspace_name = VK_TABLE_PREFIX . VK_WORKSPACE_USER;

	$fieldsWorkspace = $wpdb->get_results("SELECT * FROM $table_workspace_name WHERE activate = '1'", ARRAY_A);
	$fields = $wpdb->get_results("SELECT * FROM $table_name", ARRAY_A);
	
	//print_r($fieldsWorkspace);
	$count_rows = $wpdb->num_rows;

	//if($count_rows > 0){

	if(isset($_SESSION['user']) && !empty($_SESSION['user'])){
?>
 	
    
<!-- start vk_wapper tag -->
<div id="vk_Inner">

<div class="vkBanner"><img src="<?php echo VK_URL.'/images/banner2.jpg'; ?>" alt="banner2" /></div>
<!--<div class="top_banner"><img src="<?php //echo VK_URL.'/images/banner-2.png'; ?>" alt="banner-2" /></div>-->

<!-- start vkRow tag -->
<div class="vkRow">
    <span class="workspace">
    </span>
    <!-- start vkTabContainer tag -->
    <div class="vkTabContainer" id="vkTabContainer">
    
    	<ul class='vkTabs'>
          <li id="tab1"><a href='#post' data-toggle="tab">Post</a></li>
          <li id="tab2"><a href='#services' data-toggle="tab">Social Profiles</a></li>
        </ul>
            
        <!-- start tab des post -->
        <div id='post'>
        	<?php include('list_posts.php'); ?>
        </div>
        <!-- close tab des post -->
        
        <!-- start tab des services -->
        <div id='services' class="se">
        	<div id="loadingDiv" class="loadingDiv"></div>
        <?php
            $id = $fields[0]['id'];
            $uname = $fields[0]['username'];
            $active_services = $fields[0]['active_services'];
            $account_type = $fields[0]['account_type'];

            $return_array = json_decode(stripslashes($active_services));
            //echo "<pre>";
            //print_r($return_array);

            /* Start Header Of Services Tab List */
            echo "<div class='selct_all ser_head' id='ser_head'>";
                echo '<input type="checkbox" id="check_all" class="check_all" name="check_all">';
                echo "<label id='select_all'>Select All</label>";
	         
                echo '<ul class="customDroDwn" style="display:none;">
					    <li class="navi_a">
					         <a id="selectedLI">
						         <span class="icn workspace">
						         </span>
					         </a>
				            <span class="drpDwn">
				                <ul id="workspaceList">
				                </ul>
				            </span>
					    </li>
					</ul>';
            echo "</div>";
            /* End Header Of Services Tab List */
            ?>
            
            <!-- Start Services Body List -->
            <div class='list_services ser_body' id="ser_body">
            
            <?php 
            if( $return_array != null) {

            $ul =''; ?>

        
            
            <?php 
            foreach( $return_array as $key => $val ){
            	$disbaled = '';
            	$title = '';
            	if($val->CredentialStatus == 'Invalid'){
            		$disbaled = 'disabled';
            		$title = 'Invalid Social Profile';
            	}
            	
            	if(strpos($val->SubscriptionName, '.') !== false){
            		$subscriptionName = str_replace('.', '-', $val->SubscriptionName);
            	}
            	else{
            		$subscriptionName = str_replace(' ', '', $val->SubscriptionName);
            	}

                $ul .= 
                '<span class="col_a21">
		          	<span class="service_name_lh30" title="'.$title.'">
		                <span class="chkbx">
		                    <input title="" '.$disbaled.' type="checkbox" class="chk check_service" name="user_ids[]" 
		                        value="'.$val->ProfileSubscriptionID.'">
		                </span>
			            <span class="report_icon_30x30 r30_'.$subscriptionName.'"></span>'
		                	.substr($val->ProfileSubscriptionDesc, 0, 34).
                	'</span>
                </span>';
            }
?>   
                <?php echo $ul; ?>
                
<?php
            }

            else{
                
                echo "<br /><span style='padding-left:10px;'>Please <b>Activate</b> at least one <b>Social Profiles</b> to the <b><a target='_blank' href='https://vkonnect.com'>vkonnect.com!</a></span></b><br /><br />";   
            }
            
?>
			</div>
			<!-- End Services Body List -->

            <div class="upd_btn ser_btn">
                <!--<a target='_blank' class="add_services" href="http://www.vkonnect.com" style="margin:0 10px 0 0;">Add Services</a>-->
                <a style="text-decoration:none;" target='_blank' class="add_services vkBtn green" href="https://vkonnect.com/" style="margin:0 10px 0 0;">Add Social Profiles</a>
                <a style="text-decoration:none;" class="update_services update_ser_btn vkBtn green" id="update_services" name="update_services" href="<?php echo VK_URL . '/process.php?cmd=UpdateServices'?>">Update Social Profiles</a> 
               	<span style="display:none;">Updated succesfully</span>
            </div>
       	</div>
        <!-- close tab des services -->
            
    </div>
    <!-- close vkTabContainer tag -->
    
    <!-- start vkSummry tag -->
    <div class="innerRgt">
    
	    <div class="vkRow dis_btn">
	        <span>You are disconnect.</span>
	        <a style="text-decoration:none;" class="btn_disconnect disconnect_button green vkBtn" id="btn_disconnect" name="btn_disconnect" href="<?php echo VK_URL . '/process.php?cmd=Disconnect'?>">Log Out</a>
	    </div>
	    <?php //if(isset($_SESSION['accountType']) && $_SESSION['accountType'] == 'Pro'){ ?>
	    <!--<div>
	    	<input type="submit" id="getWorkspace" class="vkBtn green" name="get_workspace" value="Get Workspace">
	    </div>-->
	    <?php //} ?>

	    <div class="vkSummry analytics_overview">
	    	

	    	<h3 class="vkH3"><a href="#">Hello &nbsp;<?php echo $uname; ?></a></h3>

			<!--<ul id="main-menu">
				<li><a href="#">main-link 1</a>
					<ul>
						<li><a href="#">sub-link 1</a></li>
						<li><a href="#">sub-link 1</a></li>
						<li><a href="#">sub-link 1</a></li>
					</ul>
				</li>
			</ul>-->
	    
	      <ul>
	        	<li>
	            	<span class="icn_14_wBg"><img alt="" src="<?php echo VK_URL .'/images/icons/user.png'; ?>"></span>
	                 <span class="txt">Account Type: <?php if($account_type == 'Pro') {echo 'Business';} else{echo 'Basic';} ?></span>
	          </li>
	          <!--<li>
	            	<span class="icn_14_wBg"><img alt="" src="<?php //echo VK_URL .'/images/icons/user.png'; ?>"></span>
	                <span class="txt">Workspace: <?php //echo $_SESSION['profileName']; ?></span>
	          </li>-->
	          <!--<li>
	                <span class="icn_14_wBg"><img alt="" src="<?php //echo VK_URL .'/images/icons/close.png'; ?>"></span>
	                <span class="txt">Social Profiles: <?php //echo count($return_array); ?></span>
	            </li>-->
	            <!--<li>
	            	<span class="icn_14_wBg"><img alt="" src="<?php //echo VK_URL .'/images/icons/close.png'; ?>"></span>
	                <span class="txt">Expire on: 2nd Mar 2014</span>
	            </li>-->
	        <!--<li>
	            	<span class="icn_14_wBg"><img alt="" src="<?php //echo VK_URL .'/images/icons/twitter2.png';?>"></span>
	                <span class="txt">Social Profile: 1 out of 5</span>
	            </li>-->
	        <!--<li>
	            	<span class="icn_14_wBg"><img alt="" src="<?php //echo VK_URL .'/images/icons/users.png';?>"></span>
	                <span class="txt">User: 3 out of 5</span>
	            </li>-->
	        <li>
	            	<span class="icn_14_wBg"><img alt="" src="<?php echo VK_URL .'/images/icons/workspace.png';?>"></span>
	                <!--<span class="txt">Workspaces: <?php //if(isset($fieldsWorkspace) && count($fieldsWorkspace > 0)){echo count($fieldsWorkspace);}else{echo "1";} ?></span>-->
	            	<span class="txt totalWorkspace">Workspaces: <?php if(isset($_SESSION['totalWorkspace']))echo $_SESSION['totalWorkspace'];?> </span>
	            </li>
	        <!--<li>
	            	<span class="icn_14_wBg"><img alt="" src="<?php //echo VK_URL .'/images/icons/link2.png';?>"></span>
	                <span class="txt">Today Bookmark Link: 3 out of 25</span>
	            </li>-->
	        </ul>
	    </div>
	    <!-- close vkSummry tag -->
    
    </div>
    
</div>
<!-- close vkRow tag -->

</div>
<!-- close vk_wapper tag -->

	<!--<div class="update_error_msg" style=""></div>-->

<?php	
}
	else{

?>
	<!-- start vk_wapper tag -->
	<div id="vk_login">

		<div class="vk_tag">
		<h1>All in one social media sharing platform </h1>
		<p>Social Networks and Social Bookmarking together to manage all your social needs from one place.</p>
		</div>

		<!-- start vkLft tag -->
		<div class="vkLft">
			<h3 class="vkH3">Log In to vKonnect</h3>
			
			<div class="form">
				<form enctype="multipart/form-data" action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post" id="form">
		            <div class="element frmRow">
		            	
		            	<input type="text" name="username" id="vkInpt" class="text" placeholder="User Name"><br><br>
		            </div>
		            <div class="element frmRow">
		            	
		            	<input type="password" value="" name="password" id="vkInpt" class="text" placeholder="Password">
		            </div>
		            <div class="errormessage"></div>
		            <div class="successmessage"></div>
		            <div class="submit_button_div frmRow">
		                <div class="loading"></div>
		                <input type="submit" value="Log In" class="connect_button vkBtn green" name="submit">	
	                	<!--<a target='_blank' class="sign_up_button" href="http://vkonnect.com/Sign_Up">Sign Up</a>-->
	                	<a style="text-decoration:none;" target='_blank' class="sign_up_button vkBtn green" href="https://vkonnect.com/Signup.aspx">Sign Up</a>
   				 	</div>
				</form>
			</div>
		</div>
		<!-- close vkLft tag -->

		<!-- start vkRgt tag -->
		<div class="vkRgt authenticationtoken"><img src="<?php echo VK_URL.'/images/banner.jpg' ?>" alt="" align="left"/></div>
		<!-- close vkRgt tag -->

	</div>
	<!-- close vk_wapper tag -->

	<div class='done'></div>
<?php
	}
?>
		
	<script type="text/javascript">

		function ajax_request( query_string, data, url ){
	
			jQuery('.loading').show();
			jQuery.ajax({
				url: url + query_string[0],
				type: "POST",
				data: data,
				cache: false,
				success: function (data_p) {

					var jsonObj = jQuery.parseJSON(data_p);
					
					var authenticationToken = jsonObj.authencode; // get the autthentication token from the curl request

					if(jsonObj.status == 1){
						//jQuery("div.block").fadeOut('slow',function(){
							jQuery("div.errormessage").hide();
							jQuery('.successmessage').html(jsonObj.message).show();
							jQuery("div#vk_login").fadeOut('slow',function(){
							jQuery('div.done').html(jsonObj.message).fadeIn('slow');

							// send second ajax request when the first request is successful
							jQuery.ajax({
								url: url + query_string[1], 
								type: "POST",
								cache: false,
								data:{authenticationToken:authenticationToken},
								success: function (ressponse2) {
									location.reload(); //reload the page on the success
									
									var json2Obj = jQuery.parseJSON(ressponse2); // parse data in json and return json response
								
									if(json2Obj.status == 1) { 
										// response of successful curl request
										jQuery('.done').html(json2Obj.message).show();
										
									}
								
								}

							});
		
						});
						
					}
					else{

						jQuery('.successmessage').hide();
						jQuery("input[type=submit]").removeAttr("disabled");
						jQuery('.loading').hide();
						jQuery("div.errormessage").html(jsonObj.message).show();
					}	
					
				},
				error:function(x,e){

					jQuery.fancybox.hideLoading();

		            if(x.status==0){
		                alert('You are offline!!\n Please Check Your Network.');
		            }else if(x.status==404){
		                alert('Requested URL not found.');
		                window.location = '<?php echo VK_URL ?>' + '/includes/404.php';
		            }else if(x.status==500){
		                alert('Internel Server Error.');
		            }else if(e=='parsererror'){
		                alert('Error.\nParsing JSON Request failed.');
		            }else if(e=='timeout'){
		                alert('Request Time out.');
		            }else {
		                alert('Unknow Error.\n'+x.responseText);
		            }
		        }

			});
		}
		jQuery(document).ready(function() {
			
			jQuery('.connect_button').on("click", function(e){
			e.preventDefault();
			jQuery("input[type=submit]").attr("disabled", "disabled");

			var do_submit = 1;

			//get the values of the form input fields
			var uname = jQuery('input[name=username]');
			var pass = jQuery('input[name=password]');

			// validate allt the form fields
			if (uname.val().trim() == '') {

				uname.addClass('hightlight').focusin(function() {
					jQuery(this).removeClass('hightlight');
			});
				do_submit = 0;
			} else{ 
				uname.removeClass('hightlight');
			}
			
			if (pass.val().trim() == '') {
				pass.addClass('hightlight').focusin(function() {
					jQuery(this).removeClass('hightlight');
			});
				do_submit = 0;
			} else{
				pass.removeClass('hightlight');
			}

			if(do_submit==0){ 
				jQuery("input[type=submit]").removeAttr("disabled");
				return false;

			}
		
			//send the data by the POST method
			var data = { username:uname.val(), password:pass.val() };

			var query_string = ['LoginUser', 'AuthenticationToken'];

			//make the url for ajax request
			var url = "<?php echo VK_URL ?>" + "/process.php?cmd=";

			//function for pass ajax request
			ajax_request( query_string, data, url );

				
			return false;
			});
	jQuery('#btn_disconnect').on("click", function(e) {
				e.preventDefault();

				var url = jQuery('.btn_disconnect').attr('href');
				var loc = window.location.pathname + '?page=wp-vkonnect-auto-submitter/vkonnect-plugin.php';
				var message_box = confirm("Are you sure want to Log Out!");

				if (message_box == true)
					{
					jQuery.ajax({
						url: url,
						type: "POST",
						//data: data,
						cache: false,
						success: function ( response ) {

							var obj = jQuery.parseJSON(response);

							if(obj.status == 1){

								jQuery('.dis_btn span').html(obj.message).show();
								window.location = loc;	
							}
						}
						/*error:function(x,e){

				            if(x.status==0){
				                alert('You are offline!!\n Please Check Your Network.');
				            }else if(x.status==404){
				                alert('Requested URL not found.');
				                window.location = '<?php echo VK_URL ?>' + '/includes/404.php';
				            }else if(x.status==500){
				                alert('Internel Server Error.');
				            }else if(e=='parsererror'){
				                alert('Error.\nParsing JSON Request failed.');
				            }else if(e=='timeout'){
				                alert('Request Time out.');
				            }else {
				                alert('Unknow Error.\n'+x.responseText);
				            }
				        }*/
						
					});
				}
			});

		jQuery('#update_services').on("click", function(e) {
				e.preventDefault();

				var url = jQuery('.update_services').attr('href');
				var message_box = confirm("Do you want to Update your Social Profiles!");

				if (message_box == true)
				{
					// Appedning the overlay div
				    jQuery('body').append('<div id="fadeOverlay" style="opacity:0.80;display:none;position:fixed;left:0;top:0;width:100%;height:100%;z-index:9999;background:#000;"></div>');
				    // Apply fadeIn animation for the smoothing effect.
				    jQuery('#fadeOverlay').css({'filter' : 'alpha(opacity=80)'}).fadeIn();
					
					//jQuery.fancybox.showLoading();
					jQuery.fancybox.showActivity();

					jQuery.ajax({
						url: url,
						type: "POST",
						timeout:8000, // I chose 8 secs for kicks
						cache: false,
						success: function ( response ) {
							var obj = jQuery.parseJSON(response);

							if(obj.status == 1){

								jQuery('div.upd_btn span').html(obj.message).fadeIn('slow')
								jQuery.fancybox.hideActivity();
								jQuery("#fadeOverlay").fadeOut("slow", function() {jQuery(this).remove();});

								jQuery("#ser_body").html('');
								jQuery("#ser_body").append(obj.services);

								jQuery('div.upd_btn span').html(obj.message).fadeOut('slow')
								//location.reload();

							}
							else{
								jQuery.fancybox.hideActivity();
								jQuery("#fadeOverlay").fadeOut("slow", function() {jQuery(this).remove();});
								jQuery('div.upd_btn span').html('Data could not be updated').show();
							}

						}
						/*error:function(x,e){

						 	jQuery.fancybox.hideActivity();

				            if(x.status==0){
				                alert('You are offline!!\n Please Check Your Network.');
				            }else if(x.status==404){
				                alert('Requested URL not found.');
				                //jQuery('.update_error_msg').html(x.error_message);
				                window.location = '<?php echo VK_URL ?>' + '/includes/404.php';
				            }else if(x.status==500){
				                alert('Internel Server Error.');
				            }else if(e=='parsererror'){
				                alert('Error.\nParsing JSON Request failed.');
				            }else if(e=='timeout'){
				                alert('Request Time out.');
				            }else {
				                alert('Unknow Error.\n'+x.responseText);
				            }
				        }*/
						
					});
				}
			});
		});


	jQuery(document).ready(function(){

		function ajax_request_get_workspace(request_url){
			jQuery.ajax({
				url: url,
				type: "POST",
				//timeout:8000, // I chose 8 secs for kicks
				cache: false,

				beforeSend: function() {
				    jQuery("div.loadingDiv").show();
				},
				
				success: function ( response ) {
					var obj = jQuery.parseJSON(response);
					if(obj.content){
						
						jQuery("span.totalWorkspace").html('');
						jQuery("span.totalWorkspace").append('Workspaces: ' + obj.totalWorkspace);

						var items = [];
						
						for(var i =0;i <= obj.content.length-1;i++)
						{
							var item = obj.content[i];

							items.push('<li id="'+item.PostProfileId+'" class="icn_sub_nav workspace selectitem"><a>'+ item.ProfileName +'</a></li>');
							jQuery('a#selectedLI span#list').html('');
							//chosen = item.PostProfileId == obj.sessionProfileID ? obj.sessionProfileName  : "";
							jQuery('<span id="list">Workspace: '+ obj.sessionProfileName+'</span>').insertAfter("a#selectedLI span.icn");

						}

						jQuery('#ser_head .customDroDwn span.drpDwn #workspaceList').empty().html(items);
						jQuery('#ser_head .customDroDwn').show();

						if(obj.services){
							jQuery("#ser_body").html('');
							jQuery("#ser_body").append(obj.services);
						}

					}
					
					jQuery.fancybox.hideActivity();
					//jQuery("#fadeOverlay").fadeOut("slow", function() {jQuery(this).remove();});
					//jQuery('div.vkRow span.workspace').fadeOut('slow');
				},
				complete : function() {
		            jQuery("div#loadingDiv").hide();
		        }
				/*error:function(x,e){

				 	jQuery.fancybox.hideActivity();

		            if(x.status==0){
		                alert('You are offline!!\n Please Check Your Network.');
		                return false;
		            }else if(x.status==404){
		                alert('Requested URL not found.');
		                //jQuery('.update_error_msg').html(x.error_message);
		                window.location = '<?php echo VK_URL ?>' + '/includes/404.php';
		            }else if(x.status==500){
		                alert('Internel Server Error.');
		            }else if(e=='parsererror'){
		                alert('Error.\nParsing JSON Request failed.');
		            }else if(e=='timeout'){
		                alert('Request Time out.');
		            }else {
		                alert('Unknow Error.\n'+x.responseText);
		            }
		        }*/
				
			});
		}
		
		var url = "<?php echo VK_URL;?>"+"/process.php?cmd=GetWorkspaces";
		
		//window.setInterval(checkWorkspaceUpdate, 3000); //10000 = 10 sec

		//function checkWorkspaceUpdate() { ajax_request_get_workspace(url); } 
		
		//jQuery('#getWorkspace').on("click", function(e) {

		jQuery('a[href = "#services"]').on("click", function(e) {
			if(jQuery(this).hasClass('active')) return false;
			e.preventDefault();
		
			ajax_request_get_workspace(url);
		});

		function ajax_request_workspace_selected(request_url, data_post_id, data_post_name){
			selectedPostProfileID = data_post_id;
			jQuery.ajax({
				url: request_url,
				type: "POST",
				data: {selectedPostProfileID:selectedPostProfileID, selectedPostProfileName:data_post_name},
				//timeout:8000, // I chose 8 secs for kicks
				cache: false,
				success: function ( response ) {
					//alert(data_post);
					var obj = jQuery.parseJSON(response);
					if(obj.status == 1){
						jQuery.fancybox.hideActivity();
						//jQuery("div.loadingDiv").show();
						jQuery("#fadeOverlay").fadeOut("slow", function() {jQuery(this).remove();});
						//jQuery('div.upd_btn span').html(obj.message).fadeIn('slow')
						jQuery("#ser_body").html('');
						jQuery("#ser_body").append(obj.services);	
					}
					else{
						jQuery.fancybox.hideActivity();
						//jQuery("div.loadingDiv").hide();
						jQuery("#fadeOverlay").fadeOut("slow", function() {jQuery(this).remove();});
						jQuery('div.vkRow span.workspace').html(obj.message).show();
					}

				}
				 /*error:function(x,e){

				 	//jQuery.fancybox.hideActivity();
				 	jQuery("div.loadingDiv").hide();

		            if(x.status==0){
		                alert('You are offline!!\n Please Check Your Network.');
		                return false;
		            }else if(x.status==404){
		                alert('Requested URL not found.');
		                //jQuery('.update_error_msg').html(x.error_message);
		                window.location = '<?php echo VK_URL ?>' + '/includes/404.php';
		            }else if(x.status==500){
		                alert('Internel Server Error.');
		            }else if(e=='parsererror'){
		                alert('Error.\nParsing JSON Request failed.');
		            }else if(e=='timeout'){
		                alert('Request Time out.');
		            }else {
		                alert('Unknow Error.\n'+x.responseText);
		            }
		        }*/
				
			});
		}

		//jQuery('#workspaceSelect').on("change", function(e) {
		jQuery(document).on('click','#workspaceList li',function(e) {
			e.preventDefault();

			var url = "<?php echo VK_URL;?>"+"/process.php?cmd=GetServicesByWorkspace"; 

			var selectedPostProfileID = jQuery(this).attr('id');
			var selectedPostProfileName = jQuery(this).text();
			jQuery('a#selectedLI span#list').html('');
jQuery('<span id="list">Workspace: '+ selectedPostProfileName+'</span>').insertAfter("a#selectedLI span.icn");

			//jQuery('').prepend('Workspace: ' + selectedPostProfileName);
			//$(content).insertAfter('#bla');
			//jQuery('a#selectedLI').prepend('Workspace: '+obj.sessionProfileName);
			//alert(selectedPostProfileID);

			//var selectedPostProfileID = jQuery('#workspaceSelect').find(":selected").val();
			//var selectedPostProfileName = jQuery('#workspaceSelect').find(":selected").text();
			//alert(selectedPostProfileID);
			// delete cookie  
		    //jQuery.cookie('profileSelected', null);


		    // set cookie  
		    //jQuery.cookie('profileSelected', selectedPostProfileID);  
		    //alert(jQuery.cookie('profileSelected')); 
		  
			// Appedning the overlay div
		    jQuery('body').append('<div id="fadeOverlay" style="opacity:0.80;display:none;position:fixed;left:0;top:0;width:100%;height:100%;z-index:9999;background:#000;"></div>');
		    
		    // Apply fadeIn animation for the smoothing effect.
		    jQuery('#fadeOverlay').css({'filter' : 'alpha(opacity=80)'}).fadeIn();
			
			//jQuery.fancybox.showLoading();
			jQuery.fancybox.showActivity();
		    

			ajax_request_workspace_selected(url, selectedPostProfileID, selectedPostProfileName);
		});

		/*window.onload = function() {

			var url = "<?php echo VK_URL;?>"+"/process.php?cmd=GetWorkspaces";
			ajax_request_get_workspace(url);
			
			var url = "<?php echo VK_URL;?>"+"/process.php?cmd=GetServicesByWorkspace";
			//alert(url);
			//selectedProfile = jQuery.cookie('profileSelected');
			//alert(selectedProfile);
		  	ajax_request_workspace_selected(url, selectedProfile);
		};*/


		jQuery("ul.customDroDwn li.navi_a").click(function () {
		  //$("ul.customDroDwn li.navi_b").find("ul").fadeOut();
		  jQuery(this).find("ul").fadeToggle();
		  
		    });
		 
	});
	</script>

	<style type="text/css">
    .chk  {position: relative;
        }

    .chk:hover:after
        {position: absolute;
        top: 0;
        left: 0;
        content: attr(title);
        background-color: #ffa;
        color: #000;
        line-height: 1.4em;
        border: 1px solid #000;
        }
</style>
			