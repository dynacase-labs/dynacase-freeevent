<?php


namespace Dcp\Freeevent;
/*
 * Dynamic calendar methods
 *
 * @author Anakeen
 * @package FREEEVENT
*/
class Dcalendar extends \Dcp\Family\Dsearch
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
    
    function postCreated()
    {
        if ($this->getRawValue("SE_FAMID") == "") $this->setValue("SE_FAMID", getFamIdFromName($this->dbaccess, "EVENT"));
    }



    /**
     * Calendar view
     * @param string $target window target name for hyperlink destination
     * @param bool $ulink if false hyperlink are not generated
     * @param bool $abstract if true only abstract attribute are generated
     * @templateController
     */
    function viewcalendar($target = "_self", $ulink = true, $abstract = false)
    {
        
        $this->viewprop($target, $ulink, $abstract);
        $this->viewdsearch($target, $ulink, $abstract);
    }






    
    function ComputeQuery($keyword = "", $famid = - 1, $latest = "yes", $sensitive = false, $dirid = - 1, $subfolder = true, $full = false)
    {
        if ($dirid > 0) {
            
            if ($subfolder) $cdirid = getRChildDirId($this->dbaccess, $dirid);
            else $cdirid = $dirid;
        } else $cdirid = 0;;
        
        $filters = $this->getSqlGeneralFilters($keyword, $latest, $sensitive);
        $specialorder = getHttpVars("orderby");
        if ($specialorder) $this->setValue("se_orderby", $specialorder);
        $cond = $this->getSqlDetailFilter();
        if ($cond === false) return array(
            false
        );
        
        if ($cond != "") $filters[] = $cond;
        
        $text = $this->getRawValue("DCAL_TEXT");
        if ($text != "") {
            $cond = $this->getSqlCond("values", $this->getRawValue("DCAL_TEXTOP", "~*") , $text);
            $filters[] = $cond;
        }
        $idp = $this->getRawValue("DCAL_IDPRODUCER");
        if ($idp != "") {
            $cond = $this->getSqlCond("evt_frominitiatorid", "=", $idp);
            $filters[] = $cond;
        }
        $tidres = $this->getMultipleRawValues("DCAL_IDRES");
        foreach ($tidres as $k => $v) {
            if (!($v > 0)) unset($tidres[$k]);
        }
        //  print_r2($tidres);
        if (count($tidres) > 0) {
            $cond = $this->getSqlCond("evt_idres", "~y", $tidres);
            $filters[] = $cond;
        }
        
        $query = getSqlSearchDoc($this->dbaccess, $cdirid, $famid, $filters, $distinct=false, $latest == "yes", $this->getRawValue("se_trash"));
        
        return $query;
    }

}
?>
