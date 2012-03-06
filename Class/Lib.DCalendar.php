<?php
/*
 * Utilities function for freeevent
 *
 * @author Anakeen
 * @license http://www.fsf.org/licensing/licenses/agpl-3.0.html GNU Affero General Public License
 * @package FREEEVENT
*/

function cmpabsx($a, $b)
{
    
    if ($a["absx"] == $b["absx"]) return 0;
    return (intval($a["absx"]) < intval($b["absx"])) ? -1 : 1;
}
function cmpevtm1($a, $b)
{
    
    if ($a["m1"] == $b["m1"]) return 0;
    return (intval($a["m1"]) < intval($b["m1"])) ? -1 : 1;
}
?>
