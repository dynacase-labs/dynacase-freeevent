<?php
/*
 * @author Anakeen
 * @package FREEEVENT
*/

function calvresume(&$action)
{
    $dbaccess = $action->GetParam("FREEDOM_DB");
    $evi = GetHttpVars("ev", -1);
    $ev = new_Doc($dbaccess, $evi);
    $action->lay->set("id", $ev->id);
    $action->lay->set("title", $ev->getValue("evt_title"));
    $action->lay->set("shour", substr($ev->getValue("evt_begdate") , 11, 5));
    $action->lay->set("ehour", substr($ev->getValue("evt_enddate") , 11, 5));
    $action->lay->set("iconsrc", $ev->getIcon($ev->getValue("evt_frominitiatoricon")));
}
?>
  
