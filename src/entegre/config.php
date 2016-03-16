<?php

/**
 * @package entegre-core
 * @author James Linden <kodekrash@gmail.com>
 * @copyright 2016 James Linden
 * @license MIT
 */
namespace entegre;

class config {

	private $_data = [];

	private function _key( $s ) {
		return trim( strtolower( $s ) );
	}

	private function _array( $k ) {
		if( array_key_exists( $k, $this->_data ) && ! is_array( $this->_data[ $k ] ) ) {
			$this->_data[ $k ] = (array)$this->_data[ $k ];
		} else if( ! array_key_exists( $k, $this->_data ) ) {
			$this->_data[ $k ] = [];
		}
	}

	public function set( $k, $v ) {
		$k = $this->_key( $k );
		$this->_data[ $k ] = $v;
		return $this;
	}

	public function get( $k, $v = null ) {
		$k = $this->_key( $k );
		return array_key_exists( $k, $this->_data ) ? $this->_data[ $k ] : $v;
	}

	public function add( $k, $v = null ) {
		$k = $this->_key( $k );
		$this->_array( $k );
		array_push( $this->_data[ $k ], $v );
		return $this;
	}

	public function pre( $k, $v = null ) {
		$k = $this->_key( $k );
		$this->_array( $k );
		array_unshift( $this->_data[ $k ], $v );
		return $this;
	}

}

?>