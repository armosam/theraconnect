/**
 * Created by Armen Bablanyan on 06/07/2016.
 * Used:
 */
$( document ).ready(function() {

    $(document).ajaxSend(function(event, request, settings) {
        $('#loading-indicator').show();
    });

    $(document).ajaxComplete(function(event, request, settings) {
        $('#loading-indicator').hide();
    });

    /* custom scroll to element inside of container */
    jQuery.fn.scrollTo = function(elem, speed) {
        $(this).animate({
            scrollTop:  $(this).scrollTop() - $(this).offset().top + $(elem).offset().top
        }, speed == undefined ? 1000 : speed);
        return this;
    };

    /**
     * collapse the blocks on final booking summery page complete.php
     */
    $('.clickable').click(function(e){
        var $this = $(this);
        if(!$this.hasClass('panel-collapsed')) {
            $this.parents('.panel').find('.panel-body').slideUp();
            $this.addClass('panel-collapsed');
            $this.find('i').removeClass('glyphicon-chevron-up').addClass('glyphicon-chevron-down');
        } else {
            $this.parents('.panel').find('.panel-body').slideDown();
            $this.removeClass('panel-collapsed');
            $this.find('i').removeClass('glyphicon-chevron-down').addClass('glyphicon-chevron-up');
        }
    });

    /**
     * click event for category picture on the index.php
     */
    $(".category-element").on("click", function(){
        var cat_id = $(this).data('cat_id');
        $("#providersearch-service_category_id").val(cat_id);
        $("#home_page_to_search").submit();
    });

    /*$(".cell-phone").on("blur", function(e) {
        if (e.which != 8 && e.which != 0 && (e.which < 48 || e.which > 57)) {
            return false;
        }
        displayFormattedPhoneNumber("cell-phone");
    });

    displayFormattedPhoneNumber("cell-phone");*/

    // Enable popovers everywhere
    $('[data-toggle="popover"]').popover();

    //open services modal
    $('.userServiceList-link').on('click', function(e){
        e.preventDefault();
        $('#userServiceList-modal').modal('show');
    });

});



/**
 * Function is used on my gallery page to add delete x button on the pictures
 */
function addDeleteButtonForGallery(){
    var  numItems = $("#gallery_container .gallery-item").length;
    var ajax_path = '/gallery/delete/';

    if (window.location.pathname == '/provider-management/gallery-update') {
        var href = window.location.href;
        var url = new URL(href);
        var userId = url.searchParams.get('user_id');
        var branchId = url.searchParams.get('branch_id');
        ajax_path = '/provider-management/gallery-delete/';
    }

    for (var i = 0; i < numItems; i++) {
        var ctr = 0;
        var btn = $("<button/>",
            {
                "data-id": i,
                click: function (e) {
                    e.preventDefaults;
                    // Message returning from gallery/update view
                    if(confirm(message_for_delete_photo)){
                        var divDeleteId = "div#div"+$(this).data("id");
                        var imageId = (($( divDeleteId ).find("a").attr("href").split("?"))[1].split("id="))[1].split("&")[0];
                        var data = {image_id:imageId};
                        if (userId != undefined && branchId != undefined) {
                            data.user_id = userId;
                            data.branch_id = branchId;
                        }

                        $.ajax({
                            type: "post",
                            dataType: "json",
                            url: ajax_path,
                            data: data,
                        });
                        $( divDeleteId ).remove();

                    }
                }
            });
        var sp = $("<span/>",
            {
                "class": "glyphicon glyphicon-remove"
            });
        sp.appendTo(btn);
        btn.toggleClass('gallery-remove-button');

        var divElement = $( "<div/>", {
            "id": "div"+i
        });
        divElement.toggleClass('gallery-management-item');

        divElement.appendTo( "#gallery_container #w0" );
        divElement.appendTo( "#gallery_container #w1" );

        divElement.append($("#gallery_container .gallery-item")[ctr]);

        divElement.append(btn);

        ctr++;

    }
}

/**
 * Method is used to print appropriate content of container
 * @param container
 */
function printContent(container) {
    var printContents = $(container).html();
    var link = '<style type="text/css"> .list-group-item dt{float:left;} .header-text *{ line-height: 1px;} </style>';

    var w=window.open();
    w.document.write(link);
    w.document.write(printContents);
    w.print();
    w.close();
}

/**
 * Formats value of element as phone number
 * @param element
 */
function displayFormattedPhoneNumber(element)
{
    var elements = $('.'+element);

    if (elements) {
        $.each(elements, function(k,v){
            var newValue = v.value.replace(/[^\d]/g, '');
            if (newValue.substring(0, 3) == 374) {
                v.value = newValue.replace(/^(374)(\d{2})(\d{5,})$/, "($1) $2 $3");
            } else if(v.value.length == 10) {
                v.value = newValue.replace(/^(\d{3})(\d{3})(\d{3,})+$/, "($1) $2-$3");
            } else if(v.value.length == 11) {
                v.value = newValue.replace(/^(\d{1})(\d{3})(\d{3})(\d{3,})+$/, "+$1($2)$3-$4");
            }
        });
    }
}

// Allow only numeric (0-9)
(function($) {
    $.fn.inputFilter = function(inputFilter) {
        return this.on("input keydown keyup mousedown mouseup select contextmenu drop", function() {
            if (inputFilter(this.value)) {
                this.oldValue = this.value;
                this.oldSelectionStart = this.selectionStart;
                this.oldSelectionEnd = this.selectionEnd;
            } else if (this.hasOwnProperty("oldValue")) {
                this.value = this.oldValue;
                this.setSelectionRange(this.oldSelectionStart, this.oldSelectionEnd);
            }
        });
    };
}(jQuery));
