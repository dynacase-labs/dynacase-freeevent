<?php
/*
 * Static calendar methods
 *
 * @author Anakeen
 * @package FREEEVENT
*/

namespace Dcp\Freeevent;

class Scalendar extends \Dcp\Family\Dir
{

    use Calendar;
    public $eviews = array(
        "FREEEVENT:EDITCALENDAR"
    );
    public $cviews = array(
        "FREEEVENT:PLANNER",
        "FREEEVENT:VIEWCALENDAR"
    );
    public $defaultedit = "FREEEVENT:EDITCALENDAR";
    public $defaultview = "FREEEVENT:PLANNER";
    function needParameters()
    {
        return false;
    }

}

?>
