<?php
/*
 * @author Anakeen
 * @license http://www.fsf.org/licensing/licenses/agpl-3.0.html GNU Affero General Public License
 * @package FREEEVENT
*/

function calvlongtext(&$action)
{
    include_once ("FREEEVENT/calvcard.php");
    $dbaccess = $action->GetParam("FREEDOM_DB");
    $evi = GetHttpVars("ev", -1);
    setHttpVar("ev", $evi);
    calvcard($action);
    return;
}

