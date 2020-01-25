<?php

class WpCategoriesWidget extends WP_Widget {

function __construct() {
    parent::__construct(
        'wp_categories_widget', // Base ID
        __( 'Webinar Categories', 'mrwebsolution' ), // Name
        array( 'description' => esc_html__( 'Display categories list of all taxonomy post type', 'mrwebsolution' ), ) // Args
    );
    if(!is_admin())
    add_action('wcw_style',array($this,'wcw_style_func'));
    add_filter( "plugin_action_links_".plugin_basename( __FILE__ ), array(&$this,'wcw_add_settings_link') );
}
public function widget( $args, $instance ) {
    echo $args['before_widget'];
    if ( ! empty( $instance['wcw_title'] ) && !$instance['wcw_hide_title']) {
        echo $args['before_title'] . apply_filters( 'widget_title', $instance['wcw_title'] ) . $args['after_title'];
    }
    // add css 		
    do_action('wcw_style','wcw_style_func');
    /** return category list */
    if($instance['wcw_taxonomy_type']){
        $va_category_HTML ='<div class="ve-cat-widget-div">';
        $va_category_HTML .='<ul class="ve-cat-widget-listing">';
            $args_val = array( 'hide_empty=0' );				
            $excludeCat= $instance['wcw_selected_categories'] ? $instance['wcw_selected_categories'] : '';
            $wcw_action_on_cat= $instance['wcw_action_on_cat'] ? $instance['wcw_action_on_cat'] : '';
            if($excludeCat && $wcw_action_on_cat!='')
            $args_val[$wcw_action_on_cat] = $excludeCat;
            
            $terms = get_terms( $instance['wcw_taxonomy_type'], $args_val );
            if ( $terms ) {	
                foreach ( $terms as $term ) {
                    $term_link = get_term_link( $term );
                    if ( is_wp_error( $term_link ) ) {
                    continue;
                }
                    
                $carrentActiveClass='';	
                
                if($term->taxonomy=='category' && is_category())
                {
                      $thisCat = get_category(get_query_var('cat'),false);
                      if($thisCat->term_id == $term->term_id)
                        $carrentActiveClass='class="active-cat"';
                }
                 
                if(is_tax())
                {
                    $currentTermType = get_query_var( 'taxonomy' );
                    $termId= get_queried_object()->term_id;
                     if(is_tax($currentTermType) && $termId==$term->term_id)
                      $carrentActiveClass='class="active-cat"';
                }
                    
                    $va_category_HTML .='<li '.$carrentActiveClass.'><a href="' . esc_url( $term_link ) . '">' . $term->name . '</a>';
                    if (empty( $instance['wcw_hide_count'] )) {
                    $va_category_HTML .='<span class="post-count">'.$term->count.'</span>';
                    }
                    $va_category_HTML .='</li>';
                }
            }
        $va_category_HTML .='</ul></div>';
        echo $va_category_HTML;
        }	
    echo $args['after_widget'];
}
public function form( $instance ) {
    $wcw_title 					= ! empty( $instance['wcw_title'] ) ? $instance['wcw_title'] : esc_html__( 'WP Categories', 'virtualemployee' );
    $wcw_hide_title 			= ! empty( $instance['wcw_hide_title'] ) ? $instance['wcw_hide_title'] : esc_html__( '', 'virtualemployee' );
    $wcw_taxonomy_type 			= ! empty( $instance['wcw_taxonomy_type'] ) ? $instance['wcw_taxonomy_type'] : esc_html__( 'category', 'virtualemployee' );
    $wcw_selected_categories 	= (! empty( $instance['wcw_selected_categories'] ) && ! empty( $instance['wcw_action_on_cat'] ) ) ? $instance['wcw_selected_categories'] : esc_html__( '', 'virtualemployee' );
    $wcw_action_on_cat 			= ! empty( $instance['wcw_action_on_cat'] ) ? $instance['wcw_action_on_cat'] : esc_html__( '', 'virtualemployee' );
    $wcw_hide_count 			= ! empty( $instance['wcw_hide_count'] ) ? $instance['wcw_hide_count'] : esc_html__( '', 'virtualemployee' );
    ?>
    <p>
    <label for="<?php echo esc_attr( $this->get_field_id( 'wcw_title' ) ); ?>"><?php _e( esc_attr( 'Title:' ) ); ?></label> 
    <input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'wcw_title' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'wcw_title' ) ); ?>" type="text" value="<?php echo esc_attr( $wcw_title ); ?>">
    </p>
    <input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'wcw_hide_title' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'wcw_hide_title' ) ); ?>" type="checkbox" value="1" <?php checked( $wcw_hide_title, 1 ); ?>>
    <label for="<?php echo esc_attr( $this->get_field_id( 'wcw_hide_title' ) ); ?>"><?php _e( esc_attr( 'Hide Title' ) ); ?> </label> 
    </p>
    <p>
    <label for="<?php echo esc_attr( $this->get_field_id( 'wcw_taxonomy_type' ) ); ?>"><?php _e( esc_attr( 'Taxonomy Type:' ) ); ?></label> 
    <select class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'wcw_taxonomy_type' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'wcw_taxonomy_type' ) ); ?>">
                <?php 
                $args = array(
                  'public'   => true,
                  '_builtin' => false
                  
                ); 
                $output = 'names'; // or objects
                $operator = 'and'; // 'and' or 'or'
                $taxonomies = get_taxonomies( $args, $output, $operator ); 
                array_push($taxonomies,'category');
                if ( $taxonomies ) {
                foreach ( $taxonomies as $taxonomy ) {

                    echo '<option value="'.$taxonomy.'" '.selected($taxonomy,$wcw_taxonomy_type).'>'.$taxonomy.'</option>';
                }
                }

            ?>    
    </select>
    </p>
    <p>
    <select class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'wcw_action_on_cat' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'wcw_action_on_cat' ) ); ?>">
       <option value="" <?php selected($wcw_action_on_cat,'' )?> >Show All Category:</option>       
       <option value="include" <?php selected($wcw_action_on_cat,'include' )?> >Include Selected Category:</option>       
       <option value="exclude" <?php selected($wcw_action_on_cat,'exclude' )?> >Exclude Selected Category:</option>
    </select> 
    <select class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'wcw_selected_categories' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'wcw_selected_categories' ) ); ?>[]" multiple>
                <?php 			
                if($wcw_taxonomy_type){
                $args = array( 'hide_empty=0' );
                $terms = get_terms( $wcw_taxonomy_type, $args );
                echo '<option value="" '.selected(true, in_array('',$wcw_selected_categories), false).'>None</option>';
                if ( $terms ) {
                foreach ( $terms as $term ) {
                    echo '<option value="'.$term->term_id.'" '.selected(true, in_array($term->term_id,$wcw_selected_categories), false).'>'.$term->name.'</option>';
                }
                    
                }
            }

            ?>    
    </select>
    </p>
    <p>
    <input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'wcw_hide_count' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'wcw_hide_count' ) ); ?>" type="checkbox" value="1" <?php checked( $wcw_hide_count, 1 ); ?>>
    <label for="<?php echo esc_attr( $this->get_field_id( 'wcw_hide_count' ) ); ?>"><?php _e( esc_attr( 'Hide Count' ) ); ?> </label> 
    </p>
    <hr>
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
    $instance['wcw_hide_title'] 			= ( ! empty( $new_instance['wcw_hide_title'] ) ) ? strip_tags( $new_instance['wcw_hide_title'] ) : '';
    $instance['wcw_taxonomy_type'] 			= ( ! empty( $new_instance['wcw_taxonomy_type'] ) ) ? strip_tags( $new_instance['wcw_taxonomy_type'] ) : '';
    $instance['wcw_selected_categories'] 	= ( ! empty( $new_instance['wcw_selected_categories'] ) ) ? $new_instance['wcw_selected_categories'] : '';
    $instance['wcw_action_on_cat'] 			= ( ! empty( $new_instance['wcw_action_on_cat'] ) ) ? $new_instance['wcw_action_on_cat'] : '';
    $instance['wcw_hide_count'] 			= ( ! empty( $new_instance['wcw_hide_count'] ) ) ? strip_tags( $new_instance['wcw_hide_count'] ) : '';
    return $instance;
}
/** plugin CSS **/
function wcw_style_func_css()
{
    $style='<style type="text/css">/*  */.widget_wp_categories_widget{background:#fff; position:relative;}.wp_categories_widget h2{color:#4a5f6d;font-size:24px;font-weight:400;margin:0 0 25px;line-height:24px;text-transform:uppercase}.ve-cat-widget-div ul.ve-cat-widget-listing li{font-size: 16px;
margin: 0px;
border-bottom: 1px dashed #f0f0f0;
position: relative;
list-style-type: none;
line-height: 35px;}.ve-cat-widget-div ul.ve-cat-widget-listing li:last-child{border:none;}.ve-cat-widget-div ul.ve-cat-widget-listing li a{display:inline-block;color:#007acc;transition:all .5s ease;-webkit-transition:all .5s ease;-ms-transition:all .5s ease;-moz-transition:all .5s ease}.ve-cat-widget-div ul.ve-cat-widget-listing li a:hover,.ve-cat-widget-div ul.ve-cat-widget-listing li.active-cat a,.ve-cat-widget-div ul.ve-cat-widget-listing li.active-cat span.post-count{color:#ee546c}.ve-cat-widget-div ul.ve-cat-widget-listing li span.post-count{height: 30px;
min-width: 35px;
text-align: center;
background: #fff;
color: #605f5f;
border-radius: 5px;
box-shadow: inset 2px 1px 3px rgba(0, 122, 204,.1);
top: 0px;
float: right;
margin-top: 2px;}/* End category widget CSS*/</style>';
echo $style;
}
function wcw_style_func()
{
    add_action('wp_footer',array($this,'wcw_style_func_css'));
}
/** updtate plugins links using hooks**/
// Add settings link to plugin list page in admin
function wcw_add_settings_link( $links ) {
    $settings_link = '<a href="widgets.php">' . __( 'Settings Widget', 'mrwebsolution' ) . '</a> | <a href="mailto:raghunath.0087@gmail.com">' . __( 'Contact to Author', 'mrwebsolution' ) . '</a>';
    array_unshift( $links, $settings_link );
    return $links;
}
}// class WpCategoriesWidget
function register_wp_categories_widget() {
register_widget( 'WpCategoriesWidget' );
}
add_action( 'widgets_init', 'register_wp_categories_widget');