const path = require('path');

/**
 * Build a PHP function for getting the webpack version hash.
 *
 * @param {Object} stats Webpack stats object.
 * @return string JS template literal.
 */
module.exports = function AssetVersions(stats) {
  const result = ['array('];
  const assets = stats.assetsByChunkName;

  Object.keys(assets).forEach((asset) => {
    // Make sure it's an array
    const assetList = [].concat(assets[asset]);
    assetList.forEach((assetName) => {
      const pathData = path.parse(assetName);
      const ext = pathData.ext.replace('.', '');

      if ('map' !== ext) {
        result.push(`\t\t'${asset}-${ext}' => '${assetName}',`);
      }
    });
  });

  result.push('\t)');

  return `<?php
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
\t$assets = ${result.join('\n')};
\treturn ! empty( $assets[ $asset ] ) ? $assets[ $asset ] : false;
}\n`;
};