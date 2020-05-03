<?php
/*
Plugin Name: ESS Event Calendar Client
Plugin URI: https://github.com/kartevonmorgen/
Description: Easily transport events from your WordPress event calendar to ESS. 
Version: 0.1
Author: Sjoerd Takken
Author URI: https://www.sjoerdscomputerwelten.de/
Text Domain: ess-event-calendar-client
License: GPL2

This program is free software: you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation, either version 2 of the License, or
any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

defined( 'ABSPATH' ) or die( 'No script kiddies please!' );

// CHeck if Secure
if ( !defined( 'ESS_SECURE') ) {define( 'ESS_SECURE',((!empty($_SERVER['HTTPS']) && @$_SERVER['HTTPS'] !== 'off') || @$_SERVER['SERVER_PORT'] == 443 || stripos( @$_SERVER[ 'SERVER_PROTOCOL' ], 'https' ) === TRUE) ? TRUE : FALSE);}

include_once( ABSPATH . 'wp-admin/includes/plugin.php' );
if ( !is_plugin_active( 'events-interface/events-interface.php' ) ) 
{
	// Plugin is not active
  // TODO: See https://waclawjacek.com/check-wordpress-plugin-dependencies/
  echo 'The plugin Events Interface must be activated';
  die();
}


if ( ! function_exists( 'eecc_load_textdomain' ) ) {
    /**
     * Load in any language files that we have setup
     */
    function eecc_load_textdomain() {
        load_plugin_textdomain( 'ess-event-calendar-client', false, plugin_basename( dirname( __FILE__ ) ) . '/languages' );
    }
    add_action( 'plugins_loaded', 'eecc_load_textdomain' );
}

// -- View --
require_once( dirname( __FILE__ )."/inc/views/class-ess-feedbuilder.php");

// -- Controllers --
require_once( dirname( __FILE__ )."/inc/controllers/class-ess-feedhandler.php");
require_once( dirname( __FILE__ )."/inc/controllers/class-ess-admincontrol.php" );
if ( class_exists( 'FeedWriter' ) == FALSE ) 
{
  require_once( dirname( __FILE__) . "/inc/lib/ess/FeedWriter.php" );
}

$feedhandler = ESSFeedHandler::get_instance();
$feedhandler->initialize();

register_activation_hook( __FILE__, array( $feedhandler, 'set_activation'));
register_deactivation_hook( __FILE__, array( $feedhandler, 'set_deactivation'));
register_uninstall_hook( __FILE__, array( $feedhandler, 'set_uninstall' ));
