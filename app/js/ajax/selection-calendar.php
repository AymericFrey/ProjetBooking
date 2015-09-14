<?php
//génère la vue pour le menu de sélection du calendrier

define("_nova_district_token_", TRUE);
require_once "../../../models/Model.class.php";
require_once "../../../models/User.class.php";
require_once "../../../models/Doctor.class.php";

session_start();
header('Content-Type: text/html; charset=utf-8');
date_default_timezone_set('Europe/Paris');

try {
	$result = "";
	
	if(!isset($_SESSION['user'])){
		die();
	}
	
	if(isset($_GET['dateStart']) && isset($_GET['hourStart']) AND isset($_GET['doctor']) AND isset($_GET['dayStartHour']) AND isset($_GET['dayEndHour'])){
		
		require_once "../../../models/Connection.class.php";
		require_once "../../../models/Error.class.php";
		require_once "../../../models/Tools.class.php";
		require_once "../../../models/Manager.class.php";
		require_once "../../../models/SchedulesManager.class.php";
		require_once "../../../models/DoctorsManager.class.php";
		require_once "../../../models/Schedule.class.php";
		setlocale (LC_TIME, 'fr_FR');
		date_default_timezone_set('Europe/Paris');

		//vars
		$doc = intval($_GET['doctor']);
		$dateStart = intval($_GET['dateStart']); //TS du départ de l'heure cliquée
		$hourStart = intval($_GET['hourStart']); //heure en STRING de l'heure cliquée (info uniquement)
		$dayStartHour = intval($_GET['dayStartHour']); // heure de démarrage de la journée !
		$dayEndHour = intval($_GET['dayEndHour']); // heure de fin de la journée !
		
		$doctor = DoctorsManager::instance()->getDoctor($doc);
		if(get_class($doctor) != "Doctor")
			die($doc." Une erreur est survenue : ".$doctor->getMessage());
			
		
		$duration = $doctor->getRdvDuration(); //en minutes (int)
		$nbrBlocks = ceil(60 / $duration) + 2;
		$blocked = SchedulesManager::instance()->getBlockSchedule($doctor, $dateStart, $dateStart + $nbrBlocks * $duration * 60);
		$rdv = SchedulesManager::instance()->getSchedule($doctor, $dateStart, $dateStart + $nbrBlocks * $duration * 60);
		$rdv = array_merge($rdv, $blocked);
		
		//récupération du DERNIER RDV ou BLOCKED précédent l'heure de départ (ou à cheval) pour se baser dessus pour le découpage des créneaux
		$found = false;
		foreach($rdv as $sched){
			if($sched->getDateStart() == $dateStart) //le start est pile au début
				$found = true;
			else if($sched->getDateStop() >= $dateStart AND $sched->getDateStop() <= $dateStart + $duration * 60) // le stop est dans l'heure mais pas le start
				$found = true;
			else if($sched->getDateStop() >= $dateStart + $duration * 60 AND $sched->getDateStart() <= $dateStart ) //le stop et le start ne sont pas dans l'heure mais ils sont au delà et avant !
				$found = true;
				
			if($found) {
				$previous = $sched;
				break;
			}
		}
		
		//si pas de créneau on récupere le précédent dans la journée (bloquage ou schedule)
		if(!$found){
			$previous = SchedulesManager::instance()->getPreviousSchedule($doctor, $dayStartHour, $dateStart);
			
			if(get_class($previous) != "Schedule") { // pas trouvé de départ bloqué. On prend l'heure de démarrage de la journée
				$previous = new Schedule();
				$temp = intval(date('i', $dayStartHour));
				$previous->setDateStart($dayStartHour - $temp * 60);
				$previous->setDateStop($dayStartHour);
			}
			//$rdv[] = $previous;
		}
		
		
		$result = "";
		
		$hourWidth = 800 / $nbrBlocks; //pixel - border * 2
		$baseCalcul = $hourWidth / ($duration * 60);
		
		/** Au choix ici, on peut choisir de conserver l'organisation générale des créneaux ou s'adapter par rapport au dernier pris **/
		//$dateStart = $dayStartHour + floor(($dateStart - $dayStartHour) / ($doctor->getRdvDuration() * 60)) * ($doctor->getRdvDuration() * 60);
		$dateStart = $previous->getDateStop() + floor(($dateStart - $previous->getDateStop()) / ($doctor->getRdvDuration() * 60)) * ($doctor->getRdvDuration() * 60);

		for($i = 0; $i < $nbrBlocks; $i++){
			if($dateStart + $i * $duration * 60 + $duration * 60 <= $dayStartHour OR $dateStart + $i * $duration * 60 + $duration * 60 > $dayEndHour)
				continue;
		
			$cur = $dateStart + $i * $duration * 60; //timestamp gauche du block courant

			$hour = date("H:i", $cur); //visuel uniquement
			
			$class = "selection-part-content-free";
			$result .= "<div class='selection-part' style='width: ".$hourWidth."px'>
						<div class='selection-part-hour'>".$hour."</div>
						<div class='selection-part-content ".$class."' data-calendar-selection-date='".date("Y-m-d", $cur)."'>";
			
			foreach($rdv as $sched){
				$left = 0;
				$width = 0;
				
				if($sched->getDateStart() >= $cur AND $sched->getDateStart() <= $cur + $duration * 60){ //le start est dans l'heure
					$left = round($baseCalcul * ($sched->getDateStart() - $cur));
					if($sched->getDateStop() >= $cur AND $sched->getDateStop() <= $cur + $duration * 60){ //stop dans l'heure AUSSI
						//$width = round($baseCalcul * ($sched->getDateStop() - $sched->getDateStart()));
						
						//on recalcule la suite des créneaux par rapport à ce bloc
						$width = $hourWidth - $left;
						$dateStart +=  $duration * 60 - ($sched->getDateStop() - $cur);
					}
					else { //stop pas dans l'heure
						$width = $hourWidth - $left;
					}
				}
				else if($sched->getDateStop() > $cur AND $sched->getDateStop() <= $cur + $duration * 60){ // le stop est dans l'heure mais pas le start
					$width = round($baseCalcul * ($sched->getDateStop() - $cur));
					
					//on recalcule la suite des créneaux par rapport à ce bloc
					$width = $hourWidth - $left;
					//echo $duration * 60 - ($sched->getDateStop() - $cur);
					$dateStart -=  $duration * 60 - ($sched->getDateStop() - $cur); 
				}
				else if($sched->getDateStop() >= $cur + $duration * 60 AND $sched->getDateStart() <= $cur ){ //le stop et le start ne sont pas dans l'heure mais ils sont au delà et avant !
					$width = $hourWidth;
				}

				if($width != 0){
					$result .= "<div class='selection-part-content-scheduled' style='left: ".$left."px; width: ".$width."px'></div>";
				}
			}
			
			$result .= "</div>
				</div>";
		}
	}
	echo $result;
}
catch (Exception $e)
{
	echo "Forbidden";
	die();
}
?>