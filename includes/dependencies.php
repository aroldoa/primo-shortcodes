<?php

function uus_load_dependencies( $owlslider = false, $lazyload = false ){

	$list = 'done';

	$params = array(
		'bootstrap_default_cols'	=>	12
		);


	if( $owlslider ){
		$owl_carousel_js = 'owl-carousel-js';
		//get the carousel js url
		$owl_carousel_js_url = plugins_url( 'js/owl-carousel/owl.carousel.min.js', __FILE__ );	
		//get the carousel theme url
		$owl_carousel_theme_url = plugins_url( 'css/owl-carousel/owl.theme.css', __FILE__ );
	   	//get the carousel css url
		$owl_carousel_css_url = plugins_url( 'css/owl-carousel/owl.carousel.css', __FILE__ );

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
		$lazy_load_settings_js_url = plugins_url( 'js/lazy-load-xt/lazy-load-custom-settings.js', __FILE__ );	
		//get the lazy load js url
		$lazy_load_js_url = plugins_url( 'js/lazy-load-xt/jquery.lazyloadxt.min.js', __FILE__ );	
		//get the lazy load css url
		$lazy_load_css_url = plugins_url( 'css/lazy-load-xt/jquery.lazyloadxt.fadein.min.css', __FILE__ );
		//get the animate css file
		// $lazy_load_css_url = plugins_url( 'css/animate/animate.min.css', __FILE__ );

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
	$style = plugins_url( 'css/uus-style.css', __FILE__ );

	if ( !wp_script_is( $style, $list ) ) {
		//load plugin css
		wp_register_style( 'uus-style', $style, array(), '1.0' );
		//enqueue style
		wp_enqueue_style( 'uus-style' );
	}

    return $params;
}
?>