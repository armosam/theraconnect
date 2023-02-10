/**
 * ModalAjaxWidgetClicker plugin
 * @param options
 */
var modalAjaxWidgetClicker = function (options) {

    var config = $.extend({
        modalTitle: 'Info',
        modalText: 'Loading...',
        targetId: false
    }, options);

    function checkOptions(settings) {
        if (!settings.targetId) {
            throw new Error('Options "targetId" is required in "modalAjaxWidgetClicker"');
        }
    }

    function run() {

        $(config.targetId).on('show.bs.modal', function (e) {
            var url = e.relatedTarget.hasAttribute('href') ? $(e.relatedTarget).attr('href') : $(e.relatedTarget).attr('value');
            var title = e.relatedTarget.hasAttribute('title') ? $(e.relatedTarget).attr('title') : config.modalTitle;
            $(config.targetId).find('.modal-title').html(title);
            $(config.targetId).find('.modal-body').html(config.modalText);

            if (url) {
                $(config.targetId).find('.modal-body').load(url);
            }
        });

        $('.modal').on("hidden.bs.modal", function (e) {
            if ($('.modal:visible').length) {
                $('body').addClass('modal-open');
            } else {
                $('html').removeClass('modal-open');
            }
        });

        /*$(document).on('click', config.clickBtnClass, function (e) {
            e.preventDefault();

             var url = this.hasAttribute('href') ? $(this).attr('href') : $(this).attr('value');
             var title = e.relatedTarget.hasAttribute('title') ? $(e.relatedTarget).attr('title') : config.modalTitle;

            if ($(config.targetId).data('bs.modal').isShown) {
                $(config.targetId).find('.modal-body').html(config.modalText);
                $(config.targetId).find('.modal-body').load(url);
            } else {
                $(config.targetId).find('.modal-body').html(config.modalText);
                $(config.targetId).modal('show').find('.modal-body').load(url);
            }

            $(config.targetId).find('.modal-title').html(title);
        });*/
    }

    checkOptions(config);
    run();
};
