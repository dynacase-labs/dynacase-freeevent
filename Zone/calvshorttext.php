<?php
/**
 * Generated Header (not documented yet)
 *
 * @author Anakeen 2000
 * @version $Id: calvshorttext.php,v 1.1 2005/03/18 09:21:38 marc Exp $
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @package FREEDOM
 * @subpackage
 */
 /**
 */
function calvshorttext(&$action) {
  include_once("FREEEVENT/calvresume.php");
  $dbaccess = $action->GetParam("FREEDOM_DB");
  $evi = GetHttpVars("ev", -1);
  setHttpVar("ev", $evi);
  calvresume($action);
  return;
}