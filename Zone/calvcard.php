<?php
/*
 * Generated Header (not documented yet)
 *
 * @author Anakeen
 * @package FREEEVENT
*/

function calvcard(Action & $action)
{
    include_once ("FREEEVENT/calvresume.php");
    $dbaccess = $action->dbaccess;
    $evi = getHttpVars("ev", -1);
    SetHttpVar("ev", $evi);
    calvresume($action);
    
    $ev = new_Doc($dbaccess, $evi);
    
    $action->lay->set("id", $ev->id);
    $action->lay->set("description", $ev->getRawValue("evt_descr"));
    $tress = array();
    $to = array();
    $ito = 0;
    if ($ev->getRawValue("evt_idres") != "") {
        $tress = $ev->getMultipleRawValues("evt_idres");
        foreach ($tress as $k => $v) {
            $rd = new_Doc($dbaccess, $v);
            $to[$ito++]["rtitle"] = $rd->title;
        }
    }
    $action->lay->set("sdatehour", $ev->getRawValue("evt_begdate"));
    $action->lay->set("edatehour", $ev->getRawValue("evt_enddate"));
    $action->lay->set("RESSOURCES", count($to) > 0);
    $action->lay->setBlockData("RESSLIST", $to);
    return;
}
