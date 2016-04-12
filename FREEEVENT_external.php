<?php
/*
 * Functions use for input help in edition for calendars
 *
 * @author Anakeen
 * @package FREEEVENT
*/

function getFamilyByTag($tag)
{
    
    $famid = - 1;
    
    $s = new SearchDoc("", $famid);
    $s->addFilter("atags ~ '\\\\y%s\\\\y'", $tag);
    $s->setObjectReturn(true);
    $s->search();
    $dl = $s->getDocumentList();
    $tr = array();
    /**
     * @var Doc $v
     */
    foreach ($dl as $v) {
        
        $tr[] = array(
            $v->getHtmlTitle() ,
            $v->id,
            $v->getTitle()
        );
    }
    return $tr;
}

function getEventProducers()
{
    $trall[] = array(
        ___("all families events", "freeevent") ,
        " ",
        ___("all families events", "freeevent")
    );
    
    return array_merge($trall, getFamilyByTag("P"));
}
function getFamRessource()
{
    return getFamilyByTag("R");
}
function getRessource($dbaccess, $famres, $name = "")
{
    $s = new SearchDoc("", $famres);
    $s->addFilter("atags ~ '\\\\yR\\\\y'");
    $s->setObjectReturn(true);
    if ($name != "") {
        
        $s->addFilter("title ~* '%s'", $name);
    }
    $s->setSlice(100);
    
    $s->search();
    $dl = $s->getDocumentList();
    $tr = array();
    /**
     * @var Doc $v
     */
    foreach ($dl as $v) {
        
        $tr[] = array(
            $v->getHtmlTitle() ,
            $v->id,
            $v->getTitle()
        );
    }
    return $tr;
}
?>
