<?php

/**
 * @author Mike Carruth <mikecarruth@gmail.com>
 * @modifications Martin Klefas
 * @version 3.0.0
 * @package Flogr
 * @link https://github.com/Cleverbum/flogr
 */

/**
 * Defines a required constant - quits if value not set.
 *
 * @param string name   Name of constant   
 * @param string value  Value for constant
 */
function REQUIRED_SETTING($name, $value) {
    if (!isset($value) || $value == '') {
        die("<p>You need to set <b>'{$name}'</b> in <code>admin\config.php</code> before you can being using flogr.</p>");
    }
    define($name, $value, true);
}

/**
 * Defines an optional constant - wrapper for define().
 *
 * @param string name   Name of constant   
 * @param string value  Value for constant
 */
function OPTIONAL_SETTING($name, $value) {
    define($name, $value, true);
}

ini_set('include_path',
        dirname(__FILE__) . '/../include/PEAR' . PATH_SEPARATOR .
        dirname(__FILE__) . '/../admin' . PATH_SEPARATOR .
        dirname(__FILE__) . '/../pages' . PATH_SEPARATOR .
        ini_get('include_path'));

/**
 * Hide PHP warnings...nobody's perfect :)
 */
error_reporting(E_WARNING);

/**
 * Includes
 */
require_once('Log.php');
require_once('config.php');
require_once dirname(__FILE__) .'/../include/vendor/autoload.php';


/**
 * Create the flogr instance and let's get going
 */
if (!isset($flogr))
    $flogr = new Flogr();
$flogr->run();

/**
 * The Flogr:: class is responsible for mapping requests to the appropriate 
 * handler page based on the type request parameter (ex 'index.php?type=photo')
 * and setting up logging.
 *
 * @author Mike Carruth <mikecarruth@gmail.com>
 * @package Flogr
 */
class Flogr {
    /*
     * Maps request type to template page.
     *
     * @var array
     */

    var $_pageMap = array(
        '' => 'photo.php',
        'photo' => 'photo.php',
        'recent' => 'recent.php',
        'sets' => 'sets.php',
        'tags' => 'tags.php',
        'map' => 'map.php',
        'map_data' => 'map_data.php',
        'favorites' => 'favorites.php',
        'about' => 'about.php',
        'rss' => 'rss.php'
    );

    /*
     * Maps flogr log levels to PEAR::LOG
     */
    var $_logLevels = array(
        FLOGR_LOG_NONE => PEAR_LOG_NONE,
        FLOGR_LOG_ERR => PEAR_LOG_ERR,
        FLOGR_LOG_WARNING => PEAR_LOG_WARNING,
        FLOGR_LOG_DEBUG => PEAR_LOG_DEBUG
    );
    var $_logMask = PEAR_LOG_ALL;
    var $_logger = null;
    var $_logHandlers = array(
        'firebug' => null,
    );

    function __construct() {

        $this->_logger = &Log::singleton('composite', '', '', null, $this->_logLevels[FLOGR_LOG_LEVEL]);
        $keys = array_keys($this->_logHandlers);
        foreach ($keys as $key) {
            $handler = &Log::singleton($key, '', '', null, $this->_logLevels[FLOGR_LOG_LEVEL]);
            $this->_logHandlers[$key] = $handler;
            $this->_logger->addChild($handler);
        }
    }

    function logInfo($string) {
        $this->_logger->info($string);
    }

    function logWarning($string) {
        $this->_logger->warning($string);
    }

    function logErr($string) {
        $this->_logger->err($string);
    }

    function logDebug($string) {
        $this->_logger->debug($string);
    }

    function run() {
        if (defined('FLICKR_USER_ID') || defined('FLICKR_GROUP_ID')) {
            if ($_GET['type'] != 'rss' && $_GET['type'] != 'map_data') {
                include('header.php');
                include(SITE_THEME_PATH . 'header.php');
            }

            include($this->_pageMap[$_GET['type']]);
            include(SITE_THEME_PATH . $this->_pageMap[$_GET['type']]);

            if ($_GET['type'] != 'rss' && $_GET['type'] != 'map_data') {
                include(SITE_THEME_PATH . 'footer.php');
                include('footer.php');
            }
        }
    }

}

?>
