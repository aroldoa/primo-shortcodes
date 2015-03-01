<?php
/*
Plugin Name: Ultimate Utility Shortcodes
Plugin URI: http://www.datamaw.com
Description: The most popular tasks in a shortcode bundle
Version: 1.0
Author: Mario Silva
Author URI: http://www.datamaw.com
*/
function uus_load_dependencies( $owlslider, $lazyload ){

	$list = 'done';

	$params = array(
		'bootstrap_default_cols'	=>	12
		);


	if( $owlslider ){
		$owl_carousel_js = 'owl-carousel-js';
		//get the carousel js url
		$owl_carousel_js_url = plugins_url( 'includes/js/owl-carousel/owl.carousel.min.js', __FILE__ );	
		//get the carousel theme url
		$owl_carousel_theme_url = plugins_url( 'includes/css/owl-carousel/owl.theme.css', __FILE__ );
	   	//get the carousel css url
		$owl_carousel_css_url = plugins_url( 'includes/css/owl-carousel/owl.carousel.css', __FILE__ );

		if ( !wp_script_is( $owl_carousel_js, $list ) ) {
			//register owl js
			wp_register_script( $owl_carousel_js, $owl_carousel_js_url, array('jquery'), '1.0' );
			//enqueue js
			wp_enqueue_script( $owl_carousel_js ); 
			//register owl css
			wp_register_style( 'owl-carousel-css', $owl_carousel_css_url, array(), '1.0' );
			//register owl theme
			wp_register_style( 'owl-carousel-theme', $owl_carousel_theme_url, array('owl-carousel-css'), '1.0' );
			//enqueue style
			wp_enqueue_style( 'owl-carousel-theme' );
	    }
	}

	if( $lazyload){

		$lazy_load_js = 'lazy-load-xt';
		//get the lazy load settings js url
		$lazy_load_settings_js_url = plugins_url( 'includes/js/lazy-load-xt/lazy-load-custom-settings.js', __FILE__ );	
		//get the lazy load js url
		$lazy_load_js_url = plugins_url( 'includes/js/lazy-load-xt/jquery.lazyloadxt.min.js', __FILE__ );	
		//get the lazy load css url
		// $lazy_load_css_url = plugins_url( 'includes/css/lazy-load-xt/jquery.lazyloadxt.fadein.min.css', __FILE__ );
		//get the animate css file
		$lazy_load_css_url = plugins_url( 'includes/css/animate/animate.min.css', __FILE__ );

		if ( !wp_script_is( $lazy_load_js, $list ) ) {
			//register lazy load js
			wp_register_script( $lazy_load_js, $lazy_load_js_url, array('jquery'), '1.0');
			//register lazy load js
			wp_register_script( 'lazy-load-settings', $lazy_load_settings_js_url, array($lazy_load_js), '1.0' );
			//enqueue js
			wp_enqueue_script( 'lazy-load-settings' ); 
			//register owl css
			wp_register_style( 'lazy-load-css', $lazy_load_css_url, array(), '1.0' );
			//enqueue style
			wp_enqueue_style( 'lazy-load-css' );
	    }
	}

	//get plugin style
	$style = plugins_url( 'includes/css/uus-style.css', __FILE__ );
	//load plugin css
	wp_register_style( 'uus-style', $style, array(), '1.0' );
	//enqueue style
	wp_enqueue_style( 'uus-style' );

    return $params;
}

function uus_owlslider( $shortcode, $loop, $id, $numcols, $img_attr ){
	$output = "<div id='$id' class='owl-carousel'>";
    while ( $loop->have_posts() ) : $loop->the_post();
    	$image = wp_get_attachment_image_src( get_post_thumbnail_id( $loop->post->ID ));
    	$output .= "
    				<div class='post'>
    					<a id='id-" . $loop->post->ID . "' href='" . get_permalink($loop->post->ID) . "' title='" . get_the_title($loop->post->ID) ."'>" .
    						get_the_post_thumbnail( $loop->post->ID, 'large', $img_attr )
    					."</a>
    					<h3>". get_the_title($loop->post->ID) ."</h3>
    					<p>". get_the_content($loop->post->ID) ."</p>
    				</div>
    			  ";
    endwhile;
    wp_reset_query();

    $output .= "</div>";

    $output .= "<script>
					jQuery(document).ready(function() {

						jQuery('#$id').owlCarousel({

							autoPlay: 3000, //Set AutoPlay to 3 seconds

							items : $numcols,
							itemsDesktop : [1199,3],
							itemsDesktopSmall : [979,3]

						});
					 
					});
				</script>";
	return $output;
}

function uus_gridtype($gridtype, $post, $maxcols, $img_attr, $imgsize, $excerpt, $output, $buttontext, $i, $lazyload ){

	$imgurl = wp_get_attachment_image_src( get_post_thumbnail_id($post->ID), $imgsize);

	if( $lazyload ){
		$src = "data-src='" . $imgurl[0] . " ' ";
	}else{
		$src = "src='" . $imgurl[0] . " ' ";
	}

	switch ($gridtype) {
		case 'default':
			$output .= "
					<li class='col-sm-$maxcols'>
						<div class='uus-default'>
	    					<a id='uus-post-" . $post->ID . "' href='" . get_permalink($post->ID) . "' title='" . get_the_title($post->ID) ."'>
	    						<img " . $src . "class='" . $img_attr['class'] . "' alt='" . $post->post_name . "' width='" . $imgurl[1] . "' height='". $imgurl[2] ."' /> 
	    					</a>
	    				</div>
	    				<div class='uus-default-text-wrapper'>
	    					<h3>". get_the_title($post->ID) ."</h3>
	    					<p>". $excerpt ."</p>
	    				</div>
					</li>
				  ";
			break;
		case 'ladder':
			$output .= "
    				<li class='clearfix'>";
    				if($i % 2 != 0){
    					$output .= "
    					<div class='uus-ladder-img-wrapper col-sm-$maxcols'>
        					<a id='uus-post-" . $post->ID . "' href='" . get_permalink($post->ID) . "' title='" . get_the_title($post->ID) ."'>
        						<img " . $src . "class='" . $img_attr['class'] . "' alt='" . $post->post_name . "' width='" . $imgurl[1] . "' height='". $imgurl[2] ."' /> 
        					</a>
        				</div>
        				<div class='uus-ladder-text-wrapper col-sm-$maxcols'>
        					<h3 class='uus-post-title'>". get_the_title($post->ID) ."</h3>
        					<p class='uus-post-excerpt'>". $excerpt ."</p>
        				</div>";
        			}else{
        				$output .= "
        				<div class='uus-ladder-text-wrapper col-sm-$maxcols'>
        					<h3 class='uus-post-title'>". get_the_title($post->ID) ."</h3>
        					<p class='uus-post-excerpt'>". $excerpt ."</p>
        				</div>
        				<div class='uus-ladder-img-wrapper col-sm-$maxcols'>
        					<a id='uus-post-" . $post->ID . "' href='" . get_permalink($post->ID) . "' title='" . get_the_title($post->ID) ."'>
        						<img " . $src . "class='" . $img_attr['class'] . "' alt='" . $post->post_name . "' width='" . $imgurl[1] . "' height='". $imgurl[2] ."' /> 
        					</a>
        				</div>";
        			}
        	$output .= "</li>
        			  ";
        	break;
        case 'flat':
        	$output .= "
        				<li class='col-sm-6 col-md-6 col-lg-$maxcols nogutter'>
        					<div class='uus-flat-img-wrapper'>
	        					<a id='uus-post-" . $post->ID . "' href='" . get_permalink($post->ID) . "' title='" . get_the_title($post->ID) ."'>
	        						<img " . $src . "class='" . $img_attr['class'] . "' alt='" . $post->post_name . "' width='" . $imgurl[1] . "' height='". $imgurl[2] ."' /> 
	        					</a>
	        				</div>
	        				<div class='uus-flat-text-wrapper'>
		        				<h3 class='uus-post-title'>". get_the_title($post->ID) ."</h3>
		        				<p class='uus-post-excerpt'>". $excerpt ."</p>
		        				<a class='uus-post-button' href='" . get_permalink($post->ID) . "' title='" . get_the_title($post->ID) ."'>$buttontext</a>
	        				</div>
        				</li>
        			  ";
        	break;
	}

	return $output;
}
add_shortcode('posts', 'uus_posts_listing');

function uus_posts_listing($atts, $content){
	$shortcode = 'posts';
	
	//start the shortcode
	$atts = shortcode_atts(
		array(
			'id'			=>	!empty($atts['id']) ? $atts['id'] : '',
			'numcols'		=>	!empty($atts['numcols']) ? $atts['numcols'] : '4',
			'numposts'		=>	!empty($atts['numposts']) ? $atts['numposts'] : '4',
			'cptname'		=>	!empty($atts['cptname']) ? $atts['cptname'] : 'post',
			'gridtype'		=>	!empty($atts['gridtype']) ? $atts['gridtype'] : 'default',
			'imgsize'		=>	!empty($atts['imgsize']) ? $atts['imgsize'] : 'large',
			'buttontext'	=>	!empty($atts['buttontext']) ? $atts['buttontext'] : 'View Post',
			'type'			=>	!empty($atts['type']) ? $atts['type'] : '',
			'orderby'		=>	!empty($atts['orderby']) ? $atts['orderby'] : 'date',
			'order'			=>	!empty($atts['order']) ? $atts['order'] : 'DESC',
			'owlslider'		=>	!empty($atts['owlslider']) ? $atts['owlslider'] : false,
			'lazyload'		=>	!empty($atts['lazyload']) ? $atts['lazyload'] : false
			), $atts
		);

	//extract all from attributes array
	extract($atts);

	//convert string booleans from owlslider and lazyload to real booleans
	$owlslider = filter_var($owlslider, FILTER_VALIDATE_BOOLEAN);
	$lazyload = filter_var($lazyload, FILTER_VALIDATE_BOOLEAN); // true

	//extract all from params array
	extract(uus_load_dependencies($owlslider,$lazyload));

	//initialize variables
	$meta_key = '';
	$meta_value = '';

	switch ($type) {
		case '':
			$meta_query = array();
			break;
		case 'mostviews':
			$meta_query = array(
                    'key'     => 'post_views_count'
                        );
			break;
		case 'featured':
			$meta_query = array(
                    'key'     => '_featured',
                    'value'   => 'yes'
                        );
			break;
	}
	$args = array(
            'post_type'			=> $cptname,
            'post_status'  		=> 'publish',
            'posts_per_page'	=> $numposts,
            'meta_query'		=> $meta_query,
            'orderby'			=> $orderby,
            'order'				=> $order
            );
	
    $img_attr = array(
        'class' => "img-responsive",
    );

    $loop = new WP_Query( $args );
    global $post;
    if( $owlslider ){
    	$id = 'uus-owl-slider-id';
    	$output = uus_owlslider( $shortcode, $loop, $id, $numcols, $img_attr );
    }else{
    	$id = 'uus-posts-id';
    	$output = "<ul id='$id' class='uus-posts-wrapper'>";
    	$maxcols = $bootstrap_default_cols / $numcols;
    	$i=1;
		while ($loop->have_posts() ) : $loop->the_post();
			$image = wp_get_attachment_image_src( get_post_thumbnail_id( $post->ID ));
			$excerpt = apply_filters('the_excerpt', get_post_field('post_excerpt', $post->ID));
			$output = uus_gridtype($gridtype, $post, $maxcols, $img_attr, $imgsize, $excerpt, $output, $buttontext, $i, $lazyload);
			$i++;
		endwhile;
        
        wp_reset_query();
    
        $output .= "</ul>";
    }
    return $output;
}

add_shortcode('products-type', 'uus_products_type_listing');

function uus_products_type_listing($atts, $content){

	$shortcode = 'products-type';
	//extract all from params array
	extract(uus_load_dependencies());

	//start the shortcode
	$atts = shortcode_atts(
		array(
			'id'			=>	!empty($atts['id']) ? $atts['id'] : '',
			'numcols'		=>	!empty($atts['numcols']) ? $atts['numcols'] : '4',
			'numproducts'	=>	!empty($atts['numproducts']) ? $atts['numproducts'] : '4',
			'type'			=>	!empty($atts['type']) ? $atts['type'] : '',
			'orderby'		=>	!empty($atts['orderby']) ? $atts['orderby'] : 'date',
			'order'			=>	!empty($atts['order']) ? $atts['order'] : 'DESC',
			'owlslider'		=>	!empty($atts['owlslider']) ? $atts['owlslider'] : false
			), $atts
		);

	//extract all from array
	extract($atts);

	//initialize variables
	$meta_key = '';
	$meta_value = '';

	switch ($type) {
		case '':
			$meta_query = array();
			break;
		case 'best':
			$meta_query = array(
                    'key'     => 'total_sales'
                        );
			break;
		case 'featured':
			$meta_query = array(
                    'key'     => '_featured',
                    'value'   => 'yes'
                        );
			break;
		case 'sale':
			$meta_query = array(
                    'key'     => '_sale_price',
                    'value'   => 0,
                    'compare' => '>',
                    'type'    => 'NUMERIC'
                        );
			break;
	}
	$args = array(
            'post_type'			=> 'product',
            'stock'				=> 1,
            'posts_per_page'	=> $numproducts,
            'meta_query'		=> $meta_query,
            'orderby'			=> $orderby,
            'order'				=> $order
            );

	
    $img_attr = array(
        'class' => "img-responsive",
    );

    $loop = new WP_Query( $args );
    if($owlslider){
    	$id = 'uus-owl-slider-id';
        $output = uus_owlslider( $shortcode, $loop, $id, $numcols, $img_attr );
    }else{
    	$id = 'uus-products-id';
    	$output = "<ul id='$id' class='uus-products-wrapper'>";
        $maxcols = $bootstrap_default_cols / $numcols;
        while ( $loop->have_posts() ) : $loop->the_post(); global $product;
        	$image = wp_get_attachment_image_src( get_post_thumbnail_id( $product->post->ID ));
        	$output .= "
        				<li class='col-sm-$maxcols'>
        					<a id='uus-products" . $product->post->ID . "' href='" . get_permalink($product->post->ID) . "' title='" . get_the_title($product->post->ID) ."'>" .
        						get_the_post_thumbnail( $product->post->ID, 'large', $img_attr )
        					."</a>
        				</li>
        			  ";
        endwhile;
        wp_reset_query();
    
        $output .= "</ul>";
    }
    return $output;
}

add_shortcode('banner', 'uus_banner_element');
function uus_banner_element($atts, $content){
	//start the shortcode
	$atts = shortcode_atts(
		array(
			'id'			=>	!empty($atts['id']) ? $atts['id'] : 'uus-banner',
			'class'			=>	!empty($atts['class']) ? $atts['class'] : 'uus-banner-class',
			'text'			=>	!empty($atts['text']) ? $atts['text'] : 'My banner message',
			'img'			=>	!empty($atts['img']) ? $atts['img'] : '',
			), $atts
		);

	//extract all from array
	extract($atts);

	$output = "
				<div id='$id' class='$class' style='background-image:url($img);'>
					$text" .
					do_shortcode($content) .
				"</div>";
	return $output;
}
add_shortcode('button', 'uus_button_element');
function uus_button_element($atts, $content){
	//start the shortcode
	$atts = shortcode_atts(
		array(
			'id'		=>	!empty($atts['id']) ? $atts['id'] : 'uus-button',
			'class'		=>	!empty($atts['class']) ? $atts['class'] : 'uus-button-class',
			'text'		=>	!empty($atts['text']) ? $atts['text'] : 'Click Me',
			'link'		=>	!empty($atts['link']) ? $atts['link'] : get_home_url(),
			), $atts
		);

	//extract all from array
	extract($atts);
	$output = "
				<a id='$id' class='$class' href='$link'>$text</a>";
	return $output;
}

add_shortcode('categories', 'uus_get_all_categories');
function uus_get_all_categories($atts){
	//start the shortcode
	$atts = shortcode_atts(
		array(
			'id'		=>	!empty($atts['id']) ? $atts['id'] : 'uus-products-category',
			'class'		=>	!empty($atts['class']) ? $atts['class'] : 'uus-products-category-class',
			'numcols'	=>	!empty($atts['numcols']) ? $atts['numcols'] : '4',
			'orderby'	=>	!empty($atts['orderby']) ? $atts['orderby'] : 'count',
			'order'		=>	!empty($atts['order']) ? $atts['order'] : 'DESC',
			), $atts
		);

	//extract all from array
	extract($atts);

	$categories = get_terms( 'product_cat', array(
	 	'orderby'		=>	$orderby,
	 	'order'			=>	$order,
	 	'hide_empty'	=>	0,
	) );

	$output = "<ul id='$id' class='$class'>";

	foreach ($categories as $category) {
		$url = get_term_link($category);
		$category_thumbnail = get_woocommerce_term_meta($category->term_id, 'thumbnail_id', true);
		$image = wp_get_attachment_url($category_thumbnail);
		$output .= "<li class='col-sm-$numcols'><a href='$url'><img class='img-responsive' src='$image' /></a></li>";
	}

	$output .= "</ul>";
	// var_dump($category_thumbnail);
	return $output;
}

add_shortcode('portfolio', 'uus_get_portfolio');
function uus_get_portfolio($atts){
	//start the shortcode
	$atts = shortcode_atts(
		array(
			'id'		=>	!empty($atts['id']) ? $atts['id'] : 'uus-portfolio',
			'class'		=>	!empty($atts['class']) ? $atts['class'] : 'uus-portfolio-class',
			'numcols'	=>	!empty($atts['numcols']) ? $atts['numcols'] : '4',
			'orderby'	=>	!empty($atts['orderby']) ? $atts['orderby'] : 'date',
			'order'		=>	!empty($atts['order']) ? $atts['order'] : 'DESC',
			'per_page'	=>	!empty($atts['per_page']) ? $atts['per_page'] : '4',
			), $atts
		);

	//extract all from array
	extract($atts);

	$args = array(
            'post_type'			=> 'portfolio',
            'posts_per_page'	=> $per_page,
            'orderby'			=> $orderby,
            'order'				=> $order
            );

	$output = "<ul id='$id' class='portfolio'>";
	$img_attr = array(
        'class' => "img-responsive",
    );
    $loop = new WP_Query( $args );
    while ( $loop->have_posts() ) : $loop->the_post(); global $portfolio;
    	$image = wp_get_attachment_image_src( get_post_thumbnail_id( $portfolio->post->ID ));
    	$output .= "
    				<li class='col-sm-$numcols'>
    					<a id='id-" . $portfolio->post->ID . "' href='" . get_permalink($portfolio->post->ID) . "' title='" . get_the_title($portfolio->post->ID) ."'>" .
    						get_the_post_thumbnail( $portfolio->post->ID, 'large', $img_attr )
    					."</a>
    				</li>
    			  ";
    endwhile;
    wp_reset_query();

    $output .= "</ul>";
    
    return $output;
}