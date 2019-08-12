 //
//Scripts for Case data
//

/* global newCaseValidate, notify, oTable, elPrint, checkConflicts, closeCaseTab */

function formatCaseData(thisPanel, type) { //Apply CSS
    var navItem = thisPanel.siblings('.case_detail_nav').find('#item2');
    navItem.find('div').remove();

    var toolsHeight = navItem.outerHeight();
    var thisPanelHeight = navItem.closest('.case_detail_nav').height();
    var documentsWindowHeight = thisPanelHeight - toolsHeight;
    if (typeof caseNotesWindowHeight === 'undefined') {
        var caseNotesWindowHeight = thisPanelHeight - toolsHeight;
    }

    $('div.case_detail_panel_tools').css({'height': toolsHeight});
    $('div.case_detail_panel_casenotes').css({'height': caseNotesWindowHeight});
    $('div.case_detail_panel_tools_left').css({'width': '30%'});
    $('div.case_detail_panel_tools_right').css({'width': '70%'});


    //thisPanel.find('div.case_data_value').not('div.case_data_name + div.case_data_value').css({'margin-left': '21%'});

    //Apply shadow on scroll
    $('.case_detail_panel_casenotes').bind('scroll', function () {
        var scrollAmount = $(this).scrollTop();
        if (scrollAmount === 0 && $(this).hasClass('csenote_shadow')) {
            $(this).removeClass('csenote_shadow');
        } else {
            $(this).addClass('csenote_shadow');
        }
    });

    //format the form
    if (type === 'new' || type === 'edit') {
        thisPanel.find('input[name="id"]').parent().hide();
        thisPanel.find('input[name="opened_by"]').parent().remove();
        if (thisPanel.find('input[name="organization"]').val() === 'New Case') {
            thisPanel.find('input[name="organization"]').val('');
        }

        //Enable dynamic replacement of clinic and case codes in case number
        var cN = thisPanel.find('input[name="clinic_id"]');
        var cnVal = cN.val();
        if (cnVal.indexOf('ClinicType') !== -1) {
            thisPanel.find('select[name="clinic_type"]').change(function () {
                cN.val(cnVal.replace('ClinicType', $(this).find('option:selected').attr('data-code')));
            });
        }

        if (cnVal.indexOf('CaseType') !== -1) {
            thisPanel.find('select[name="case_type"]').change(function () {
                cN.val(cnVal.replace('CaseType', $(this).find('option:selected').attr('data-code')));
            });
        }

        //Add onbeforeunload event to prevent empty cases
        $(window).bind('beforeunload', function () {
            return 'You may have unsaved changes on a case.  Please ' +
            'either save any changes or close the case\'s tab before leaving this page';
        });

        //Disable case number editing
        thisPanel.find('input[name="clinic_id"]').attr('disabled', true)
        .after('<a class="force_edit small" href="#">Let me edit this</a>');

        thisPanel.find('a.force_edit').click(function (event) {
            event.preventDefault();
            var dialogWin = $('<div class="dialog-casenote-delete" title="Are you sure?"><p>' +
            'ClinicCases automatically assigns the next available case number.  If your ' +
            'case number contains "CaseType" or "ClinicType", these values will be ' +
            'replaced when you change those fields below.</p><br /><p>Manually editing ' +
            'a case number may have undesirable results. Are you sure?</p></div>').dialog({
                autoOpen: false,
                resizable: false,
                modal: true,
                buttons: {
                    'Yes': function () {
                        $('input[name="clinic_id"]').attr('disabled', false).focus();
                        thisPanel.find('a.force_edit').remove();
                        $(this).dialog('destroy');
                    },
                    'No': function () {
                        $(this).dialog('destroy');
                    }
                }
            });

            $(dialogWin).dialog('open');
        });

        //Add case re-opening feature
        if (thisPanel.find('input[name="date_close"]').val() !== '') {
            thisPanel.find('input[name="date_close"]').after('<a class="force_reopen small" href="#">Re-open this case</a>');

            thisPanel.find('a.force_reopen').click(function (event) {
                event.preventDefault();
                var dialogWin = $('<div class="dialog-casenote-delete" title="Are you sure?">' +
                '<p>This will re-open the case. Are you sure?</p></div>').dialog({
                    autoOpen: false,
                    resizable: false,
                    modal: true,
                    buttons: {
                        'Yes': function () {
                            $('input[name="date_close"]').datepicker('setDate', null).next().html('');
                            $('select[name="dispo"]').val('').trigger('liszt:updated');
                            thisPanel.find('a.force_reopen').remove();
                            $(this).dialog('destroy');
                        },
                        'No': function () {
                            $(this).dialog('destroy');
                        }
                    }
                });

                $(dialogWin).dialog('open');
            });
        }
        //Add chosen to selects
        thisPanel.find('select').chosen();

        //Align dual input fields with the first ones
        thisPanel.find('span.dual_input').css({'display': 'block'});

        //Align multi-text fields and email fields with the first ones
        thisPanel.find('span.multi-text').css({'display': 'block'});

        //Add link to trigger a new dual field
        thisPanel.find('.new_case_data_display').has('span.dual_input').each(function () {
            $(this).find('span.dual_input').last().after('<a class="add_another small" href="#">Add another</a>');
        });

        //Add link to a new multi-text field
        thisPanel.find('.new_case_data_display').has('span.multi-text').each(function () {
            $(this).find('span.multi-text').last().after('<a class="add_another small" href="#">Add another</a>');
        });

        //Add link to a new multi-text field
        thisPanel.find('.new_case_data_display').has('.m-textarea').each(function () {
            $(this).find('textarea').each(function() {
                if(!$(this).val()) {
                    var currentdate = new Date();
                    var dateStr = "" +
                        (currentdate.getMonth()+1) + "/" +
                        currentdate.getDate()      + "/"  +
                        currentdate.getFullYear();
                    $(this).attr("data-date", dateStr);
                    $(this).val(dateStr + "\n");
                }
            })
            $(this).find('textarea').last().after('<a class="add_another small" href="#">Add another</a>');
        });

        //Add datepickers
        thisPanel.find('input.date_field').each(function () {
            var b = $.datepicker.parseDate('yy-mm-dd', $(this).val());
            var buttonVal = $.datepicker.formatDate('mm/dd/yy', b);
            $(this).datepicker({
                dateFormat: 'yy-mm-dd',
                showOn: 'button',
                buttonText: buttonVal,
                onSelect: function (dateText) {
                    var c = $.datepicker.parseDate('yy-mm-dd', dateText);
                    var displayDate = $.datepicker.formatDate('mm/dd/yy', c);
                    $(this).next().html(displayDate);
                }
            });
        });

        //Add textarea expander
        thisPanel.find('textarea').TextAreaExpander(100, 250);

        //highlight the tab so user knows there are unsaved changes
        $('#case_detail_tab_row').find('li.ui-state-active').addClass('ui-state-highlight');

        //Change name on tab when user enters last name
        thisPanel.find('input[name="first_name"]').focus();

        $('input[name = "last_name"]').keyup(function () {
            var fname = thisPanel.find('input[name="first_name"]').val();
            $(this).closest('#case_detail_tab_row')
            .find('li.ui-state-active').find('a').html(escapeHtml($(this).val()) + ', ' + escapeHtml(fname));
            //Put client name on case title
            $(this).closest('#case_detail_tab_row')
                .find('div.case_title')
                .html('<h2>' + escapeHtml(fname) + ' ' + escapeHtml($(this).val()) + '</h2>');
        });

        //If there is no last name, put the organization name on the tab
        $('input[name = "organization"]').keyup(function () {
            var lnameVal = $(this).closest('form').find('input[name="last_name"]').val();
            if (lnameVal === '') {
                $(this).closest('#case_detail_tab_row')
                .find('li.ui-state-active').find('a').html(escapeHtml($(this).val()));

                //Put organization name on case title
                $(this).closest('#case_detail_tab_row').find('div.case_title').html('<h2>' + escapeHtml($(this).val()) + '</h2>');
            }
        });

        $('input:[name="number_children"]').keyup(calcPctPovertyLevel);
        $('input:[name="number_adults"]').keyup(calcPctPovertyLevel);
        $('input:[name="annual_seniors"]').keyup(calcPctPovertyLevel);

        $('input:[name="monthly_income"]').keyup(calcAnnualIncome);
        $('input:[name="annual_income"]').keyup(calcMonthlyIncome);

        // Make sure hitting 'ENTER' on input's doesn't do anything
        $('.new_case_data input').bind('keyup keypress', function(e) {
            var keyCode = e.keyCode || e.which;
            if (keyCode === 13) { 
                e.preventDefault();
                return false;
            }
        });

        thisPanel.find('button.case_modify_submit').button({icons: {primary: 'fff-icon-page-white-get'}, text: true});
        thisPanel.find('button.case_cancel_submit').button({icons: {primary: 'fff-icon-cross'}, text: true});
    } else {  //display case data
        //format buttons
        thisPanel.find('button.case_data_edit').button({icons: {primary: 'fff-icon-page-edit'}, text: true});
        thisPanel.find('button.case_data_delete').button({icons: {primary: 'fff-icon-bin-closed'}, text: true});
        thisPanel.find('button.case_data_print').button({icons: {primary: 'fff-icon-printer'}, text: true});

        //remove the id
        thisPanel.find('div.id_display').remove();
    }

    var sectionTitleClass = '.case_data_section_title';
    if (type === 'new' || type === 'edit') {
        sectionTitleClass = '.new_case_data_section_title';
    }
    
    thisPanel.find('.case_detail_panel_casenotes').scrollTop(0);

    $.each(thisPanel.find(sectionTitleClass), function() { 
        var menuText = $(this).text();

        if(menuText === "CASE INTAKE") {
            menuText = "Case Intake";
        } else if(menuText === "Tenant Identity") {
            menuText = "Tenant ID";
        } else if(menuText === "Other Conflict Check Information") {
            menuText = "Conflicts";
        } else if(menuText === "Tenant Personal Details") {
            menuText = "Tenant Info";
        } else if(menuText === "Household Size and Finances") {
            menuText = "Household";
        } else if(menuText === "Housing") {
            menuText = "Housing";
        } else if(menuText === "Eviction") {
            menuText = "Eviction";
        }else if(menuText === "ATTORNEY ADVICE") {
            menuText = "Attorney Advice";
        } else if(menuText === "VPLC FOLLOW UP & REVIEW") {
            menuText = "VPLC Follow Up";
        } else {
            menuText = null;
        }

        if(menuText) {
            var bgColor = $( this ).css( "background-color" );
            if(bgColor == "rgb(195, 217, 255)") {
                navItem.append('<div class="header-primary" onclick="scrollToHeader(' + ($(this).offset().top - 200) + ')">' + menuText + '</div>');
            } else if(bgColor == "rgb(255, 255, 220)") {
                navItem.append('<div class="header-secondary" onclick="scrollToHeader(' + ($(this).offset().top - 200)+ ')">' + menuText + '</div>'); 
            }
        }
    });
}

//User clicks on Case Data in left-side navigation
$('.case_detail_nav #item2').live('click', function () {
    if($(this).hasClass('selected')) return;

    var thisPanel = $(this).closest('.case_detail_nav').siblings('.case_detail_panel');
    var caseId = $(this).closest('.case_detail_nav').siblings('.case_detail_panel').data('CaseNumber');
    var type;

    if ($(this).hasClass('new_case')) {
        type = 'new';
    } else {
        type = 'display';
    }

    thisPanel.load('lib/php/data/cases_case_data_load.php', {
        'id': caseId,
        'type': type
    }, function () {
        formatCaseData(thisPanel, type);
    });
});

var scrollToHeader = function(location) {
    $('.case_detail_panel_casenotes').scrollTop(location);
    return false;
}

//Listen for edit
$('button.case_data_edit').live('click', function () {
    var thisPanel = $(this).closest('.case_detail_panel');
    var thisCaseId = thisPanel.data('CaseNumber');
    thisPanel.load('lib/php/data/cases_case_data_load.php', {
        'id': thisCaseId,
        'type': 'edit'
    }, function () {
        formatCaseData(thisPanel, 'edit');
    });
});

//Listen for delete
$('button.case_data_delete').live('click', function () {
    var caseId = $(this).closest('.case_detail_panel').data('CaseNumber');
    var tgt = $(this).closest('.ui-tabs-panel').attr('id');
    var dialogWin = $('<div class="dialog-casenote-delete" title="Are you sure?"><p>' +
    'This will completely delete this case and all its associated data. <br /><b>This cannot be undone</b>.</p>' +
    '<p>Are you sure?</p></div>')
    .dialog({
        autoOpen: false,
        resizable: false,
        modal: true,
        buttons: {
            'Yes': function () {
                $.post('lib/php/data/cases_case_data_process.php', {id: caseId, action: 'delete'}, function (data) {
                    var serverResponse = $.parseJSON(data);
                    if (!serverResponse.error) {
                        var el = $('a[href="#' + tgt + '"]').closest('li');
                        closeCaseTab(true,el);
                        oTable.fnReloadAjax();
                    }
                    notify(serverResponse.message);
                });
                $(this).dialog('destroy');
            },
            'No': function () {
                $(this).dialog('destroy');
            }
        }
    });
    $(dialogWin).dialog('open');
});

//Submit the form
$('button.case_modify_submit').live('click', function (event) {
    event.preventDefault();
    var resultTarget = $(this).closest('.case_detail_panel');
    var thisCaseId = resultTarget.data('CaseNumber');
    var actionType;

    //Server-side script does different things depending on whether
    //this is a new case which is just being opened or this is an
    //existing case which is being edited.  So, set variable:
    if ($(this).hasClass('update_new_case')) {
        actionType = 'update_new_case';
    } else {
        actionType = 'edit';
    }

    $(window).unbind('beforeunload');
    var formVals = resultTarget.find('form');

    //enable clinic_id field or else serializeArray won't pick up value
    formVals.find('input[name="clinic_id"]').attr({'disabled': false});
    var errString = newCaseValidate(formVals);
    var formValsArray = formVals.serializeArray();

    //notify user or errors or submit form
    if (errString.length) {
        notify(errString, true);

        //Reapply onbeforeunload event to prevent empty cases
        $(window).bind('beforeunload', function () {
            return 'You may have unsaved changes on a case.  Please either save' +
            ' any changes or close the case\'s tab before leaving this page';
        });

        $('input.ui-state-error').focus(function () {
            $(this).removeClass('ui-state-error');
        });
        return false;
    } else {
        //Reserialize form, taking out dual_inputs and multi-texts.  They are
        //dealt with seperately.

        var form = resultTarget.find('form');
        var formValsOk = form
        .find(':not(span.dual_input input, span.dual_input select, span.multi-text input, .m-textarea)')
        .serializeArray();
        formValsOk.push({'name': 'action', 'value': actionType});

        //Extract values from all multi-text fields
        formVals.find('.new_case_data_display').has('.multi-text').each(function () {
            var dataObj = {};
            var dataName = $(this).find('input').attr('name');
            $(this).find('span.multi-text').each(function () {
                var dataValue = $(this).find('input').val();
                if (dataValue.length > 0) {
                    dataObj[dataValue] = 'name';
                }
            });

            if (!$.isEmptyObject(dataObj)) {
                var dataJson = JSON.stringify(dataObj);
                formValsOk.push({'name': dataName, 'value': dataJson});
            }
        });

        //Extract values from all dual inputs
        formVals.find('.new_case_data_display').has('.dual_input').each(function () {
            var dataObj = {};
            var dataName = $(this).find('input:last').attr('name');
            $(this).find('span.dual_input').each(function () {
                var dataType = $(this).find('select.dual').val();
                var dataValue = $(this).find('input:last').val();
                if (dataValue.length > 0) {
                    dataObj[dataValue] = dataType;
                }
            });

            if (!$.isEmptyObject(dataObj)) {
                var dataJson = JSON.stringify(dataObj);
                formValsOk.push({'name': dataName, 'value': dataJson});
            }
        });

        //Extract values from all textarea-multi
        formVals.find('.new_case_data_display').has('.m-textarea').each(function () {
            var dataObj = {};
            var dataName = $(this).find('.m-textarea:last').attr('name');
            $(this).find('.m-textarea').each(function (i) {
                var dataValue = $(this).val();
                if (dataValue.length > 0 && dataValue != $(this).attr('data-date') + "\n") {
                    dataObj[i] = dataValue;
                }
            });

            if (!$.isEmptyObject(dataObj)) {
                var dataJson = JSON.stringify(dataObj);
                formValsOk.push({'name': dataName, 'value': dataJson});
            }
        });

        //Submit to server
        $.post('lib/php/data/cases_case_data_process.php', formValsOk, function (data) {
            var serverResponse = $.parseJSON(data);
            if (serverResponse.error === true) {
                notify(serverResponse.message, true);
            } else {
                notify(serverResponse.message);
                $('#case_detail_tab_row').find('li.ui-state-active')
                    .removeClass('ui-state-highlight ui-state-error new_case');

                resultTarget.load('lib/php/data/cases_case_data_load.php', {
                    'id': thisCaseId,
                    'type': 'display'
                }, function () {
                    formatCaseData(resultTarget, 'display');
                });

                //Refresh the table; see Cases.js
                ColReorder.fnReset(oTable);
                oTable.fnReloadAjax(null, function() {
                    $('#chooser').change();
                });
                //Notify of conflicts; see casesCaseDetail.js
                checkConflicts(thisCaseId);
            }
        });
    }
});

//Cancel New Case or Edit
$('button.case_cancel_submit').live('click', function (event) {
    event.preventDefault();
    var thisPanel = $(this).closest('.case_detail_panel');
    var caseId = $(this).closest('.case_detail_panel').data('CaseNumber');
    var type;

    if ($(this).hasClass('cancel_new_case')) {
        var dialogWin = $('<div class="dialog-casenote-delete" title="Are you sure?"><p>' +
        'This will delete your new case. Are you sure?</p></div>')
        .dialog({
            autoOpen: false,
            resizable: false,
            modal: true,
            buttons: {
                'Yes': function () {
                    $.post('lib/php/data/cases_case_data_process.php', {id: caseId, action: 'delete'}, function (data) {
                        var serverResponse = $.parseJSON(data);
                        if (!serverResponse.error) {
                            var el = $('li.new_case.ui-state-active');
                            closeCaseTab(true,el);
                        }
                        notify(serverResponse.message);
                    });
                    $(this).dialog('destroy');
                },
                'No': function () {
                    $(this).dialog('destroy');
                }
            }
        });
        $(dialogWin).dialog('open');
    } else {
        type = 'display';
        thisPanel.load('lib/php/data/cases_case_data_load.php', {'id': caseId, 'type': type}, function () {
            formatCaseData(thisPanel, type);
        });
    }
    $(window).unbind('beforeunload');
});

//Listen for print
$('button.case_data_print').live('click', function () {
    elPrint($(this).closest('div.case_detail_panel_tools')
    .siblings('div.case_detail_panel_casenotes'), 'Case Data: ' +
    $(this).closest('.case_detail_panel').siblings('.case_detail_bar').find('.case_title').text());
});

//Add another multi-text or dual
$('a.add_another').live('click', function (event) {
    event.preventDefault();

    // check for textarea-multi
    if($(this).prev('textarea').length) {
        var newTextarea = $(this).prev('textarea').clone();
        $(this).prev('textarea').after(newTextarea);
        var currentdate = new Date();
        var dateStr = "" +
            (currentdate.getMonth()+1) + "/" +
            currentdate.getDate()      + "/"  +
            currentdate.getFullYear();
        newTextarea.attr("data-date", dateStr);
        newTextarea.val(dateStr + "\n");
        newTextarea.focus();
        return;
    }

    var newField = $(this).prev('span').clone();
    newField.find('input').val('');
    newField.find('select').val('');
    newField.css({'display': 'block'});

    //deal with chosen
    newField.find('select').removeClass('chzn-done').css({'display': 'block'}).removeAttr('id').next('div').remove();

    //Insert new field, format
    $(this).prev('span').after(newField);
    newField.find('select').chosen();
    newField.find('input').focus();

});

function calcAnnualIncome() {
    var monthlyIncome = $('input:[name="monthly_income"]').val();
    $('input:[name="annual_income"]').val(monthlyIncome * 12);
    calcPctPovertyLevel();
}

function calcMonthlyIncome() {
    var annualIncome = $('input:[name="annual_income"]').val();
    $('input:[name="monthly_income"]').val(Math.floor(annualIncome / 12));
    calcPctPovertyLevel();
}

function calcPctPovertyLevel() {
    var personsToDollars = {
        1: 12490,
        2: 16910,
        3: 21330,
        4: 25750,
        5: 30170,
        6: 34590,
        7: 39010,
        8: 43430
    };

    var additionalDollarsPerPerson = 4420;

    var annualIncome = $('input:[name="annual_income"]').val();

    var persons = 0;
    persons += parseInt($('input:[name="number_children"]').val());
    persons += parseInt($('input:[name="number_adults"]').val());
    persons += parseInt($('input:[name="number_seniors"]').val());

    var povertyLevel = 0;
    if(persons < 9) {
        povertyLevel = personsToDollars[persons];
    } else {
        povertyLevel = personsToDollars[8];
        povertyLevel += additionalDollarsPerPerson * (persons - 8);
    }

    var pct = Math.floor(annualIncome / povertyLevel * 100);
    $('input:[name="poverty_level"]').val(pct);
}
