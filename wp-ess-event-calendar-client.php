<?php
/*
Plugin Name: WP ESS Event Calendar Client
Plugin URI: https://github.com/kartevonmorgen/
Description: Easily transport events from your WordPress event calendar to ESS. 
Version: 0.1
Author: Sjoerd Takken
Author URI: https://www.sjoerdscomputerwelten.de/
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

$loaderClass = WP_PLUGIN_DIR . '/wp-libraries/inc/lib/plugin/class-wp-pluginloader.php';
if(!file_exists($loaderClass))
{
  echo "Das Plugin 'wp-libraries' muss erst installiert und aktiviert werden";
  exit;
}

include_once( ABSPATH . 'wp-admin/includes/plugin.php' );
include_once( $loaderClass);

// CHeck if Secure
if ( !defined( 'ESS_SECURE') ) {define( 'ESS_SECURE',((!empty($_SERVER['HTTPS']) && @$_SERVER['HTTPS'] !== 'off') || @$_SERVER['SERVER_PORT'] == 443 || stripos( @$_SERVER[ 'SERVER_PROTOCOL' ], 'https' ) === TRUE) ? TRUE : FALSE);}


class WPESSEventCalendarPluginLoader 
  extends WPPluginLoader
{
  public function init()
  {
    $this->add_dependency('wp-libraries/wp-libraries.php');
    $this->add_dependency('wp-events-interface/wp-events-interface.php');

    // -- View --
    $this->add_include('/inc/views/class-ess-feedbuilder.php');

    // -- Controllers --
    $this->add_include('/inc/controllers/class-ess-feedhandler.php');
    $this->add_include('/inc/controllers/class-ess-admincontrol.php' );
    $this->add_include('/inc/lib/ess/FeedWriter.php' );
  }

  public function start()
  {
    $this->add_starter( ESSFeedHandler::get_instance() );
  }

  public function activate()
  {
		flush_rewrite_rules();

		if ( !current_user_can( 'activate_plugins' )) 
    {
      return;
    }

    $plugin = isset( $_REQUEST[ 'plugin' ] ) ? $_REQUEST[ 'plugin' ] : $this->get_plugin_id();

    // Checks Permissions
    check_admin_referer( "activate-plugin_{$plugin}" );
  }

  public function deactivate()
  {
		if ( !current_user_can( 'activate_plugins' ) ) 
    {
      return;
    }

    $plugin = isset( $_REQUEST[ 'plugin' ] ) ? $_REQUEST[ 'plugin' ] : $this->get_plugin_id();

    // Checks Permissions
    check_admin_referer( "deactivate-plugin_{$plugin}" );
  }

	public function uninstall()
  {
    if ( ! current_user_can( 'activate_plugins' ) ) 
    {
      return;
    }

    // Checks Permissions
    check_admin_referer( 'bulk-plugins' );
  }
}

$loader = new WPESSEventCalendarPluginLoader();
$loader->register( __FILE__, 30);
