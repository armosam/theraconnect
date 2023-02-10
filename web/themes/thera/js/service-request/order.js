/**
 * Order Class
 * @constructor
 */

function Order() {
    this.booking_start_date_moment = null;
    this.service_booking_start_date = '';
    this.service_booking_start_time = '';
    this.total_duration = 0.0;

}

Order.prototype = {
    constructor: Order,

    getEl: function getEl(element_id) {
        return jQuery(element_id);
    },

    disableDaysOfDatePicker: function (days) {
        this.service_booking_start_date = '';
        this.service_booking_start_time = '';
        this.booking_start_date_moment = null;
        //this.getEl('#order-service_start').val('');
        // Setting current language for date picker
        $.fn.kvDatepicker.prototype.constructor.defaults.language = this.getEl('.language-picker a.active').attr('title').substr(0, 2);
        this.getEl('#order-service_start-kvdate').kvDatepicker('setStartDate', new Date());
        if (days) {
            this.getEl('#order-service_start-kvdate').kvDatepicker('setDatesDisabled', days);
        } else {
            this.getEl('#order-service_start-kvdate').kvDatepicker('setEndDate', '-2d');
        }
    },

    enableDaysOfDatePicker: function () {
        this.getEl('#order-service_start').val(this.getEl('#order-service_start').val().split('+')[0]);
        this.getEl('#order-service_start-kvdate').kvDatepicker('setStartDate', new Date());
        this.getEl('#order-service_start-kvdate').kvDatepicker('setEndDate', '');
        this.getEl('#order-service_start-kvdate').kvDatepicker('update', this.getEl('#order-service_start').val().split(' ')[0]);
        var OrderDate = new Date(this.getEl('#order-service_start').val().split(' ')[0]).getTime()
        $('.datepicker-days').find("[data-date='" + OrderDate + "']").addClass('active');
    },

    disableTimes: function () {
        this.service_booking_start_time = '';
        this.getEl('#time_unavailable_msg').removeClass('hidden');
        this.getEl('.order-start-time').html('');
        this.getEl('.order-start-time input').prop('checked', false);
        this.getEl('.order-start-time input').prop('disabled', true);
        this.getEl('.order-start-time label').css('color', '#ff0000');
    },

    enableTimes: function () {
        this.getEl('#time_unavailable_msg').addClass('hidden');
        this.getEl('.order-start-time input').prop('checked', false);
        this.getEl('.order-start-time input').prop('disabled', false);
        this.getEl('.order-start-time label').css('color', '#1c1c1c');
    },


    setSelectedDate: function(e){
        this.service_booking_start_date = e.format();
        this.booking_start_date_moment = e;
        this.getAvailableHoursByDate(e.format('yyyy-mm-dd'));
    },

    setOrderSelectedDate: function(e){
        this.service_booking_start_date = e;
        this.booking_start_date_moment = e;
        this.getAvailableHoursByDate(e);
    },

    getAvailableHoursByDate: function (d) {
        var me = this,
            data = [];

        $.grep(me.getEl('#order_reschedule_form').serializeArray(), function (item) {
            if (item.name === 'Order[service_start]') {
                item.value = d;
            }
            data.push({name: item.name, value: item.value});
        });
        $.ajax({
            type: 'POST',
            dataType: 'json',
            url: '/order/json-get-schedule-times-for-date-by-order',
            data: $.param(data),
            success: function (success_data) {

                if (success_data.length <= 0) {
                    // disabling current day and time picker if there are no time available
                    me.disableDaysOfDatePicker([new Date(d)]);
                    me.disableTimes();
                }else{
                    me.service_booking_start_time = '';
                    me.displayAvailableHours(success_data);
                }
            }
        });
    },

    displayAvailableHours: function (hours) {

        if (Array.isArray(hours)) {
            this.getEl('.order-start-time').html('');
            this.getEl('.order-start-time').loadTemplate("#available_hours", hours, {isFile:false});
            this.enableTimes();
            this.calculateServiceTime();
            return true
        } else {
            this.disableTimes();
            return true;
        }
    },

    setSelectedTime: function (selectedTime) {
        if (this.getEl('#order-service_start').val() != '' && this.service_booking_start_date) {
            this.service_booking_start_time = selectedTime;
            this.getEl('#order-service_start').val(this.service_booking_start_date + ' ' + this.service_booking_start_time);
            // Payment part is disabled now so there is only option. So we will enable submit button from here
            this.calculateServiceTime();
        }
    },

    calculateServiceTime: function(){
        var me = this,
            current_service_time = this.service_booking_start_time;

        $.each(me.services, function(i, data){
            data.service_time = [];
            if (current_service_time !== '') {

                for (var j = 0; j < data.service_count; j++) {
                    data.service_time.push({value: j, content: current_service_time});
                    current_service_time = moment(current_service_time, 'HH:mm:ss').add(parseFloat(data.service_frequency), 'seconds').format('HH:mm:ss');
                }
            }
        });
    },

    initReschedulePage: function () {
        var me = this;
        this.enableDaysOfDatePicker();
        this.setOrderSelectedDate(this.getEl('#order-service_start').val().split(' ')[0]);

        this.getEl('#order-service_start-kvdate').on('changeDate', function(e){
            me.setSelectedDate(e);
        });

        this.getEl('.order-start-time').on('click', 'input', function(e) {
            me.setSelectedTime(e.currentTarget.defaultValue);
        });
    },

    initApprovePage: function (config) {
        var me = this,
            settings = $.extend({
                url: '',
                message: ''
            }, config || {});

        this.getEl('.order-view').on('click', '.order-accept', function(e) {
            var confirmed = window.confirm(settings.message),
                data = {
                    id: $(e.target).data("id")
                };

            /** AJAX call to approve order */
            if(confirmed == true){
                $.ajax({
                    type: 'POST',
                    dataType: 'json',
                    url: settings.url,
                    data: $.param(data),
                    success: function(response){
                        if(response.result===false){
                            alert(response.message);
                        } else {
                            $.pjax.reload({container: '#order_view'});
                        }
                    }
                });
            }
        });
    },

    initOrderCancellationPage: function(config){
        var me = this,
            settings = $.extend({
                url: '',
                message: ''
            }, config || {});

        this.getEl('.order-view').on('click', '.order-cancel-modal', function(e) {
            e.preventDefault();
            me.getEl('#orderCancel').modal('show');
            me.getEl('#order_id').val(me.getEl(e.target).data('id'));
        });

        this.getEl('.modal-body').on('click', 'button#cancel_order_btn', function(e){
            var confirmed = window.confirm(settings.message),
                data = {
                    id: me.getEl("#order_id").val(),
                    cancellation_reason: me.getEl('#cancellation-reason').val()
                };

            /** AJAX call to cancel order */
            if(confirmed == true){
                $.ajax({
                    type: 'POST',
                    dataType: 'json',
                    url: settings.url,
                    data: $.param(data),
                    success: function(){
                        me.getEl('#orderCancel').modal('hide');
                        $.pjax.reload({container: "#order_view"});
                    }
                });
            }
        });
    },

    initOrderRejectPage: function(config){
        var me = this,
            settings = $.extend({
                url: '',
                message: ''
            }, config || {});

        this.getEl('.order-view').on('click', '.order-reject-modal', function(e) {
            e.preventDefault();
            me.getEl('#orderReject').modal('show');
            me.getEl('#order_id').val(me.getEl(e.target).data('id'));
        });

        this.getEl('.modal-body').on('click', 'button#reject_order_btn', function(e){
            var confirmed = window.confirm(settings.message),
                data = {
                    id: me.getEl("#order_id").val(),
                    rejection_reason: me.getEl('#rejection-reason').val()
                };

            /** AJAX call to reject order */
            if(confirmed == true){
                $.ajax({
                    type: 'POST',
                    dataType: 'json',
                    url: settings.url,
                    data: $.param(data),
                    success: function(){
                        me.getEl('#orderReject').modal('hide');
                        $.pjax.reload({container: "#order_view"});
                    }
                });
            }
        });
    },

    addNotes: function(){
        // var me = this;
        var order_notes = this.getEl('textarea[name="Order[notes]"]');
        var form = this.getEl('#form_order_notes');

        this.getEl('.order-view').on('click', 'textarea[name="Order[notes]"]', function(e) {
            this.readOnly=false;
            $('#notes_submit').removeClass('hidden');
        });

        this.getEl('.order-view').on('click', '#notes_cancel', function(e) {
            order_notes.prop('readOnly', true);
            $('#notes_submit').addClass('hidden');
        });

        this.getEl('.order-view').on('submit', '#form_order_notes', function(e){
            e.preventDefault();
            var data = {
                notes: order_notes.val(),
                id: $(this).data('order_id')
            };
            $.ajax({
                type: 'POST',
                dataType: 'json',
                url: '/order/json-add-notes-to-order',
                data: $.param(data),
                success: function (response) {
                    if(response.result === true) {
                        order_notes.prop('readOnly', true);
                        $('#notes_submit').addClass('hidden');
                    }
                }
            });
        });
    }
};
