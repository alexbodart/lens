<?php
session_start();

	include_once "libs/maLibUtils.php";
	include_once "libs/maLibSQL.pdo.php";
	include_once "libs/maLibSecurisation.php"; 
	include_once "libs/modele.php"; 

	$addArgs = "";

	if ($action = valider("action"))
	{
		ob_start ();
		echo "Action = '$action' <br />";
		// ATTENTION : le codage des caractères peut poser PB si on utilise des actions comportant des accents... 
		// A EVITER si on ne maitrise pas ce type de problématiques

		/* TODO: A REVOIR !!
		// Dans tous les cas, il faut etre logue... 
		// Sauf si on veut se connecter (action == Connexion)

		if ($action != "Connexion") 
			securiser("login");
		*/

		// Un paramètre action a été soumis, on fait le boulot...
		switch($action)
		{
			
			
			// Connexion //////////////////////////////////////////////////
			case 'Connexion' :
				// On verifie la presence des champs login et passe
				if ($login = valider("login"))
				if ($passe = valider("passe"))
				{
					$passe=md5($passe);
					// On verifie l'utilisateur, 
					// et on crée des variables de session si tout est OK
					// Cf. maLibSecurisation
					if (verifUser($login,$passe)) {
						// tout s'est bien passé, doit-on se souvenir de la personne ? 
						if (valider("remember")) {
							setcookie("login",$login , time()+60*60*24*30);
							setcookie("passe",$password, time()+60*60*24*30);
							setcookie("remember",true, time()+60*60*24*30);
						} else {
							setcookie("login","", time()-3600);
							setcookie("passe","", time()-3600);
							setcookie("remember",false, time()-3600);
						}
					$addArgs="?view=accueil";
					}
					else $addArgs="?view=login&error=1"; 	
				}
				// On redirigera vers la page index automatiquement

			break;
			case 'jouer' :
				$joueur=$_SESSION['id'];
				if($sncf = valider("sncf"))
				{
					if ($_POST['sncf'] == 'blanche') {
						AjouterScore($joueur,'sncf',1);
						$addArgs ="?view=jeu&succes=1";
					}
					else {
						AjouterScore($joueur,'sncf',0);
						$addArgs ="?view=jeu&succes=0";
					}
				}
				
			break;

			case 'Logout' :
				session_destroy();
				$addArgs = "?view=accueil";
			break;

			case 'signIn' :
			if ($nom = valider("nom"))
				if ($prenom = valider("prenom"))
					if ($login = valider("pseudo"))
						if ($passe = valider("password"))
							if ($passe2 = valider("password_confirm"))
							{
								if($passe==$passe2)
								{
									// TODO verifier que le pseudo n'est pas utilisé 
									$passeCrypt=md5($passe);
									AjouterUtilisateur($nom,$prenom,$login,$passeCrypt);
									$addArgs = "?view=login&success=1";
								}
								else{
				    					$addArgs = "?view=sub&error=1";
									}
									
							}
							else
							{
									$addArgs = "?view=sub&error=mdpdifferents";
							}			
			break;

		}

	}

	// On redirige toujours vers la page index, mais on ne connait pas le répertoire de base
	// On l'extrait donc du chemin du script courant : $_SERVER["PHP_SELF"]
	// Par exemple, si $_SERVER["PHP_SELF"] vaut /chat/data.php, dirname($_SERVER["PHP_SELF"]) contient /chat

	$urlBase = dirname($_SERVER["PHP_SELF"]) . "/index.php";
	// On redirige vers la page index avec les bons arguments

	header("Location:" . $urlBase . $addArgs);

	// On écrit seulement après cette entête
	ob_end_flush();
	
?>










