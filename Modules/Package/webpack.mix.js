const dotenvExpand = require("dotenv-expand");
dotenvExpand(
    require("dotenv").config({ path: "../../.env" /*, debug: true*/ })
);

const mix = require("laravel-mix");
require("laravel-mix-merge-manifest");

mix.setPublicPath("../../public").mergeManifest();

mix.js(
    __dirname + "/Resources/assets/js/app.js",
    "modules/Package/script.js"
)
    .vue()
    .sourceMaps()
    .sass(
        __dirname + "/Resources/assets/sass/app.scss",
        "modules/Package/style.css"
    );

if (mix.inProduction()) {
    mix.version();
}
