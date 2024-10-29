<?php
function answerbase_get_widget() {
	$product = get_product();

	$sku = '';
	if (wc_product_sku_enabled()) {

        $sku = $product->get_sku();
	    if ($product->is_type('variable')) {
            $variations = $product->get_available_variations();
            if (is_array($variations)) {
                foreach( $variations as $i => $variation ) {
                    if ($variation['sku'] != '') {
                        $sku = $variation['sku'];
                    }
                    break;
                }
            }
        }
    } 
	if ($sku == '')
	{
        $sku = $product->get_id();
    }

    $answerbase_settings = get_option('answerbase_settings', wc_answerbase_get_default_settings());
    $image = wp_get_attachment_image_src( get_post_thumbnail_id( $product->post->ID ), 'single-post-thumbnail' );

	if($product->post->comment_status == 'open') {
		$content = "<script type=\"text/javascript\">
            ab_full_header_col = '%23000';
            ab_full_background_col = 'initial';
            ab_full_link_col = '%23000';
            ab_full_category = '-1';
            ab_full_product_show = '1';
            ab_full_category_show = '1';
            ab_full_questionlisttype = 'popular';
            ab_full_display_count = '3';
            ab_full_askaquestion = 'popup';
            ab_full_openlinks = 'popup';
            ab_full_title = 'Ask%20a%20Question';
            ab_full_width = '100%';
            ab_full_showborder = 0;
            ab_full_showmargins = 0;
            ab_full_enable_automatic_updates = \"Yes\";
            ab_full_product_id = \"".$sku."\";
             
            ab_full_product_title = \"".$product->get_title()."\";  // Your Product Name or Title
            ab_full_product_price = \"".$product->get_price()."\";  // Your Product Price
            ab_full_product_description = \"".strip_tags(str_replace(array("\r", "\n"), '', $product->get_description()))."\"    // Your Product Description (HTML not supported)
				.replace(/\\r?\\n|\\r/g, \" \")
				.replace(/\\t/g, \"\")
				.replace(/\\xa0/g, \" \") // Handles &nbsp;
				.replace(/\\xae/g, \"\") // Handles &reg;
                .trim()
                .substr(0, 500);
    
            ab_full_product_photo_URL = \"".$image[0]."\";    // The URL of Your Product's Photo Resides
    
            if (ab_full_product_photo_URL.startsWith(\"//\")) {
                ab_full_product_photo_URL = \"https:\" + ab_full_product_photo_URL;
            }
    
            ab_full_product_page_URL = window.document.location.href; // The URL of Your Product Page
            // End Product Definition Parameters
    
            ab_full_product_page_URL = encodeURI(ab_full_product_page_URL);
    
            var ab_full_extraparams = '';
            if(typeof ab_full_enable_automatic_updates == 'string')
                ab_full_extraparams += ('&ab_full_enable_automatic_updates=' + ab_full_enable_automatic_updates);
            if(typeof ab_full_product_id == 'string')
                ab_full_extraparams += ('&ab_full_product_id=' + ab_full_product_id);
            if(typeof ab_full_product_title == 'string')
                ab_full_extraparams += ('&ab_full_product_title=' + encodeURIComponent(ab_full_product_title));
            if(typeof ab_full_product_price == 'string')
                ab_full_extraparams += ('&ab_full_product_price=' + ab_full_product_price);
            if(typeof ab_full_product_description == 'string')
                ab_full_extraparams += ('&ab_full_product_description=' + encodeURIComponent(ab_full_product_description));
            if(typeof ab_full_product_photo_URL == 'string')
                ab_full_extraparams += ('&ab_full_product_photo_URL=' + ab_full_product_photo_URL);
            if(typeof ab_full_product_page_URL == 'string')
                ab_full_extraparams += ('&ab_full_product_page_URL=' + ab_full_product_page_URL);
            if (typeof ab_full_product_category_url == 'string')
                ab_full_extraparams += ('&ab_full_product_category_url=' + ab_full_product_category_url);


            document.write('<scr'+'ipt type=\"text/javascript\" src=\"https://" . $answerbase_settings['service_url'] . "/FullFeaturedWidget.ashx?output=javascript&listquestiontype=popular&displaycount=3&categoryid=-1&showproduct=0&showcategory=1&askquestion=popup&openlinks=popup'+ab_full_extraparams+'\"></scr'+'ipt><br/>');
    </script>";
		return $content;
	}
}
?>