### Zoom Video Conferencing on WordPress ###

Author: Deepen Bajracharya/Adeel Raza  
Author URI: https://elearningevolve.com   
Plugin URI: https://elearningevolve.com/products/  wordpress-zoom-integration      
Donate link: https://elearningevolve.com/products/support-zoom-plugin-development    
Slug: wordpress-zoom-addon  
Tags: zoom video conference, video conference, zoom, zoom video conferencing, web conferencing, online meetings   
Requires at least: 4.9  
Tested up to: 5.3.2  
Stable tag: 3.1.5  
Requires PHP: 5.4  
License: GPLv2 or later  
License URI: http://www.gnu.org/licenses/gpl-2.0.html  

Gives you the power to conduct Zoom Meetings directly on WordPress, check reports and create users from your WordPress dashboard.

## Description ##

This is an extended version of Video Conferencing with Zoom API (by Deepen Bajracharya), this plugin enables you to conduct Zoom meetings directly on your WordPress site using a simple shortcode.

Complete details about the plugin can be found [here](https://elearningevolve.com/products/wordpress-zoom-integration/)

**Few Features:**

1. Manage Meetings
2. List/Add Users
3. Clean and Friendly UI
4. Shortcodes
5. Daily and Account Reports
6. Directly conduct zoom meeting on your site using a simple shortcode
7. Restrict access for non-logged in WordPress users

**Limitations**

* Webinar module not integrated

**Use shortcode**

* [zoom_api_link meeting_id="zoom_meeting_id" class="your_class" id="your_id" title="Text with Zoom Window"] -> You can show the link of your meeting in your site anywhere using the shortcode. Replace your zoom meeting ID in place of "zoom_meeting_id".

* Or add from icon in classic editor. Not integrated for gutenberg !

**Find a Short Documentation or Guide on how to setup: [Here](https://elearningevolve.com/products/wordpress-zoom-integration/)**

**Using Action Hooks**

* 1. zvc_after_create_meeting( $meeting_id, $host_id, $meeting_time ) *
Hook this method in your functions.php file in order to run a custom script after a meeting has been created.

* 2. zvc_after_update_meeting( $meeting_id, $host_id, $meeting_time ) *
Hook this method in your functions.php file in order to run a custom script after a meeting has been updated.

* 3. zvc_after_create_user( $created_id, $created_email ) *
Hook this method in your functions.php file in order to run a custom script after a user is created.

**Please consider giving a [5 star thumbs up](https://elearningevolve.com/products/wordpress-zoom-integration/) if you found this useful.**

Any additional features, suggestions related to translations you can contact me via [email](https://elearningevolve.com/contact).

### Installation ###
Search for the plugin -> add new dialog and click install, or download and extract the plugin, and copy the the Zoom plugin folder into your wp-content/plugins directory and activate.

## Frequently Asked Questions ##
= How to show Zoom Meetings on Front =

* By using shortcode like [zoom_api_link meeting_link="meeting_link" class="your_class" id="your_id" title="Text with Zoom Window"] you can show the link of your meeting in front.


## Changelog ##
### 3.1.5 ###
* Added: Meeting password option on step 1 of the meeting page
* Added: Helpful error messages incase the API Keys are entered incorrectly or error from Zoom API 
* Added: Helpful messages with meeting fields on create/edit meetings
* Fixed: Prevent Zoom password screen on step 2 after entering name
* Fixed: Zoom added a new setting to prevent login for participants before joining meeting. More details [here](https://elearningevolve.com/blog/zoom-added-the-setting-to-disable-login-for-meeting-participants)

### 3.1.4 ###
* Added: Zoom services status check link in plugin admin pages
* Fixed: Typo in alternative host field message on meeting add/edit

### 3.1.3 ###
* Fixed: Prevent blank screen Fatal error on activating base plugin along with this plugin
* Fixed: README.md updated for working with Git repository, a Git repo is created for the plugin for version tracking and bug reporting via Github.
Please report the bugs and issues from now on the Github repo [here](https://github.com/elearning-evolve/zoom-wordpress-integration) 
* Fixed: End/Resume Meeting button on backend meeting list now ends/starts Zoom meeting
* Fixed: Add browser compatibility for fullscreen option in Zoom meeting window
* Fixed: Report Functionality fixed for Viewing All Meetings Report

* Added: Display meeting status on meeting list page
* Added: Helpful notes pointing to Prerequisites of User management & Reports section
* Added: Option to Subscribe for Important updates from Zoom Meetings -> Subscribe
* Added: Translation files to translate text in Italian (translation credits: Antonio Graziano).
* Added: Added Option to manage Width and Height of Zoom iFrame Window through Zoom Meetings ->Settings.

### 3.1.2 ###
* Alert: Zoom web client is down and shows a 403 Forbidden error, the issue is already reported on [Zoom forum](https://devforum.zoom.us/t/zoom-web-client-is-down/10829)
* Fixed: (The most requested one) Zoom Join Via App link not working
* Added: (The most requested one) The ability for Host/Co-host to join the meeting from the plugin Zoom window
* Added: Support for Alternative Host to start the meeting like a Host (Not tested)
* Added: Re-Enabled the user management section in the plugin to give the ability to create Alternative hosts from the plugin.
Please note that in order to use the plugin's user management area you should be able to access this link from your Zoom account (https://zoom.us/account/user)
It is accessible only for Free account(with Credit Card added), Pro, Business, Education, or Enterprise account
* Trick: Recieved a lot of requests from users about the ability for Co-Host to start the meeting without the HOST.
  This can be done by checking -> Join Before Host check on the plugin Zoom meeting edit page.

* Added: Option to add your theme specific CSS classes to buttons from plugin Settings
* Added: Ability to change text on the alternative meeting button from plugin Settings

### 3.1.1 ###
* Added: Action(video_conferencing_zoom_before_render_widnow) and Filter(video_conferencing_zoom_before_window_content) to extend the plugin
* Fixed: Prevent meeting host from being redirected to Zoom on the meeting page
* Fixed: Resolve issues with Audio not working for meeting particpants on Chrome browser <br />
[Blog post to address Commonly occuring Audio and Video Issues in Zoom Meeting](https://elearningevolve.com/blog/audio-video-issues-zoom-meeting)

### 3.1.0 ###
* Fixed: Added work around for the Chrome incompatibility issue preventing Zoom window to work on WordPress page
* Added: Added option to disable plugin link share notice on admin
[Full details about this release here](https://elearningevolve.com/blog/zoom-integration-with-wordpress-v3-1-0/)

### 3.0.9 ###
* Added: Add Chrome incompatibility notice above meeting window and alternative link to join meeting
* Added: Option on settings page to enable/disable notice text above Zoom window <br />
[Full details about this release here](https://elearningevolve.com/blog/wordpress-zoom-integration-shortcode-not-working-after-recent-chrome-version-update-80-0-3987-132/)

### 3.0.8 ###
* Added: Option on settings page to enable/disable help text above Zoom window
* Added: Show Zoom window shortcode on adminn Zoom meeting edit page

* Fixed: Prevent plugin assets loading on all frontend pages
* Fixed: Replaced fatal error displayed on plugin activation due to previously installed basic version with helpful text

### 3.0.7 ###
* Fixed: Timeout issue on plugin page and fix slow loading backend

### 3.0.6 ###
* Added: Translation files to translate text in Hebrew (translation credits: Asaf Epshtain)
* Fixed: Make text translatable on settings page

### 3.0.5 ###
* Added: Support for RTL language.
* Added: Zoom API keys generation link on the settings page.
* Fixed: The meeting countdown timer is now compatible with RTL languages.

### 3.0.4 ###
* Added: Multi lingual support in the plugin.
* Added: Translation files to translate text in Dutch (translation credits: Ingo Ahnfeldt)

### 3.0.3 ###
* Fixed: Styling issues on mobile & tablet devices.
* Added: Download Zoom App links incase Zoom meeting window doesn't work.

### 3.0.2 ###
* Fixed: Styling issue for Zoom window on mobile.
* Added: Support for joining meeting directly from WordPress page on mobile/tablet
* Added: Show Zoom App download buttons on mobile/tablet

### 3.0.1 ###
* Added: Support for restricting access to logged-in WordPress users
* Fixed: Redirect users to join the meeting page directly without clicking "join from the browser" in the Zoom window.

View the full description of the changelog, [here] (https://elearningevolve.com/wordpress-zoom-integration-changelog)