<?php
/**
 * Event Class
 *
 * @author Anakeen 2005
 * @version $Id: Method.Event.php,v 1.17 2007/04/27 11:41:36 eric Exp $
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @package FREEEVENT
 */
 /**
 */

var $calVResume     = "FREEEVENT:CALVRESUME";
var $calVCard       = "FREEEVENT:CALVCARD";
var $calVLongText   = "FREEEVENT:CALVLONGTEXT";
var $calVShortText  = "FREEEVENT:CALVSHORTTEXT";
var $calVData       = "FREEEVENT:CALVDATA";

var $calPopupMenu = array();

var $XmlResume =  "FREEEVENT:XMLRESUME";
var $XmlHtmlContent =  "FREEEVENT:XMLHTMLCONTENT";
public static $sqlindex=array("evtini"=>array("unique"=>true,
					      "on"=>"evt_idinitiator,evt_transft"));
/**
 * return all atomic event found in period between $d1 and $d2 for this event
 * by default it is itself.
 * this method must be change by derived class when events can be repeat.
 * @param date $d1 begin date in iso8601 format YYYY-MM-DD HH:MM
 * @param date $d2 end date in iso8601 format
 * @return array array of event. These events returned are not objects but only a array of variables.
 */
function explodeEvt($d1,$d2) {
  return array(get_object_vars($this));
}
function explodeEvtTest($d1,$d2) {
  $t1[]=get_object_vars($this);
  $this->setValue("evt_begdate","10/12/2003");
  $this->evt_enddate="20/12/2003";
  $t1[]=get_object_vars($this);
  return $t1;;
}

function getEventIcon() {  
  $eicon=$this->getValue("EVT_ICON");
  if ($eicon=="")  return $this->getValue("EVT_FROMINITIATORICON");
  return "";
}

function XmlResume() {
  global $action;
  $this->lay->set("id", $this->id);
  $this->lay->set("pid", $this->getValue("evt_idinitiator"));
  $this->lay->set("revdate", $this->revdate);
  $this->lay->set("revstatus", ($this->doctype=='Z' ? 2 : 1));
  $this->lay->set("title", $this->getValue("evt_title"));
  $this->lay->set("displaymode", $this->displayMode());
  $this->lay->set("time", FrenchDateToUnixTs($this->getValue("evt_begdate")));
  $dur = FrenchDateToUnixTs($this->getValue("evt_enddate")) - FrenchDateToUnixTs($this->getValue("evt_begdate"));
  $dur = ($dur<0 ? -$dur : $dur);
  $this->lay->set("duration", $dur);
  
  $style = $this->displayStyles();
  $this->lay->setBlockData("style", $style);
  
  $this->lay->set("content", $this->viewdoc($this->XmlHtmlContent));
  
  
  $this->lay->set("menuref", $this->fromid);
  $mref = $this->setMenuRef();
  if (count($mref)>0) 
    $this->lay->setBlockData("miUse", implode(",",$mref));
  else 
    $this->lay->setBlockData("miUse", "");
  $this->lay->set("setRefMenu", true);

  return;
}

function XmlHtmlContent() {
  $this->lay->set("evtitle", $this->getValue("evt_title"));
  $this->lay->set("evfamicon", $this->getIcon($this->getValue("evt_icon")));
  $this->lay->set("evstart", substr($this->getValue("evt_begdate"),0,16));
  $this->lay->set("evend", substr($this->getValue("evt_enddate"),0,16));
  return;
}


function displayMode() {
  return 1;
}

function displayStyles() {
  return array( array("sid" => "color", "sval" => "green"), array("sid" => "background-color", "sval" => "white"));
}

function setEventMenu() {
  return array();
}

function setMenuRef() {
  return array();
}

function getBgColor() {
  return "#e0e0e0";
}
function getFgColor() {
  return "";
}
function getBottomColor() {
  return $this->getBgColor();
}
function getTopColor() {
  return $this->getBgColor();
}
function getLeftColor() {
  return $this->getBgColor();
}
function getRightColor() {
  return $this->getBgColor();
}
function getIconSize() {
  return getParam('SIZE_IMG-XX-SMALL','10px');
}
function calvdata() {
  $this->lay->set("ID", $this->id);
  $this->lay->set("FROMID", $this->fromid);
  $this->lay->set("EVT_IDINITIATOR", $this->getValue("evt_idinitiator",$this->id));
  $this->lay->set("EVT_FROMINITIATORID", $this->getValue("evt_frominitiatorid",$this->fromid));

  $this->lay->set("displayable", ($this->isConfidential()?"false":"true"));
  $this->lay->set("title", addSlashes($this->getTitle()));
  $this->lay->set("start", FrenchDateToUnixTs($this->getValue("evt_begdate"),true));
  $this->lay->set("lstart", $this->getValue("evt_begdate"));
  $this->lay->set("lend", $this->getValue("evt_enddate"));
  $this->lay->set("end", FrenchDateToUnixTs($this->getValue("evt_enddate"),true));
  $this->lay->set("alarm", "''");
  
  if (method_exists($this, "getMenuLoadUrl")) $this->lay->set("menuurl", $this->getMenuLoadUrl());
  else  $this->lay->set("menuurl", "");

  $this->lay->set("icons", "'".$this->getIcon($this->getValue("evt_icon"))."'");
  $this->lay->set("bgColor", $this->getBgColor());
  $this->lay->set("fgColor", $this->getFgColor());
  $this->lay->set("topColor",$this->getTopColor() );
  $this->lay->set("bottomColor", $this->getBottomColor());
  $this->lay->set("rightColor", $this->getRightColor());
  $this->lay->set("leftColor", $this->getLeftColor());
  $this->lay->set("iconSize", $this->getIconSize());
  $this->lay->set("fastedit", "false");
  $this->lay->set("editable", ($this->canEdit()==""?"true":"false"));
}
?>
