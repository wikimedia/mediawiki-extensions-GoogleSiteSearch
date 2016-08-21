<?php
class GoogleSiteSearch {

	public static function searchPrepend( $specialSearch, $output, $term ) {
		global $wgGoogleSiteSearchCSEID;
		global $wgGoogleSiteSearchOnly;
		global $wgGoogleSiteSearchCharset;

		# Return immediately if the CSE ID is not configured
		if ( !$wgGoogleSiteSearchCSEID ) {
			return true;
		}

		# Return immediately if no search term was supplied
		if ( !$term ) {
			return true;
		}

		$dir = dirname( __FILE__ ) . '/';
		$lang = $specialSearch->getLanguage();

		# Allow for local overrides of the base HTML
		if ( file_exists( $dir . 'GoogleSiteSearch.content.html' ) ) {
			$outhtml = file_get_contents ( $dir . 'GoogleSiteSearch.content.html' );
		} else {
			$outhtml = file_get_contents ( $dir . 'GoogleSiteSearch.content.default.html' );
		}

		# Replace variable data in the HTML
		$outhtml = str_replace( '_GSS_CSE_ID_', FormatJson::encode( $wgGoogleSiteSearchCSEID ), $outhtml );
		$outhtml = str_replace( '_GSS_TERM_ESCAPE_', FormatJson::encode( $term ), $outhtml );
		$outhtml = str_replace( '_GSS_LANG_', FormatJson::encode( $lang->getCode() ), $outhtml );
		$outhtml = str_replace( '_GSS_LOADING_', htmlentities( wfMessage( 'googlesitesearch-loading', $wgGoogleSiteSearchCharset ) ), $outhtml );

		# Add it!
		$output->addWikiText( '== ' . wfMessage( 'googlesitesearch-google-results' ) . ' ==' );
		$output->AddHTML( $outhtml );

		# Do not return wiki results if configured that way
		if ( $wgGoogleSiteSearchOnly ) {
			return false;
		} else {
			$output->addWikiText( '== ' . wfMessage( 'googlesitesearch-wiki-results' ) . ' ==' );
			return true;
		}
	}

}
