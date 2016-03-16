<?php

/**
 * @package entegre-core
 * @author James Linden <kodekrash@gmail.com>
 * @copyright 2016 James Linden
 * @license MIT
 */
namespace entegre;

spl_autoload_register( function ( $n ) {
	$n = str_replace( '\\', '/', strtolower( ltrim( strtolower( substr( $n, 0, 7 ) ) == 'entegre' ? substr( $n, 7 ) : $n, '\\' ) ) );
	$f = __DIR__ . '/' . $n . '.php';
	if( is_file( $f ) ) {
		require_once $f;
	}
} );

function ap( $var ) {
	return is_array( $var ) && count( $var ) > 0;
}

function akp( $key, $data ) {
	if( is_array( $key ) && is_array( $data ) ) {
		$i = 0;
		foreach( $key as $k ) {
			if( array_key_exists( $k, $data ) && ! empty( $data[ $k ] ) ) {
				$i ++;
			}
		}
		return $i == count( $key );
	} else {
		return is_array( $data ) && array_key_exists( $key, $data ) && ! empty( $data[ $key ] );
	}
}

function node( $tag, $attr = null, $child = null ) {
	return new \entegre\factory\node( $tag, $attr, $child );
}

function id( $p = null ) {
	static $_id = 1;
	$s = ( ! empty( $p ) ? $p : 'eio' ) . $_id;
	$_id ++;
	return $s;
}

function optionselect( $value = null, $default = 0, $data = [] ) {
	$data = ap( $data ) ? $data : [];
	if( is_integer( $value ) ) {
		return $data[ ( $value >= 0 && $value <= count( $data ) ? $value : $default ) ];
	} else {
		$value = trim( strtolower( $value ) );
		return in_array( $value, $data ) ? $value : $data[ $default ];
	}
}

function cfg() {
	static $_cfg;
	if( ! $_cfg ) {
		$_cfg = new \entegre\config();
		$_cfg->set( 'generator', 'Entegre/0.1' );
		$_cfg->set( 'jquery_version', '2.1.4' );
		$_cfg->set( 'search_order', [] );
	}
	return $_cfg;
}

function E( $cls, $arg = null ) {
	$i = null;
	$o = cfg()->get( 'search_order', [] );
	foreach( $o as $x ) {
		$f = "entegre\\$x\\E";
		if( is_callable( $f ) ) {
			$i = $f( $cls, $arg );
		} else {
			$c = strtolower( "entegre\\$x\\$cls" );
			if( class_exists( $c ) ) {
				$i = new $c( $arg );
			}
		}
		if( ! empty( $i ) ) {
			return $i;
		}
	}
	if( $cls == 'page' ) {
		$i = new \entegre\factory\page();
	}
	if( empty( $i ) ) {
		$i = node( $cls, $arg );
	}
	return $i;
}

?>