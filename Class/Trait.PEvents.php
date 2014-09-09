<?php
/*
 * Produce events methods
 *
 * @author Anakeen
 * @license http://www.fsf.org/licensing/licenses/agpl-3.0.html GNU Affero General Public License
 * @package FREEEVENT
*/
namespace Dcp\Freeevent;
trait EventProduct {
    /**
     * Produce events
     * Add or Update events
     */
    function setEvent()
    {
        
        return $this->pEventDefault();
    }
    /**
     * Use for derived event by the producer to set added attributes
     * @param \Dcp\Freeevent\Event $e event object
     */
    function setEventSpec(&$e)
    {
    }
    /**
     * Delete events
     * Delete related events
     */
    function deleteEvent()
    {
        return $this->dEventDefault();
    }
    
    function postDelete()
    {
        $this->deleteEvent();
    }
    /**
     * identificator of the attribute which containt the begin date for event
     * @var string
     */
    public $eventAttBeginDate;
    /**
     * identificator of the attribute which containt the end date for event
     * @var string
     */
    public $eventAttEndDate;
    /**
     * identificator of the attribute which containt the description of the  event
     * @var string
     */
    public $eventAttDesc;
    /**
     * identificator of the attribute which containt the code of the event
     * @var string
     */
    public $eventAttCode;
    /**
     * identificators of the attribute which containt the document id of the ressource
     * @var array
     */
    public $eventRessources = array();
    /**
     * name of the family event
     * @var string
     */
    public $eventFamily = "EVENT";
    /**
     * produce event based on default methods
     * @access private
     * @return string error text (empty if no error)
     */
    function pEventDefault()
    {
        
        $evt = $this->getDefaultEvent();
        //  if (($this->control("edit")=="")||(isset($this->withoutControl))) { // can modify only if can modify productor
        $evt->disableEditControl();
        if ($evt->isAlive()) {
            if (($evt->getRawValue("evt_begdate") != $this->getEventBeginDate()) || ($evt->getRawValue("evt_enddate") != $this->getEventEndDate())) {
                $evt->addHistoryEntry(sprintf(_("Change period from [%s %s] to [%s %s]") , $evt->getRawValue("evt_begdate") , $evt->getRawValue("evt_enddate") , $this->getEventBeginDate() , $this->getEventEndDate()));
            } else {
                /**
                 * @var \Doc|EventProduct $this
                 */
                $evt->addHistoryEntry(sprintf(_("Changes from document \"%s\" [%d]") , $this->getTitle() , $this->id));
            }
        }
        $evt->setValue("evt_begdate", $this->getEventBeginDate());
        $evt->setValue("evt_enddate", $this->getEventEndDate());
        $evt->setValue("evt_desc", $this->getEventDesc());
        $evt->setValue("evt_code", $this->getEventCode());
        
        $evt->setValue("evt_idcreator", $this->getEventOwner());
        $evt->setValue("evt_transft", 'pEventDefault');
        $evt->setValue("evt_itransft", 'mEventDefault');
        $evt->setValue("evt_idinitiator", $this->initid);
        $evt->setValue("evt_title", $this->getEventTitle());
        $evt->setValue("evt_idres", $this->getEventRessources());
        $err = "";
        $this->setEventSpec($evt);
        if (!$evt->isAlive()) {
            $err = $evt->Add();
        }
        if ($err == "") $err = $evt->refresh();
        if ($err == "") {
            $err = $evt->modify();
        }
        $evt->enableEditControl();
        
        return $err;
    }
    /**
     * delete event based on default methods
     * @access private
     * @return string error text (empty if no error)
     */
    function dEventDefault()
    {
        $err = "";
        /**
         * @var \Doc|EventProduct $this
         */
        $evt = createDoc($this->dbaccess, $this->eventFamily, false);
        if ($evt) {
            include_once ("FDL/Lib.Dir.php");
            $s = new \SearchDoc($this->dbaccess, $this->eventFamily);
            $s->addFilter("evt_idinitiator=%d", $this->initid);
            $s->addFilter("evt_transft='pEventDefault'");
            $s->setObjectReturn(false);
            $tevt = $s->search();
            
            if (count($tevt) > 0) {
                $evt = new_Doc($this->dbaccess, $tevt[0]["id"]);
            }
        }
        if ($evt->isAlive()) {
            $err = $evt->delete(true, false, true);
        }
        return $err;
    }
    /**
     * get the begin date for the event
     * @return string timestamp the date in iso8601 format or native (French)
     */
    function getEventBeginDate()
    {
        /**
         * @var \Doc|EventProduct $this
         */
        return $this->getRawValue($this->eventAttBeginDate);
    }
    /**
     * get the end date for the event
     * @return string timestamp the date in iso8601 format or native (French)
     */
    function getEventEndDate()
    {
        /**
         * @var \Doc|EventProduct $this
         */
        return $this->getRawValue($this->eventAttEndDate);
    }
    /**
     * get the owner of the event
     * @return int freedom id user
     */
    function getEventOwner()
    {
        /**
         * @var \Doc|EventProduct $this
         */
        $u = new \Account("", $this->owner);
        return $u->fid;
    }
    /**
     * get the title of the event
     * @return string
     */
    function getEventTitle()
    {
        /**
         * @var \Doc|EventProduct $this
         */
        return $this->title;
    }
    /**
     * get the description of the event
     * @return string
     */
    function getEventDesc()
    {
        /**
         * @var \Doc|EventProduct $this
         */
        return $this->getRawValue($this->eventAttDesc);
    }
    /**
     * get the category of the event
     * @return string
     */
    function getEventCode()
    {
        /**
         * @var \Doc|EventProduct $this
         */
        return $this->getRawValue($this->eventAttCode);
    }
    /**
     * get the ressources
     * @return array array of ressources
     */
    function getEventRessources()
    {
        /**
         * @var \Doc|EventProduct $this
         */
        $tr = array();
        foreach ($this->eventRessources as $rid) {
            $v = $this->getRawValue($rid);
            if ($v != "") $tr[] = $v;
        }
        return $tr;
    }
    /**
     * reinit static variable
     */
    function complete()
    {
        /**
         * @var \Doc|EventProduct $this
         */
        $this->getDefaultEvent(true); // reset
        
    }
    /**
     * get the default event
     * @param bool $reset
     * @return Event the event object
     */
    function getDefaultEvent($reset = false)
    {
        static $__evtid = 0;
        
        if ($reset) {
            $__evtid = 0;
            return true;
        }
        
        if ($__evtid == 0) {
            /**
             * @var \Doc|EventProduct $this
             */
            $s = new \SearchDoc($this->dbaccess, $this->eventFamily);
            $s->addFilter("evt_idinitiator='%d'", $this->initid);
            $s->addFilter("evt_transft='pEventDefault'");
            $s->overrideViewControl();
            $s->setSlice(1);
            $tevt = $s->search();
            // search if already created
            if (count($tevt) > 0) $__evtid = $tevt[0]["id"];
        }
        
        if ($__evtid == 0) {
            $evt = createDoc($this->dbaccess, $this->eventFamily, false);
        } else {
            $evt = new_Doc($this->dbaccess, $__evtid);
        }
        return $evt;
    }
}
