<!-- <div class="main-menu"> -->
<div class="thim-nav-wrapper <?php echo get_theme_mod( 'thim_header_size', 'default' ) == 'full_width' ? 'header_full' : 'container'; ?>">
	<div class="row">
		<div class="navigation col-sm-12">
			<div class="tm-table">
                <div class="menu-right table-cell table-right">
                    <?php
                    $current_user = wp_get_current_user();

					if ($current_user->ID > 0)
					{
						if ( is_active_sidebar( 'menu_right_2' ) ) {
							echo '<ul>';
							dynamic_sidebar( 'menu_right_2' );
							echo '</ul>';
						}
					}
					else
					{
						if ( is_active_sidebar( 'menu_right' ) ) {
							echo '<ul>';
							dynamic_sidebar( 'menu_right' );
							echo '</ul>';
						}
					}
					
                    ?>
                </div>

                <div class="menu-mobile-effect navbar-toggle" data-effect="mobile-effect">
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                </div>

			</div>
			<!--end .row-->
		</div>
	</div>
</div>
