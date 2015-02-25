<?php

/**
* Clé secrète fournie par l'équipe frankiz lors de l'inscription du site.
* Cette clé sert à signer les requêtes et à authentifier le site.
*/
$FKZ_KEY = "385jtXeJ";
   global $FKZ_KEY;
  /**
   * Prendre le timestamp permet d'éviter le rejet de la requête
   */
  $timestamp = time();
  /**
   * url de la page de login, doit correspondre *exactement* à celle entrée dans
   * la base de données de Frankiz (définie lors de l'inscription)
   */
  $site = 'http://www.xprojets.com/platal/login';
  /**
   * Champ non utile pour l'authentification et retransmis tel quel par frankiz. 
   * Il est prévu pour pouvoir mettre en place un système de redirection après 
   * authentification, vers la page à partir de laquelle le client avait tenté de se connecter.
   */
  $location  = "..." ;
  /**
   * Nature de la requête.
   * Fkz renverra ici à la fois les noms de la personne mais aussi ses droits dans différents groupes.
   * Il faut cependant que le site ait les droits sur les informations en question (à définir lors de son inscription).
   */
  $request = json_encode(array('names', 'email', 'promo'));
  
  $hash = md5($timestamp . $site . $FKZ_KEY . $request);

  $remote  = 'https://www.frankiz.net/remote?timestamp=' . $timestamp .
      '&site=' . $site .
      '&location=' . $location .
      '&hash=' . $hash .
      '&request=' . $request;
   header("Location:" . $remote);
   exit();

