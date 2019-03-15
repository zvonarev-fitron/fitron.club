const { mix } = require('laravel-mix');

mix.options({
    hmrOptions: {
        port: '3030',
    },
})
//    .webpackConfig({
//        devtool: "inline-source-map"
//    })
    // .copyDirectory('resources/images', 'public/images')
    //
    // .js('packages/login/resources/js/auth/login.js', 'public/js/auth')
    // .js('resources/js/tableExample.js', 'public/js')
    // .js('resources/js/selectExample.js', 'public/js')
    // .js('resources/js/enso.js', 'public/js')
//    .js('resources/js/mainslider.js', 'public/js/sliders')
    .js('resources/js/phonesend.js', 'public/js/work')

    // .sass('packages/login/resources/sass/auth/login.scss', 'public/css/auth')
    // .sass('resources/sass/enso.scss', 'public/css')
//    .sourceMaps();


// .sass('resources/sass/themes/light.scss', 'public/themes/light/bulma.min.css')
// .sass('resources/sass/themes/dark.scss', 'public/themes/dark/bulma.min.css');

if (process.env.NODE_ENV === 'production') {
    mix.version();
}


// mix.options({
//   sourcemaps: 'inline-source-map'
// });
