<?php
defined("_nova_district_token_") or die('');
?>
<div class="gestion-menu">
	<ul class="menu_horizontal">
		<li><a href="index.php?p=gestion">Général</a></li>
		<li><a href="index.php?p=gestion&m=block">Blocages de créneaux</a></li>
		<li><a href="index.php?p=gestion&m=config">Configuration</a></li>
		<li><a href="index.php?p=booking&doctor=<?php echo $_SESSION['user']->getIdMember(); ?>">Ma page publique</a></li>
	</ul>
</div>