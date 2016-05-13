<?php
/*
 * @author Anakeen
 * @package FREEEVENT
*/

function calvshorttext(Action & $action)
{
    include_once ("FREEEVENT/calvresume.php");
    $evi = getHttpVars("ev", -1);
    SetHttpVar("ev", $evi);
    calvresume($action);
    return;
}
