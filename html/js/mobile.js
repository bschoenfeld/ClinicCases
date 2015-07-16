/* global unescape, moment */

//Get url parameters
function getParameterByName(name) {
    name = name.replace(/[\[]/, "\\\[").replace(/[\]]/, "\\\]");
    var regex = new RegExp("[\\?&]" + name + "=([^&#]*)"),
        results = regex.exec(location.search);
    return results == null ? "" : decodeURIComponent(results[1].replace(/\+/g, " "));
}
//Responsive tabs feature
(function($) {

    'use strict';

    $(document).on('show.bs.tab', '.nav-tabs-responsive [data-toggle="tab"]', function(e) {
        var $target = $(e.target);
        var $tabs = $target.closest('.nav-tabs-responsive');
        var $current = $target.closest('li');
        var $parent = $current.closest('li.dropdown');
        $current = $parent.length > 0 ? $parent : $current;
        var $next = $current.next();
        var $prev = $current.prev();
        var updateDropdownMenu = function($el, position) {
            $el
            .find('.dropdown-menu')
            .removeClass('pull-xs-left pull-xs-center pull-xs-right')
            .addClass('pull-xs-' + position);
        };

        $tabs.find('>li').removeClass('next prev');
        $prev.addClass('prev');
        $next.addClass('next');

        updateDropdownMenu($prev, 'left');
        updateDropdownMenu($current, 'center');
        updateDropdownMenu($next, 'right');
    });

})(jQuery);

$(document).ready(function () {
    //Select correct subtab based on url
    var tab = getParameterByName('tabsection');

    if (tab.length) {
        $('#myTab a[href="#' + tab + '"]').tab('show');
    } else {
        $('#myTab a.default-tab').tab('show');
    }

    //Adds tabsection to url for tab-panes which will have
    //multiple levels; preserves navigation by back button
    $('#myTab a.multi-level').click(function () {
        var current = document.location.search;
        var addTab = $(this).attr('href').substring(1);
        document.location.search = current + '&tabsection=' + addTab;
    });

    //Display cases based on open/closed status
    $('select[name="case-status"]').change(function () {
        $('li.table-case-item').removeClass('search-result-hit search-result-miss');
        $('.table-case-item').toggle();
    });

    //Search Cases
    $('input.case-search').keyup(function () {
        var searchVal = $(this).val().toLowerCase();
        var caseStatus = $('select[name="case-status"]').val();
        var targetClass;
        if (caseStatus === 'open') {
            targetClass = 'table-case-open';
        } else {
            targetClass = 'table-case-closed';
        }
        $('li.table-case-item').removeClass('search-result-hit search-result-miss');
        $('li.' + targetClass).each(function () {
            if ($(this).find('a').text().toLowerCase().indexOf(searchVal) !== -1) {
                $(this).addClass('search-result-hit');
            } else {
                $(this).addClass('search-result-miss');
            }
        });

    });

    //Hide system case id
    $('#caseData dd:eq(0)').hide();
    $('#caseData dt:eq(0)').hide();

    //Handle document downloads
    $('a.doc-item').click(function (event) {
        event.preventDefault();
        var itemId = $(this).attr('data-id');
        var itemExt = $(this).attr('data-ext');
        if (itemExt === 'url') {
            $.post('lib/php/data/cases_documents_process.php',
            {'item_id': itemId, 'action': 'open', 'doc_type': 'document'},
            function (data) {
                var serverResponse = $.parseJSON(data);
                window.open(serverResponse.target_url, '_blank');
            });
        } else if (itemExt === 'ccd') {
            $.post('lib/php/data/cases_documents_process.php',
            {'item_id': itemId, 'action': 'open', 'doc_type': 'document'},
            function (data) {
                var serverResponse = $.parseJSON(data);
                var hideList;
                var ccdItem = '<a class="ccd-clear btn" href="#"><i class="icon-chevron-left"></i> Back</a><h2>' +
                unescape(serverResponse.ccd_title) + '</h2>' + serverResponse.ccd_content;
                if ($('.doc-list').length) {
                    hideList = $('.doc-list').detach();
                    $('#caseDocs').append(ccdItem);
                    //Close a ccd document after viewing
                    $('.tab-content').on('click', 'a.ccd-clear', function (event) {
                        event.preventDefault();
                        $('#caseDocs').html('').append(hideList);
                    });
                } else {
                    hideList = $('#activities_feed').detach();
                    $('.home-container').append(ccdItem);
                    $('.home-container').on('click', 'a.ccd-clear', function (event) {
                        event.preventDefault();
                        $('#caseDocs').html('').append(hideList);
                        $('.home-container').html('').append(hideList);
                    });
                }
            });
        } else {
            $.download('lib/php/data/cases_documents_process.php', {'item_id': itemId, 'action': 'open', 'doc_type': 'document'});
        }
    });

    //Add chosen to selects
    //Must initialize with size on hidden div: see https://github.com/harvesthq/chosen/issues/1297
    $('#ev_users').chosen({ width: '16em' });

    //Submit Quick Adds
    //Case notes
    $.validator.addMethod('timeReq', function (value) {
        return !(value === '0' && $('select[name="csenote_hours"]').val() === '0');
    }, 'You must enter some time.');

    $.validator.addMethod('nameReq', function (value) {
        return !(value === '' && $('input[name="first_name"]').val() === '' && $('input[name="organization"]').val() === '');
    }, 'Please provide the name of a person or organziation');

    $('form[name="quick_cn"]').validate({
        errorClass: 'text-error',
        errorElement: 'span',
        rules: {
            csenote_minutes: {timeReq: true}
        },
        submitHandler: function (form) {
            var thisForm = $('form[name="quick_cn"]');
            var dateVal = $('select[name="c_month"]').val() + '/' +
            $('select[name="c_day"]').val() + '/' + $('select[name="c_year"]').val();
            $('input[name="csenote_date"]').val(dateVal);
            $.post('lib/php/data/cases_casenotes_process.php', thisForm.serialize(), function (data) {
                var serverResponse = $.parseJSON(data);
                if (serverResponse.error) {
                    $('#notifications').show().html(serverResponse.message).delay(2000).fadeOut();
                } else {
                    var successMsg = '<p class="text-success">' + serverResponse.message +
                    '</p><p><a class="btn show-form" href="#">Add Another?</a></p>';
                    thisForm[0].reset();
                    var hideForm = $('form[name="quick_cn"]').detach();
                    $('#qaCaseNote').append(successMsg);
                    $('a.show-form').click(function (event) {
                        event.preventDefault();
                        $('#qaCaseNote').html('').append(hideForm);
                    });
                }
            });
        }
    });

    //Case events
    $('form[name="quick_event"]').validate({
        errorClass: 'text-error',
        errorElement: 'span',
        submitHandler: function () {
            var thisForm = $('form[name="quick_event"]');
            var startVal = thisForm.find('select[name="c_month"]').eq(0).val() + '/' + thisForm.find('select[name="c_day"]').eq(0).val() +
            '/' + thisForm.find('select[name="c_year"]').eq(0).val() + ' ' +  thisForm.find('select[name="c_hours"]').eq(0).val() +
            ':' + thisForm.find('select[name="c_minutes"]').eq(0).val() +
            ' ' + thisForm.find('select[name="c_ampm"]').eq(0).val();
            $('input[name="start"]').val(startVal);

            var endVal = thisForm.find('select[name="c_month"]').eq(1).val() + '/' + thisForm.find('select[name="c_day"]').eq(1).val() +
            '/' + thisForm.find('select[name="c_year"]').eq(1).val() + ' ' +  thisForm.find('select[name="c_hours"]').eq(1).val() +
            ':' + thisForm.find('select[name="c_minutes"]').eq(1).val() +
            ' ' + thisForm.find('select[name="c_ampm"]').eq(1).val();
            $('input[name="end"]').val(endVal);

            //serialize form values
            var evVals = thisForm.not('select[name="responsibles"]').serializeArray();
            var resps = thisForm.find('select[name="responsibles"]').val();
            var respsObj = $.extend({}, resps);
            evVals.unshift(respsObj); //put this object at the beginning
            var allDayVal = null;
            if (thisForm.find('input[name = "all_day"]').is(':checked')) {
                allDayVal = 'on';
            } else {
                allDayVal = 'off';
            }

            $.post('lib/php/data/cases_events_process.php', {
                'task': thisForm.find('input[name = "task"]').val(),
                'where': thisForm.find('input[name = "where"]').val(),
                'start': thisForm.find('input[name = "start"]').val(),
                'end': thisForm.find('input[name = "end"]').val(),
                'all_day': allDayVal,
                'notes': thisForm.find('textarea[name = "notes"]').val(),
                'responsibles': resps,
                'action': 'add',
                'case_id': thisForm.find('select[name = "case_id"]').val()
            }, function (data) {
                var serverResponse = $.parseJSON(data);
                if (serverResponse.error) {
                    $('#notifications').show().html(serverResponse.message).delay(2000).fadeOut();
                } else {
                    var successMsg = '<p class="text-success">' + serverResponse.message +
                    '</p><p><a class="btn show-form" href="#">Add Another?</a></p>';
                    thisForm[0].reset();
                    $('#ev_users').trigger('liszt:updated');
                    var hideForm = $('form[name="quick_event"]').detach();
                    $('#qaEvent').append(successMsg);
                    $('a.show-form').click(function (event) {
                        event.preventDefault();
                        $('#qaEvent').html('').append(hideForm);
                    });
                }
            });
        }
    });

    //Convenience method for advancing end date
    $('form[name="quick_event"] div.date-picker:eq(0) select').change(function () {
        var el = $(this).attr('name');
        $(this).closest('.date-picker').siblings('.date-picker').find('select[name=' + el + ']').val($(this).val());
    });

    //Case contacts
    $('form[name="quick_contact"]').validate({
        errorClass: 'text-error',
        errorElement: 'span',
        rules: {
            last_name: {nameReq: true}
        },
        submitHandler: function () {
            var thisForm = $('form[name="quick_contact"]');
            var phoneData = {};
            phoneData[$('#qaContact select[name="phone_type"]').val()] = $('#qaContact input[name="phone"]').val();
            var phone = JSON.stringify(phoneData);
            var emailData = {};
            emailData[$('#qaContact select[name="email_type"]').val()] = $('#qaContact input[name="email"]').val();
            var email = JSON.stringify(emailData);
            $.post('lib/php/data/cases_contacts_process.php', {
                    'first_name': thisForm.find('input[name = "first_name"]').val(),
                    'last_name': thisForm.find('input[name = "last_name"]').val(),
                    'organization': thisForm.find('input[name = "organization"]').val(),
                    'contact_type': thisForm.find('select[name = "contact_type"]').val(),
                    'address': thisForm.find('textarea[name = "address"]').val(),
                    'city': thisForm.find('input[name = "city"]').val(),
                    'state': thisForm.find('select[name = "state"]').val(),
                    'zip': thisForm.find('input[name = "zip"]').val(),
                    'phone': phone,
                    'email': email,
                    'url': thisForm.find('input[name = "url"]').val(),
                    'notes': thisForm.find('textarea[name = "notes"]').val(),
                    'action': 'add',
                    'case_id': thisForm.find('select[name = "case_id"]').val()
                }, function (data) {
                    var serverResponse = $.parseJSON(data);
                    if (serverResponse.error === true) {
                        $('#notifications').show().html(serverResponse.message).delay(2000).fadeOut();
                    } else {
                        var successMsg = '<p class="text-success">' + serverResponse.message +
                        '</p><p><a class="btn show-form" href="#">Add Another?</a></p>';
                        thisForm[0].reset();
                        var hideForm = $('form[name="quick_contact"]').detach();
                        $('#qaContact').append(successMsg);
                        $('a.show-form').click(function (event) {
                            event.preventDefault();
                            $('#qaContact').html('').append(hideForm);
                        });
                    }
                });
        }
    });

    //Case sections
    $('.li-expand > a').click(function (event) {
        event.preventDefault();
        $(this).parent().find('ul').toggle();
    });

    //Messages
    $('.container').on('click', 'div.msg-header', function (event) {
        event.stopPropagation();
        var target = $(this).closest('li');
        $(this).next('ul').toggle();
        if (!target.find('.ul-reply').length) { //if we haven't already loaded replies
            $.get('html/templates/mobile/Messages.php', {type: 'replies', thread_id: target.attr('data-thread')}, function (data) {
                target.find('li').last().append(data);
                //mark as read
                $.post('lib/php/data/messages_process.php', {action: 'mark_read', id: target.attr('data-thread')});

            });
        }
    });

    $('.truncate').click(function (event) {
        event.preventDefault();
        event.stopPropagation();
        $(this).toggleClass('truncate');
    });

    var folderType = getParameterByName('type');
    var folder;

    if (folderType) {
        folder =  folderType;
        if (folderType === 'search') {
            $('.search-query').val(getParameterByName('s'));
            $('select[name="msg-status"]').append('<option  value="sr">Search Results</option>');
            setTimeout(function () {
                $('select[name="msg-status"] option').last().prop('selected', true);
            }, 1);
        }
    } else {
        folder = 'inbox';
    }

    $('select[name="msg-status"]').val(getParameterByName('type')).change(function () {
        location.href = 'index.php?i=Messages.php&type=' + $(this).val();
    });

    $('.search-submit').click(function () {
        location.href = 'index.php?i=Messages.php&type=search&s=' + $('.search-query').val();
    });

    $('.container').eq(1).on('click', '.send-reply', function (event) {
        event.preventDefault();
        var threadId =  $(this).closest('.li-expand-msg').attr('data-thread');
        var replyText = $(this).prev().val();
        var msgBody = $(this).parents('ul').eq(1);
        $.post('lib/php/data/messages_process.php',
            {action: 'reply', thread_id: threadId, reply_text: replyText},
            function (data) {
                var serverResponse = $.parseJSON(data);
                $('#notifications').show().html(serverResponse.message).delay(2000).fadeOut();
                msgBody.find('.ul-reply').remove();
                msgBody.hide();
            });
    });

    $('.btn-new-msg').click(function (event) {
        event.preventDefault();
        var hideMsg = $('.msg_display, .row:eq(2)').detach();
        $('.msg-new').show();
        $('#msg_tos, #msg_ccs, #msg_file').chosen({ width: '16em' });

        $('form[name="send_message"]').validate({
            errorClass: 'text-error',
            errorElement: 'span',
            errorPlacement: function (error, element) {
                if (element.is(':hidden')) {
                    element.next().parent().append(error);
                }
                else {
                    error.insertAfter(element);
                }

            },
            onsubmit: function () { //special handling for chosen selects. see http://goo.gl/myKIz
                var ChosenDropDowns = $('.chzn-done');
                ChosenDropDowns.each(function () {
                    var ID = $(this).attr('id');
                    if (!$(this).valid()) {
                        $('#' + ID + '_chzn a').addClass('input-validation-error');
                    } else {
                        $('#' + ID + '_chzn a').removeClass('input-validation-error');
                    }
                });
            },
            submitHandler: function () {
                var thisForm = $('form[name="send_message"]');
                $.post('lib/php/data/messages_process.php', thisForm.serialize(), function (data) {
                    var serverResponse = $.parseJSON(data);
                    if (serverResponse.error) {
                        $('#notifications').show().html(serverResponse.message).delay(2000).fadeOut();
                    } else {
                        $('#notifications').show().html(serverResponse.message).delay(2000).fadeOut();
                        thisForm[0].reset();
                        $('select').trigger('liszt:updated');
                        $('.text-error').remove();
                        $('.msg-new').hide();
                        $('#msg-head').append(hideMsg);
                    }
                });
            }
        });

        var settings = $.data($('form[name="send_message"]')[0], 'validator').settings;
        settings.ignore += ':not(.chzn-done)';

        $('.msg-cancel').click(function (event) {
            event.preventDefault();
            $('.msg-new').hide();
            $('#msg-head').append(hideMsg);
            $('form[name="send_message"]')[0].reset();
            $('select').trigger('liszt:updated');
        });

    });

    //Pagination for messages
    $('.msg_display').on('click', '.add-more', function (event) {
        event.preventDefault();
        $(this).remove();
        var msgUrl = $(this).attr('href');
        $.get(msgUrl, function (data) {
            var moreMsg = $(data).find('.msg_display > ul').html();
            $('.msg_display > ul').append(moreMsg);
        });

    });

    //Handle board downloads
    $('.board-container a.attachment').click(function (event) {
        event.preventDefault();
        var itemId = $(this).attr('data-id');
        $.download('lib/php/data/board_process.php', {'item_id': itemId, 'action': 'download'});
    });

    $('input.board-search').keyup(function () {
        var searchVal = $(this).val().toLowerCase();
        $('div.board-item').removeClass('search-result-hit search-result-miss');
        $('div.board-item').each(function () {
            if ($(this).find('.searchable').children().andSelf().text().toLowerCase().indexOf(searchVal) !== -1) {
                $(this).addClass('search-result-hit');
            } else {
                $(this).addClass('search-result-miss');
            }
        });

    });

    //Home Nav
    var calendarViewed = false;
    $('#home-nav-toggle input').change(function() {
            if ($(this).attr('id') === 'option1'){
                $('#upcoming').removeClass('visible-xs-block').addClass('hidden-xs');
                $('#activities').removeClass('hidden-xs').addClass('visible-xs-block');
            } else {
                $('#activities').removeClass('visible-xs-block').addClass('hidden-xs');
                $('#upcoming').removeClass('hidden-xs').addClass('visible-xs-block');
                if (!calendarViewed){
                    showEvent();
                    calendarViewed = true;
                }
            }
        });

    //Initialize calendar

    //function to pad month values with leading zero
    function pad(n){return n<10 ? '0'+n : n;}

    function showEvent (monthSearch){
        $('#fail').hide();
        if (monthSearch === 'undefined'){
            var curDate  = new Date();
            monthSearch = curDate.getFullYear() + '-' + pad(curDate.getMonth() + 1);
        }

        if ($('[id^=' + monthSearch + ']').length > 0){ //if there are any events this month
            $('#upcoming_events_list').stop(true).scrollTo('#' + $('[id^=' + monthSearch + ']')[0].id, {duration:0, interrupt:true});
            if($('[id^=' + monthSearch + ']').closest('a').hasClass('noncase-event')){
                $('[id^=' + monthSearch + ']').closest('a').addClass('cal-noncase-event');
            } else {
                $('[id^=' + monthSearch + ']').closest('a').addClass('cal-case-event');
            }
        } else {
            $('#fail').show();
            $('#upcoming_events_list').stop(true).scrollTo('#fail', {duration:0, interrupt:true});
        }
    }

    $('#calendar').zabuto_calendar({
        legend: [
            {type: 'block', label: 'Case Event', classname: 'cal-case-event'},
            {type: 'block', label: 'Non-case Event', classname: 'cal-noncase-event'}
        ],
        ajax: {
            url: 'lib/php/data/home_events_load.php?summary=1',
            modal: false
        },
        action: function() {
            var target = this.id.substr(this.id.lastIndexOf('_') +1);
            $('#upcoming_events_list').stop(true).scrollTo('#' + target, {duration:1000, interrupt:true});
            $('.list-group-item').removeClass('cal-noncase-event cal-case-event');
            if ($('#' + target).closest('a').hasClass('noncase-event')){
                $('#' + target).closest('a').addClass('cal-noncase-event');
            } else {
                $('#' + target).closest('a').addClass('cal-case-event');
            }
        },
        action_nav: function() {
            //find events for current month in the events list
            showEvent($('#' + this.id).data('to').year + '-' +  pad($('#' + this.id).data('to').month));
        }
    });

    if ($('#upcoming_events_list').length > 0){
        $.ajax({
            url: 'lib/php/data/home_events_load.php',
            dataType: 'json',
            success: function (data) {
                var display = '<div class="list-group">';
                var startTime, endTime, bgType;
                data.forEach(function(data){
                    //Create (non-unique, I'm afraid) id for date
                    var d = data.start;
                    var zabId = d.split(' ');
                    //Format times
                    if (data.allDay){
                        startTime = moment(data.start).format('MMMM Do YYYY');
                        endTime = moment(data.end).format('MMMM Do YYYY');
                    } else {
                        startTime = moment(data.start).format('MMMM Do YYYY, h:mm a');
                        endTime = moment(data.end).format('MMMM Do YYYY, h:mm a');
                    }
                    //Bgcolor based on case/non-case
                    if (data.caseId === 'NC'){
                        bgType = 'noncase-event';
                    } else {
                        bgType = 'case-event';
                    }
                    display += '  <a href="#" class="list-group-item list-group-item-cal ' + bgType +
                    '"> <h3 class="list-group-item-heading text-center" id="' + zabId[0] + '">' + data.shortTitle + '</h3>' +
                    '<dl class="dl-horizontal">' +
                    '<dt class="list-group-item-text">Start:</dt><dd> ' + startTime + '</dd>' +
                    '<dt class="list-group-item-text">End:</dt><dd> ' + endTime + '</dd>' +
                    '<dt class="list-group-item-text">Where:</dt><dd> ' + data.where +  '</dd>' +
                    '<dt class="list-group-item-text">Case: </dt><dd> ' + data.caseName +  '</dd></dl>' +
                    '<p class="list-group-item-text text-center">' + data.description +  '</p></a>';

                });
                display += '</div>';
                $('#upcoming_events_list').html(display)
                .append('<h3 id="fail">No events this month</h3><div style="height:400px"></div>');
                //Look for any events in current month
                showEvent();
            }
        });
    }
});
