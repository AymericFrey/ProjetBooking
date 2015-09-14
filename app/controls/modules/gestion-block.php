<?php
defined("_nova_district_token_") or die('');

if(isset($_POST['form-block']))
{
	if(isset($_POST['ddebut']) AND isset($_POST['hdebut']) AND isset($_POST['dfin']) AND isset($_POST['hfin']) AND isset($_POST['infos']))
	{
		if(Tools::checkDate($_POST['ddebut']) AND Tools::checkDate($_POST['dfin']) AND Tools::checkTime($_POST['hdebut']) AND Tools::checkTime($_POST['hfin']) )
		{	
			if(isset($_POST['everyweek']))
			{
				if(isset($_POST['numberweeks']))
				{
					if($_POST['numberweeks']<1 OR $_POST['numberweeks']>52)
					{
						$errors['blocage-horaires'] = new Error("Le nombre de semaine pour la récursivité doit être compris entre 1 et 52");	
					}
					else
					{
						for($i=0;$i<$_POST['numberweeks'];$i++)
						{
							$block = new Schedule();

							$datedebut = strtotime($_POST['ddebut']." ".$_POST['hdebut'].":0");
							$datefin = strtotime($_POST['dfin']." ".$_POST['hfin'].":0");
							
							//Ajout de i semaine (i x 7 jours x 24 heures x 3600 secondes) à la date de début
							$block->setDateStart($datedebut+(24*3600*7*$i));	
						
							//Ajout de i semaine (i x 7 jours x 24 heures x 3600 secondes) à la date de fin
							$block->setDateStop($datefin+(24*3600*7*$i));	
						
							$block->setNote($_POST['infos']);
							$block->setRecursion('7');
							$block->setIdDoctor($_SESSION['user']->getIdMember());

							$errors['blocage-horaires'] = SchedulesManager::instance()->addBlock($block);
						}
					}
				}
				else
					$errors['blocage-horaires'] = new Error("Veuillez indiquer le nombre de semaines pour la récursivité");
			}
			else
			{
				$block = new Schedule();

				$block->setDateStart(strtotime($_POST['ddebut']." ".$_POST['hdebut'].":0"));
				$block->setDateStop(strtotime($_POST['dfin']." ".$_POST['hfin'].":0"));
				$block->setNote($_POST['infos']);
				$block->setRecursion('0');
				$block->setIdDoctor($_SESSION['user']->getIdMember());

				$errors['blocage-horaires'] = SchedulesManager::instance()->addBlock($block);
			}
		}
		else
			$errors['blocage-horaires']= new Error("Veuillez remplir tous les champs correctement");
	}
	else
		$errors['blocage-horaires']= new Error("Veuillez remplir tous les champs correctement");

}

//Suppresion d'un blocage
if(isset($_GET['del']) AND isset($_GET['id']))
{
	if($_GET['del']=="delete")
		//SchedulesManager::instance()->deleteBlock($_GET['id']);
		SchedulesManager::instance()->deleteBlock($_SESSION['user']->getIdMember(),$_GET['id']);
}

// Récupération des blocages récursifs
$recursiveblocks = SchedulesManager::instance()->getRecursiveSchedule($_SESSION['user']->getIdMember());

// Récupération des blocages non récursifs
$blocks = SchedulesManager::instance()->getRecursiveSchedule($_SESSION['user']->getIdMember(),0);

//vue
include(dirname(__FILE__).'/../../views/modules/gestion-block.php');
?>