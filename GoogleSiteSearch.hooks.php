<?php
class GoogleSiteSearch {

	public static function searchPrepend( $specialSearch, $output, $term ) {
		global $wgGoogleSiteSearchCSEID;
		global $wgGoogleSiteSearchOnly;
		global $wgGoogleSiteSearchAttributes;

		# Return immediately if the CSE ID is not configured
		if ( !$wgGoogleSiteSearchCSEID ) {
			return true;
		}

		# Return immediately if no search term was supplied
		if ( !$term ) {
			return true;
		}

		# Default attributes, may be overridden by $wgGoogleSiteSearchAttributes.
		$gcseAttributesDefault = array(
			'gname' => 'mw-googlesitesearch',
			'linkTarget' => '',
		);

		# Attributes which may not be overridden.
		$gcseAttributesImmutable = array(
			'autoSearchOnLoad' => 'false',
		);

		$gcseAttributes = array_merge( $gcseAttributesDefault, $wgGoogleSiteSearchAttributes, $gcseAttributesImmutable );

		# Generate HTML5-compatible <div> attributes.
		$gcseAttributesDiv = array();
		foreach ( $gcseAttributes as $key => $value ) {
			$gcseAttributesDiv['data-' . $key] = $value;
		}
		$gcseAttributesDiv['id'] = $gcseAttributes['gname'];
		$gcseAttributesDiv['class'] = 'gcse-searchresults-only';

		$html = Html::rawElement( 'div', array( 'id' => 'mw-googlesitesearch-container' ),
			Html::element( 'script', array(), 'var mwGSSCallback = function() { google.search.cse.element.getElement(' . FormatJson::encode( $gcseAttributes['gname'] ) . ').execute(' . FormatJson::encode( $term ) . '); }; window.__gcse = { callback: mwGSSCallback }; (function() { var gcse = document.createElement("script"); gcse.type = "text/javascript"; gcse.async = true; gcse.src = "https://cse.google.com/cse.js?cx=" + ' . FormatJson::encode( $wgGoogleSiteSearchCSEID ) . '; var s = document.getElementsByTagName("script")[0]; s.parentNode.insertBefore(gcse, s); })();' )
			. Html::Element( 'div', $gcseAttributesDiv, wfMessage( 'googlesitesearch-loading' ) )
		);

		# Allow hook override of HTML
		Hooks::run( 'GoogleSiteSearchHTML', [ $specialSearch, $term, &$html ] );

		# Add it!
		$output->addWikiText( '== ' . wfMessage( 'googlesitesearch-google-results' ) . ' ==' );
		$output->AddHTML( $html );

		# Do not return wiki results if configured that way
		if ( $wgGoogleSiteSearchOnly ) {
			return false;
		} else {
			$output->addWikiText( '== ' . wfMessage( 'googlesitesearch-wiki-results' ) . ' ==' );
			return true;
		}
	}

}
