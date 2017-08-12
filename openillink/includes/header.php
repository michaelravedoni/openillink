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
// Headers common to all pages
//

header ('Content-type: text/html; charset=utf-8');
$debugOn = false;
error_reporting(-1);
ini_set('display_errors', 'On');

echo "<!DOCTYPE html>\n";
echo "<html lang=\"" . $lang . "\">\n";
echo "<head>\n";
echo "<meta charset=\"utf-8\">\n";
echo "<meta name=\"viewport\" content=\"width=device-width, initial-scale=1\">\n";

echo "<title>";
if (!empty($myhtmltitle))
    echo $myhtmltitle;
else
    echo "OpenILLink";
echo "</title>\n";
echo "\n";

echo '<link rel="home" href="'.$configSiteUrl.'" />' ;

echo '
<link rel="stylesheet" href="'.$configSiteUrl.'/css/bulma.min.css">
<link rel="stylesheet" href="'.$configSiteUrl.'/css/bulma-style.css">
<link rel="stylesheet" media="print" href="'.$configSiteUrl.'/css/print.css">
<link rel="stylesheet" href="'.$configSiteUrl.'/css/awesome/css/font-awesome.min.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">';

echo '
<script type="text/javascript" src="https://code.jquery.com/jquery-3.1.0.min.js"></script>
<script type="text/javascript" src="'.$configSiteUrl.'/js/bulma.js"></script>
<script type="text/javascript" src="'.$configSiteUrl.'/js/script.js"></script>
';

echo "</head>\n";
if (empty($mybodyonload)){
  $mybodyonload = '';
}
echo "<body onload=\"" . $mybodyonload . "\">\n";

echo '
<nav class="navbar has-shadow">
	<div class="container">
		<div class="navbar-brand">
			<a class="navbar-item" href="'.$configSiteUrl.'"><span class="title is-3">'.$openIllinkOfficialTitle[$lang].'</span></a>
			<div class="navbar-burger burger" data-target="navMenu">
				<span></span>
				<span></span>
				<span></span>
			</div>
		</div>
		
		<div id="navMenu" class="navbar-menu">
			<div class="navbar-start">
			</div>
			<div class="navbar-end">
				<a class="navbar-item is-tab" href="' .$atozlinkurl[$lang]. '" title="' . $atozname[$lang] . '"><span class="icon"><i class="fa fa-compass"></i></span></a>
				<div class="navbar-item has-dropdown is-hoverable">
					<a class="navbar-link">'.strtoupper($lang).'</a>
					<div class="navbar-dropdown">
						<a class="navbar-item" href="'.$configSiteUrl.'?lang=en" title="English">EN</a>
						<a class="navbar-item" href="'.$configSiteUrl.'?lang=fr" title="Français">FR</a>
						<a class="navbar-item" href="'.$configSiteUrl.'?lang=de" title="Deutsch">DE</a>
						<a class="navbar-item" href="'.$configSiteUrl.'?lang=it" title="Italiano">IT</a>
						<a class="navbar-item" href="'.$configSiteUrl.'?lang=es" title="Español">ES</a>
					</div>
				</div>
				<span class="navbar-item"><a class="button is-info" href="index.php" title="' .$neworder[$lang]. '">' .$neworder[$lang]. '</a></span>
				<a class="navbar-item is-tab" href="login.php" title="Login"><i class="fa fa-sign-in"></i></a>
			</div>
		</div>
	</div>
</nav>
';

echo '
	<section class="section">
<div class="container">
';

?>