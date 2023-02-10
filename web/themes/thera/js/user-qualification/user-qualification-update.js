
/**
 * @version v0.1
 * @author Armen Bablanyan
 * @copyright All Rights Reserved 2019
 * @type {{init: DynamicOption.init, setAttribute: DynamicOption.setAttribute, getAttribute: DynamicOption.getAttribute, getLocalFormat: (function(*=): string), reloadQualificationCategory: DynamicOption.reloadQualificationCategory, addQualification: DynamicOption.addQualification, getQualifications: DynamicOption.getQualifications, getQualificationData: (function(): []), getQualification: DynamicOption.getQualification, removeQualification: DynamicOption.removeQualification, getDbFormat: (function(*=): string), setQualificationData: DynamicOption.setQualificationData}}
 */
var DynamicOption = (function($) {

    var qualificationData = [];
    var language = $('html')[0].lang;


    return {

        getDbFormat: function (date) {
            if(date) {
                var tmp;
                if (language === 'hy-AM') {
                    tmp = date.split('.');
                    date = tmp[2] + '-' + tmp[1] + '-' + tmp[0];
                } else if (language === 'ru-RU') {
                    tmp = date.split('.');
                    date = tmp[2] + '-' + tmp[1] + '-' + tmp[0];
                } else if (language === 'en-US') {
                    tmp = date.split('/');
                    date = tmp[2] + '-' + tmp[0] + '-' + tmp[1];
                }
            }
            return date;
        },

        getLocalFormat: function (date) {
            if(date) {
                var tmp = date.split('-');
                if (language === 'hy-AM') {
                    date = tmp[2] + '.' + tmp[1] + '.' + tmp[0];
                } else if (language === 'ru-RU') {
                    date = tmp[2] + '.' + tmp[1] + '.' + tmp[0];
                } else if (language === 'en-US') {
                    date = tmp[1] + '/' + tmp[2] + '/' + tmp[0];
                }
            }
            return date;
        },

        reloadQualificationCategory: function (qualification_category_id) {
            var me = this;
            qualification_category_id = parseInt(qualification_category_id, 10);
            $('#qualification_body_' + qualification_category_id).html('');

            $('#qualification_row').template("qualification_row");
            $.tmpl("qualification_row", qualificationData[qualification_category_id]).appendTo('#qualification_body_' + qualification_category_id);

            // update qualification_at date to DB format for hidden input
            $.each(qualificationData[qualification_category_id], function(i, item){
                if(item && item.qualification_id){
                    var el = $('#userqualification-'+item.qualification_id+'-qualification_at');
                    if(el && item.qualification_at){
                        el.val(me.getDbFormat(item.qualification_at));
                    }
                }
            });
        },

        setQualificationData: function (data) {
            qualificationData = data;
        },

        getQualificationData: function () {
            return qualificationData;
        },

        getQualifications: function (category) {
            if(!qualificationData[parseInt(category, 10)]){
                return null;
            }
            return qualificationData[parseInt(category, 10)]
        },

        getQualification: function (category, item) {
            if(!qualificationData[parseInt(category, 10)]){
                return null;
            }
            if(!qualificationData[parseInt(category, 10)][parseInt(item, 10)]){
                return null;
            }
            return qualificationData[parseInt(category, 10)][parseInt(item, 10)];
        },

        addQualification: function (category, item, data) {
            if(qualificationData[parseInt(category, 10)]){
                qualificationData[parseInt(category, 10)][parseInt(item, 10)] = data;
                this.reloadQualificationCategory(category);
            }
        },

        removeQualification: function (category, item) {
            if(qualificationData[parseInt(category, 10)][parseInt(item, 10)]){
                delete qualificationData[parseInt(category, 10)][parseInt(item, 10)];
                this.reloadQualificationCategory(parseInt(category, 10));
            }
        },

        setAttribute: function (category, item, attribute, value) {
            if(qualificationData[parseInt(category, 10)]){
                qualificationData[parseInt(category, 10)][parseInt(item, 10)][attribute] = value;
            }
        },

        getAttribute: function (category, item, attribute) {
            if(!(qualificationData[parseInt(category, 10)] && qualificationData[parseInt(category, 10)][parseInt(item, 10)] && qualificationData[parseInt(category, 10)][parseInt(item, 10)][attribute])){
                return null;
            }
            return qualificationData[parseInt(category, 10)][parseInt(item, 10)][attribute];
        },

        init: function() {
            var me = this;
            $( ".add-item" ).each(function( index ) {
                var qualification_category_id = parseInt($( this ).data('qualification_category_id'), 10);
                qualificationData[qualification_category_id] = [];

                $('#qualification_body_' + qualification_category_id + ' tr td button.delete-item').each(function( index ) {
                    var qualification_id = parseInt($( this ).data('qualification_id'), 10);
                    var qualification_name = $( this ).data('qualification_name');
                    var qualification_at = me.getLocalFormat($(this).data('qualification_at'));

                    $("#qualification_category_" + qualification_category_id + ">option[value='" + qualification_id + "']").attr('disabled', 'disabled');

                    qualificationData[qualification_category_id][qualification_id] = {
                        qualification_category_id: qualification_category_id,
                        qualification_id: qualification_id,
                        qualification_name: qualification_name,
                        qualification_at: qualification_at
                    };
                });
            });
        }
    };

})(jQuery);


DynamicOption.init();

/** Add new item */
$('#my-qualifications').on('click', '.add-item', function (event) {

    var qualification_category_id = $(event.target).data('qualification_category_id');
    var selection = $('#qualification_category_' + qualification_category_id).select2('data');

    if(selection && selection[0] !== undefined){
        selection = selection[0];
        var qualification_id = parseInt(selection.id, 10);
        var qualification_name = selection.text;

        if(!selection.disabled && selection.selected && !(qualification_id in DynamicOption.getQualifications(qualification_category_id)) ){
            $("#qualification_category_" + qualification_category_id + ">option[value='" + qualification_id + "']").attr('disabled', 'disabled');
            DynamicOption.addQualification(qualification_category_id, qualification_id, {qualification_category_id: qualification_category_id, qualification_id: qualification_id, qualification_name: qualification_name, qualification_at: ''});
        }
    }
});

/** Remove item */
$('#my-qualifications').on('click', '.delete-item', function (event) {

    var qualification_category_id = parseInt($(this).data('qualification_category_id'), 10),
        qualification_id = parseInt($(this).data('qualification_id'), 10);

    if(DynamicOption.getQualification(qualification_category_id, qualification_id)){
        $('#qualification_category_' + qualification_category_id + '>option[value="' + qualification_id + '"]').removeAttr('disabled');
        DynamicOption.removeQualification(qualification_category_id, qualification_id);
    }
});

/** Change date and update data */
$('.form-qualifications-body').on('change', '.specialist-datepicker', function (event) {

    var qualification_category_id = parseInt($(this).data('qualification_category_id'), 10),
        qualification_id = parseInt($(this).data('qualification_id'), 10);

    DynamicOption.setAttribute(qualification_category_id, qualification_id, 'qualification_at', $(this).val());
    var date = DynamicOption.getDbFormat( $(this).val() );
    $('#userqualification-' + qualification_id + '-qualification_at').val(date);
});