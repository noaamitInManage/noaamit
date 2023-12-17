if (typeof site_lang === "undefined") {
    var site_lang = 'en';
}

var translationsArr;
var translations_loaded = false;

function load_translations()
{
    var translations_file_path = '/_static/translations.' + site_lang + '.json';
    var translations_file_request = new XMLHttpRequest();
    translations_file_request.open('GET', translations_file_path, true);
    translations_file_request.onload = function() {
        if (translations_file_request.status >= 200 && translations_file_request.status < 400) {
            translationsArr = JSON.parse(translations_file_request.responseText);
            translations_loaded = true;
        }
    };
    translations_file_request.send();
}

load_translations();

function lang(key, replaceThis, replaceWith)
{
    if (!translations_loaded) {
        return '';
    }

    if (typeof translationsArr[key] === "undefined") {
        return '';
    }

    var value = translationsArr[key];

    if (typeof replaceThis !== "undefined" && typeof replaceWith !== "undefined") {
        value = value.replace(replaceThis, replaceWith);
    }

    return value;
}