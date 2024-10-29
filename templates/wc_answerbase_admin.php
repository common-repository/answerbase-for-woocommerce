<?php
function wc_answerbase_settings_page() {
	if ( function_exists('current_user_can') && !current_user_can('manage_options') ) {
		die(__(''));
	}
	else if (version_compare(phpversion(), '5.2.0') > 0) {

        if (isset($_POST['store_settings'])) {
            check_admin_referer( 'answerbase_settings_form' );

            $answerbase_settings = get_option('answerbase_settings', wc_answerbase_get_default_settings());
            $answerbase_settings['location'] = $_POST['answerbase_location'];
            $answerbase_settings['tab_name'] = $_POST['answerbase_tab_name'];
            update_option('answerbase_settings', $answerbase_settings);

            wc_answerbase_gui_settings();
        }
        else {
            if (isset($_GET['s_id']) && isset($_GET['m_id'])) {
                $answerbase_settings = get_option('answerbase_settings', wc_answerbase_get_default_settings());
                $answerbase_settings['service_url'] = $_GET['s_id'];
                $answerbase_settings['admin_url'] = $_GET['m_id'];
                update_option('answerbase_settings', $answerbase_settings);

                wc_answerbase_gui_settings();
            }
            else {
                if (wc_answerbase_is_registered()) {
                    wc_answerbase_gui_settings();
                } else {
                    $url = "https://answerbase.com/Woocommerce/ConnectToStore?";
                    $url .= 'storeName=' . get_bloginfo('name');
                    $url .= "&";
                    $url .= 'storeUrl=' . urlencode($_SERVER['SERVER_NAME']);
                    $url .= "&";
                    $url .= 'adminEmail=' . get_bloginfo('admin_email');
                    $url .= "&";
                    $url .= 'returnUrl=' . urlencode((isset($_SERVER['HTTPS']) ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]");

                    echo sprintf("<iframe src='%s' width='1100' height='1000'></iframe>", $url);
                }
            }
        }
	}
	else {
		echo "<h1>The required PHP version is at least 5.2";
	}
}

function wc_answerbase_gui_settings(){
    $answerbase_settings = get_option('answerbase_settings', wc_answerbase_get_default_settings());
    $settings_html =  "<div class='wrap'>"
        .screen_icon( ).
        "<h2>Answerbase Settings</h2>";

    if (isset($_GET['s_id']) && isset($_GET['m_id'])) {
        $settings_html .= "<h4>Answerbase has been successfully installed on your store.</h4>";
    }
    
    $settings_html .=  "<h4>You can adjust your Answebase settings below.</h4>
			  <form  method='post' id='yotpo_settings_form'>
			  	<table class='form-table'>".
        wp_nonce_field('answerbase_settings_form').
        "<fieldset>	                 	                 
	    	         <tr valign='top'>			
				       <th>Choose widget location</th>
				       <td>
				         <select id='answerbase-widget-location' name='answerbase_location' onchange = 'showAnswerbaseProperties();' >
						   <option value='after_product' ".selected('after_product',$answerbase_settings['location'], false).">After product details</option>
				  	       <option value='bottom' ".selected('bottom',$answerbase_settings['location'], false).">Bottom of the page</option>
			 		       <option value='tab' ".selected('tab',$answerbase_settings['location'], false).">Product tab</option>
			 	           <option value='other' ".selected('other',$answerbase_settings['location'], false).">Other</option>
				         </select>
				         <p id='location-details' class='description'>
				            Please do the following to place your widget in desired location:
				            <br/><br/>
                              1. Find \"Plugins\" on the left navigation and select \"Editor\".
                              <br/>
                              2. On the top you'll see a dropdown where you can \"Select plugin to edit\", select the Woocommerce option
                              <br/>
                              3. Click on the \"templates\" option to expand that menu
                              <br/>
                              4. Click on the \"content-single-product.php\" page
                              <br/>
                              5. From here, you can paste the line \"wc_answerbase_get_widget\" whereever you'd like the widget displayed
                            <br/><br/>
                            If you need any help with installation, you can contact our support team to assist in setup by emailing <a href='mailto:support@answerbase.com'>support@answerbase.com</a>
                         </p>
		   		       </td>
		   		     </tr>
		   		     <tr id='tab-name-row' valign='top'>
		   		       <th>Select tab name:</th>
		   		       <td><input type='text' name='answerbase_tab_name' value='".$answerbase_settings["tab_name"]."' />
		   		            <p class='description'>
				            This is a title of the tab on that will be rendered on product's page.
				            </p>
				        </td>
		   		     </tr>
		           </fieldset>
		         </table></br>			  		
				<input id='answerbase_save' type='submit' name='store_settings' value='Save'/>
			</div>
		  </form> 
		  
		  <br/>
		  <h4>To manage your content and the full administration interface, go to <a href='https://".$answerbase_settings["admin_url"]."' target='_blank'>Answerbase Administration Portal</a></h4>	
		  
		  <script type='text/javascript'>	    
		    function showAnswerbaseProperties(){
                switch (document.getElementById('answerbase-widget-location').value){
					case 'after_product':
					case 'bottom':
                        document.getElementById('tab-name-row').style.display = 'none';
                        document.getElementById('location-details').style.display = 'none';
                        break;
                    case 'tab':
                        document.getElementById('tab-name-row').style.display = '';
                        document.getElementById('location-details').style.display = 'none';
                        break;
                    case 'other':
                        document.getElementById('tab-name-row').style.display = 'none';
                        document.getElementById('location-details').style.display = 'block';
                        break;
                }
            };
		    
		    document.addEventListener('DOMContentLoaded', function () {
                showAnswerbaseProperties();
            }, false);
            </script>	  		  
		</div>";

    echo $settings_html;
}


?>