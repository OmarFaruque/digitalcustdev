<?php

/**
 * Class FE_Editor
 *
 * @since 3.0.0
 */
class FE_Editor {
	/**
	 * @var FE_Course_Editor[]
	 */
	protected static $editors = array();

	/**
	 * FE_Editor constructor.
	 */
	protected function __construct() {
	}

	/**
	 * @param string $action
	 *
	 * @return bool|mixed
	 */
	public function dispatch( $action ) {
		$callback = preg_replace( '~[-]+~', '_', preg_replace( '~^fe/~', '', $action ) );

		if ( is_callable( array( $this, $callback ) ) ) {
			return call_user_func( array( $this, $callback ) );
		} elseif ( is_callable( $callback ) ) {
			return call_user_func( $callback );
		}


		return false;
	}

	/**
	 * Get editor instance
	 *
	 * @param string $ed
	 *
	 * @return bool|FE_Course_Editor
	 */
	public static function get( $ed ) {
		if ( empty( self::$editors[ $ed ] ) ) {
			$editor = 'FE_' . ucfirst( $ed ) . '_Editor';

			if ( class_exists( $editor ) ) {
				self::$editors[ $ed ] = new $editor();
			}
		}

		return ! empty( self::$editors[ $ed ] ) ? self::$editors[ $ed ] : false;
	}
}