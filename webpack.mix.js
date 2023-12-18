const mix = require("laravel-mix");
const BundleAnalyzerPlugin =
    require("webpack-bundle-analyzer").BundleAnalyzerPlugin;
const VuetifyLoaderPlugin = require("vuetify-loader/lib/plugin");
mix.js("vue_app/app.js", "public/app/main.js")
    .webpackConfig({
        plugins: [new BundleAnalyzerPlugin()],
    })
    .version();
