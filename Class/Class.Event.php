<?php
/*
 * Event Class
 *
 * @author Anakeen
 * @package FREEEVENT
*/

namespace Dcp\Freeevent;

class Event extends \Dcp\Family\Document
{
    var $calVResume = "FREEEVENT:CALVRESUME";
    var $calVCard = "FREEEVENT:CALVCARD";
    var $calVLongText = "FREEEVENT:CALVLONGTEXT";
    var $calVShortText = "FREEEVENT:CALVSHORTTEXT";
    var $calVData = "FREEEVENT:CALVDATA";
    
    var $calPopupMenu = array();
    
    var $XmlResume = "FREEEVENT:XMLRESUME";
    var $XmlHtmlContent = "FREEEVENT:XMLHTMLCONTENT";
    public static $sqlindex = array(
        "evtini" => array(
            "unique" => true,
            "on" => "evt_idinitiator,evt_transft"
        )
    );
    /**
     * return all atomic event found in period between $d1 and $d2 for this event
     * by default it is itself.
     * this method must be change by derived class when events can be repeat.
     * @param string $d1 begin date in iso8601 format YYYY-MM-DD HH:MM
     * @param string $d2 end date in iso8601 format
     * @return array array of event. These events returned are not objects but only a array of variables.
     */
    function explodeEvt($d1, $d2)
    {
        $vals=$this->getValues();
        foreach ($this->fields as $k=>$v) {
            if (is_numeric($k)) {
                $vals[$v]=$this->$v;
            }
        }
        $vals["attrids"]=$this->attrids;
        $vals["values"]=$this->values;
        return array($vals);
    }

    
    function getEventIcon()
    {
        $eicon = $this->getRawValue("EVT_ICON");
        if ($eicon == "") return $this->getRawValue("EVT_FROMINITIATORICON");
        return "";
    }

    /**
     * @templateController Xml event
     */
    function XmlResume()
    {
        $this->lay->set("id", $this->id);
        $this->lay->set("pid", $this->getRawValue("evt_idinitiator"));
        $this->lay->set("revdate", $this->revdate);
        $this->lay->set("revstatus", ($this->doctype == 'Z' ? 2 : 1));
        $this->lay->set("title", $this->getRawValue("evt_title"));
        $this->lay->set("displaymode", $this->displayMode());
        $this->lay->set("time", StringDateToUnixTs($this->getRawValue("evt_begdate")));
        $dur = StringDateToUnixTs($this->getRawValue("evt_enddate")) - StringDateToUnixTs($this->getRawValue("evt_begdate"));
        $dur = ($dur < 0 ? -$dur : $dur);
        $this->lay->set("duration", $dur);
        
        $style = $this->displayStyles();
        $this->lay->setBlockData("style", $style);
        
        $this->lay->set("content", $this->viewdoc($this->XmlHtmlContent));
        
        $this->lay->set("menuref", $this->fromid);
        $mref = $this->setMenuRef();
        if (count($mref) > 0) $this->lay->setBlockData("miUse", implode(",", $mref));
        else $this->lay->setBlockData("miUse", "");
        $this->lay->set("setRefMenu", true);
        
        return;
    }

    /**
     * @templateController Xml event
     */
    function XmlHtmlContent()
    {
        $this->lay->set("evtitle", $this->getRawValue("evt_title"));
        $this->lay->set("evfamicon", $this->getIcon($this->getRawValue("evt_icon")));
        $this->lay->set("evstart", substr($this->getRawValue("evt_begdate") , 0, 16));
        $this->lay->set("evend", substr($this->getRawValue("evt_enddate") , 0, 16));
        return;
    }
    
    function displayMode()
    {
        return 1;
    }
    
    function displayStyles()
    {
        return array(
            array(
                "sid" => "color",
                "sval" => "green"
            ) ,
            array(
                "sid" => "background-color",
                "sval" => "white"
            )
        );
    }
    
    function setEventMenu()
    {
        return array();
    }
    
    function setMenuRef()
    {
        return array();
    }
    
    function getBgColor()
    {
        return "#e0e0e0";
    }
    function getFgColor()
    {
        return "";
    }
    function getBottomColor()
    {
        return $this->getBgColor();
    }
    function getTopColor()
    {
        return $this->getBgColor();
    }
    function getLeftColor()
    {
        return $this->getBgColor();
    }
    function getRightColor()
    {
        return $this->getBgColor();
    }
    function getIconSize()
    {
        return getParam('SIZE_IMG-XX-SMALL', '10px');
    }
    function calvdata()
    {
        $this->lay->set("ID", $this->id);
        $this->lay->set("FROMID", $this->fromid);
        $this->lay->set("EVT_IDINITIATOR", $this->getRawValue("evt_idinitiator", $this->id));
        $this->lay->set("EVT_FROMINITIATORID", $this->getRawValue("evt_frominitiatorid", $this->fromid));
        
        $this->lay->set("displayable", ($this->isConfidential() ? "false" : "true"));
        $this->lay->set("title", addSlashes($this->getTitle()));
        $this->lay->set("start", StringDateToUnixTs($this->getRawValue("evt_begdate") , true));
        $this->lay->set("lstart", $this->getRawValue("evt_begdate"));
        $this->lay->set("lend", $this->getRawValue("evt_enddate"));
        $this->lay->set("end", StringDateToUnixTs($this->getRawValue("evt_enddate") , true));
        $this->lay->set("alarm", "''");
        
        if (method_exists($this, "getMenuLoadUrl")) $this->lay->set("menuurl", $this->getMenuLoadUrl());
        else $this->lay->set("menuurl", "");
        
        $this->lay->set("icons", "'" . $this->getIcon($this->getRawValue("evt_icon")) . "'");
        $this->lay->set("bgColor", $this->getBgColor());
        $this->lay->set("fgColor", $this->getFgColor());
        $this->lay->set("topColor", $this->getTopColor());
        $this->lay->set("bottomColor", $this->getBottomColor());
        $this->lay->set("rightColor", $this->getRightColor());
        $this->lay->set("leftColor", $this->getLeftColor());
        $this->lay->set("iconSize", $this->getIconSize());
        $this->lay->set("fastedit", "false");
        $this->lay->set("editable", ($this->canEdit() == "" ? "true" : "false"));
    }
}
