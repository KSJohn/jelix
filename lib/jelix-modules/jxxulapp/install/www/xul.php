<?php
/**
* @package  demo
* @subpackage www
* @version  $Id$
* @author
* @contributor
* @copyright
*/

require_once ('../../lib/jelix/init.php');

require_once ('.././application.init.php');

require_once (JELIX_LIB_CORE_PATH.'request/jClassicRequest.class.php');

$config_file = 'configxul.classic.ini.php';

$jelix = new jCoordinator($config_file);
$req=new jClassicRequest();
$req->defaultResponseType='xul';
$jelix->process($req);

?>