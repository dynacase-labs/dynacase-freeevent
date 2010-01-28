<?php
/**
 * Generated Header (not documented yet)
 *
 * @author Anakeen 2000
 * @version $Id: calvlongtext.php,v 1.1 2005/03/18 09:21:38 marc Exp $
 * @license http://www.fsf.org/licensing/licenses/agpl-3.0.html GNU Affero General Public License
 * @package FREEDOM
 * @subpackage
 */
 /**
 */
function calvlongtext(&$action) {
  include_once("FREEEVENT/calvcard.php");
  $dbaccess = $action->GetParam("FREEDOM_DB");
  $evi = GetHttpVars("ev", -1);
  setHttpVar("ev", $evi);
  calvcard($action);
  return;
}

