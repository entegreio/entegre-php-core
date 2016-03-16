<?php

/**
 * @package entegre-core
 * @author James Linden <kodekrash@gmail.com>
 * @copyright 2016 James Linden
 * @license MIT
 */
namespace entegre\factory;

class node {
	
	use \entegre\factory\attr;
	use \entegre\factory\children;

	protected $t = null;

	public function __construct( $tag, $attr = null, $children = null ) {
		$this->t = strtolower( (string)$tag );
		if( ! empty( $attr ) ) {
			$this->attr( $attr );
		}
		if( ! empty( $children ) ) {
			$this->child( $children );
		}
	}

	public function build() {
		$nc = [ 'br', 'hr', 'img', 'link', 'meta', 'meta-equiv', 'input' ];
		$a = $this->buildattrs();
		$s = '<' . $this->t . ( empty( $a ) ? '' : ' ' . $a ) . '>';
		if( ! in_array( $this->t, $nc ) ) {
			$s .= $this->buildchildren() . '</' . $this->t . '>';
		}
		return $s;
	}

}

?>