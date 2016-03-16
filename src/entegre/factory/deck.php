<?php

/**
 * @package entegre-core
 * @author James Linden <kodekrash@gmail.com>
 * @copyright 2016 James Linden
 * @license MIT
 */
namespace entegre\factory;

trait deck {

	protected $c = [];

	public function card( $t, $b = null, $a = null ) {
		$x = [ 'title' => empty( $t ) ? null : $t, 'body' => empty( $b ) ? null : $b, 'attr' => empty( $a ) ? null : $a ];
		$this->c[] = $x;
		return $this;
	}

}

?>