<?php
/*
 * @author Anakeen
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

