﻿<?php
// ***************************************************************************
// ***************************************************************************
// ***************************************************************************
// This file is part of OpenILLink software.
// Copyright (C) 2005, 2006, 2007, 2008, 2009, 2010, 2011, 2012, 2013, 2015, 2016, 2017 CHUV.
// Original author(s): Pablo Iriarte <pablo@iriarte.ch>
// Other contributors are listed in the AUTHORS file at the top-level
// directory of this distribution.
// 
// OpenILLink is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
// 
// OpenILLink is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
// 
// You should have received a copy of the GNU General Public License
// along with OpenILLink.  If not, see <http://www.gnu.org/licenses/>.
// 
// ***************************************************************************
// ***************************************************************************
// ***************************************************************************
// Order details only for administrators
//

require_once ("connexion.php");
require_once ("includes/toolkit.php");

if (($monaut == "admin")||($monaut == "sadmin")||($monaut == "user")){
    $id= ((!empty($_GET['id'])) && isValidInput($_GET['id'],8,'s',false)) ? $_GET['id'] : NULL;
    $myhtmltitle = "Commandes de " . $configinstitution[$lang] . " : détail de la commande " . $id;
    if ($id){

		$codeSpecial = array();
		$codeIn = array();
		$codeOut = array();
		$codeTrash = array();
		$statusInfo = readStatus($codeIn, $codeOut, $codeTrash, $codeSpecial);
		$sharedLibrariesArray = getSharingLibrariesForBib($monbib);
		$locListArray = getLibraryLocalizationCodes($monbib);
		$servListArray = getLibraryUnitCodes($monbib);
		$library_signature = getLibrarySignature($monbib);

        $req = "SELECT orders.*, status.title1 AS statusname, status.help1 AS statushelp, status.special AS statusspecial, status.color AS statuscolor, libraries.name1 AS libname, localizations.name1 AS locname, units.name1 AS unitname ".
        "FROM orders LEFT JOIN status ON orders.stade = status.code LEFT JOIN libraries ON orders.bibliotheque = libraries.code LEFT JOIN localizations ON orders.localisation = localizations.code LEFT JOIN units ON orders.service = units.code ".
        "WHERE orders.illinkid LIKE ? GROUP BY orders.illinkid ORDER BY orders.illinkid DESC";
        $result = dbquery($req,array($id), 'i');
        $nb = iimysqli_num_rows($result);
        require ("headeradmin.php");
        require ("email.php");
        for ($i=0 ; $i<$nb ; $i++){
            $enreg = iimysqli_result_fetch_array($result);
            $id = $enreg['illinkid'];
            $date = $enreg['date'];
            $stade = $enreg['stade'];
            $localisation = $enreg['localisation'];
            $nom = $enreg['nom'].', '.$enreg['prenom'];
            $mail = $enreg['mail'];
            $locname = $enreg['locname'];
            $unitname = $enreg['unitname'];
            $statusname = $enreg['statusname'];
            $statushelp = $enreg['statushelp'];
            $statusspecial = $enreg['statusspecial'];
            $statuscolor = $enreg['statuscolor'];
            $libname = $enreg['libname'];
            $libcode = $enreg['bibliotheque'];

			$is_my_bib = ($monbib == $enreg['bibliotheque']);
			$is_my_service = (in_array($enreg['service'], $servListArray));
			$is_my_localisation = (in_array($localisation, $locListArray));
			$is_shared = ((!empty($enreg['bibliotheque'])) && in_array($enreg['bibliotheque'], $sharedLibrariesArray) && empty($localisation) && in_array($stade, $codeSpecial['new']));

            if ($mail){
                $pos1 = strpos($mail,';');
                $pos2 = strpos($mail,',');
                $pos3 = strpos($mail,' ');
                if (($pos1 === false)&&($pos2 === false)&&($pos3 === false)){
                    $maillog = strtolower($mail);
                }
                else{
                    if (($pos1 != false)&&($pos2 != false)&&($pos3 != false))
                        $pos = min($pos1, $pos2, $pos3);
                    else if (($pos1 != false)&&($pos2 != false))
                            $pos = min($pos1, $pos2);
                        else if (($pos1 != false)&&($pos3 != false))
                                $pos = min($pos1, $pos3);
                            else if (($pos2 != false)&&($pos3 != false))
                                    $pos = min($pos2, $pos3);
                                else if ($pos1 != false)
                                        $pos = $pos1;
                                    else if ($pos2 != false)
                                            $pos = $pos2;
                                        else if ($pos3 != false)
                                            $pos = $pos3;
                    $maillog = substr($mail,0,$pos);
                    $maillog = strtolower($maillog);
                }
                $mailg = $maillog . $secure_string_guest_login;
                $passwordg = substr(md5($mailg), 0, 8);
            }
            $adresse = $enreg['adresse'].', '.$enreg['code_postal'].' '.$enreg['localite'];
            $titreper = $enreg['titre_periodique'];
            $titreart = $enreg['titre_article'];
            echo "<div class=\"box\"><div class=\"box-content\">\n";
            echo "<table border=\"0\" cellpadding=\"0\" cellspacing=\"0\" width=\"100%\">\n";
            echo "<tr><td valign=\"top\" width=\"74%\">\n";
            echo "<b>Commande no : </b>".$id;
            if ($enreg['urgent']=='1' || $enreg['urgent']=='oui')
                echo " (<b><font color=\"red\">Commande URGENTE</font></b>)\n";
            if ($enreg['urgent']=='3' || $enreg['urgent']=='non')
                echo " (<font color=\"SteelBlue\">Commande pas prioritaire</font>)\n";
			if ($is_shared){
				echo '<span class="isSharedOrder">Commande entrante partagée</span>';
			}
            if (($enreg['type_doc']!='article') && ($enreg['type_doc']!='Article'))
                echo "&nbsp;&nbsp;&nbsp;<img src=\"img/book.png\">";
            echo "<br /><b>Date de la commande : </b>".$date;
            if ($enreg['envoye']>0)
                echo "\n<br /><b>". __("Date of shipment") ." : </b>".htmlspecialchars($enreg['envoye']);
            if ($enreg['facture']>0)
                echo "\n<br /><b>Date de facturation : </b>".htmlspecialchars($enreg['facture']);
            if ($enreg['renouveler']>0)
                echo "\n<br /><b>Date de renouvellement : </b>".htmlspecialchars($enreg['renouveler']);
            echo "\n<br /><b>Bibliothèque d'attribution : </b>";
			if (!$is_my_bib) {
				echo '<span class="notMyBib">'. htmlspecialchars($libname) . " (". htmlspecialchars($libcode).")" . '</span>';
			} else {
				echo htmlspecialchars($libname) . " (". htmlspecialchars($libcode).")";
			}
            if ($localisation) {
				echo "\n<br /><b>Localisation : </b>" ;
				if (!$is_my_localisation) {
					echo '<span class="notMyLocalisation">'.htmlspecialchars($locname) . " (" . htmlspecialchars($localisation) . ")".'</span>';
				} else {
					echo htmlspecialchars($locname) . " (" . htmlspecialchars($localisation) . ")";
				}
			}
            echo "<br /><b>Statut : \n";
            echo "<a href=\"#\" onclick=\"return false\" class=\"statusLink\" title=\"".htmlspecialchars($statushelp)."\"><font color=\"".htmlspecialchars($statuscolor)."\">".htmlspecialchars($statusname)."</font></a></b>";
            if ($statusspecial == "renew"){
                if ($enreg['renouveler'])
                    echo " le ".htmlspecialchars($enreg['renouveler']);
            }
            echo "<br /><b>Lecteur : </b><a href=\"list.php?folder=search&champ=nom&term=".htmlspecialchars(urlencode ($nom))."\" title=\"chercher les commandes de ce lecteur\">\n";
            echo htmlspecialchars($nom)."</a>\n";
            // formated e-mails
            if ($mail){
                echo "<br /><b>E-mail : </b><a href=\"list.php?folder=search&champ=email&term=".htmlspecialchars(urlencode($mail))."\" title=\"chercher les commandes pour cet email\">".htmlspecialchars($mail)."</a>\n";
                $monhost = "http://" . $_SERVER['SERVER_NAME'];
                $monuri = $monhost . rtrim(dirname($_SERVER['PHP_SELF']), '/\\') . "/";
                displayMailText($monaut, $monuri, $enreg, $emailTxt, $titreart, $titreper, $nom, $maillog, $passwordg, $mail, $library_signature);
            }
            if ($enreg['adresse'])
                echo "<br /><b>Adresse : </b>".htmlspecialchars($adresse);
            if ($enreg['service']) {
				$service_class = "";
				if (!$is_my_service) {
					$service_class = ' class="notMyService" ';
				}
                echo "<br /><b>Service : </b><a ".$service_class."href=\"list.php?folder=search&champ=service&term=".htmlspecialchars(urlencode($enreg['service']))."\" title=\"chercher les commandes de ce service\">".htmlspecialchars($enreg['service'])."</a>\n";
            }
			if ($enreg['type_doc'])
                echo "<br /><b>Type de document : </b>".htmlspecialchars($enreg['type_doc']);
            echo "<br />\n";
            if ($enreg['titre_article'])
                echo "<b>Titre : </b><a href=\"list.php?folder=search&champ=atitle&term=".htmlspecialchars(urlencode ($enreg['titre_article']))."\" title=\"chercher les commandes pour ce titre\">".htmlspecialchars($enreg['titre_article'])."</a><br />\n";
            if ($enreg['auteurs'])
                echo "<b>Auteur(s) : </b>".htmlspecialchars($enreg['auteurs'])."<br />\n";
            if ($enreg['titre_periodique']){
                if (($enreg['type_doc']=='article') || ($enreg['type_doc']=='Article'))
                    echo "<b>P&eacute;riodique : </b>\n";
                if ($enreg['type_doc']=='journal')
                    echo "<b>P&eacute;riodique : </b>\n";
                if (($enreg['type_doc']=='Livre') || ($enreg['type_doc']=='book'))
                    echo "<b>Livre : </b>\n";
                if (($enreg['type_doc']=='thesis') || ($enreg['type_doc']=='These') || ($enreg['type_doc']=='Thèse'))
                    echo "<b>Th&egrave;se : </b>\n";
                if (($enreg['type_doc']=='Chapitre') || ($enreg['type_doc']=='preprint') || ($enreg['type_doc']=='bookitem'))
                    echo "<b>In : </b>\n";
                if (($enreg['type_doc']=='autre') || ($enreg['type_doc']=='Autre') || ($enreg['type_doc']=='other'))
                    echo "<b>In : </b>\n";
                if (($enreg['type_doc']=='Congres') || ($enreg['type_doc']=='proceeding') || ($enreg['type_doc']=='conference'))
                    echo "<b>In : </b>\n";
                echo "</b><a href=\"list.php?folder=search&champ=title&term=".htmlspecialchars(urlencode ($enreg['titre_periodique']))."\" title=\"chercher les commandes pour ce titre\">".htmlspecialchars($enreg['titre_periodique'])."</a>\n";
            }
            if ($enreg['volume'])
                echo "<br /><b>Volume : </b>".htmlspecialchars($enreg['volume']);
            if ($enreg['numero'])
                echo "\n<br /><b>Issue : </b>".htmlspecialchars($enreg['numero']);
            if ($enreg['supplement'])
                echo "\n<br /><b>Suppl. : </b>".htmlspecialchars($enreg['supplement']);
            if ($enreg['pages'])
                echo "\n<br /><b>Pages : </b>".htmlspecialchars($enreg['pages']);
            if ($enreg['annee'])
                echo "\n<br /><b>Ann&eacute;e : </b>".htmlspecialchars($enreg['annee']);
            if ($enreg['issn'])
                echo "\n<br /><b>ISSN : </b>".htmlspecialchars($enreg['issn']);
            if ($enreg['eissn'])
                echo "\n<br /><b>eISSN : </b>".htmlspecialchars($enreg['eissn']);
            if ($enreg['isbn'])
                echo "\n<br /><b>ISBN : </b>".htmlspecialchars($enreg['isbn']);
            if ($enreg['PMID'])
                echo "\n<br /><b>PMID : </b><a href=\"https://www.ncbi.nlm.nih.gov/entrez/query.fcgi?otool=ichuvlib&cmd=Retrieve&db=pubmed&dopt=citation&list_uids=".htmlspecialchars(urlencode ($enreg['PMID']))."\" target=\"_blank\">".htmlspecialchars($enreg['PMID'])."</a>\n";
            if ($enreg['uid']){
				if (substr($enreg['uid'], 0, 4) === "MMS:" && $configMMSdiscoveryurl[$lang] != "") {
					echo '\n<br /><b>Autre identificateur : </b><a target="_blank" href="' . str_replace("{MMS_ID}", htmlspecialchars(urlencode(substr($enreg['uid'], 4))), $configMMSdiscoveryurl[$lang]). '">' . htmlspecialchars($enreg['uid']) . '</a>';
				} else {
					echo "\n<br /><b>Autre identificateur : </b>".htmlspecialchars($enreg['uid']);
				}
			}
            if ($enreg['cgra'])
                echo "\n<br /><b>Code de gestion A : </b>".htmlspecialchars($enreg['cgra']);
            if ($enreg['cgrb'])
                echo "\n<br /><b>Code de gestion B : </b>".htmlspecialchars($enreg['cgrb']);
            if ($enreg['tel'])
                echo "\n<br /><b>No t&eacute;l. : </b>".htmlspecialchars($enreg['tel']);
            if ($enreg['saisie_par'])
                echo "\n<br /><b>Saisie par : </b>".htmlspecialchars($enreg['saisie_par']);
            if ($enreg['ip'])
                echo "\n<br /><b>Adresse IP : </b>".htmlspecialchars($enreg['ip']);
            if ($enreg['referer'])
                echo "\n<br /><b>URL de provenance : </b>".htmlspecialchars(rawurldecode($enreg['referer']));
            if ($enreg['arrivee'])
                echo "\n<br /><b>Arriv&eacute;e par : </b>".htmlspecialchars($enreg['arrivee']);
            if ($enreg['envoi_par'])
                echo "\n<br /><b>Envoyer par : </b>";
            if ($enreg['envoi_par'] == 'surplace')
                echo "<b><font color=\"red\">Avertir le lecteur si disponible sur place</font></b>";
            else
                echo htmlspecialchars($enreg['envoi_par']);
            if ($enreg['prix'])
                echo "\n<br /><b>Prix : </b>".htmlspecialchars($enreg['prix']);
            if ($enreg['prepaye'])
                echo "\n<br /><b><font color=\"green\">Payé à l'avance : </b>".htmlspecialchars(strtr($enreg['prepaye'], "on", "OK"))." </font>";
            if ($enreg['ref'])
                echo "\n<br /><b>Réf. fournisseur : </b>".htmlspecialchars($enreg['ref']);
            if ($enreg['refinterbib'])
                echo "\n<br /><b>Réf. interne à la bibliothèque : </b>".htmlspecialchars($enreg['refinterbib']);
            if ($mail)
                echo "\n<br /><b>Code accès guest : </b> Username: ".htmlspecialchars($maillog)." | Password: ".htmlspecialchars($passwordg);
            if ($enreg['remarquespub'])
                echo "\n<br /><b>Commentaire public : </b>".nl2br(htmlspecialchars($enreg['remarquespub']));
            if ($enreg['remarques'])
                echo "\n<br /><b>Commentaire professionnel : </b>".nl2br(htmlspecialchars($enreg['remarques']));
            echo "\n<br /><br /><b>Historique de la commande : </b>\n<br />".str_replace('&lt;br /&gt;', '<br />', htmlspecialchars($enreg['historique']));
            echo "</td>\n";
            echo "<td valign=\"top\" width=\"26%\">\n";
            require ("links.php");
            echo "</td></tr></table>\n";
        }
        echo "</div></div><div class=\"box-footer\"><div class=\"box-footer-right\"></div></div>\n";
        require ("footer.php");
        }
    else{
        require ("header.php");
        require ("loginfail.php");
        require ("footer.php");
    }
}
else{
    require ("header.php");
    require ("loginfail.php");
    require ("footer.php");
}
?>
