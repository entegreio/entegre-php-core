<?php

/**
 * @package entegre-core
 * @author James Linden <kodekrash@gmail.com>
 * @copyright 2016 James Linden
 * @license MIT
 */
namespace entegre\factory;

trait attr {

	protected $a = [];

	private function _attr( $k, $v = null ) {
		$k = (string)$k;
		if( ! \entegre\akp( $k, $this->a ) ) {
			$this->a[ $k ] = [];
		}
		if( \entegre\ap( $v ) ) {
			$this->a[ $k ] = array_merge( $this->a[ $k ], $v );
		} else {
			$this->a[ $k ][] = $v;
		}
	}

	public function attr( $k, $v = null, $o = false ) {
		if( ! empty( $k ) ) {
			if( $o === true ) {
				$this->a = $k;
			} else {
				if( \entegre\ap( $k ) ) {
					if( empty( $v ) ) {
						foreach( $k as $k1 => $v1 ) {
							$this->_attr( $k1, $v1 );
						}
					} else {
						foreach( $k as $k1 ) {
							$this->_attr( $k1, $v );
						}
					}
				} else {
					$this->_attr( $k, $v );
				}
			}
		}
		return $this;
	}

	public function attrs( $d ) {
		if( \entegre\ap( $d ) ) {
			$this->a = $d;
		}
		return $this;
	}

	public function attrdrop( $k, $v = null ) {
		if( ! empty( $k ) && array_key_exists( $k, $this->a ) ) {
			if( empty( $v ) ) {
				unset( $this->a[ $k ] );
			} else {
				foreach( $this->a[ $k ] as $i => $x ) {
					if( $x == $v ) {
						unset( $this->a[ $k ][ $i ] );
					}
				}
			}
		}
		return $this;
	}

	private function _buildattr( $k, $v ) {
		$v = trim( \entegre\ap( $v ) ? implode( ' ', $v ) : $v );
		return $k . ( strlen( $v ) == 0 ? null : '="' . $v . '"' );
	}

	protected function buildattrs() {
		$s = [];
		foreach( $this->a as $k => $v ) {
			$s[] = $this->_buildattr( $k, $v );
		}
		return implode( ' ', $s );
	}

}

?>