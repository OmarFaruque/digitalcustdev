<?php
/**
 * @author Deepen.
 * @created_on 6/21/19
 */

if ( ! function_exists( 'dcd_core_set_notification' ) ) {
	function dcd_core_set_notification( $type ) {
		if ( $type === "success" ) {
			setcookie( 'dcd_core_updated', true, time() + 60, "/" );
		} else if ( $type === "error" ) {
			setcookie( 'dcd_core_updated', false, time() + 60, "/" );
		}
	}
}

if ( ! function_exists( 'dcd_core_get_notification' ) ) {
	function dcd_core_get_notification( $msg ) {
		if ( isset( $_COOKIE['dcd_core_updated'] ) && $_COOKIE['dcd_core_updated'] === "1" ) {
			?>
            <div class="message message-success" role="alert">
                <p><?php echo $msg; ?></p>
            </div>
			<?php
		}

		if ( isset( $_COOKIE['dcd_core_updated'] ) && $_COOKIE['dcd_core_updated'] === "0" ) {
			?>
            <div class="message message-danger" role="alert">
                <p><?php echo $msg; ?></p>
            </div>
			<?php
		}
	}
}

if ( ! function_exists( 'dcd_core_get_curriculum_type_icons' ) ) {
	function dcd_core_get_curriculum_type_icons( $type ) {
		$icon = false;
		if ( $type === "lp_lesson" ) {
			$icon = '<i class="fa fa-book"></i>';
		}

		if ( $type === "lp_quiz" ) {
			$icon = '<i class="fa fa-clock-o"></i>';
		}

		if ( $type === "lp_assignment" ) {
			$icon = '<i class="fa fa-tasks"></i>';
		}

		return $icon;
	}
}

if ( ! function_exists( 'dcd_core_get_template' ) ) {
	/**
	 * @param $template_name
	 * @param array $args
	 * @param string $template_path
	 * @param string $default_path
	 */
	function dcd_core_get_template( $template_name, $args = array(), $template_path = '', $default_path = '' ) {
		learn_press_get_template( $template_name, $args, learn_press_template_path() . '/addons/dcd-core/', DIGITALCUSTDEV_PLUGIN_PATH . 'templates/' );
	}
}

if ( ! function_exists( 'dcd_core_folderSize' ) ) {
	function dcd_core_folderSize( $dir ) {
		$count_size = 0;
		$count      = 0;
		$dir_array  = scandir( $dir );
		foreach ( $dir_array as $key => $filename ) {
			if ( $filename != ".." && $filename != "." ) {
				if ( is_dir( $dir . "/" . $filename ) ) {
					$new_foldersize = foldersize( $dir . "/" . $filename );
					$count_size     = $count_size + $new_foldersize;
				} else if ( is_file( $dir . "/" . $filename ) ) {
					$count_size = $count_size + filesize( $dir . "/" . $filename );
					$count ++;
				}
			}
		}

		return $count_size;
	}
}