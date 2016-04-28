<?php
include_once(dirname(__FILE__)."/../../cache/siteManager.php");

$path_to_modx_config = '../../../'.MGR_DIR.'/includes/config.inc.php';

include_once($path_to_modx_config);

if (isset($_REQUEST[$site_sessionname]))
	session_id($_REQUEST[$site_sessionname]); //without this always generate new session

startCMSSession();

include_once "../../../".MGR_DIR."/includes/document.parser.class.inc.php";
$modx = new DocumentParser;
$modx->loadExtension("ManagerAPI");
$modx->getSettings();

// get module data
$rs = $modx->db->select('properties', $modx->getFullTableName('site_modules'), 'id = '.$_REQUEST['id'], '', '1');
if ($modx->db->getRecordCount($rs) > 0){
	$properties = $modx->db->getValue($rs);
}

// load module configuration
$parameters = !empty($properties) ? $modx->parseProperties($properties) : array();

include_once('classes/management.class.inc.php');

if (class_exists('GalleryManagement'))
{
	$manager = new GalleryManagement($parameters);
	if (!$_SESSION['mgrValidated'])
	{
		echo json_encode(array('result'=>'error','msg'=>$manager->lang['access_denied']));
		die;
	}	
	$res = $manager->executeAction();
	if ($res===TRUE)
		echo json_encode(array('result'=>'ok'));
	elseif ($res===FALSE)
		echo json_encode(array('result'=>'error','msg'=>$manager->lang['operation_error']));
	else echo $res;	
}	
else
	$modx->logEvent(1, 3, 'Error loading Portfolio Galleries management module');


?>
