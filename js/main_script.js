// tp get the accesss token from Vkonnect
function ajax_request( query_string, data, url ){
	
			jQuery('.loading').show();
			jQuery.ajax({
				url: url + query_string[0],
				type: "POST",
				data: data,
				cache: false,
				success: function (data_p) {
					
					//var url = url + query_string[1];

					var jsonObj = jQuery.parseJSON(data_p);
					
					var authenticationToken = jsonObj.authencode; // get the autthentication token from the curl request

					if(jsonObj.status == 1){
						jQuery("div.block").fadeOut('slow',function(){
						jQuery('.done').html(jsonObj.message).fadeIn('slow');

						// send second ajax request when the first request is successful
						jQuery.ajax({
							url: url + query_string[1], 
							type: "POST",
							cache: false,
							data:{authenticationToken:authenticationToken},
							success: function (ressponse2) {
								
								location.reload(); //reload the page on the success
								var json2Obj = jQuery.parseJSON(ressponse2); // parse data in json and return json response
								
								//check response status
								if(json2Obj.status == 1) { 

									// get the values from the successful curl request
									var SubscriptionId = json2Obj.SubscriptionId;// by default 0
									var SubscriptionName = json2Obj.SubscriptionName;
									var SubscriptionUrl = json2Obj.SubscriptionUrl;
									var ICONImage = json2Obj.ICONImage;
									
								}
							
							}

						});
	
					});
					
				}
				else{

					// error show when the condition not full fill
					jQuery("input[type=submit]").removeAttr("disabled");
					jQuery('.loading').hide();
					jQuery("div.errormessage").html(jsonObj.message);
				}	
					
				}

			});
		}
