const mix = require('laravel-mix');
require('laravel-mix-merge-manifest');
const assetPath = process.env.NODE_ENV === 'production' ?'assets': 'dev';

mix.js('resources/js/app.js', `public/${assetPath}/js`)
    .postCss('resources/css/app.css', `public/${assetPath}/css`, [
        require('postcss-import'),
        require('tailwindcss'),
    ]);
if (mix.inProduction()) {
    mix.version().mergeManifest();
}else{
    mix.options({ manifest: false });
}
mix.browserSync(
    {
        proxy: process.env.APP_URL
    }

);
