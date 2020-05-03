<?php
/**
  * Controller ESSFeedHandler Output
  * The FeedHandler generates an ESS Feed and make it 
  * available, so other Websites can import this feed.
  *
  * @author  	Brice Pissard, Sjoerd Takken
  * @copyright 	No Copyright.
  * @license   	GNU/GPLv2, see https://www.gnu.org/licenses/gpl-2.0.html
  * @link		https://github.com/essfeed
  */
class ESSFeedHandler
{
	const EM_ESS_ARGUMENT 	= 'em_ess';

  private static $instance = null;

	private function __construct() 
  {
  }

	public function initialize() 
  {
    // Init was to early, so we do it after all plugins are loaded
    add_filter( 'wp_loaded', array( $this, 'start' ));

    add_filter( 'rewrite_rules_array', 
                array( $this, 'get_rewrite_rules_array'));
    add_filter( 'query_vars', array( $this, 'get_query_vars'));
  }

  /** 
   * The object is created from within the class itself
   * only if the class has no instance.
   */
  public static function get_instance()
  {
    if (self::$instance == null)
    {
      self::$instance = new ESSFeedHandler();
    }
    return self::$instance;
  }

	public function set_activation()
	{
		flush_rewrite_rules();

		if ( !current_user_can( 'activate_plugins' )) 
    {
      return;
    }

    $plugin = isset( $_REQUEST[ 'plugin' ] ) ? $_REQUEST[ 'plugin' ] : 'ess-event-calendar-client';

    // Checks Permissions
    check_admin_referer( "activate-plugin_{$plugin}" );
	}

	public function set_deactivation()
	{
		if ( !current_user_can( 'activate_plugins' ) ) 
    {
      return;
    }

    $plugin = isset( $_REQUEST[ 'plugin' ] ) ? $_REQUEST[ 'plugin' ] : 'ess-event-calendar-client';

    // Checks Permissions
    check_admin_referer( "deactivate-plugin_{$plugin}" );
  }

	public function set_uninstall()
  {
    if ( ! current_user_can( 'activate_plugins' ) ) 
    {
      return;
    }

    // Checks Permissions
    check_admin_referer( 'bulk-plugins' );

		// Important: Check if the file is the one that was registered during the uninstall hook.
    if ( __FILE__ != WP_UNINSTALL_PLUGIN ) 
    {
      return;
    }
  }

	function start()
	{
    // Start UI Part
    $adminControl = ESSAdminControl::get_instance();
    $adminControl->start();
    
    // Handle requests
		if ( preg_match( '/^\/?em_ess\/?$/', $_SERVER['REQUEST_URI']) || !empty( $_REQUEST[ ESSFeedHandler::EM_ESS_ARGUMENT ] ) )
    {
      
      $cat = ( isset( $_REQUEST[ 'event_cat'] ) )? $_REQUEST[ 'event_cat'] : ''; 
      $feedBuilder = new ESSFeedBuilder();
      $feedBuilder->output($cat);
      //( ( isset( $_REQUEST[ 'event_id'] ) )? $_REQUEST[ 'event_id'] : '' ),
      //( ( isset( $_REQUEST[ 'page'] ) )? $_REQUEST[ 'page'] : '' ),
      //( ( isset( $_REQUEST[ 'download']   )? ( ( intval( $_REQUEST[ 'download' ] ) >= 1 )? TRUE : FALSE ) : FALSE ) ),
      //( ( isset( $_REQUEST[ 'push'])? ( ( intval( $_REQUEST[ 'push'] ) >= 1 )? TRUE : FALSE ) : FALSE ))
      //);
			die;
		}
	}

	public function get_rewrite_rules_array( $rules )
	{
		return $rules + array( "/ess/?$"=>'index.php?'. ESSFeedHandler::EM_ESS_ARGUMENT . '=1' );
	}

	public function get_query_vars( $vars )
	{
		array_push( $vars, ESSFeedHandler::EM_ESS_ARGUMENT );
		return $vars;
	}
}
