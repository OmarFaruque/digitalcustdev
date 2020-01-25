<?php 
class WplatestWebiners extends WP_Widget {

function __construct() {
    parent::__construct(
        'wp_latestWebinars_widget', // Base ID
        __( 'Latest Webinar\'s', 'mrwebsolution' ), // Name
        array( 'description' => esc_html__( 'Display Latest Webinar\'s', 'mrwebsolution' ), ) // Args
    );
    if(!is_admin())
    add_filter( "plugin_action_links_".plugin_basename( __FILE__ ), array(&$this,'wcw_add_settings_link') );
}
    public function widget( $args, $instance ) {

        // echo 'Args: <br/><pre>';
        // print_r($args);
        // echo '</pre>';
        echo $args['before_widget'];
        if ( ! empty( $instance['wcw_title'] ) ) {
            echo $args['before_title'] . apply_filters( 'widget_title', $instance['wcw_title'] ) . $args['after_title'];
        }
        $limit = $instance['limit'];
        
        $args = array(
            'post_type' => 'lp_course',
            'post_status' => array('publish'),
            'numberposts' => $limit,
            'meta_key' => '_course_type', 
            'meta_value' => 'webinar'
        );
        $recent_posts = wp_get_recent_posts( $args );
        ?>
        <div class="thim-course-list-sidebar">
            <?php foreach($recent_posts as $single):
            $single = (object) $single;
                $price = number_format((int)get_post_meta( $single->ID, '_lp_price', true ), 2);
                $thumbimg = wp_get_attachment_url( get_post_thumbnail_id($single->ID) );
                
                $alt = get_post_meta ( get_post_thumbnail_id($single->ID), '_wp_attachment_image_alt', true );
            ?>
			<div class="lpr_course <?php echo ($thumbimg !='') ? 'has-post-thumbnail':''; ?>">
				<?php if($thumbimg != ''): ?>
                <div class="course-thumbnail">
                    <img src="<?php echo $thumbimg; ?>" alt="<?php $alt; ?>">
                </div>				
                <?php endif; ?>
                <div class="thim-course-content">
					<h3 class="course-title">
                        <a href="<?php echo get_the_permalink($single->ID); ?>"> <?php echo $single->post_title; ?></a>
                    </h3>						
                    <div class="course-price" itemprop="offers" itemscope="" itemtype="http://schema.org/Offer">
                        <div class="value " itemprop="price">
                            <?php echo $price .' '. learn_press_get_currency_symbol(); ?>        
                        </div>
                        <meta itemprop="priceCurrency" content="RUB">
                    </div>			
                </div>
			</div>
            <?php endforeach; ?>
					
		</div>
        <div class="theiaStickySidebar"></div>
        <?php
        $args['after_widget'] = '</aside>';
        echo $args['after_widget'];
    }
    public function form( $instance ) {
        $wcw_title 	= ! empty( $instance['wcw_title'] ) ? $instance['wcw_title'] : esc_html__( 'Latest Webinars', 'virtualemployee' );
        $limit 	= ! empty( $instance['limit'] ) ? $instance['limit'] : 0;
        ?>
        <p>
            <label for="<?php echo esc_attr( $this->get_field_id( 'wcw_title' ) ); ?>"><?php _e( esc_attr( 'Title:' ) ); ?></label> 
            <input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'wcw_title' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'wcw_title' ) ); ?>" type="text" value="<?php echo esc_attr( $wcw_title ); ?>">
        </p>
        <p>
            <label for="<?php echo esc_attr( $this->get_field_id( 'limit' ) ); ?>"><?php _e( esc_attr( 'Limit number Webinar\'s:' ) ); ?></label> 
            <input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'limit' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'limit' ) ); ?>" type="number" value="<?php echo esc_attr( $limit ); ?>">
        </p>
        <?php 
    }

    /**
     * Sanitize widget form values as they are saved.
     *
     * @see 
     *
     * @param array $new_instance Values just sent to be saved.
     * @param array $old_instance Previously saved values from database.
     *
     * @return array Updated safe values to be saved.
     */
    public function update( $new_instance, $old_instance ) {
        $instance = array();
        $instance['wcw_title'] 					= ( ! empty( $new_instance['wcw_title'] ) ) ? strip_tags( $new_instance['wcw_title'] ) : '';
        $instance['limit'] 			            = ( ! empty( $new_instance['limit'] ) ) ? strip_tags( $new_instance['limit'] ) : 0;
        return $instance;
    }
        
    
    /** updtate plugins links using hooks**/
    // Add settings link to plugin list page in admin
    function wcw_add_settings_link( $links ) {
        $settings_link = '<a href="widgets.php">' . __( 'Settings Widget', 'mrwebsolution' ) . '</a> | <a href="mailto:raghunath.0087@gmail.com">' . __( 'Contact to Author', 'mrwebsolution' ) . '</a>';
        array_unshift( $links, $settings_link );
        return $links;
    }
}
// End class WplatestWebiners
function register_wp_latestwebinars_widget() {
register_widget( 'WplatestWebiners' );
}
add_action( 'widgets_init', 'register_wp_latestwebinars_widget');