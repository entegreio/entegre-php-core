<?php

/**
 * @package entegre-core
 * @author James Linden <kodekrash@gmail.com>
 * @copyright 2016 James Linden
 * @license MIT
 */
namespace entegre\factory;

trait children {

	protected $c = [];

	public function child( $v ) {
		if( ! empty( $v ) ) {
			if( \entegre\ap( $v ) ) {
				$this->c = array_merge( $this->c, $v );
			} else {
				$this->c[] = $v;
			}
		}
		return $this;
	}

	protected function buildchildren() {
		$s = [];
		foreach( $this->c as $v ) {
			if( is_object( $v ) && method_exists( $v, 'build' ) ) {
				$s[] = $v->build();
			} else {
				$s[] = (string)$v;
			}
		}
		return implode( ' ', $s );
	}

}

?>