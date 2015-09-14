<?php
define("_nova_district_token_", TRUE);
include_once "../../init.php";

//controle d'acces 1
if(!isset($_SESSION['user']) OR $_SESSION['user']->getIdMember() <= 0 && Tools::getParentClass($_SESSION['user']) != "Doctor")
	header("Location: ../index.php");

require_once(dirname(__FILE__).'/../../libs/html2pdf/html2pdf.class.php');

if(isset($_GET['date']) AND Tools::checkDate($_GET['date']))
{
	$temp = explode('-', $_GET['date']);
	if(isset($temp[0]) AND isset($temp[1]) AND isset($temp[2]))
		$day = mktime(0, 0, 0, intval($temp[1]), intval($temp[2]), intval($temp[0]));
}

//controle d'acces 2
if(!isset($day))
	header("Location: ../index.php");
	
$dailyScheduleList = SchedulesManager::instance()->getScheduleList($_SESSION['user']->getIdMember(), $day, $day + 24*3600);
	
$content = "
		<table style='width: 100%;'>
			<tr>
				<td style='width: 65%'></td>
				<td style='text-align: center; background-color: #2a7edd; color: white; padding: 6px; border-radius: 5px'>
					BOOKING
				</td>
			</tr>
			<tr>
				<td style='width: 65%'></td>
				<td style='text-align: right; padding: 6px; color: #2c2c2c; '>
					Emploi du temps du ".Calendar::instance()->getDays()[date('w', $day)]." ".date("d-m-Y", $day)."
				</td>
			</tr>
			<tr>
				<td style='width: 65%'></td>
				<td style='text-align: right; padding-right: 6px; color: grey; font-size: 80%'>
					généré le ".Calendar::instance()->getDays()[date('w', time())]." ".date("d-m-Y", time())." à ".date("H\h:i", time())."
				</td>
			</tr>
		</table>
		<br /><br /><br />
		<br /><br /><br />
	";
	
if(count($dailyScheduleList) > 0){
	foreach ($dailyScheduleList as $schedule){ 
		$sexe = "homme";
		if(isset($schedule['sexe']))
			$sexe = $schedule['sexe'];
			
		$infos = "Aucune note particulière sur ce rendez-vous";
		if($schedule['note'] != "")
			$infos = $schedule['note'];
	
		$content .= "
			<table style='width:100%'>
				<tr>
					<td style='width: 10%; padding: 4px; color: #e36b00'>".date("H:i", $schedule['date_start'])."</td>
					<td rowspan='3'><img src='../img/".$sexe."_icon.jpg' style='height: 75px; margin-right: 20px'/></td>
					<td style='padding: 4px;font-size: 110%'><span style='text-transform: uppercase; font-size: 110%'>".$schedule['nom']."</span> 
					".ucfirst($schedule['prénom'])." (age)</td>
				</tr>
				<tr>
					<td style='width: 10%; padding: 4px; color: #d59054'>".(($schedule['date_stop'] - $schedule['date_start']) / 60)."mn</td>
					<td style='padding: 4px; color: grey'>
						".$schedule['téléphone']."<br />
						".$schedule['ville']." (".$schedule['code postal'].")
						<br />
					</td>
				</tr>
				<tr>
					<td style='width: 10%; padding: 4px;'></td>
					<td style='padding: 4px; color: grey'>
						".$infos."
					</td>
				</tr>
			</table>
			<hr style='height: 1px; border: none; background: lightgrey' />";
	}

	
}
else
	$content .= "<table style='width:100%'>
		<tr><td style='text-align: center; width: 100%'>Aucun rendez-vous prévu ce jour</td></tr>
	</table>";
	
$html2pdf = new HTML2PDF('P','A4','fr');
$html2pdf->WriteHTML($content);
$html2pdf->Output('Emploi du temps 17-05-2014.pdf');

?>