<?php
defined("_nova_district_token_") or die('');

/**
* @class	Calendar
* @brief	Singleton qui manipule les dates et l'emploi du temps
*/
class Calendar
{ 
	/** 
	* @brief	Instance unique de Calendar
	* @var		$instance
	*/
	private static $instance = null;
	
	
	/** 
	* @brief	Retourne l'instance de la classe et permet d'instancier un Calendar si c'est le premier appel.
	* @return	Calendar		Retourne l'instance de la classe
	*/
	public static function instance()
	{
		if(self::$instance == null)
			self::$instance = new Calendar();
		return self::$instance;
	}
	
	/** 
	* @brief	Liste des jours dans l'ordre des Dates PHP
	* @var		$days
	*/
	private $days = array('Dimanche', 'Lundi', 'Mardi', 'Mercredi', 'Jeudi', 'Vendredi', 'Samedi');
	
	/** 
	* @brief	Liste des mois
	* @var		$months
	*/
	private $months = array('Janvier', 'Février', 'Mars', 'Avril', 'Mai', 'Juin', 'Juillet', 'Août', 'Septembre', 'Octobre', 'Novembre', 'Décembre');
	
	/** 
	* @brief	Nombre de jours à afficher sur l'emploi du temps
	* @var		$nbrDays
	*/
	private $nbrDays = 10;
	
	/** 
	* @brief	Largeur en pixel de la zone horaire de l'emploi du temps
	* @var		$width
	*/
	private $width = 966; 

	
	/** 
	* @brief	Permet de générer un interval de dates espacées d'un jour entre une date de départ et un nombre de jours
	* @param	DateTime			$fromDate		Date de départ
	* @param	int					$length			Nombre de jours à générer
	* @return	Array[DateTime]		Liste de dates
	*/
	public function getInterval($fromDate, $length)
	{
		$results = array();
		
		for($i = 0; $i < $length; $i++){
			$results[] = clone $fromDate;
			$fromDate->add(new DateInterval('P1D'));
		}
		return $results;
	}
	
	/** 
	* @brief	Permet de générer le code HTML d'un emploi du temps depuis une date de départ pour un praticien en particulier
	* @param	DateTime	$dateStart		Date de départ
	* @param	Doctor		$doctor			Praticien
	* @return	String		Code HTML
	*/
	public function getDoctorCalendar($dateStart, $doctor){
		//var générales
		$dateStart->setTime($doctor->getStartHour("h"), 0, 0);
		$fromDate = $dateStart->getTimestamp();
		$toDate = $dateStart->getTimestamp() + $this->nbrDays * 24 * 3600;
		$roundStart = true; //on commence à une heure PILE
		$roundEnd = true; //on fini à une heure PILE
		
		if($doctor->getStartHour("m") > 0)
			$roundStart = false;
		if($doctor->getEndHour("m") > 0)
			$roundEnd = false;
		
		//init
		$days = $this->getInterval($dateStart, $this->nbrDays); //tableau de DateTime
		$rdv = SchedulesManager::instance()->getSchedule($doctor->getIdMember(), $fromDate, $toDate);
		$rdv2 = SchedulesManager::instance()->getBlockSchedule($doctor->getIdMember(), $fromDate, $toDate);
		$rdv = array_merge($rdv, $rdv2);


		//vars de boucle
		$hours = "";
		$result = "";
		$nbrHours = ceil($doctor->getWorkTime() / 60.0);
		$hoursToHTML = $doctor->getWorkTime() * 60;
		
		//on corrige si endHour Minute < start hour !
		if($doctor->getStartHour('m') > 0){
			if($doctor->getEndHour('m') <= $doctor->getStartHour('m'))
				$nbrHours++;
		}
		
		
		$firstHourOfFirstDay = 0; // heure du premier jour de la quinzaine stocké en INT
		$hourWidth = floor(($this->width - $nbrHours) / $nbrHours);//px (on retire le border qui ajoute 1px)
		$d = 0;

		//début du draw
		$result .= "<div class='calendar-info-hours'>
						<div class='calendar-info-hour calendar-info-hour-first'></div>";
		for($i = 0; $i < $nbrHours; $i++){
			$result .= "<div class='calendar-info-hour' style='width: ".$hourWidth."px'>".($doctor->getStartHour('h') + $i)."h</div>";
		}
		$result .= "</div>";

		foreach($days as $day){ //chaque jours de la quinzaine récupérée
			if($firstHourOfFirstDay == 0) 
				$firstHourOfFirstDay = $day->getTimestamp();

			$dayName = $this->days[$day->format('w')];
			$dayNbr = $day->format('d');
			$result .= "<div class='calendar-item'>
							<div class='calendar-day'>
								<div>
									".$dayName." ".$dayNbr."
								</div>
							</div>
							<div class='calendar-hours' data-calendar-startHour='".(($firstHourOfFirstDay + $doctor->getStartHour('m') * 60) + $d * 3600 * 24)."'  data-calendar-endHour='".(($firstHourOfFirstDay + $doctor->getStartHour('m') * 60) + $d * 3600 * 24 + $hoursToHTML)."'>";
							
			for($i = 0; $i < $nbrHours; $i++){
				$cur = $firstHourOfFirstDay + $i * 3600 + $d * 3600 * 24;
				$result .= "<div class='calendar-hour' style='width: ".$hourWidth."px' data-calendar-day='".$cur."' data-calendar-hour='".($doctor->getStartHour('h') + $i)."'>";
				
				$baseCalcul = $hourWidth / 3600;
				$left = 0;
				$width = 0;
				
				if($i == 0 && $roundStart == false){ //on a un départ décalé
					$width = round($baseCalcul * ($doctor->getStartHour('m') * 60));
					$result .= "<div class='calendar-rdv calendar-block' style='left: ".$left."px; width: ".$width."px'></div>";
				}
				if($i == $nbrHours - 1 && $roundEnd == false){ //on a un départ décalé
					$left = round($baseCalcul * ($doctor->getEndHour('m') * 60));
					$width = $hourWidth - $left;
					$result .= "<div class='calendar-rdv calendar-block' style='left: ".$left."px; width: ".$width."px'></div>";
				}
				
				
				foreach($rdv as $sched){
					$left = 0;
					$width = 0;
				
					if($sched->getDateStart() >= $cur AND $sched->getDateStart() <= $cur + 3600){ //le start est dans l'heure
						if($sched->getDateStop() >= $cur AND $sched->getDateStop() <= $cur + 3600){ //stop dans l'heure AUSSI
							$left = round($baseCalcul * ($sched->getDateStart() - $cur));
							$width = round($baseCalcul * ($sched->getDateStop() - $sched->getDateStart()));
						}
						else { //stop pas dans l'heure
							$left = round($baseCalcul * ($sched->getDateStart() - $cur));
							$width = $hourWidth - $left;
						}
					}
					else if($sched->getDateStop() >= $cur AND $sched->getDateStop() <= $cur + 3600){ // le stop est dans l'heure mais pas le start
						$width = round($baseCalcul * ($sched->getDateStop() - $cur));
					}
					else if($sched->getDateStop() >= $cur + 3600 AND $sched->getDateStart() <= $cur ){ //le stop et le start ne sont pas dans l'heure mais ils sont au delà et avant !
						$width = $hourWidth;
					}
					
					if($width != 0){ //si on doit dessiner un rdv/bloquage
						$class = "calendar-reserved";
						$tmp = $sched->getIdMember();
						if(!isset($tmp))
							$class = "calendar-block";
						
						$result .= "<div class='calendar-rdv ".$class."' style='left: ".$left."px; width: ".$width."px'></div>";
					}
				}
				$result .= "</div>";
			}
			
			$result .= "</div></div>";
			$d++;
		}

		return $result;
	}
	
	// getters
	public function getDays()
	{
		return $this->days;
	}
	
	public function getMonths()
	{
		return $this->months;
	}
}
?>