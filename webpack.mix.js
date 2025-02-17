const mix = require("laravel-mix");
const path = require('path')

if(process.env.MIX_PUBLIC_PATH !== null && process.env.MIX_PUBLIC_PATH !== undefined && process.env.MIX_PUBLIC_PATH !== '') {
    mix.setPublicPath('public')
      .webpackConfig({
        output: {publicPath: process.env.MIX_PUBLIC_PATH}
      });
}
/**
 *
 * !Copy Assets
 *
 * -----------------------------------------------------------------------------
 */
// icon fonts
mix.copy(
  "node_modules/@fortawesome/fontawesome-free/webfonts/*",
  "public/webfonts"
);
/**
 *
 * !Backend/Dashboard
 *
 * -----------------------------------------------------------------------------
 */
// Build Backend/Dashboard SASS
mix.sass("resources/sass/libs.scss", "public/css/libs.min.css")
    .sass("public/scss/hope-ui.scss", "public/css")
    .sass("public/scss/custom.scss", "public/css")
    .sass("public/scss/dark.scss", "public/css")
    .sass("public/scss/rtl.scss", "public/css")
    .sass("public/scss/customizer.scss", "public/css")
    .sass("public/scss/pro.scss", "public/css");

// Backend/Dashboard Styles
mix.styles(
    [
        "public/css/hope-ui.css",
        "public/css/pro.css",
    ],
    "public/css/backend.css"
);

mix.styles([
  "node_modules/@fortawesome/fontawesome-free/css/all.min.css"
], 'public/css/icon.min.css')

// Backend/Dashboard Scripts
mix.js("resources/js/libs.js", "public/js/core/libs.min.js")
    .js("resources/js/backend-custom.js", "public/js/backend-custom.js");

mix.scripts(
    [
        "public/js/core/libs.min.js",
        "public/js/backend-custom.js",
    ],
    "public/js/backend.js"
);

mix.alias({
    '@': path.join(__dirname, 'resources/js')
});
mix.js('resources/js/app.js', 'public/js')
.sass('resources/sass/app.scss', 'public/css');

mix.js("resources/js/setting-vue.js", "public/js/setting-vue.min.js")

mix.js("resources/js/profile-vue.js", "public/js/profile-vue.min.js")

mix.js("resources/js/import-export.js", "public/js/import-export.min.js")

// Global Vue Script
mix.js('resources/js/vue/app.js', 'public/js/vue.min.js').vue();
mix.js('resources/js/vue/booking-form.js', 'public/js/booking-form.min.js').vue();

/**
 * !Module Based Script & Style Bundel
 * @path Modules/{module_name}/app.js (This Could be vue, react, vanila javascript)
 * @path Module/{module_name}/app.scss (There is all module css)
 *
 * !Final Build Path Should Be
 * @path public/modules/{module_name}/script.js
 * @path public/modules/{module_name}/style.js
 *
 * *USAGE IN BLADE FILE*
 * ? <link rel="stylesheet" href="{{ mix('modules/{module_name}/style.css') }}">
 * ? <script src="{{ mix('modules/{module_name}/script.js') }}"></script>
 */

const Modules = require("./modules_statuses.json");
const Fs = require("fs");

for (const key in Modules) {
    if (Object.hasOwnProperty.call(Modules, key)) {
        if (
            Fs.existsSync(
                `${__dirname}/Modules/${key}/Resources/assets/js/app.js`
            )
        ) {
            mix.js(
                `${__dirname}/Modules/${key}/Resources/assets/js/app.js`,
                `modules/${key.toLocaleLowerCase()}/script.js`
            )
                .vue()
                .sourceMaps();
        }
        if (
            Fs.existsSync(
                `${__dirname}/Modules/${key}//Resources/assets/sass/app.scss`
            )
        ) {
            mix.sass(
                `${__dirname}/Modules/${key}//Resources/assets/sass/app.scss`,
                `modules/${key.toLocaleLowerCase()}/style.css`
            ).sourceMaps();
        }
    }
}

// !For Production Build Added To Version on File for cache
if (mix.inProduction()) {
    mix.version();
}
