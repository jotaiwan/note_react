const Encore = require('@symfony/webpack-encore');
const path = require('path');

// Manually configure the runtime environment if not already configured yet by the "encore" command.
// It's useful when you use tools that rely on webpack.config.js file.
if (!Encore.isRuntimeEnvironmentConfigured()) {
    Encore.configureRuntimeEnvironment(process.env.NODE_ENV || 'dev');
}

Encore
    // Directory where compiled assets will be stored
    .setOutputPath('public/build/')
    // Public path for assets, typically /build
    .setPublicPath('/build')

    // Main JS entry point
    .addEntry('app', './app.js')
    .addEntry('noteLinkFunctions', './public/assets/note/noteLinkFunctions.js')
    .addEntry('noteBuilder', './public/assets/note/noteBuilder.js')  // New JS file
    .addEntry('noteEditable', './public/assets/note/noteEditable.js')  // New JS file
    .addEntry('stockUpdate', './public/assets/note/stockUpdate.js')  // New JS file
    .enableSingleRuntimeChunk()

    // Enable SASS/SCSS if needed
    .enableSassLoader()

    // Enable PostCSS for loading css
    // .enablePostCssLoader()

    // Enables source maps in dev mode
    .enableSourceMaps(!Encore.isProduction())

    // Versioning to create unique file names for cache busting
    .enableVersioning()

    // Automatically provide jQuery globally
    .autoProvidejQuery()

    // Enable Babel for JS transpiling (webpack-encore will handle Babel configuration)
    // Comment out or remove the manual Babel config to avoid conflicts.
    //.configureBabel((config) => {
    //    config.presets.push('@babel/preset-env');
    //})
    ;

module.exports = Encore.getWebpackConfig();
