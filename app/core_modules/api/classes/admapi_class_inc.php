<?php
/**
 * ADM interface class
 * 
 * XML-RPC (Remote Procedure call) class
 * 
 * PHP version 5
 * 
 * This program is free software; you can redistribute it and/or modify 
 * it under the terms of the GNU General Public License as published by 
 * the Free Software Foundation; either version 2 of the License, or 
 * (at your option) any later version.
 * This program is distributed in the hope that it will be useful, 
 * but WITHOUT ANY WARRANTY; without even the implied warranty of 
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the 
 * GNU General Public License for more details.
 * You should have received a copy of the GNU General Public License 
 * along with this program; if not, write to the 
 * Free Software Foundation, Inc., 
 * 59 Temple Place - Suite 330, Boston, MA  02111-1307, USA.
 * 
 * @category  Chisimba
 * @package   api
 * @author    Paul Scott <pscott@uwc.ac.za>
 * @copyright 2007 Paul Scott
 * @license   http://www.gnu.org/licenses/gpl-2.0.txt The GNU General Public License
 * @version   CVS: $Id$
 * @link      http://avoir.uwc.ac.za
 * @see       core
 */
// security check - must be included in all scripts
if (!
/**
 * Description for $GLOBALS
 * @global entry point $GLOBALS['kewl_entry_point_run']
 * @name   $kewl_entry_point_run
 */
$GLOBALS['kewl_entry_point_run']) {
    die("You cannot view this page directly");
}
// end security check


/**
 * ADM XML-RPC Class
 * 
 * Class to provide Chisimba ADM XML-RPC functionality
 * 
 * @category  Chisimba
 * @package   api
 * @author    Paul Scott <pscott@uwc.ac.za>
 * @copyright 2007 Paul Scott
 * @license   http://www.gnu.org/licenses/gpl-2.0.txt The GNU General Public License
 * @version   Release: @package_version@
 * @link      http://avoir.uwc.ac.za
 * @see       core
 */
class admapi extends object
{

	/**
     * init method
     * 
     * Standard Chisimba init method
     * 
     * @return void  
     * @access public
     */
	public function init()
	{
		try {
			$this->objConfig = $this->getObject('altconfig', 'config');
			$this->objLanguage = $this->getObject('language', 'language');
        	$this->objUser = $this->getObject('user', 'security');
        	$this->objAdmOps = $this->getObject('admops', 'adm');
        	$this->objIni = $this->getObject('ini', 'config');
        	$this->objXMLThing = $this->getObject('xmlthing', 'utilities');
		}
		catch (customException $e)
		{
			customException::cleanUp();
			exit;
		}
	}
	
	
	public function checkVersionApi()
	{
		$version = $this->objEngine->version;
		$val = new XML_RPC_Value($version, 'string');
		return new XML_RPC_Response($val);
		// Ooops, couldn't open the file so return an error message.
		return new XML_RPC_Response(0, $XML_RPC_erruser+1, $this->objLanguage->languageText("mod_packages_fileerr", "packages"));
	}
	
	public function getFullLogApi()
	{
		$val = new XML_RPC_Value('not yet implemented', 'string');
		return new XML_RPC_Response($val);
	}
	
	public function sendLogFileApi()
	{
		$val = new XML_RPC_Value('not yet implemented', 'string');
		return new XML_RPC_Response($val);
	}
	
	public function getLastMirrorTimeApi()
	{
		$val = new XML_RPC_Value('not yet implemented', 'string');
		return new XML_RPC_Response($val);
	}
	
	public function registerServerApi($server)
	{
		$serv = $server->getParam(0);
		$serv = $serv->scalarval();
		$surl = $server->getParam(1);
		$surl = $surl->scalarval();
		$semail = $server->getParam(2);
		$semail = $semail->scalarval();
		
		$serverarr = array('name' => $serv, 'url' => $surl, 'email' => $semail);
		//$this->objIni->createAdmConfig($serverarr);
		//check for the directory structure
		if(!file_exists($this->objConfig->getcontentBasePath().'adm/'))
		{
			mkdir($this->objConfig->getcontentBasePath().'adm/', 0777);
		}
		// write the server list file
		$cfile = $this->objConfig->getcontentBasePath().'adm/adm.xml';
		if(!file_exists($cfile))
		{
			$this->objXMLThing->createDoc();
			$this->objXMLThing->startElement('adm');
			$this->objXMLThing->startElement('server');
			$this->objXMLThing->writeElement('servername', $serverarr['name']);
			$this->objXMLThing->writeElement('serverapiurl', $serverarr['url']);
			$this->objXMLThing->writeElement('serveremail', $serverarr['email']);
			$this->objXMLThing->writeElement('regtime', date('r'));
			$this->objXMLThing->endElement(); // server
			$this->objXMLThing->endElement(); // adm
			
			$this->objXMLThing->endDTD();
			$string = $this->objXMLThing->dumpXML();
			file_put_contents($cfile, $string);
		}
		else {
			// the file does exist - i.e. not the first record
			$xmlstr = file_get_contents($cfile);
			$xml = new SimpleXMLElement($xmlstr);
			foreach($xml->server as $server)
			{
				log_debug($server);
				$admopts[] = $server['servername'];
				$admopts[] .= $server['serverapiurl'];
				$admopts[] .= $server['serveremail'];
				$admopts[] .= $server['regtime'];
			}
			log_debug($admopts);
		}
		
		
		$val = new XML_RPC_Value('TRUE', 'string');
		return new XML_RPC_Response($val);
	}
}
?>