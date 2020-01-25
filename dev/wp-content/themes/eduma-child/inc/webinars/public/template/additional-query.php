<?php 
// Sidebar Filter Start


if(isset($_REQUEST["date-filter"])){
    $dateperiod = $_REQUEST ["date-filter"];
    $condition2 = array(
        'post_type'           => 'lp_course',
        'post_status' => 'publish',
        'ignore_sticky_posts' => true,
        'meta_query' => array(
            'relation' => 'AND',    
            array(
                'key' => '_course_type',
                'value' => 'webinar'
            )
        )
    );
    $nargs = $condition2;
    $posts_ids = array();
    switch($dateperiod){
        case 'future':
            $posts_ids = allcourseidsbytime($nargs, $dateperiod);
        break;
        case 'inprogress':
            $posts_ids = allcourseidsbytime($nargs, $dateperiod);
        break;
        case 'passed':
            $posts_ids = allcourseidsbytime($nargs, $dateperiod);
        break;
    }
    if(count($posts_ids) > 0) $condition['post__in'] = $posts_ids;
}

if(isset($_REQUEST["course-price-filter"])){
    $price = $_REQUEST["course-price-filter"];
    switch($price){
        case 'paid':
            $condition['meta_query'][] = array(
                    'key' => '_lp_price',
                     'value' => 0,
                     'type' => 'numaric',
                     'compare' => '>'
                );
        break;
        case 'free':
            $condition['meta_query'][] = array(
                'relation' => 'OR',
                array(
                    'key' => '_lp_price',
                    'value' => 0,
                    'type' => 'numaric',
                    'compare' => '<='
                ),
                array(
                    'key' => '_lp_price',
                    'compare' => 'NOT EXISTS'
                )
            );
        break;
        default:
        $condition = $condition;

    }

} // End if(isset($_REQUEST["course-price-filter"]))

// Webinar Category filter 
if(isset($_REQUEST['webinar-cate-filter'])){
    $cats = $_REQUEST['webinar-cate-filter'];
    if(count($cats) > 0){
        $condition['tax_query'] = array(
            array(
                'taxonomy'  => 'webinar_categories',
                'field'     => 'term_id',
                'terms'     => $cats,
            )
        );
    }
}
// Sidebar Filter End


/*
* Orderby
*/
if(isset($_REQUEST['orderby'])){
    switch ( $_POST['orderby'] ) {
        case 'alphabetical':
            $condition['orderby'] = 'title';
            $condition['order'] = 'ASC';

            break;
        case 'most-members':
            $condition['orderby'] = 'meta_value_num';
            $condition['meta_key'] = 'thim_real_student_enrolled';
            $condition['order'] = 'DESC';
            break;
        default:
            $condition['orderby'] = 'date';
            $condition['order'] = 'DESC';
    }
}



/*
* Search
*/
if(isset($_REQUEST["ws"])){
    $title = $_REQUEST["ws"];
    $condition['s'] = $_REQUEST["ws"];
} //  End Search


$testimonials = new WP_Query( $condition );
$_COOKIE['testimonials'] = $testimonials;


