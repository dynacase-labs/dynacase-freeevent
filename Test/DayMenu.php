<?php
/*
 * @author Anakeen
 * @license http://www.fsf.org/licensing/licenses/agpl-3.0.html GNU Affero General Public License
 * @package FDL
*/

namespace Dcp\Freeevent\Test;

class DayMenu extends \Dcp\Family\Event
{
    public function explodeEvt($d1, $d2)
    {
        
        include_once ("FDL/Lib.Util.php");
        
        $jdi1 = ($d1 == "") ? 0 : Iso8601ToJD($d1);
        $jdi2 = StringDateToJD($this->getRawValue("evt_begdate"));
        $jd1 = max($jdi1, $jdi2); // search begin date
        $jdi1 = ($d2 == "") ? 5000000 : Iso8601ToJD($d2);
        $jdi2 = StringDateToJD($this->getRawValue("evt_enddate"));
        $jd2 = min($jdi1, $jdi2); // search end date
        $day = intval($this->getRawValue("DEVT_DAY")); // the day to repeat
        if (($day < 1) || ($day > 7)) {
            print "error day $day";
            return array();
        }
        $djd1 = jdWeekDay($jd1);
        $jd1+= ($day - $djd1 + 7) % 7; // search the first day
        $te = array();
        $te1 = parent::explodeEvt($d1, $d2);
        
        for ($i = $jd1; $i < $jd2; $i+= 7) {
            $te[$i] = $te1[0];
            $te[$i]["evt_begdate"] = jd2cal($i); // change date period
            $te[$i]["evt_enddate"] = jd2cal($i + 1); // one day later
            $te[$i]["evt_desc"] = $i;
        }
        
        return $te;
    }
}
