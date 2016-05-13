<?php
/*
 * @author Anakeen
 * @package FREEEVENT
*/

function calvresume(Action & $action)
{
    $evi = getHttpVars("ev", -1);
    $ev = new_Doc($action->dbaccess, $evi);
    $action->lay->set("id", $ev->id);
    $action->lay->set("title", $ev->getRawValue("evt_title"));
    $action->lay->set("shour", substr($ev->getRawValue("evt_begdate") , 11, 5));
    $action->lay->set("ehour", substr($ev->getRawValue("evt_enddate") , 11, 5));
    $action->lay->set("iconsrc", $ev->getIcon($ev->getRawValue("evt_frominitiatoricon")));
}
