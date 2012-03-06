<?php
/*
 * @author Anakeen
 * @license http://www.fsf.org/licensing/licenses/agpl-3.0.html GNU Affero General Public License
 * @package FREEEVENT
*/

function calvshorttext(&$action)
{
    include_once ("FREEEVENT/calvresume.php");
    $dbaccess = $action->GetParam("FREEDOM_DB");
    $evi = GetHttpVars("ev", -1);
    setHttpVar("ev", $evi);
    calvresume($action);
    return;
}
