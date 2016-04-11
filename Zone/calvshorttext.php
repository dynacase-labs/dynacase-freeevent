<?php
/*
 * @author Anakeen
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
