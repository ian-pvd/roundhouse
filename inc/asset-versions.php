<?php
/**
 * Get the version for a given asset.
 *
 * @NOTE
 * - This file is written with a node build script.
 * - Do not edit this file by hand.
 * - See: ./assets/config/asset-versions.js
 *
 * @param string $asset The asset name.
 * @return string The asset version.
 */
function pvd_get_asset_version( $asset ) {
	$assets = array(
		'post-css' => 'post.da5449.css',
		'post-js' => 'post.da5449.js',
		'site-css' => 'site.da5449.css',
		'site-js' => 'site.da5449.js',
	);
	return ! empty( $assets[ $asset ] ) ? $assets[ $asset ] : false;
}
