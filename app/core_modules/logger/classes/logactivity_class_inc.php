<?
// security check - must be included in all scripts
if (!$GLOBALS['kewl_entry_point_run'])
{
    die("You cannot view this page directly");
}

/**
* An activity logging class. It logs the userId, module, and 
* additional parameters.
* 
* @author Derek Keats
* @copyright GPL
* @package logger
* @version 0.1
*/
class logactivity extends dbTable
{

    /**
    * Property to hold the user object
    * 
    * @var string $objUser The user object
    */
    var$objUser;
    
    /**
    * Property to hold the modules object
    * 
    * @var string $objMod The user object
    */
    var$objMod;
    
    /**
    * Property for the event code
    * 
    * @var string $eventcode The eventcode for the logged event
    */
    var $eventcode;
    
    /**
    * Property for the name of the parameter to save
    * 
    * @var string $eventcode The parameter name
    */
    var $eventParamName;
    
    /**
    * Property for the value of the parameter to save
    * 
    * @var string $eventcode The parameter value
    */
    var $eventParamValue;
    
    /**
    * Property for the value of flag saying whether the 
    * paramValue is multilingualized
    * 
    * @var string $eventcode The parameter value
    */
    var $isLanguageCode;
    
    /**
    * Property to set whether to log more than once per
    * session. Use for often updated modules for example
    * chat refresh.
    * 
    * @var BOOLEAN $logOncePerSession @values TRUE|FALSE
    * 
    */
    var $logOncePerSession;
    
    /**
    * Constructor method 
    */
    function init()
    {
         //  Set the parent table
         parent::init('tbl_logger');
         //  Set default to log each time the page is loaded
         $this->logOncePerSession=FALSE;
         //  Get an instance of the user object
         $this->objUser = & $this->getObject('user', 'security');
         //  Get an instance of hte modulesadmin class to check if 
         //  the logging module is registered
         $this->objMod = & $this->getObject('modulesadmin', 'modulelist');
    } #function init
    
    /**
    * Method to add the current event. It checks if the property logOncePerSession
    * is set and if so it checks if it has already been loged in this session.
    */
    function log()
    {
        // Only try to do it if Logger is registered
        if ($this->objMod->checkIfRegistered('logger', 'logger')) {
            //Set the default value for eventcode
            $this->eventcode="pagelog";
            $this->eventParamName="parameters";
            $this->eventParamValue=$this->_cleanParams();
            $this->isLanguageCode=FALSE;
            $module = $this->getParam('module', NULL);
            //Check if its set to log once per session
            if ($this->logOncePerSession==TRUE) {
                if (!$this->_isLoggedThisSession($module)) {
                    $this->_setSessionFlag($module);
                    if ($this->objUser->isLoggedIn()) {
                        $this->_logData();
                        return TRUE;
                    } else {
                        return FALSE;
                    }
                    
                }
            } else {
                if ($this->objUser->isLoggedIn()) {
                    $this->_logData();
                    return TRUE;
                } else {
                    return FALSE;
                }
            }
        } else {
            return FALSE;
        } # if
      
    } # function log()
    
    /**
    * Method to log an event other than a page event
    */
    function logEvent($eventcode)
    {
        $this->_logData();
    }
    
    /**
    * Method to set a param
    * 
    * @param string $param The parameter to set
    * @param string $value The value to set it to
    * 
    */
    function setParam($param, $value)
    {
        $this->$param = $value;
        return TRUE;
    }
   
    /********************* PRIVATE METHODS BELOW THIS LINE *****************/
    
    /**
    * Method to return the querystring with the module=modulecode
    * part removed. I use preg_replace for case insensitive replacement.
    */
    function _cleanParams()
    {
        if (defined($_SERVER['QUERY_STRING'])) {
            $str = $_SERVER['QUERY_STRING'];
            $str = preg_replace("/module=/i", NULL, $str);
            $module=$this->getParam('module', NULL);
            $str = preg_replace("/$module/i", NULL, $str);
        } else {
           $str=NULL;
        }
        return $str;
    } #function _cleanParams
    
    /**
    * Method to get the datetiem for now
    */
    function _getDateTime()
    {
        return date("Y/m/d H:i:s");
    } #function _getDateTime
    
    /**
    * Method to return the context for Logging
    */
    function _getContext()
    {
        $this->objDBContext = & $this->getObject('dbcontext', 'context');
        return $this->objDBContext->getContextCode();
    } #function _getContext
    
    /**
    * Method to check if there is a session log variable set for 
    * the module that is passed to it
    * 
    * @param string $module The module to look up, usually the 
    * current module
    * 
    */
    function _isLoggedThisSession($module)
    {
        return $this->getSession($module.'_log', FALSE);
    } #function _isLoggedThisSession
    
    /**
    * Method to set a session log variable set for 
    * the module that is passed to it
    * 
    * @param string $module The module to set, usually the 
    * current module
    * 
    */
    function _setSessionFlag($module)
    {
        $this->setSession($module.'_log', TRUE);
    } #function _setSessionFlag
    
    /**
    * Method to log the data to the database
    *  id - The framework generated primary key 
    *  userId - The userId of the currently logged in user
    *  module - The module code from the querystring
    *  eventCode - A code to represent the event
    *  eventParamName - The type of event parameters sent
    *  eventParamValue - Any parameters the event needs to send
    *  context - The context of the event
    *  dateCreated - The datetime stamp for the event
    */
    function _logData()
    {
        $this->insert(array(
          'userId' => $this->objUser->userId(),
          'module' => $this->getParam('module', NULL),
          'eventCode' => $this->eventcode,
          'eventParamName' => $this->eventParamName,
          'eventParamValue' => $this->eventParamValue,
          'isLanguageCode' => $this->isLanguageCode,
          'context' => $this->_getContext(),
          'dateCreated' => $this->_getDateTime()));
     } #function _logData
        

}  #end of class

?>
