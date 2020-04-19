<?php
/**
 * @author Deepen.
 * @created_on 7/15/19
 */
?>
<script>
function openCity(evt, tabName) {
  var i, tabcontent, tablinks;
  tabcontent = document.getElementsByClassName("tabcontent");
  for (i = 0; i < tabcontent.length; i++) {
    tabcontent[i].style.display = "none";
  }
  tablinks = document.getElementsByClassName("tablinks");
  for (i = 0; i < tablinks.length; i++) {
    tablinks[i].className = tablinks[i].className.replace(" active", "");
  }
  document.getElementById(tabName).style.display = "block";
  evt.currentTarget.className += " active";
}
</script>
<style>
body {font-family: Arial;}

/* Style the tab */
.tab {
  overflow: hidden;
  border: 1px solid #ccc;
  background-color: #f1f1f1;
}

/* Style the buttons inside the tab */
.tab button {
  background-color: inherit;
  float: left;
  border: none;
  outline: none;
  cursor: pointer;
  padding: 14px 16px;
  transition: 0.3s;
  font-size: 17px;
}

/* Change background color of buttons on hover */
.tab button:hover {
  background-color: #ddd;
}

/* Create an active/current tablink class */
.tab button.active {
  background-color: #ccc;
}

/* Style the tab content */
.tabcontent {
  display: none;
  padding: 6px 12px;
  border: 1px solid #ccc;
  border-top: none;
}
</style>

<?php 
$active = (isset($_REQUEST['page']) && $_REQUEST['page'] == 'zooom-webinars') ? 'nav-tab-active':'';
$active = (isset($_REQUEST['page']) && $_REQUEST['page'] == 'zooom-webinars') ? 'nav-tab-active':'';

$args = array( 'post_type' => 'lp_course', 'posts_per_page' => -1, 'post_status' => array('publish', 'pending', 'draft', 'auto-draft', 'future', 'private', 'inherit', 'trash'), 'meta_key' => '_course_type', 'meta_value' => 'webinar' );
$listArgs = $args;
?>


<div id="First" class="tabcontent-custom" style="display: block;">
<div class="wrap">
    <h1 class="wp-heading-inline custom_larft"><?php _e( 'Webinar Courses', 'webinars' ); ?></h1>
    <a href="<?php echo admin_url( 'post-new.php?post_type=lp_course' ); ?>" class="page-title-action"><?php _e('Add New', 'webinar'); ?></a>
    <div id="webinar-wp-list-table">
        <div id="webinar-post-body">

  <ul class="subsubsub">
    <li class="all"><a href="<?php echo admin_url( 'admin.php?page=zoom-webinars&all_posts=1' ); ?>"><?php _e('All', 'webinar'); ?> 
      <?php 
        $query = new WP_Query( $args );
        $all = $query->post_count;
      ?>
      <span class="count">(<?php echo $all; ?>)</span></a> |</li>
    <li class="mine"><a href="<?php echo admin_url( 'admin.php?page=zoom-webinars&author=103' ); ?>"><?php _e('Mine', 'webinar'); ?> 
      <?php 
      $args['author'] = get_current_user_id();
      $query = new WP_Query( $args );
      $all = $query->post_count;
      ?>
      <span class="count">(<?php echo $all; ?>)</span></a> |</li>
    <li class="publish"><a href="<?php echo admin_url( 'admin.php?page=zoom-webinars&post_status=publish' ); ?>"><?php _e('Published', 'webinar'); ?> 
    <?php 
      unset($args['author']);
      $args['post_status'] = 'publish';
      $query = new WP_Query( $args );
      $all = $query->post_count;
    ?>
    <span class="count">(<?php echo $all; ?>)</span></a> |</li>
    <li class="draft"><a href="<?php echo admin_url( 'admin.php?page=zoom-webinars&post_status=draft' ); ?>"><?php _e('Drafts', 'webinar'); ?> 
    <?php 
      $args['post_status'] = 'draft';
      $query = new WP_Query( $args );
      $all = $query->post_count;
    ?>
    <span class="count">(<?php echo $all; ?>)</span></a> |</li>
    <li class="pending"><a href="<?php echo admin_url( 'admin.php?page=zoom-webinars&post_status=pending' ); ?>"><?php _e('Pending', 'webinar'); ?> 
    <?php 
      $args['post_status'] = 'pending';
      $query = new WP_Query( $args );
      $all = $query->post_count;
    ?>
    <span class="count">(<?php echo $all; ?>)</span></a> |</li>
    <li class="trash"><a href="<?php echo admin_url( 'admin.php?page=zoom-webinars&post_status=trash' ); ?>"><?php _e('Trash', 'webinar'); ?> 
    <?php 
      $args['post_status'] = 'trash';
      $query = new WP_Query( $args );
      $all = $query->post_count;
    ?>
    <span class="count">(<?php echo $all; ?>)</span></a></li>
  </ul>



        <form id="webinar-list-form" method="get">
                <input type="hidden" name="page" value="<?php echo $_REQUEST['page'] ?>"/>
        <?php
        // echo 'test Omar';
        $this->webinar_list_table->search_box( __( 'Searc Webinars', 'webinars' ), 'nds-user-find' );
        
        ?>
        

        <?php
        
        
        $this->webinar_list_table->display();
        
        if ( get_current_screen()->id === 'learnpress_page_zoom-webinars' ) {
          $option = sprintf( '<option value="">%s</option>', __( 'Search by user', 'learnpress' ) );
    
          if ( $user = get_user_by( 'id', LP_Request::get_int( 'author' ) ) ) {
            $option = sprintf( '<option value="%d" selected="selected">%s</option>', $user->ID, $user->user_login );
          }
        }

        $listArgs['posts_per_page'] = 1;
        $listArgs['order_by'] = 'post_date';
        $listArgs['order'] = 'ASC';
        $list_first = get_posts($listArgs);
        $firstDate = $list_first[0]->post_date;

        $listArgs['order'] = 'DESC';
        $list_last = get_posts($listArgs);
        
        $last_date = $list_last[0]->post_date;
        $interval = DateInterval::createFromDateString('1 month');
        $period = new DatePeriod(
          new DateTime($firstDate),
          $interval,
          new DateTime($last_date)
      );

        echo 'list first: <pre>';
        print_r($period);
        echo '</pre>';
        foreach ($period as $key => $value) {
         $date = $value->format('Y-m-d');  
         echo 'date t: ' . $date . '<br/>';    
        }
				?>
        </form>

      <!-- Additionsl Markup using js -->
      <script>
                jQuery(function ($) {
                    // var $input = $('#post-search-input');
                    var $input = $('#nds-user-find-search-input');
                    if (!$input.length) {
                        return;
                    }

                    var $form = $($input[0].form),
                        $select = $('<select name="author" id="author"></select>').append($('<?php echo $option;?>')).insertAfter($input).select2({
                            ajax: {
                                url: window.location.href + '&lp-ajax=search-authors',
                                dataType: 'json',
                                s: ''
                            },
                            placeholder: '<?php echo __( 'Search by user', 'learnpress' );?>',
                            minimumInputLength: 3,
                            allowClear: true
                        }).on('select2:select', function () {
                            $('input[name="author"]').val($select.val())
                        });

                    $form.on('submit', function (e) {
                        var url = window.location.href.removeQueryVar('author').addQueryVar('author', $select.val());
                    });


                    /* Webinar Date Filter */
                    


                })</script>


      <!-- End Additional Markup -->




        </div>
    </div>
</div>
</div>
<div id="Second" class="tabcontent">
  Loading...
</div>
<div id="Third" class="tabcontent">
  Loading...
</div>