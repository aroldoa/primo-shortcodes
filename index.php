<?php
/*
Plugin Name: Ultimate Utility Shortcodes
Plugin URI: http://www.datamaw.com
Description: The most popular tasks in a shortcode bundle
Version: 1.0
Author: Mario Silva
Author URI: http://www.datamaw.com
*/
function uus_load_dependencies(){
	$params = array(
		'bootstrap_default_cols'	=>	12
		);
	$owl_carousel_js = 'owl-carousel-js';
	$list = 'done';

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

function uus_gridtype($gridtype, $post, $maxcols, $img_attr, $excerpt, $output, $i){
	switch ($gridtype) {
		case 'dgrid':
			$output .= "
					<li class='col-sm-$maxcols'>
						<div class='dgrid'>
	    					<a id='id-" . $post->ID . "' href='" . get_permalink($post->ID) . "' title='" . get_the_title($post->ID) ."'>" .
	    						get_the_post_thumbnail( $post->ID, 'large', $img_attr )
	    					."</a>
	    				</div>
	    				<div class='dgrid-text-wrapper'>
	    					<h3>". get_the_title($post->ID) ."</h3>
	    					<p>". $excerpt ."</p>
	    				</div>
					</li>
				  ";
			break;
		case 'lgrid':
			$output .= "
    				<li class='clearfix'>";
    				if($i % 2 != 0){
    					$output .= "
    					<div class='lgrid-img-wrapper col-sm-$maxcols'>
        					<a id='id-" . $post->ID . "' href='" . get_permalink($post->ID) . "' title='" . get_the_title($post->ID) ."'>" .
        						get_the_post_thumbnail( $post->ID, 'large', $img_attr )
        					."</a>
        				</div>
        				<div class='lgrid-text-wrapper col-sm-$maxcols'>
        					<h3>". get_the_title($post->ID) ."</h3>
        					<p>". $excerpt ."</p>
        				</div>";
        			}else{
        				$output .= "
        				<div class='lgrid-text-wrapper col-sm-$maxcols'>
        					<h3>". get_the_title($post->ID) ."</h3>
        					<p>". $excerpt ."</p>
        				</div>
        				<div class='lgrid-img-wrapper col-sm-$maxcols'>
        					<a id='id-" . $post->ID . "' href='" . get_permalink($post->ID) . "' title='" . get_the_title($post->ID) ."'>" .
        						get_the_post_thumbnail( $post->ID, 'large', $img_attr )
        					."</a>
        				</div>";
        			}
        	$output .= "</li>
        			  ";
        	break;
        case 'fgrid':
        	$output .= "
        				<li class='col-sm-$maxcols nogutter'>
        					<div class='fgrid-img-wrapper'>
	        					<a id='id-" . $post->ID . "' href='" . get_permalink($post->ID) . "' title='" . get_the_title($post->ID) ."'>" .
	        						get_the_post_thumbnail( $post->ID, 'large', $img_attr )
	        					."</a>
	        				</div>
	        				<div class='fgrid-text-wrapper'>
		        				<h3>". get_the_title($post->ID) ."</h3>
		        				<p>". $excerpt ."</p>
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
	//extract all from params array
	extract(uus_load_dependencies());
	//start the shortcode
	$atts = shortcode_atts(
		array(
			'id'			=>	!empty($atts['id']) ? $atts['id'] : 'owl-slider-wrapper',
			'numcols'		=>	!empty($atts['numcols']) ? $atts['numcols'] : '4',
			'numposts'		=>	!empty($atts['numposts']) ? $atts['numposts'] : '4',
			'cptname'		=>	!empty($atts['cptname']) ? $atts['cptname'] : 'post',
			'gridtype'		=>	!empty($atts['gridtype']) ? $atts['gridtype'] : 'dgrid',
			'type'			=>	!empty($atts['type']) ? $atts['type'] : '',
			'orderby'		=>	!empty($atts['orderby']) ? $atts['orderby'] : 'date',
			'order'			=>	!empty($atts['order']) ? $atts['order'] : 'DESC',
			'owlslider'		=>	!empty($atts['owlslider']) ? $atts['owlslider'] : false
			), $atts
		);

	//extract all from attributes array
	extract($atts);

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
    	$output = uus_owlslider( $shortcode, $loop, $id, $numcols, $img_attr );
    }else{
    	$output = "<ul id='$id' class='posts'>";
    	$maxcols = $bootstrap_default_cols / $numcols;
    	$i=1;
		while ($loop->have_posts() ) : $loop->the_post();
			$image = wp_get_attachment_image_src( get_post_thumbnail_id( $post->ID ));
			$excerpt = apply_filters('the_excerpt', get_post_field('post_excerpt', $post->ID));
			$output = uus_gridtype($gridtype, $post, $maxcols, $img_attr, $excerpt, $output, $i);
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
			'id'			=>	!empty($atts['id']) ? $atts['id'] : 'owl-slider-wrapper',
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
        $output = uus_owlslider( $shortcode, $loop, $id, $numcols, $img_attr );
    }else{
    	$output = "<ul id='$id' class='products'>";
        $maxcols = $bootstrap_default_cols / $numcols;
        while ( $loop->have_posts() ) : $loop->the_post(); global $product;
        	$image = wp_get_attachment_image_src( get_post_thumbnail_id( $product->post->ID ));
        	$output .= "
        				<li class='col-sm-$maxcols'>
        					<a id='id-" . $product->post->ID . "' href='" . get_permalink($product->post->ID) . "' title='" . get_the_title($product->post->ID) ."'>" .
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

add_shortcode('banner', 'banner_element');
function banner_element($atts, $content){
	//start the shortcode
	$atts = shortcode_atts(
		array(
			'id'			=>	!empty($atts['id']) ? $atts['id'] : 'ultimate-banner',
			'class'			=>	!empty($atts['class']) ? $atts['class'] : 'ultimate-banner-class',
			'text'			=>	!empty($atts['text']) ? $atts['text'] : 'My banner message',
			'img'			=>	!empty($atts['img']) ? $atts['img'] : '',
			), $atts
		);

	//extract all from array
	extract($atts);

	$output = "
				<div id='$id' class='$class' style='background-image:url($img);'>
					<p>$text</p>" .
					do_shortcode($content) .
				"</div>";
	return $output;
}
add_shortcode('button', 'button_element');
function button_element($atts, $content){
	//start the shortcode
	$atts = shortcode_atts(
		array(
			'id'		=>	!empty($atts['id']) ? $atts['id'] : 'ultimate-button',
			'class'		=>	!empty($atts['class']) ? $atts['class'] : 'ultimate-button-class',
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

add_shortcode('categories', 'get_all_categories');
function get_all_categories($atts){
	//start the shortcode
	$atts = shortcode_atts(
		array(
			'id'		=>	!empty($atts['id']) ? $atts['id'] : 'ultimate-products-category',
			'class'		=>	!empty($atts['class']) ? $atts['class'] : 'ultimate-products-category-class',
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

add_shortcode('portfolio', 'get_portfolio');
function get_portfolio($atts){
	//start the shortcode
	$atts = shortcode_atts(
		array(
			'id'		=>	!empty($atts['id']) ? $atts['id'] : 'ultimate-portfolio',
			'class'		=>	!empty($atts['class']) ? $atts['class'] : 'ultimate-portfolio-class',
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