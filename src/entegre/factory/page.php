<?php

/**
 * @package entegre-core
 * @author James Linden <kodekrash@gmail.com>
 * @copyright 2016 James Linden
 * @license MIT
 */
namespace entegre\factory;

class page {

	protected $title = null;

	protected $style = [ 'screen' => [] ];

	protected $script = [ 'head' => [], 'body' => [] ];

	protected $head = [];

	protected $attr = [];

	protected $child = [];

	protected $clean = false;

	public function __construct( $attr = null ) {
		if( \entegre\ap( $attr ) ) {
			$this->attr = $attr;
		}
		$this->head( 'meta', [ 'charset' => 'utf-8' ] );
		$this->head( 'meta', [ 'name' => 'lang', 'content' => 'en' ] );
		$this->head( 'meta', [ 'name' => 'viewport', 'content' => 'width=device-width, initial-scale=1.0' ] );
		$this->head( 'meta', [ 'name' => 'x-ua-compatible', 'content' => 'IE=edge' ] );
		$this->head( 'meta', [ 'name' => 'mobileoptimized', 'content' => '300' ] );
		$this->head( 'meta', [ 'http-equiv' => 'cleartype', 'content' => 'on' ] );
		$this->head( 'meta', [ 'name' => 'generator', 'content' => \entegre\cfg()->get( 'generator' ) ] );
		$this->script( 'https://cdnjs.cloudflare.com/ajax/libs/jquery/' . \entegre\cfg()->get( 'jquery_version', '2.1.4' ) . '/jquery.min.js', true );
	}

	public function clean() {
		$this->clean = true;
		return $this;
	}

	public function title( $str ) {
		$this->title = trim( $str );
		return $this;
	}

	public function head( $type, $attr ) {
		$type = trim( strtolower( $type ) );
		if( in_array( $type, [ 'meta', 'link' ] ) ) {
			if( ! \entegre\akp( $type, $this->head ) ) {
				$this->head[ $type ] = [];
			}
			$this->head[ $type ][] = $attr;
		}
		return $this;
	}

	public function style( $url, $media = 'screen' ) {
		$media = trim( strtolower( $media ) );
		if( ! \entegre\akp( $media, $this->style ) ) {
			$this->style[ $media ] = [];
		}
		$url = \entegre\ap( $url ) ? $url : [ $url ];
		foreach( $url as $x ) {
			$this->style[ $media ][] = $x;
		}
		return $this;
	}

	public function script( $url, $body = false ) {
		$body = (boolean)$body;
		$url = \entegre\ap( $url ) ? $url : [ $url ];
		foreach( $url as $x ) {
			$this->script[ ( $body ? 'body' : 'head' ) ][] = $x;
		}
		return $this;
	}

	public function child( $var ) {
		if( is_array( $var ) ) {
			$this->child = array_merge( $this->child, $var );
		} else {
			$this->child[] = $var;
		}
		return $this;
	}

	protected function _meta( $group, $key, $value ) {
		switch( $group ) {
			case 'twitter':
				if( $key == 'img:src' ) {
					$this->head( 'meta', [ 'name' => 'twitter:' . $key, 'href' => $value ] );
				} else {
					$this->head( 'meta', [ 'name' => 'twitter:' . $key, 'content' => $value ] );
				}
			break;
			case 'opengraph':
				if( $key == 'image' ) {
					$this->head( 'meta', [ 'name' => 'og:' . $key, 'href' => $value ] );
				} else {
					$this->head( 'meta', [ 'name' => 'og:' . $key, 'content' => $value ] );
				}
			break;
			case 'facebook':
				$this->head( 'meta', [ 'name' => 'fb:' . $key, 'content' => $value ] );
			break;
			case 'google':
				$this->head( 'meta', [ 'name' => 'google-' . $key, 'content' => $value ] );
			break;
		}
	}

	public function meta( $group, $key, $value = null ) {
		$group = trim( strtolower( $group ) );
		if( in_array( $group, [ 'twitter', 'opengraph', 'google', 'facebook' ] ) ) {
			if( \entegre\ap( $key ) ) {
				foreach( $key as $k => $v ) {
					$this->_meta( $group, trim( strtolower( $k ) ), $v );
				}
			} else if( ! empty( $key ) && ! empty( $value ) ) {
				$this->_meta( $group, trim( strtolower( $key ) ), $value );
			}
		}
		return $this;
	}

	protected function build_head( $group ) {
		if( \entegre\ap( $this->head ) && \entegre\akp( $group, $this->head ) && \entegre\ap( $this->head[ $group ] ) ) {
			$s = [];
			foreach( $this->head[ $group ] as $v ) {
				$s[] = new node( $group, $v );
			}
			return $s;
		}
		return null;
	}

	protected function build_script( $group ) {
		if( \entegre\ap( $this->script ) && \entegre\akp( $group, $this->script ) && \entegre\ap( $this->script[ $group ] ) ) {
			$s = [];
			foreach( $this->script[ $group ] as $v ) {
				$s[] = new node( 'script', [ 'src' => $v ] );
			}
			return $s;
		}
		return null;
	}

	private function _tidy( $s ) {
		if( class_exists( 'tidy' ) ) {
			$o = [ 'doctype' => '<!DOCTYPE HTML>', 'hide-comments' => true, 'tidy-mark' => false, 'indent' => true, 'indent-spaces' => 4, 'new-blocklevel-tags' => 'article,header,footer,section,nav', 'new-inline-tags' => 'video,audio,canvas,ruby,rt,rp', 'new-empty-tags' => 'source', 
					'sort-attributes' => 'alpha', 'vertical-space' => false, 'output-xhtml' => true, 'wrap' => 180, 'wrap-attributes' => false, 'break-before-br' => false ];
			$s = tidy_parse_string( $s, $o, 'utf8' );
			tidy_clean_repair( $s );
			$s = str_replace( '<html lang="en" xmlns="http://www.w3.org/1999/xhtml">', null, $s );
			$s = str_replace( '>' . PHP_EOL . '</script>', '></script>', $s );
			$s = trim( $s );
		}
		return $s;
	}

	public function build() {
		$html = new node( 'html', [ 'lang' => 'en' ] );
		$head = new node( 'head' );
		$head->child( $this->build_head( 'meta' ) );
		$head->child( new node( 'title', null, $this->title ) );
		$head->child( $this->build_head( 'link' ) );
		if( \entegre\ap( $this->style ) ) {
			foreach( $this->style as $m => $s ) {
				foreach( $s as $s1 ) {
					$x = new node( 'link', [ 'rel' => 'stylesheet', 'href' => $s1 ] );
					if( $m != 'screen' ) {
						$x->attr( 'media', $m );
					}
					$head->child( $x );
				}
			}
		}
		$head->child( $this->build_script( 'head' ) );
		if( method_exists( $this, 'custom_head' ) ) {
			$head->child( $this->custom_head() );
		}
		$html->child( $head );
		unset( $head );
		$body = new node( 'body', $this->attr );
		if( \entegre\ap( $this->child ) ) {
			$body->child( $this->child );
		}
		$body->child( $this->build_script( 'body' ) );
		$html->child( $body );
		unset( $body );
		$s = $html->build();
		unset( $html, $x, $m, $s1 );
		if( $this->clean === true ) {
			$s = $this->_tidy( $s );
		}
		return '<!DOCTYPE html>' . PHP_EOL . $s;
	}

	public function output() {
		header( 'Content-type: text/html; charset=utf-8' );
		echo $this->build();
		unset( $this );
	}

}

?>