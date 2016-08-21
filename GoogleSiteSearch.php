<?php
/** \file
* \brief Contains setup code for the GoogleSiteSearch Extension.
*/

/**
 * GoogleSiteSearch Extension for MediaWiki
 *
 * Copyright (C) Ryan Finnie
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License along
 * with this program; if not, write to the Free Software Foundation, Inc.,
 * 51 Franklin Street, Fifth Floor, Boston, MA 02110-1301, USA.
 * http://www.gnu.org/copyleft/gpl.html
 */

if ( function_exists( 'wfLoadExtension' ) ) {
	wfLoadExtension( 'GoogleSiteSearch' );
	// Keep i18n globals so mergeMessageFileList.php doesn't break
	$wgMessagesDirs['GoogleSiteSearch'] = __DIR__ . '/i18n';
	wfWarn(
		'Deprecated PHP entry point used for GoogleSiteSearch extension. ' .
		'Please use wfLoadExtension instead, ' .
		'see https://www.mediawiki.org/wiki/Extension_registration for more details.'
	);
	return;
}

$extensionJsonFilename = dirname( __FILE__ ) . '/extension.json';
$extensionJsonData = FormatJson::decode( file_get_contents( $extensionJsonFilename ), true );
$wgExtensionCredits[$extensionJsonData['type']][] = array(
	'path' => __FILE__,
	'name' => $extensionJsonData['name'],
	'author' => $extensionJsonData['author'],
	'url' => $extensionJsonData['url'],
	'descriptionmsg' => $extensionJsonData['descriptionmsg'],
	'version' => $extensionJsonData['version'],
	'license-name' => $extensionJsonData['license-name'],
);

# Default configuration globals
if ( !isset( $wgGoogleSiteSearchCSEID ) ) {
	$wgGoogleSiteSearchCSEID = '';
}
if ( !isset( $wgGoogleSiteSearchOnly ) ) {
	$wgGoogleSiteSearchOnly = false;
}
if ( !isset( $wgGoogleSiteSearchCharset ) ) {
	$wgGoogleSiteSearchCharset = 'UTF-8';
}

# Define internationalizations
$wgMessagesDirs['GoogleSiteSearch'] = __DIR__ . '/i18n';

# Define extension hooks
$wgAutoloadClasses['GoogleSiteSearch'] = __DIR__ . '/GoogleSiteSearch.hooks.php';

# Hook into SpecialSearchResultsPrepend
$wgHooks['SpecialSearchResultsPrepend'][] = 'GoogleSiteSearch::searchPrepend';
