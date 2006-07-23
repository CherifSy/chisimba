<?php
// security check - must be included in all scripts
if (!$GLOBALS['kewl_entry_point_run']) {
    die("You cannot view this page directly");
}

/**
 * Class used to access the decision table rule bridge table.
 *
 * @access public
 * @copyright (c) 2000-2004, Kewl.NextGen ( http://kngforge.uwc.ac.za )
 * @package decisiontable
 * @subpackage access
 * @version 0.1
 * @since 21 February 2005
 * @author Paul Scott based on methods by Jonathan Abrahams
 * @filesource
 */

class dbDecisionTableRule extends dbTable {

    /**
     * Property used to store the unique id.
     *
     * @access public
     * @var $_id
     */
    public $_id = NULL;

    /**
     * The object initialisation method.
     *
     * @access public
     * @param void
     * @return void
     */
    public function init()
    {
        parent::init('tbl_decisiontable_decisiontable_rule');
    }

    /**
     * Method to upgrade tables
     *
     * @access public
     * @param void
     * @return void
     */
    public function upgradeTable()
    {
        /**
         * @todo FIX CALL TO LIKE - MySQL specific code!!!
         */
    	$sqlTableExists = sprintf( 'SHOW TABLES LIKE "%s"', $this->_tableName);
        $arrTableExists = $this->getArray( $sqlTableExists );
        if( empty ( $arrTableExists ) ) {
            $sqldata = array();
            @include_once './modules/decisiontable/sql/'.$this->_tableName.'.sql';
            $this->query( $sqldata[0] );
        }

    }

    /**
     * Method to add a rule to the decisionTable.
     *
     * @access public
     * @param object Rule object.
     * @param object DecisionTable object.
     * @return uniqueId|false Return the unique Id or false if already exists.
     */
    public function add( $rule, $decisionTable )
    {
        // no Duplicates
        $checkDups  = $rule->_id."' AND ";
        $checkDups .= " decisiontableId = '".$decisionTable->_id;
        if( $this->valueExists( 'ruleId', $checkDups ) ) {
            return FALSE;
        }

        // Package it
        $arrDTaction = array();
        $arrDTaction['decisionTableId'] = $decisionTable->_id;
        $arrDTaction['ruleId'] = $rule->_id;
        // Insert it
        return $this->insert( $arrDTaction );
    }

    /**
     * Method to check for duplicates
     *
     * @access public
     * @param string $rule
     * @param string $decisionTable
     * @return bool
     */
    public function checkDuplicate($rule, $decisionTable )
     {
        return is_null( $this->retrieveId( $rule,$decisionTable ) ) ? FALSE : TRUE;
     }

    /**
     * Method to retrieve all rules for the decisionTable.
     *
     * @access public
     * @param object The decisionTable object.
     * @return array of all actions for this decisionTable
     */
     public function retrieve( $objDecisionTable )
     {
         // Get the action for this decisionTable
         $objRule = $this->getObject( 'rule' );
         $join = $this->join( 'INNER JOIN', $objRule->_tableName , array( 'ruleId'=>'id' ) );
         $filter = " WHERE decisiontableId = '".$objDecisionTable->_id."'";
         // Get all Rules for this decisionTable

         return $this->getAll($join.$filter, array( $objRule->_tableName.'id',  $objRule->_tableName.'name' ));
     }

    /**
     * Method to retrieve a rule for the decisionTable.
     *
     * @access public
     * @param object The rule object.
     * @param object The decisionTable object.
     * @return id of rule for this decisionTable
     */
     public function retrieveId( &$objRule, &$objDecisionTable )
     {
         // Get the action for this decisionTable
         $join = $this->join( 'INNER JOIN', $objRule->_tableName, array( 'ruleId'=>'id' ) );
         $filter = " WHERE decisiontableId = '".$objDecisionTable->_id."'";
         $filter.= " AND ".$objRule->_tableName.".name = '".$objRule->_name."'";
         $arr = $this->getAll($join.$filter, array ( $objRule->_tableName.'id' ) );

         if( !empty($arr) ){
            return $arr[0]['id'];
         } else {
            return NULL;
         }
     }
}
?>