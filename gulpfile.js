const { src , dest, series, watch} = require('gulp')
const sass = require('gulp-sass')(require('sass'));
sass.compiler = require('dart-sass');

function compilarSASS(){
    return src("./src/scss/app.scss")
    .pipe(sass())
    .pipe(dest("./build/css"));
}

function minificarcss(){
    return src("./src/scss/app.scss")
    .pipe(sass({
        outputStyle: 'compressed'
    }))
    .pipe(dest("./build/css"));
}


function watchArchivos(){
    watch("./src/scss/**/*.scss",compilarSASS); 
}

exports.compilarSASS = compilarSASS;
exports.minificarcss = minificarcss;
exports.watchArchivos = watchArchivos;
    