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
<div class="tab">
<button class="tablinks active" onclick="openCity(event, 'First')">Webinars</button>
<button class="tablinks" onclick="openCity(event, 'Second')"><a href="/dev/wp-admin/edit-tags.php?taxonomy=webinar_categories&post_type=lp_course">Webinar Categories</a></button>
<button class="tablinks" onclick="openCity(event, 'Third')"><a href="/dev/wp-admin/edit-tags.php?taxonomy=webinar_tag&post_type=lp_course">Webinar Tags</a></button>
</div>
<div id="First" class="tabcontent" style="display: block;">
<div class="wrap">
    <h2><?php _e( 'Webinar Courses', $this->plugin_text_domain ); ?></h2>
    <div id="webinar-wp-list-table">
        <div id="webinar-post-body">
            <form id="webinar-list-form" method="get">
                <input type="hidden" name="page" value="<?php echo $_REQUEST['page'] ?>"/>
				<?php
				$this->webinar_list_table->search_box( __( 'Search Webinars', $this->plugin_text_domain ), 'nds-user-find' );
				$this->webinar_list_table->display();
				?>
            </form>
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