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
?>


<div id="First" class="tabcontent-custom" style="display: block;">
<div class="wrap">
    <h1 class="wp-heading-inline custom_larft"><?php _e( 'Webinar Courses', 'webinars' ); ?></h1>
    <a href="<?php echo admin_url( 'post-new.php?post_type=lp_course' ); ?>" class="page-title-action"><?php _e('Add New', 'webinar'); ?></a>
    <div id="webinar-wp-list-table">
        <div id="webinar-post-body">

        <ul class="subsubsub">
	<li class="all"><a href="<?php echo admin_url( 'admin.php?page=zoom-webinars&post_type=lp_course&all_posts=1' ); ?>"><?php _e('All', 'webinar'); ?> <span class="count">(94)</span></a> |</li>
	<li class="mine"><a href="<?php echo admin_url( 'admin.php?page=zoom-webinars&post_type=lp_course&author=103' ); ?>"><?php _e('Mine', 'webinar'); ?> <span class="count">(36)</span></a> |</li>
	<li class="publish"><a href="edit.php?post_status=publish&amp;post_type=lp_course">Published <span class="count">(16)</span></a> |</li>
	<li class="draft"><a href="edit.php?post_status=draft&amp;post_type=lp_course">Drafts <span class="count">(36)</span></a> |</li>
	<li class="pending"><a href="edit.php?post_status=pending&amp;post_type=lp_course">Pending <span class="count">(42)</span></a> |</li>
	<li class="trash"><a href="edit.php?post_status=trash&amp;post_type=lp_course">Trash <span class="count">(32)</span></a></li>
</ul>



        <form id="webinar-list-form" method="get">
                <input type="hidden" name="page" value="<?php echo $_REQUEST['page'] ?>"/>
        <?php
        // echo 'test Omar';
        $this->webinar_list_table->search_box( __( 'Search 5 Webinars', 'webinars' ), 'nds-user-find' );
        $this->webinar_list_table->display();
        
        if ( get_current_screen()->id === 'learnpress_page_zoom-webinars' ) {
          $option = sprintf( '<option value="">%s</option>', __( 'Search by user', 'learnpress' ) );
    
          if ( $user = get_user_by( 'id', LP_Request::get_int( 'author' ) ) ) {
            $option = sprintf( '<option value="%d" selected="selected">%s</option>', $user->ID, $user->user_login );
          }
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
                    })
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