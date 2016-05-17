<?php
/*
 * @author Anakeen
 * @package FREEEVENT
*/

function calvlongtext(Action & $action)
{
    include_once ("FREEEVENT/calvcard.php");
    $evi = getHttpVars("ev", -1);
    SetHttpVar("ev", $evi);
    calvcard($action);
    return;
}
