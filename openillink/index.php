<?php
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
// 
// Home page : order form
//

require ("includes/config.php");
require ("includes/authip.php");
require ("includes/authcookie.php");
require_once ("includes/connexion.php");

$myhtmltitle = $configname[$lang] . " : nouvelle commande";
$mybodyonload = "document.commande.nom.focus(); remplirauto();";
if (($monaut == "admin")||($monaut == "sadmin")||($monaut == "user")){
    require ("includes/headeradmin.php");
	// Not necessary (23.08.2017) MR
    /*echo "<h1><center>" . __("Document order form to the ") . " <a href=\"" . $configlibraryurl[$lang] . "\" target=\"_blank\">" . $configlibrary[$lang] . "</a></center></h1>\n";*/
	// Not defined (21.07.2017) MR
	/*if (isset($secondmessage)) {
		echo "<h2><center>" . __("") . "</center></h2>\n";
	}*/
    echo "<script type=\"text/javascript\">\n";
    echo "function textchanged(changes) {\n";
    echo "document.fiche.modifs.value = document.fiche.modifs.value + changes + ' - ';\n";
    echo "}\n";
    echo "function ajoutevaleur(champ) {\n";
    echo "var champ2 = champ + 'new';\n";
    echo "var res = document.getElementById(champ).value;\n";
    echo "if (res == 'new')\n";
    echo "{\n";
    echo "document.getElementById(champ2).style.display='inline';\n";
    echo "}\n";
    echo "}\n";
    echo "</script>\n";
    echo "<form action=\"new.php\" method=\"POST\" enctype=\"x-www-form-encoded\" name=\"commande\" onsubmit=\"javascript:okcooc()\">\n";
    // START Management Fields
    echo "<input name=\"table\" type=\"hidden\"  value=\"orders\">\n";
    echo "<input name=\"userid\" type=\"hidden\"  value=\"".htmlspecialchars($monnom)."\">\n";
    echo "<input name=\"bibliotheque\" type=\"hidden\"  value=\"".htmlspecialchars($monbib)."\">\n";
    echo "<input name=\"sid\" type=\"hidden\"  value=\"\">\n";
    echo "<input name=\"pid\" type=\"hidden\"  value=\"\">\n";
    if (!empty($referer))
        echo "<input name=\"referer\" type=\"hidden\" value=\"" . htmlspecialchars(rawurlencode($referer)) . "\">\n";
    else
        echo "<input name=\"referer\" type=\"hidden\" value=\"\">\n";
    echo "<input name=\"action\" type=\"hidden\" value=\"saisie\">\n";
    echo "<input name=\"source\" type=\"hidden\" value=\"adminform\">\n";
    echo "<div class=\"box\"><div class=\"box-content\">\n";
    echo "<table border=\"0\" cellspacing=\"0\" cellpadding=\"3\" width=\"100%\">\n";
    echo "<tr><td colspan=\"4\">\n";
	echo '<label for="stade">';
    echo __("Status") . " * </label>: \n";
    echo "<select name=\"stade\" id=\"stade\">\n";
    $reqstatus="SELECT code, title1, title2, title3, title4, title5 FROM status ORDER BY code ASC";
    $optionsstatus="";
    $resultstatus = dbquery($reqstatus);
    while ($rowstatus = iimysqli_result_fetch_array($resultstatus)){
        $codestatus = $rowstatus["code"];
        $namestatus["fr"] = $rowstatus["title1"];
        $namestatus["en"] = $rowstatus["title2"];
        $namestatus["de"] = $rowstatus["title3"];
        $namestatus["it"] = $rowstatus["title4"];
        $namestatus["es"] = $rowstatus["title5"];
        $optionsstatus.="<option value=\"" . htmlspecialchars($codestatus) . "\"";
        $optionsstatus.=">" . htmlspecialchars($namestatus[$lang]) . "</option>\n";
    }
    echo $optionsstatus;
    echo "</select>\n";
    echo "&nbsp;&nbsp;&nbsp;&nbsp;\n";
	echo '<label for="localisation">';
    echo __("Localization") . "</label> : &nbsp;\n";
    echo "<select name=\"localisation\" id=\"localisation\">\n";
    echo "<option value=\"\"></option>";
    echo "<optgroup label=\"" . __("Our Localizations") . "\">\n";
    $reqlocalisation="SELECT code, library, name1, name2, name3, name4, name5 FROM localizations WHERE library = ? ORDER BY name1 ASC";
    $optionslocalisation="";
    $resultlocalisation = dbquery($reqlocalisation, array($monbib), "s");
    while ($rowlocalisation = iimysqli_result_fetch_array($resultlocalisation)){
        $codelocalisation = $rowlocalisation["code"];
        $namelocalisation["fr"] = $rowlocalisation["name1"];
        $namelocalisation["en"] = $rowlocalisation["name2"];
        $namelocalisation["de"] = $rowlocalisation["name3"];
        $namelocalisation["it"] = $rowlocalisation["name4"];
        $namelocalisation["es"] = $rowlocalisation["name5"];
        $optionslocalisation.="<option value=\"".htmlspecialchars($codelocalisation)."\"";
        $optionslocalisation.=">" . htmlspecialchars($namelocalisation[$lang]) . "</option>\n";
    }
    echo $optionslocalisation;
    // select other libraries
    $reqlocalisationext="SELECT code, name1, name2, name3, name4, name5 FROM libraries WHERE code != ? ORDER BY name1 ASC";
    $optionslocalisationext="";
    $resultlocalisationext = dbquery($reqlocalisationext, array($monbib), "s");
    $nbext = iimysqli_num_rows($resultlocalisationext);
    if ($nbext > 0){
        while ($rowlocalisationext = iimysqli_result_fetch_array($resultlocalisationext)){
            $codelocalisationext = $rowlocalisationext["code"];
            $namelocalisationext["fr"] = $rowlocalisationext["name1"];
            $namelocalisationext["en"] = $rowlocalisationext["name2"];
            $namelocalisationext["de"] = $rowlocalisationext["name3"];
            $namelocalisationext["it"] = $rowlocalisationext["name4"];
            $namelocalisationext["es"] = $rowlocalisationext["name5"];
            $optionslocalisationext.="<option value=\"".htmlspecialchars($codelocalisationext)."\">" . htmlspecialchars($namelocalisationext[$lang]) . "</option>\n";
        }
        echo "<optgroup label=\"" . htmlspecialchars(__("Network libraries")) . "\">\n";
        echo $optionslocalisationext;
    }
    echo "</select>\n";
    echo "</td></tr>";

    echo "<tr><td colspan=\"4\">\n";
	echo '<label for="urgent">';
    echo __("Priority") . "</label> : <select name=\"urgent\" id=\"urgent\">\n";
    echo "<option value=\"2\" selected>" . __("Normal") . "</option>\n";
    echo "<option value=\"1\">" . __("Urgent") . "</option>\n";
    echo "<option value=\"3\">" . __("Not a priority") . "</option>\n";
    echo "</select>\n";
	if ($displayFormOrderSourceField) {
		echo "&nbsp;&nbsp;&nbsp;&nbsp;<label for=\"source\">" . __("Origin of the order") . "</label> : \n";
		echo "<select name=\"source\" id=\"source\" onchange=\"ajoutevaleur('source');\">\n";
		echo "<option value=\"\"> </option>\n";
		$reqsource = "SELECT arrivee FROM orders WHERE arrivee != '' GROUP BY arrivee ORDER BY arrivee ASC";
		$optionssource = "";
		$resultsource = dbquery($reqsource);
		while ($rowsource = iimysqli_result_fetch_array($resultsource)){
			$codesource = $rowsource["arrivee"];
			$optionssource.="<option value=\"".htmlspecialchars($codesource)."\">".htmlspecialchars($codesource)."</option>\n";
		}
		echo $optionssource;
		echo "<option value=\"new\">" . __("Add new value...") . "</option>\n";
		echo "</select>\n";
		echo "&nbsp;<input name=\"sourcenew\" id=\"sourcenew\" type=\"text\" size=\"20\" value=\"\" style=\"display:none\">\n";
	}
    echo "</td></tr><tr><td>\n";
	echo '<label for="datesaisie">';
    echo "<a href=\"#\" title=\"" . __("to be completed only if different from the current date") . "\">" . __("Order date") . "</a></label> : </td><td> \n";
    echo "<input name=\"datesaisie\" id=\"datesaisie\" type=\"text\" size=\"10\" value=\"\" class=\"tcal\">\n";
    echo "</td><td>\n";
	echo '<label for="envoye">';
    echo __("Date of shipment") . "</label> : </td><td>\n";
    echo "<input name=\"envoye\" id=\"envoye\" type=\"text\" size=\"10\" value=\"\" class=\"tcal\">\n";
    echo "</td></tr><tr><td>\n";
	echo '<label for="facture">';
    echo __("Invoice date") . "</label> : </td><td>\n";
    echo "<input name=\"facture\" id=\"facture\" type=\"text\" size=\"10\" value=\"\" class=\"tcal\">\n";
    echo "</td><td>\n";
	echo '<label for="renouveler">';
    echo __("To be renewed on") . "</label> : </td><td>\n";
    echo "<input name=\"renouveler\" id=\"renouveler\" type=\"text\" size=\"10\" value=\"\" class=\"tcal\">\n";
    echo "</td></tr><tr><td colspan=\"4\">\n";
	echo '<label for="prix">';
    echo format_string(__("Price (%currency)"), array('currency' => $currency)) . "</label> : &nbsp;\n";
    echo "<input name=\"prix\" id=\"prix\" type=\"text\" size=\"5\" value=\"\">\n";
    echo "&nbsp;&nbsp;(<input type=\"checkbox\" name=\"avance\" id=\"avance\" value=\"on\" /><label for=\"avance\">" . __("order paid in advance") . "</label>) &nbsp;&nbsp;&nbsp;&nbsp;\n";
    echo "</td></tr><tr><td colspan=\"4\">\n";
	echo '<label for="ref">';
    echo __("Provider Ref.") . "</label> : &nbsp;\n";
    echo "<input name=\"ref\" id=\"ref\" type=\"text\" size=\"20\" value=\"\">&nbsp;&nbsp;&nbsp;\n";
	if ($displayFormInternalRefField) {
		echo '<label for="refinterbib">';
		echo __("Internal ref. to the library") . "</label> : &nbsp;\n";
		echo "<input name=\"refinterbib\" id=\"refinterbib\" type=\"text\" size=\"20\" value=\"\">";
	}
	echo "</td></tr>\n";
	echo "<tr><td valign=\"top\">\n";
	echo '<label for="remarques">';
    echo __("Professional Notes") . "</label> : \n";
    echo "</td><td valign=\"bottom\" colspan=\"3\"><textarea name=\"remarques\" id=\"remarques\" rows=\"2\" cols=\"60\" valign=\"bottom\"></textarea>\n";
    echo "</td></tr>\n";
    echo "</table>\n";
    echo "</div></div>\n";
    echo "<div class=\"box-footer\"><div class=\"box-footer-right\"></div></div>\n";
}
else{
    // display to guest or users not logged in
    if ($monaut == "guest")
        require ("includes/headeradmin.php");
    if ($monaut == "")
        require ("includes/header.php");
    // Not necessary (23.08.2017) MR
	/*echo "<h1><center>" . __("Document order form to the ") . " <a href=\"" . $configlibraryurl[$lang] . "\" target=\"_blank\">" . $configlibrary[$lang] . "</a></center></h1>\n";*/
	// Not defined (21.07.2017) MR
	/*if (isset($secondmessage)) {
		echo "<h2><center>" . __("") . "</center></h2>\n";
	}
	*/
    echo "<div class=\"notification has-text-centered\">\n";
    echo "<b><font color=\"red\">" . __("Please note, all orders are subject to a financial contribution") . "</font></b><br />" . __("Contact us by email for more information (pricing, billing, etc.)") . " : <a href=\"mailto:" . $configlibraryemail[$lang] . "\">" . $configlibraryemail[$lang] . "</a>\n";
    echo "</div>\n";
	
    echo "<form action=\"new.php\" method=\"POST\" enctype=\"x-www-form-encoded\" name=\"commande\" onsubmit=\"javascript:okcooc()\">\n";
    echo "<input name=\"table\" type=\"hidden\" value=\"orders\">\n";
    echo "<input name=\"userid\" type=\"hidden\" value=\"\">\n";
    echo "<input name=\"bibliotheque\" type=\"hidden\" value=\"\">\n";
    echo "<input name=\"sid\" type=\"hidden\" value=\"\">\n";
    echo "<input name=\"pid\" type=\"hidden\" value=\"\">\n";
    if (!empty($referer))
        echo "<input name=\"referer\" type=\"hidden\" value=\"" . htmlspecialchars(rawurlencode($referer)) . "\">\n";
    else
        echo "<input name=\"referer\" type=\"hidden\" value=\"\">\n";
    echo "<input name=\"action\" type=\"hidden\" value=\"saisie\">\n";
    echo "<input name=\"source\" type=\"hidden\" value=\"publicform\">\n";
}
// END Management Fields

// START User Fields
// Display to all users
echo '
<section class="message">
	<div class="message-header">'.__("Personal informations").'</div>
	<div class="message-body">
	
	<div class="field is-horizontal">
      <label class="label field-label is-normal" for="nom">'.__("Name").' *</label>
      <div class="field-body">
       <div class="field">
        <div class="control">
         <input id="nom" name="nom" class="input" type="text" placeholder="'.__("Name").' : e.g. Dupont" required>
        </div>
       </div>
       <div class="field has-addons">
        <div class="control is-expanded">
         <input id="prenom" name="prenom" class="input" type="text" placeholder="' .__("First name") .': e.g. Jean" required>
        </div>
       </div>';
if ($directoryurl1 != "")
	echo " <a href=\"javascript:directory('$directoryurl1')\" title=\"" . __("Search the name in the directory of the hospital") . "\"><span class=\"button is-small\"><i aria-hidden=\"true\" class=\"fa fa-address-book\"></i></span></a>\n";
if ($directoryurl2 != "")
	echo "<a href=\"javascript:directory('$directoryurl2')\" title=\"" . __("Search the name in the directory of the university") . "\"><span class=\"button is-small\"><i aria-hidden=\"true\" class=\"fa fa-university\"></i></span></a>\n";
echo '
      </div>
	  </div>
	
	  <div class="field is-horizontal">
      <label class="label field-label is-normal" for="service">'.__("Unit").' *</label>
      <div class="field-body">
       <div class="field">
        <div class="control">
         <div class="select is-fullwidth">
          <select id="service" name="service" required>
						<option></option>';
$unitsortlang = "name1";
if ($lang == "en")
    $unitsortlang = "name2";
if ($lang == "de")
    $unitsortlang = "name3";
if ($lang == "it")
    $unitsortlang = "name4";
if ($lang == "es")
    $unitsortlang = "name5";
if ($ip1 == 1)
    $requnits="SELECT code, $unitsortlang FROM units WHERE internalip1display = 1 ORDER BY $unitsortlang ASC";
else if ($ip2 == 1)
        $requnits="SELECT code, $unitsortlang FROM units WHERE internalip2display = 1 ORDER BY $unitsortlang ASC";
    else
        $requnits="SELECT code, $unitsortlang FROM units WHERE externalipdisplay = 1 ORDER BY $unitsortlang ASC";
$optionsunits="";
$resultunits = dbquery($requnits);
while ($rowunits = iimysqli_result_fetch_array($resultunits)){
    $codeunits = $rowunits["code"];
    $nameunits = $rowunits[$unitsortlang];
    $optionsunits.="<option value=\"" . htmlspecialchars($codeunits) . "\"";
    $optionsunits.=">" . htmlspecialchars($nameunits) . "</option>\n";
}
echo $optionsunits;
echo '
          </select>
         </div>
        </div>
       </div>
       <div class="field">
        <div class="control">
         <input id="servautre" name="servautre" class="input" type="text" placeholder="'.__("Other unit").'">
        </div>
       </div>
      </div>
	  </div>
	  
	  <div class="field is-horizontal">
      <label class="label field-label is-normal" for="mail">'.__("E-Mail").'</label>
      <div class="field-body">
       <div class="field">
        <div class="control">
         <input id="mail" name="mail" class="input" type="email" placeholder="e.g. jean.dupont@exemple.com">
        </div>
       </div>
       <div class="field">
        <div class="control">
         <input id="tel" class="input" type="tel" placeholder="'.__("Téléphone").': e.g. +41 79 123 45 67" pattern="^((\+\d{1,3}(-| )?\(?\d\)?(-| )?\d{1,5})|(\(?\d{2,6}\)?))(-| )?(\d{3,4})(-| )?(\d{4})(( x| ext)\d{1,5}){0,1}$">
        </div>
       </div>
      </div>
	  </div>
	  
	  <div class="field is-horizontal">
      <label class="label field-label is-normal" for="adresse">'.__("Private address").'</label>
      <div class="field-body">
       <div class="field">
        <div class="control">
         <input id="adresse" name="adresse" class="input" type="text" placeholder="e.g. Rue de Lausanne 2">
        </div>
       </div>
       <div class="field">
        <div class="control">
         <input id="postal" name="postal" class="input" type="text" placeholder="e.g. 1001">
        </div>
       </div>
       <div class="field">
        <div class="control">
         <input id="localite" name="localite" class="input" type="text" placeholder="e.g. Lausanne">
        </div>
       </div>
      </div>
	  </div>
	  
	  <div class="field is-horizontal">
      <label class="label field-label is-normal" for="adresse">'.__("Transmission").'</label>
      <div class="field-body">
       <div class="field">
        <div class="control">
         <label class="radio field-label is-normal">
          <input type="radio" checked id="envoimail" name="envoi" value="mail"> '.__("Send by e-mail (billed)").'</label>
         <label class="radio field-label is-normal">
          <input type="radio" id="envoisurplace" name="envoi" value="surplace"> '.__("Let me know and I come to make a copy (not billed)").'</label>
        </div>
       </div>
      </div>
	  </div>
	  
	  <div class="field is-horizontal">
      <label class="label field-label is-normal" for="adresse"></label>
      <div class="field-body">
       <div class="field">
        <div class="control">
         <label class="checkbox">
          <input id="cooc" name="cooc" type="checkbox" value="on">
          '.__("Remember data for future orders (cookies allowed)").' | (<a href="javascript:coocout()">'. __("delete the cookie") .'</a>)
        </label>
        </div>
       </div>
      </div>
	  </div>
	  
	</div>
</section>';


echo '
	<section class="message">
		<div class="message-header">'.__("Document").'</div>
    <div class="message-body">
		<div class="box">
			
		<div class="field is-horizontal">
       <label class="label field-label is-normal column is-4" for="tid">'. __("Fill in the order using") .'</label>
       <div class="field-body">
        <div class="field has-addons">
         <div class="control">
          <span class="select is-fullwidth">
			<select id="tid" name="tid">';
				foreach($lookupuid as $value) {
echo "<option value=\"" . htmlspecialchars($value["code"]) . "\">" . htmlspecialchars($value["name"]) . "</option>\n";
}
echo'
			</select>
		</span>
         </div>
         <div class="control"><input class="input" name="uids" placeholder="'. __("Identification number") .'" type="text" value=""></div>
         <div class="control"><input class="button is-primary" onclick="lookupid()" type="button" value="'. __("Fill in") .'"></div>
        </div>
       </div>
	   </div>
	   
	   </div> <!-- end .box -->
	   
	   <div class="field is-horizontal">';
if((!empty($doctypesmessage)) && $doctypesmessage[$lang])
	echo '<label class="label field-label is-normal" for="genre">'. $doctypesmessage[$lang] . __("Document type").'</label>';
else
	echo '<label class="label field-label is-normal" for="genre">'.__("Document type").'</label>';

echo '
      <div class="field-body">
       <div class="field">
        <div class="control">
         <div class="select is-fullwidth">
          <select id="genre" name="genre">';
			  foreach($doctypes as $value) {
echo "<option value=\"" . htmlspecialchars($value["code"]) . "\">" . htmlspecialchars($value["name"]) . "</option>\n";
}
echo '
		</select>
         </div>
        </div>
       </div>
      </div>
	  </div>
	   
	  <div class="field is-horizontal">
      <label class="label field-label is-normal" for="atitle">'.__("Article").'</label>
      <div class="field-body">
       <div class="field">
        <div class="control">
         <input class="input" id="atitle" name="atitle" type="text" placeholder="'.__("Title of article or chapter").'">
        </div>
       </div>
      </div>
	  </div>
	  
	  <div class="field is-horizontal">
      <label class="label field-label is-normal" for="genre">Périodique</label>
      <div class="field-body">
       <div class="field">
        <div class="control">
         <input class="input" id="title" name="title" type="text" placeholder="Titre du périodique ou du livre">
        </div>
       </div>
       <a href="javascript:openlist(\''.$periodical_title_search_url.'\')"><span class="button is-small" title="'. __("check on journals database") .'"><i aria-hidden="true" class="fa fa-database"></i></span></a>
      </div>
	  </div>
	  
	  <div class="field is-horizontal">
      <label class="label field-label is-normal" for="date"></label>
      <div class="field-body">
       <div class="field">
        <div class="control">
         <input class="input" id="date" name="date" type="text" placeholder="'.__("Year").'">
        </div>
       </div>
       <div class="field">
        <div class="control">
         <input class="input" id="volume" name="volume" type="text" placeholder="'.__("Volume").'">
        </div>
       </div>
       <div class="field">
        <div class="control">
         <input class="input" id="issue" name="issue" type="text" placeholder="'.__("Issue (No)").'">
        </div>
       </div>
       <div class="field">
        <div class="control">
         <input class="input" id="suppl" name="suppl" type="text" placeholder="'.__("Supplement").'">
        </div>
       </div>
       <div class="field">
        <div class="control">
         <input class="input" id="pages" name="pages" type="text" placeholder="'.__("Pages").'">
        </div>
       </div>
      </div>
	  </div>
	  
	  <div class="field is-horizontal">
      <label class="label field-label is-normal" for="edition"></label>
      <div class="field-body">
       <div class="field">
        <div class="control">
         <input class="input" id="edition" name="edition" type="text" placeholder="'.__("Edition (for books)").'">
        </div>
       </div>
       <div class="field">
        <div class="control">
         <input class="input" id="issn" name="issn" type="text" placeholder="ISSN / ISBN">
        </div>
       </div>
       <div class="field">
        <div class="control">
         <input class="input" id="uid" name="uid" type="text" placeholder="UID">
        </div>
       </div>
      </div>
	  </div>
	  
	  <div class="field is-horizontal">
      <label class="label field-label is-normal" for="remarquespub">'.__("Notes").'</label>
      <div class="field-body">
       <div class="field">
        <div class="control">
         <textarea id="remarquespub" name="remarquespub" class="textarea" placeholder="" rows="2"></textarea>
        </div>
       </div>
      </div>
	  </div>
	  
	  <div class="field is-grouped">
      <input type="submit" class="button is-primary" value="'. __("Submit") .'" onsubmit="javascript:okcooc();document.body.style.cursor = \'wait\';" />
      <input type="reset" value="'. __("Reset") .'" class="button is-link" />
	  </div>
	
	</div>
	</section>';
	
echo "</form>\n";

require ("includes/footer.php");
?>
