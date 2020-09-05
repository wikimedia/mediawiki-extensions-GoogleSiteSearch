<?php
class GoogleSiteSearch {

	/**
	 * @param SpecialSearch $specialSearch
	 * @param OutputPage $output
	 * @param string $term
	 * @return bool
	 */
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
		$gcseAttributesDefault = [
			'gname' => 'mw-googlesitesearch',
			'linkTarget' => '',
		];

		# Attributes which may not be overridden.
		$gcseAttributesImmutable = [
			'autoSearchOnLoad' => 'false',
		];

		$gcseAttributes = array_merge( $gcseAttributesDefault, $wgGoogleSiteSearchAttributes, $gcseAttributesImmutable );

		# Generate HTML5-compatible <div> attributes.
		$gcseAttributesDiv = [];
		foreach ( $gcseAttributes as $key => $value ) {
			$gcseAttributesDiv['data-' . $key] = $value;
		}
		$gcseAttributesDiv['id'] = $gcseAttributes['gname'];
		$gcseAttributesDiv['class'] = 'gcse-searchresults-only';

		$html = Html::rawElement( 'div', [ 'id' => 'mw-googlesitesearch-container' ],
			Html::element( 'script', [], 'var mwGSSCallback = function() { google.search.cse.element.getElement(' . FormatJson::encode( $gcseAttributes['gname'] ) . ').execute(' . FormatJson::encode( $term ) . '); }; window.__gcse = { callback: mwGSSCallback }; (function() { var gcse = document.createElement("script"); gcse.type = "text/javascript"; gcse.async = true; gcse.src = "https://cse.google.com/cse.js?cx=" + ' . FormatJson::encode( $wgGoogleSiteSearchCSEID ) . '; var s = document.getElementsByTagName("script")[0]; s.parentNode.insertBefore(gcse, s); })();' )
			. Html::element( 'div', $gcseAttributesDiv, wfMessage( 'googlesitesearch-loading' )->text() )
		);

		# Allow hook override of HTML
		Hooks::run( 'GoogleSiteSearchHTML', [ $specialSearch, $term, &$html ] );

		# Add it!
		$output->addWikiTextAsInterface( '== ' . wfMessage( 'googlesitesearch-google-results' )->text() . ' ==' );
		$output->addHtml( $html );

		# Do not return wiki results if configured that way
		if ( $wgGoogleSiteSearchOnly ) {
			return false;
		} else {
			$output->addWikiTextAsInterface( '== ' . wfMessage( 'googlesitesearch-wiki-results' )->text() . ' ==' );
			return true;
		}
	}

}
