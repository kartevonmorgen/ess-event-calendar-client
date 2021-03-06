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
class ESSFeedHandler extends WPPluginStarter
{
	const EM_ESS_ARGUMENT 	= 'em_ess';

  private static $instance = null;

	private function __construct() 
  {
  }

	public function start() 
  {
    // Init was to early, so we do it after all plugins are loaded
    add_filter( 'wp_loaded', array( $this, 'load' ));

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
	}

	public function set_deactivation()
	{
  }


	function load()
	{
    // Start UI Part
    $adminControl = ESSAdminControl::get_instance();
    $adminControl->start();
    
    // Handle requests
		if ( preg_match( '/^\/?em_ess\/?$/', $_SERVER['REQUEST_URI']) || !empty( $_REQUEST[ ESSFeedHandler::EM_ESS_ARGUMENT ] ) )
    {
      
      $cat = ( isset( $_REQUEST[ 'cat'] ) )? $_REQUEST[ 'cat'] : ''; 
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
