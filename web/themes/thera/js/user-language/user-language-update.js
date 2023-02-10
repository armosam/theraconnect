
/**
 * @version v0.1
 * @author Armen Bablanyan
 * @copyright All Rights Reserved 2019
 * @type {{init: DynamicOption.init, setAttribute: DynamicOption.setAttribute, getAttribute: DynamicOption.getAttribute, getLocalFormat: (function(*=): string), reloadLanguageCategory: DynamicOption.reloadLanguageCategory, addLanguage: DynamicOption.addLanguage, getLanguages: DynamicOption.getLanguages, getLanguageData: (function(): []), getLanguage: DynamicOption.getLanguage, removeLanguage: DynamicOption.removeLanguage, getDbFormat: (function(*=): string), setLanguageData: DynamicOption.setLanguageData}}
 */
var DynamicOption = (function($) {

    var languageData = [];

    return {

        reloadLanguageBody: function () {

            $('#language_body').html('');
            $('#language_row').template("language_row");
            console.log($.tmpl("language_row", languageData), languageData);
            $.tmpl("language_row", languageData).appendTo('#language_body');
        },

        setLanguageData: function (data) {
            languageData = data;
        },

        getLanguageData: function () {
            return languageData;
        },

        getLanguages: function () {
            if(!languageData){
                return [];
            }
            return languageData;
        },

        getLanguage: function (code) {
            var data = languageData.filter(function( obj ) {
                return obj.language_code === code;
            });
            return data;
        },

        addLanguage: function (code, data) {
            this.removeLanguage(code);
            languageData.push(data);
            this.reloadLanguageBody();
        },

        removeLanguage: function (code) {
            languageData = languageData.filter(function( obj ) {
                return obj.language_code !== code;
            });
            this.reloadLanguageBody();
        },

        setAttribute: function (code, attribute, value) {
            if(languageData[code]){
                languageData[code][attribute] = value;
            }
        },

        getAttribute: function (code, attribute) {
            if(!(languageData[code] && languageData[code][attribute])){
                return null;
            }
            return languageData[code][attribute];
        },

        init: function() {

            $( ".add-item" ).each(function( index ) {
                languageData = [];

                $('#language_body tr td button.delete-item').each(function( index ) {
                    var language_code = $( this ).data('language_code');
                    var language_name = $( this ).data('language_name');

                    $("#languages>option[value='" + language_code + "']").attr('disabled', 'disabled');

                    languageData.push({
                        language_code: language_code,
                        language_name: language_name
                    });
                });
            });
        }
    };

})(jQuery);


DynamicOption.init();

/** Add new item */
$('#my-languages').on('click', '.add-item', function (event) {

    var selection = $('#languages').select2('data');
    if(selection && selection[0] !== undefined){
        selection = selection[0];
        var language_code = selection.id;
        var language_name = selection.text;

        if(!selection.disabled && selection.selected && language_code && !(language_code in DynamicOption.getLanguages()) ){
            $("#languages>option[value='" + language_code + "']").attr('disabled', 'disabled');
            DynamicOption.addLanguage(language_code, {language_code: language_code, language_name: language_name});
        }
    }
});

/** Remove item */
$('#my-languages').on('click', '.delete-item', function (event) {

    var language_code = $(this).data('language_code');

    if(DynamicOption.getLanguage(language_code)){
        $('#languages>option[value="' + language_code + '"]').removeAttr('disabled');
        DynamicOption.removeLanguage(language_code);
    }
});