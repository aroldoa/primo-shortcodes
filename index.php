<?php
/*
Plugin Name: Ultimate Utility Shortcodes
Plugin URI: http://www.datamaw.com
Description: The most popular tasks in a shortcode bundle
Version: 1.0
Author: Mario Silva
Author URI: http://www.datamaw.com
*/

add_shortcode('uus', 'ultimate');

function ultimate($atts, $content){
	//get the carousel js url
	$owl_carousel_js = plugins_url( 'includes/js/owl-carousel/owl.carousel.min.js', __FILE__ );

	//get the carousel css url
	$owl_carousel_css = plugins_url( 'includes/css/owl-carousel/owl.carousel.css', __FILE__ );

	//get the carousel theme url
	$owl_carousel_theme = plugins_url( 'includes/css/owl-carousel/owl.theme.css', __FILE__ );

	//register owl css
	wp_register_style( 'owl-carousel-css', $owl_carousel_css, array(), '1.0' );

	//register owl css
	wp_register_style( 'owl-carousel-theme', $owl_carousel_theme, array('owl-carousel-css'), '1.0' );

	//register owl js
	wp_register_script( 'owl-carousel-js', $owl_carousel_js, array('jquery'), '1.0');

	//enqueue style
	wp_enqueue_style( 'owl-carousel-theme' );

	//enqueue js
	wp_enqueue_script( 'owl-carousel-js' ); 


	//start the shortcode
	$atts = shortcode_atts(
		array(
			'id'			=>	!empty($atts['id']) ? $atts['id'] : 'owl-slider-wrapper',
			'numcols'		=>	!empty($atts['numcols']) ? $atts['numcols'] : '4',
			'numproducts'	=>	!empty($atts['numproducts']) ? $atts['numproducts'] : '4',
			'type'			=>	!empty($atts['type']) ? $atts['type'] : 'new',
			'owlslider'		=>	!empty($atts['owlslider']) ? $atts['owlslider'] : false,
			), $atts
		);

	//extract all from array
	extract($atts);

	//initialize variables
	$meta_query = array();
	$meta_key = '';
	$meta_value = '';
	$date = 'date';

	switch ($type) {
		case 'new':
			$date = 'date';
			break;
		case 'best':
			$meta_query[] = array(
                    'key'     => 'total_sales'
                        );
			break;
		case 'featured':
			$meta_query[] = array(
                    'key'     => '_featured',
                    'value'   => 'yes'
                        );
			break;
		case 'sale':
			$meta_query[] = array(
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
            'orderby'			=> $date,
            'order'				=> 'DESC'
            );

	
    $img_attr = array(
        'class' => "img-responsive",
    );

    $loop = new WP_Query( $args );
    if($owlslider){
        $output = "<div id='$id' class='owl-carousel'>";
        while ( $loop->have_posts() ) : $loop->the_post(); global $product;
        	$image = wp_get_attachment_image_src( get_post_thumbnail_id( $product->post->ID ));
        	$output .= "
        				<div class='product'>
        					<a id='id-" . $product->post->ID . "' href='" . get_permalink($product->post->ID) . "' title='" . get_the_title($product->post->ID) ."'>" .
        						get_the_post_thumbnail( $product->post->ID, 'large', $img_attr )
        					."</a>
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
    }else{
    	$output = "<ul id='$id' class='products'>";
        
        while ( $loop->have_posts() ) : $loop->the_post(); global $product;
        	$image = wp_get_attachment_image_src( get_post_thumbnail_id( $product->post->ID ));
        	$output .= "
        				<li class='col-sm-$numcols'>
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