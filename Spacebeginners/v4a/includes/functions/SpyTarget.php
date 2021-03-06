<?php

/**
 * SpyTarget
 *
 * @version 1
 * @copyright 2008
 */

// ----------------------------------------------------------------------------------------------------------------
//
// SpyTarget
//
// $TargetPlanet -> Enregistrement 'planet' de la base de donnees
// $Mode         -> Ce que l'on va notifier
//                  0 -> Ressources, 1 -> Flotte, 2 ->Defenses, 3 -> Batiments, 4 -> Technologies
// $TitleString  -> Chaine definissant le titre ou la parcelle de titre a afficher
function SpyTarget ( $TargetPlanet, $Mode, $TitleString ) {
	global $lang, $resource;

	$LookAtLoop = true;
	if       ($Mode == 0) {
		$String  = "<table width=\"440\"><tr><td class=\"c\" colspan=\"5\">";
		$String .= $TitleString ." ". $TargetPlanet['name'];
		$String .= " <a href=\"galaxy.php?mode=3&galaxy=". $TargetPlanet["galaxy"] ."&system=". $TargetPlanet["system"]. "\">";
		$String .= "[". $TargetPlanet["galaxy"] .":". $TargetPlanet["system"] .":". $TargetPlanet["planet"] ."]</a>";
		$String .= " am ". date("d-m-Y H:i:s", time() ) ."</td>";
		$String .= "</tr><tr>";
		$String .= "<td width=220>". $lang['Metal']     ."</td><td width=220 align=right>". pretty_number($TargetPlanet['metal'])      ."</td><td>&nbsp;</td>";
		$String .= "<td width=220>". $lang['Crystal']   ."</td></td><td width=220 align=right>". pretty_number($TargetPlanet['crystal'])    ."</td>";
		$String .= "</tr><tr>";
		$String .= "<td width=220>". $lang['Deuterium'] ."</td><td width=220 align=right>". pretty_number($TargetPlanet['deuterium'])  ."</td><td>&nbsp;</td>";
		$String .= "<td width=220>". $lang['Appolonium'] ."</td><td width=220 align=right>". pretty_number($TargetPlanet['appolonium'])  ."</td><td>&nbsp;</td>";
		$String .= "</tr><tr>";
		$String .= "<td width=220>". $lang['Energy']    ."</td><td width=220 align=right>". pretty_number($TargetPlanet['energy_max']) ."</td>";
		$String .= "</tr>";
		$LookAtLoop = false;
	} elseif ($Mode == 1) {
		$ResFrom[0] = 200;
		$ResTo[0]   = 299;
		$Loops      = 1;
	} elseif ($Mode == 2) {
		$ResFrom[0] = 400;
		$ResTo[0]   = 499;
		$ResFrom[1] = 500;
		$ResTo[1]   = 599;
		$Loops      = 2;
	} elseif ($Mode == 3) {
		$ResFrom[0] = 1;
		$ResTo[0]   = 99;
		$Loops      = 1;
	} elseif ($Mode == 4) {
		$ResFrom[0] = 100;
		$ResTo[0]   = 199;
		$Loops      = 1;
	}elseif ($Mode == 5) {
		$ResFrom[0] = 600;
		$ResTo[0]   = 616;
		$Loops      = 1;			
	}elseif ($Mode == 6){
	
		$stayfleets = doquery("SELECT * FROM {{table}} WHERE fleet_end_galaxy = {$TargetPlanet["galaxy"]} AND fleet_end_system = {$TargetPlanet["system"]} AND fleet_end_planet = {$TargetPlanet["planet"]} AND `fleet_mission` = '5';", 'fleets');
		$String  = "<table width=\"440\" cellspacing=\"1\"><tr><td class=\"c\" colspan=\"". ((2 * SPY_REPORT_ROW) + (SPY_REPORT_ROW - 1))."\">". $TitleString ."</td></tr>";

		while ($stayfleet = mysql_fetch_array($stayfleets)){
			$String  .= "<tr><td><u>".$lang['sys_spy_stayfleetsdetected'].BuildPlanetAdressLink ( $TargetPlanet )."</u></td></tr>";
			$todoarray    = explode (";", $stayfleet['fleet_array']);
			$row     = 0;
			
			for ($QElement = 0; $QElement < count($todoarray); $QElement++) {
				$onetype = explode (",", $todoarray[$QElement] );
				$name=$onetype[0];
				$num = $onetype[1];
				
				if ($row == 0) {
						$String  .= "<tr>";
					}
				$String  .= "<td align=left>".$lang['tech'][$name]."</td><td align=right>".$num."</td>";
				if ($row < SPY_REPORT_ROW - 1) {
					$String  .= "<td>&nbsp;</td>";
				}
				$row++;
				if ($row == SPY_REPORT_ROW) {
					$String  .= "</tr>";
					$row      = 0;
			}
		
	  }
	}$LookAtLoop = false;
    
	

	}if ($LookAtLoop == true) {
		$String  = "<table width=\"440\" cellspacing=\"1\"><tr><td class=\"c\" colspan=\"". ((2 * SPY_REPORT_ROW) + (SPY_REPORT_ROW - 1))."\">". $TitleString ."</td></tr>";
		$Count       = 0;
		$CurrentLook = 0;
		while ($CurrentLook < $Loops) {
			$row     = 0;
			for ($Item = $ResFrom[$CurrentLook]; $Item <= $ResTo[$CurrentLook]; $Item++) {
				if ( $TargetPlanet[$resource[$Item]] > 0) {
					if ($row == 0) {
						$String  .= "<tr>";
					}
					$String  .= "<td align=left>".$lang['tech'][$Item]."</td><td align=right>".$TargetPlanet[$resource[$Item]]."</td>";
					if ($row < SPY_REPORT_ROW - 1) {
						$String  .= "<td>&nbsp;</td>";
					}
					$Count   += $TargetPlanet[$resource[$Item]];
					$row++;
					if ($row == SPY_REPORT_ROW) {
						$String  .= "</tr>";
						$row      = 0;
					}
				}
			}

			while ($row != 0) {
				$String  .= "<td>&nbsp;</td><td>&nbsp;</td>";
				$row++;
				if ($row == SPY_REPORT_ROW) {
					$String  .= "</tr>";
					$row      = 0;
				}
			}
			$CurrentLook++;
		} // while
	}
	$String .= "</table>";

	$return['String'] = $String;
	$return['Count']  = $Count;
	return $return;
}

?>