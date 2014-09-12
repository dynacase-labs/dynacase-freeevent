<?php
/*
 * @author Anakeen
 * @license http://www.fsf.org/licensing/licenses/agpl-3.0.html GNU Affero General Public License
 * @package FDL
 */


namespace Dcp\Freeevent;
/*
 * Dynamic calendar methods
 *
 * @author Anakeen
 * @license http://www.fsf.org/licensing/licenses/agpl-3.0.html GNU Affero General Public License
 * @package FREEEVENT
*/
/**
 * Class Calendar
 *
 * @target \Dir
 * @package Dcp\Freeevent
 */
trait Calendar {
    
    public $xmlview = "FREEEVENT:XMLEVLIST";
    /**
     * return all atomic event found in period between $d1 and $d2
     *
     * @param string $d1 begin date in iso8601 format YYYY-MM-DD HH:MM
     * @param string $d2 end date in iso8601 format
     * @param bool $exploded
     * @param array $filter
     * @param int|string $famid [=EVENT] to limit search on this family
     * @return array array of event. These events returned are not objects but only a array of variables.
     */
    function getEvents($d1 = "", $d2 = "", $exploded = true, $filter = array() , $famid = "EVENT")
    {
        if ($d2 == "") $filter[] = "evt_begdate is not null";
        else $filter[] = "evt_begdate <= '$d2'";
        if ($d1 == "") $filter[] = "evt_enddate is not null";
        else $filter[] = "evt_enddate >= '$d1'";
        /**
         * @var \Dir $this
         */
        $tev = $this->getContent(true, $filter);
        if (!$exploded) return $tev;
        $tevx = array();
        foreach ($tev as $v) {
            /**
             * @var Event $doc
             */
            $doc = getDocObject($this->dbaccess, $v);
            $tevtx1 = $doc->explodeEvt($d1, $d2);
            //	      $tevx+=$tevtx1;
            $tevx = array_merge($tevx, $tevtx1);
        }
        
        return $tevx;
    }
    /**
     * Calendar edit view
     * @param string $target window target name for hyperlink destination
     * @param bool $ulink if false hyperlink are not generated
     * @param bool $abstract if true only abstract attribute are generated
     * @templateController
     */
    function editcalendar($target = "_self", $ulink = true, $abstract = false)
    {
        /**
         * @var \Dir $this
         */
        $this->editattr();
        $this->viewprop($target, $ulink, $abstract);
        $oa = $this->getAttribute("se_famid");
        $this->lay->set("viewcriteria", ($oa != false));
    }
    /**
     * planner view
     * @param string $target window target name for hyperlink destination
     * @param bool $ulink if false hyperlink are not generated
     * @param bool|string $abstract if true only abstract attribute are generated
     * @templateController
     */
    function planner($target = "finfo", $ulink = true, $abstract = "Y")
    {
        include_once ("FREEEVENT/Lib.DCalendar.php");
        include_once ("WHAT/Lib.Color.php");
        global $action;
        /**
         * @var \Dcp\Freeevent\Scalendar|\Dcp\Freeevent\Dcalendar $this
         */
        if ($this->needParameters()) {
            // redirect to zone viewdsearch
            
            /**
             * @var \Dcp\Family\Dsearch|Calendar $this
             */
            $this->lay = new \Layout(getLayoutFile("FREEDOM", "viewdsearch.xml") , $action);
            $this->viewdsearch($target, $ulink, $abstract);
            $this->lay->set("saction", getHttpVars("saction", "FDL_CARD"));
            $this->lay->set("sapp", getHttpVars("sapp", "FDL"));
            $this->lay->set("sid", getHttpVars("sid", "id"));
            $this->lay->set("starget", getHttpVars("starget", "_self"));
            $this->lay->set("stext", ___("view planner","freeevent"));
            return;
        }
        $action->parent->AddJsRef("FDL:JDATE.JS", true);
        $action->parent->AddJsRef("FREEEVENT:PLANNER.JS", true);
        $action->parent->AddCssRef("FREEEVENT:PLANNER.CSS", true);
        //  $action->parent->AddCssRef($action->GetParam("CORE_PUBURL")."/FREEEVENT/Layout/planner.css",true);
        if (getHttpVars("byres") != "") $byres = (getHttpVars("byres", "N") == "Y");
        else $byres = (($this->getRawValue("DCAL_GROUPBY", "BYRES")) == "BYRES");
        $this->lay->set("byres", $byres);
        
        $idxc = $this->getRawValue("DCAL_COLORIDX", "ir"); // color index (by ressource by default)
        $korder1 = $this->getRawValue("DCAL_ORDERIDX1", "absx");; // begin date by default
        $korder2 = $this->getRawValue("DCAL_ORDERIDX2");
        $kdesc1 = $this->getRawValue("DCAL_ORDERDESC1");
        $kdesc2 = $this->getRawValue("DCAL_ORDERDESC2");
        $dlum = $this->getRawValue("DCAL_LUMINANCE", "0.8");
        // window time interval
        $hwstart = getHttpVars("wstart");
        if ($hwstart) {
            $wstart = Iso8601ToJD($hwstart);
            if (!$wstart) $wstart = FrenchDateToJD($hwstart);
        } else $wstart = getHttpVars("jdstart");
        
        $hwend = getHttpVars("wend");
        if ($hwend) {
            $wend = Iso8601ToJD($hwend);
            if (!$wend) $wend = FrenchDateToJD($hwend);
        } else $wend = getHttpVars("jdend");
        
        if (!$wstart) {
            $isoperiode = getHttpVars("isoperiod", strftime("%Y-%m", time()));
            if ($isoperiode) {
                if (preg_match("/([0-9]+)-([0-9]+)/", $isoperiode, $reg)) {
                    // month period
                    $wstart = FrenchDateToJD(sprintf("01/%02d/%04d", $reg[2], $reg[1]));
                    $wend = FrenchDateToJD(sprintf("01/%02d/%04d", $reg[2] + 1, $reg[1]));
                } elseif (preg_match("/([0-9]+)/", $isoperiode, $reg)) {
                    // year period
                    $wstart = FrenchDateToJD(sprintf("01/01/%04d", $reg[1]));
                    $wend = FrenchDateToJD(sprintf("01/01/%04d", $reg[1] + 1));
                }
            }
        }
        //  print "<br>wstart:$wstart:".jd2cal($wstart);
        // print "<br>wend:$wend:".jd2cal($wend);
        $mstart = 5000000; // vers 9999
        $mend = 0;
        $qstart = "";
        $qend = "";
        if ($wstart) {
            $mstart = $wstart;
            $mstart = floor($mstart + 0.5) - 0.5; // begin at 00:00
            $qstart = jd2cal($wstart);
        }
        if ($wend) {
            $mend = $wend;
            $mend = floor($mend) + 0.5; // end at 00:00
            $qend = jd2cal($wend);
        }
        
        $tevt = $this->getEvents($qstart, $qend);
        foreach ($tevt as $k => $v) {
            
            $mdate1 = StringDateToJD(getv($v, "evt_begdate"));
            $mdate2 = StringDateToJD(getv($v, "evt_enddate"));
            if ($wstart) {
                if (($mdate2 < $mstart) || ($mdate1 > $wend)) {
                    unset($tevt[$k]);
                } else {
                    $tevt[$k]["m1"] = max($mdate1, $mstart);
                    $tevt[$k]["m2"] = min($mdate2, $mend);
                }
            } else {
                if ($mstart > $mdate1) $mstart = $mdate1;
                $tevt[$k]["m1"] = $mdate1;
                if ($mdate2 > $mend) $mend = $mdate2;
                $tevt[$k]["m2"] = $mdate2;
            }
        }
        
        $tidres = $this->getMultipleRawValues("DCAL_IDRES");
        $onlyres = ($this->getRawValue("dcal_viewonlyres", "all") == "only");
        $delta = $mend - $mstart;
        $titleinline = ($this->getRawValue("dcal_prestitle", "INLINE") == "INLINE");
        $titleinleft = ($this->getRawValue("dcal_prestitle", "INLINE") == "LEFT");
        $iconinline = ($this->getRawValue("dcal_presicon", "INLINE") == "INLINE");
        $this->lay->set("inleft", $titleinleft);
        $this->lay->set("icoinline", $iconinline);
        $this->lay->set("dday100", round($delta));
        $this->lay->set("dday50", round($delta * 0.5));
        $this->lay->set("dday10", round($delta * 0.1));
        $this->lay->set("ppar", $this->urlWhatEncodeSpec(""));
        $sub = 0;
        $idc = 0;
        $tres = $colorredid = $RN = array();
        //   print "delta=$delta";
        //   print " - <B>".microtime_diff(microtime(),$mb)."</B> ";
        $residx = array();
        foreach ($tevt as $k => $v) {
            $tr = $this->rawValueToArray(getv($v, "evt_idres"));
            $tresname = $this->rawValueToArray(getv($v, "evt_res"));
            //$x = floor(100 * ($v["m1"] - $mstart) / $delta);
            $w = floor(100 * ($v["m2"] - $v["m1"]) / $delta);
            foreach ($tr as $ki => $ir) {
                if ($onlyres && (!in_array($ir, $tidres))) continue;
                if (!isset($residx[$ir])) $residx[$ir] = count($residx) + 1;
                $RN[$sub] = array(
                    "w" => sprintf("%d", ($w < 1) ? 1 : $w) ,
                    "absx" => $v["m1"],
                    "absw" => $v["m2"] - $v["m1"],
                    "line" => $k,
                    "subline" => $residx[$ir],
                    //"subline"=>$colorredid[$ir],
                    "ir" => "$ir",
                    "idx" => $sub,
                    "evticon" => ($iconinline) ? $this->getIcon($v["evt_icon"]) : '',
                    "rid" => getv($v, "evt_idinitiator") ,
                    "fid" => getv($v, "evt_frominitiatorid") ,
                    "eid" => getv($v, "id") ,
                    "res" => $tresname[$ki],
                    "subtype" => getv($v, "evt_code") ,
                    "divtitle" => ($titleinline) ? (((($v["m2"] - $v["m1"]) > 0) ? '' : ___("DATE ERROR","freeevent")) . $v["title"]) : '',
                    "divtitle2" => ($titleinleft) ? (((($v["m2"] - $v["m1"]) > 0) ? '' : ___("DATE ERROR","freeevent")) . $v["title"]) : '',
                    "desc" => str_replace(array(
                        "\n",
                        "\r",
                        "'"
                    ) , array(
                        "<br/>",
                        "",
                        "&quot;"
                    ) , ((sprintf("<img src=\"%s\" style=\"float:left;width:48px\"><b>%s</b></br><i>%s</i><br/>%s - %s<br/>%s", $this->getIcon(getv($v, "evt_icon")) , $v["title"], getv($v, "evt_frominitiator") , substr(getv($v, "evt_begdate") , 0, 10) , (substr(getv($v, "evt_enddate") , 0, 10) != substr(getv($v, "evt_begdate") , 0, 10)) ? substr(getv($v, "evt_enddate") , 0, 10) : substr(getv($v, "evt_begdate") , 11, 5) . "/" . substr(getv($v, "evt_enddate") , 11, 5) , getv($v, "evt_desc")))))
                );
                
                if (!isset($colorredid[$RN[$sub][$idxc]])) $colorredid[$RN[$sub][$idxc]] = $idc++;
                $sub++;
                $tres[$ir] = array(
                    "divid" => "div$ir",
                    "res" => $tresname[$ki]
                );
            }
        }
        if (count($tres) > 0) {
            $plancolor = $action->read("plancolor");
            $deltacolor = 0;
            if (is_array($plancolor)) {
                // detect new colors to set
                $diff = array_diff(array_keys($colorredid) , array_keys($plancolor));
                $deltacolor = count($plancolor);
                $idc = 0;
                foreach ($plancolor as $k => $v) { // recompute color in case of luminance change
                    $plancolor[$k] = HSL2RGB($this->getColorAngle($idc++) * 360, 1, $dlum);
                }
            } else {
                $diff = array_keys($colorredid);
            }
            
            $col = $plancolor;
            foreach ($diff as $k => $v) {
                $col[$v] = HSL2RGB($this->getColorAngle($k + $deltacolor) * 360, 1, $dlum);
            }
            $action->register("plancolor", $col);
            
            if ($byres) {
                foreach ($RN as $k => $v) {
                    $RN[$k]["color"] = $col[$v[$idxc]];
                }
            } else {
                $k1 = $korder1;
                $k2 = $korder2;
                if ($kdesc1 == "DESC") {
                    $r11 = 1;
                    $r12 = - 1;
                } else {
                    $r11 = - 1;
                    $r12 = 1;
                }
                if ($kdesc2 == "DESC") {
                    $r21 = 1;
                    $r22 = - 1;
                } else {
                    $r21 = - 1;
                    $r22 = 1;
                }
                $cname = get_class($this);
                if (!getHttpVars("orderby")) {
                    $sortfunc = create_function('$a,$b', 'return ' . $cname . '::cmpevt($a,$b,"' . $k1 . '","' . $k2 . '","' . $r11 . '","' . $r12 . '","' . $r21 . '","' . $r22 . '");');
                    uasort($RN, "$sortfunc");
                }
                
                $y = 0;
                foreach ($RN as $k => $v) {
                    $RN[$k]["color"] = $col[$v[$idxc]];
                    $RN[$k]["subline"] = $y++;
                }
            }
            
            $this->lay->setBlockData("RES", $tres);
            $this->lay->setBlockData("BAR", $RN);
        }
        
        if (!$wstart) {
            $mstart = floor($mstart) - 0.5; // begin at 00:00
            $mend = floor($mend) + 0.5; // end at 00:00
            
        }
        
        $this->lay->set("begdate", jd2cal($mstart, "French"));
        $this->lay->set("enddate", jd2cal($mend, "French"));
        $this->lay->set("mstart", $mstart);
        $this->lay->set("mend", $mend);
        $this->lay->set("id", $this->id);
        $this->lay->set("vid", GetHttpVars("vid"));
        $this->lay->set("zone", GetHttpVars("zone"));
        //  print "<HR>". print " - <B>".microtime_diff(microtime(),$mb)."</B>";
        // print "<hr>";
        
    }
    /**
     * return angle of color 0..2Pi => 0..1
     * for a color number range compute the next color in color circle
     */
    function getColorAngle($x)
    {
        if ($x <= 0) return 0;
        $p = floor(log($x, 2));
        $n = pow(2, $p - 1);
        $da = 1 / (2 * $n) + ($x - 2 * $n) / $n;
        return $da / 2;
    }
    
    function cmpevt($a, $b, $k1 = "absx", $k2 = "absw", $r11 = - 1, $r12 = 1, $r21 = - 1, $r22 = 1)
    {
        if ($a[$k1] == $b[$k1]) {
            if ($k2 == "") return 0;
            if ($a[$k2] == $b[$k2]) return 0;
            return (($a[$k2]) < ($b[$k2])) ? $r21 : $r22;
        }
        return (($a[$k1]) < ($b[$k1])) ? $r11 : $r12;
    }
    /**
     * @templateController Event list
     * @param string $target
     * @param bool $ulink
     * @param string $abstract
     */
    function XmlEvList($target = "finfo", $ulink = true, $abstract = "N")
    {
        
        $lastrev = GetHttpVars("lastrev", 0);
        $d1 = GetHttpVars("ts", time() - (24 * 3600 * 30));
        $d2 = GetHttpVars("te", time() + (24 * 3600 * 30));
        
        $sd1 = strftime("%Y-%m-%d %H:%M", $d1);
        $sd2 = strftime("%Y-%m-%d %H:%M", $d2);
        $evt = array();
        /**
         * @var \Dir|Calendar $this
         */
        $this->lay->set("uptime", time());
        
        $evmenu = array();
        $items = array();
        
        $filter[] = "revdate>=" . $lastrev;
        
        if ($lastrev > 0) $this->setValue("se_trash", "also");
        $tevt = $this->getEvents($sd1, $sd2, true, $filter);
        foreach ($tevt as $v) {
            /**
             * @var Event $ev
             */
            $ev = getDocObject($this->dbaccess, $v);
            $evt[count($evt) ]["cevent"] = $ev->viewdoc($ev->XmlResume);
            if (!isset($evmenu[$ev->fromid])) {
                $evmenu[$ev->fromid]["famid"] = $ev->fromid;
                $items[$ev->fromid] = $ev->setEventMenu();
            }
        }
        
        $this->lay->set("fstart", $sd1);
        $this->lay->set("fend", $sd2);
        $this->lay->set("start", $d1);
        $this->lay->set("end", $d2);
        $this->lay->setBlockData("EVENTS", $evt);
        
        $this->lay->setBlockData("MENUS", $evmenu);
        foreach ($evmenu as $k => $v) {
            $this->lay->setBlockData("MITEMS$k", $items[$k]);
        }
        header('Content-Type: text/xml; charset="utf-8"');
        
        return;
    }
}
?>
