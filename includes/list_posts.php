	<!-- list of all posts in wordpress table format -->
<?php

	//get the number of records in the database table
	include(VK_FILE_PATH . '/class/pagination.class.php');

	global $post;

	// Count the number of publish post.
	$count_posts = wp_count_posts();		
	$pagination_count = $count_posts->publish;

	if($pagination_count > 0) {
	    //get current page
	    $this_page = (isset($_GET['p']) && $_GET['p'] > 0)? (int) $_GET['p'] : 1;
	    //Records per page
	    $per_page = 4;
	    //Total Page
	    $total_page = ceil($pagination_count/$per_page);
	 
	    //initiate the pagination variable
	    $pag = new pagination();
	    //Set the pagination variable values
	    $pag->Items($pagination_count);
	    $pag->limit($per_page);
	    $pag->target("admin.php?page=wp-vkonnect-auto-submitter/vkonnect-plugin.php");
	    $pag->currentPage($this_page);
	 
	    //Done with the pagination
	    //Now get the entries
	    //But before that a little anomaly checking
	    $list_start = ($this_page - 1)*$per_page;
	    if($list_start >= $pagination_count)  //Start of the list should be less than pagination count
	        $list_start = ($pagination_count - $per_page);
	    if($list_start < 0) //list start cannot be negative
	        $list_start = 0;
	    $list_end = ($this_page * $per_page) - 1;

	    // regiister the header of the table
	    $columns = array(
		'title' => 'Title',
		'author' => 'Author',		
		'categories' => 'Categories',
		'date' => 'Date',
		'options' => 'Tags',
		'previously-posted-on-vkonnect' => 'Previously Posted On vKonnect',
		'publish' => 'Publish'
		);
		register_column_headers('vk-post-table', $columns); 
		// END of register header of the table

		?>

	 <?php

	    //Get the data from the database
	    $args = array('posts_per_page' => $per_page, 'offset'=> $list_start, 'post_type' => 'post', 'suppress_filters'  => true);

		$list_posts = get_posts($args);
	 
	    if($list_posts) {
	        //Do something with it! Probably display table
	        ?>
	       <div id="error_msg" class="error_msg">Please select at least one Social Profile!</div>
	       <table class="widefat" cellspacing="0">
			<thead>
				<tr>
					<?php print_column_headers('vk-post-table'); ?>
				</tr>

			</thead>
			<tfoot>
				<tr>
					<?php print_column_headers('vk-post-table', false); ?>
				</tr>
			</tfoot>
			<tbody>
	            <tbody>
	                <?php
	                //loop through
	                foreach($list_posts as $key => $post) : setup_postdata($post); ?> 
	                	<?php $post_tag = wp_get_post_tags( $post->ID );

		                		$field = $wpdb->get_results("SELECT * FROM wp_vk_counter WHERE post_id =".$post->ID);
								$number_rows = $wpdb->num_rows;
	                    ?>
	                <tr>
							<td>
							    <a target="_blank" href="<?php the_permalink() ?>" title="<?php the_title_attribute(); ?>">
							      	<?php the_title(); ?>
							    </a>
							</td>
							<td>
								<?php the_author(); ?>
							</td>
							<td>
								<a target="_blank" href="<?php the_permalink() ?>">
									<?php the_category(); ?>
								</a>
							</td>
							<td>
								<?php the_time('F j, Y'); ?>
							</td>
							<td>
								<?php	
									if($post_tag){	
										foreach($post_tag as $key => $tag){
											echo '<a href="' . get_term_link( $tag, 'post_tag' ) . '" title="' . sprintf( __( "View all posts in %s" ), $tag->name ) . '" ' . '>' . $tag->name.'</a>'. ', ';
										}
									}
									else{
										echo "	---	 ";
									}
								?>
							</td>
							<td>
								<?php
									if($number_rows > 0)
									{
										echo "Posted ".$field[0]->counter." times"; 
									}
									else{
										echo "	---	 ";
									}
								 ?>
							</td>
							<td>
								<a rel="publish_btn" data-fancybox-type="inline" id="publish_<?php echo $post->ID; ?>" name="name_<?php echo $key; ?>" class="publish_vkBtn vkBtn medium green" href="<?php echo VK_URL . '/process.php?cmd=SavePostContents'; ?>"/>Publish</a>
							</td>
						</tr>
	                    <?php
	                endforeach;
	                ?>
	            </tbody>
	        </table>
	        <?php
	        //Now display the pagiantion links
	        ?>
	            <div class="tablenav">
	                <div class="tablenav-pages">
	                    <span class="displaying-num"><?php echo $pagination_count; ?> items</span>
	                    <?php $pag->show(); ?>
	                </div>
	            </div>
	        <?php
	    }
	    else {
	        echo '<div class="error"><p>Something Went wrong! Check</p></div>';
	    }
	}
	else {
	    echo '<div class="error"><p>No posts found.</p></div>';
	}
?>

		
		<div id="postvkonnect"></div>
		

	<?php wp_enqueue_script('vk_script16','http://ajax.aspnetcdn.com/ajax/jquery.validate/1.9/jquery.validate.js'); ?>

	
	<script type="text/javascript">

			jQuery(document).ready(function() {

				jQuery('span.service_name_lh30').tipsy({
			 		gravity: jQuery.fn.tipsy.autoNS,
				});
				
				//select all check boxex
				jQuery('#check_all').on('click', function () {
				    jQuery('div.list_services .check_service:enabled').prop('checked', this.checked);;
				});

				jQuery('a.publish_vkBtn').on('click', function() {

					jQuery(this).attr("disabled", "disabled");

					jQuery('div#post .error_msg').hide();

					var data = [];

					if(jQuery(".chk:checkbox:checked").length > 0){
						data = { 'user_ids' : [] };
						jQuery(".chk:checked").each(function() {
						  data['user_ids'].push(jQuery(this).val());
						});
					}
					else{
						jQuery('div#post .error_msg').show().delay(1000).fadeOut(1000);
						jQuery("a.publish_vkBtn").removeAttr("disabled");
						return false;
						
					}

					var p_id = jQuery(this).attr('id');

					var action = jQuery(this).attr('href');
			
			 		jQuery('body').append('<div id="fadeOverlay" style="opacity:0.80;display:none;position:fixed;left:0;top:0;width:100%;height:100%;z-index:9999;background:#000;"></div>');
				    // Apply fadeIn animation for the smoothing effect.
				    jQuery('#fadeOverlay').css({'filter' : 'alpha(opacity=80)'}).fadeIn();

					//jQuery.fancybox.showLoading();
					jQuery.fancybox.showActivity();

					jQuery.ajax({
					    type: "POST",
					    cache: false,
					    url: action, // post the data to the appropriate url
					    data: { publish_id:p_id,social_chk:data }, // pass id to the page
					    success: function (data) {

					    	jQuery("#fadeOverlay").fadeOut("slow", function() {jQuery(this).remove();});
					    	jQuery('.check_all').attr('checked', false);
					    	jQuery('div.list_services').find(':checkbox').attr('checked', false);

					    	var obj = jQuery.parseJSON(data);
					    	// on success, post (preview) returned response in fancybox
					    	if(obj.status == 1){
						    	jQuery.fancybox(obj.message, {
							        // fancybox API options
						            'width' : 400,
						            'height' : 100,
						            'autoDimensions' : false,
						            'autoScale' : false,
							}); // fancybox
						    	location.reload();
						    	 
					    	}
					    	else if(obj.status == 2){
						    	jQuery.fancybox(obj.message, {
							        // fancybox API options
						            'width' : 400,
						            'height' : 100,
						            'autoDimensions' : false,
						            'autoScale' : false,
							}); // fancybox
						    	//redirect to vkonnect site if payment is due
						    	window.location = 'https://www.vkonnect.com/Subscription/Billing.aspx';
						    	 
					    	}
						    else{
						    	//jQuery.fancybox.hideLoading();
						    	jQuery.fancybox.hideActivity();
						    	jQuery("a.publish_vkBtn").removeAttr("disabled");
						    	jQuery('div#post .error_msg').html(obj.message).show();
						    }
						    
						    jQuery("a.publish_vkBtn").removeAttr("disabled");

					    } // success
 
					    //its_btn

					    
				    }); // ajax
	
				
		        return false;

			    });

			});
		</script>

	

