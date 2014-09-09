<?php
/*
 * Static calendar methods
 *
 * @author Anakeen
 * @license http://www.fsf.org/licensing/licenses/agpl-3.0.html GNU Affero General Public License
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
