<?php

session_start();
require('../lib/php/auth/session_check.php');

if ($_SESSION['group'] != 'admin') {
    exit('You do not have permission');
}

?>

<html>

<head>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
</head>

<body>

<h4>Manage Practice Panther</h4>

<a class='btn btn-secondary m-3' href='connect.php'>Reconnect to Practice Panther</a>

<hr>

<div>
    <button class="btn btn-primary m-3" id='sync-button'>Prepare Sync (no data will be modified)</button>
    <div class="m-3" id='sync-details'></div>

    <button class="btn btn-warning m-3" style='display:none' id='sync-run-button'>Run All Tasks (Practice Panther will be modified)</button>
    <div class="m-3" id='sync-status'></div>

    <button class="btn btn-info m-3" style='display:none' id='conflict-run-button'>Check for Conflicts</button>
    <div class="m-3" id='conflict-status'></div>
    <div class="m-3" id='conflicts-found'></div>
</div>

<hr>

<div>
    <button class="btn btn-primary m-3" id='delete-all-button'>Prepare Delete All (no data will be modified)</button>
    <div class="m-3" id='delete-all-details'></div>
    <button class="btn btn-danger m-3" style='display:none' id='delete-all-run-button'>Delete All EH Contacts (Practice Panther will be modified)</button>
    <div class="m-3" id='delete-all-status'></div>
</div>

<script
  src="https://code.jquery.com/jquery-3.4.1.min.js"
  integrity="sha256-CSXorXvZcTkaix6Yvo6HppcZGetbYMGWSFlBw8HfCJo="
  crossorigin="anonymous"></script>

<script>
    function runSyncTask(tasks, name, statusId, next) {
        var allDone = true;

        $.each(tasks, function(i) {
            var task = this;
            if(task.done) return true;

            allDone = false;

            $.post(name + '.php', task)
                .done(function(data) {
                    task.done = true;
                    $('#' + statusId).html(name + ' - ' + (i+1) + ' of ' + tasks.length);
                    runSyncTask(tasks, name, statusId, next);
                })
                .fail(function(e){
                    console.log(e);
                    $('#' + statusId).html(e.responseText);
                });
            return false;
        });

        if(allDone) next();
    }

    function runNextAdd() {
        runSyncTask(window.toSync.adds, 'add', 'sync-status', runNextDelete);
    }

    function runNextDelete() {
        runSyncTask(window.toSync.deletes, 'delete', 'sync-status', syncComplete);
    }

    function syncComplete() {
        $('#sync-status').html('Sync Complete');
    }

    function deleteAllComplete() {
        $('#delete-all-status').html('Delete All Complete');
    }

    function conflictComplete() {
        $('#conflict-status').html('Conflict Check Complete');
    }

    $(function() {

        $('#sync-button').click(function() {
            window.toSync = null;

            $('#sync-details').html("Preparing Sync");
            $('#sync-status').html("");
            $('#sync-run-button').hide();
            $('#conflict-run-button').hide();

            $.get('sync.php')
                .done(function(data){
                    window.toSync = data;

                    if(data.adds.length || data.deletes.length) {
                        $('#sync-details').html('Sync Tasks To Run: <br>');
                        $('#sync-details').append(data.adds.length + ' contacts will be added <br>');
                        $('#sync-details').append(data.deletes.length + ' contacts will be deleted <br>');
                        $('#sync-run-button').show();
                    } else {
                        $('#sync-details').html('Nothing to sync. Conflict check can be run on EH cases.');
                        $('#conflict-run-button').show();
                    }
                })
                .fail(function(e){
                    console.log(e);
                    $('#sync-details').html(e.responseText);
                });
        });

        $('#sync-run-button').click(function() {
            if(!window.toSync) return;
            runNextAdd();
        });

        $('#delete-all-button').click(function() {
            window.toDelete = null;

            $('#delete-all-details').html("Getting list of contacts to delete");
            $('#delete-all-run-button').hide();

            $.get('delete_all.php')
                .done(function(data){
                    $('#delete-all-details').html(data.length + ' contacts will be deleted from Practice Panther');
                    $('#delete-all-run-button').show();
                    window.toDelete = data;
                })
                .fail(function(e){
                    console.log(e);
                    $('#sync-details').html(e.responseText);
                });
        });

        $('#delete-all-run-button').click(function() {
            if(!window.toDelete) return;
            runSyncTask(window.toDelete, 'delete', 'delete-all-status', deleteAllComplete);
        });

        $('#conflict-run-button').click(function() {
            $.get('conflicts.php')
                .done(function(data){
                    console.log(data);
                    if (data.conflicts.length) {
                        $('#conflict-status').html(data.conflicts.length + ' conflicts found');
                        $.each(data.conflicts, function() {
                            // Write EH contact details
                            var text = this.eh.ehCaseNumber;
                            text += ' - <b>' + this.eh.firstName + ' ' + this.eh.lastName + '</b> ' + this.eh.role;
                            if (this.eh.adverseParty == 'No')
                                text += ' (Not Adverse)';
                            else
                                text += ' (Adverse)';

                            // Write PP contact details
                            text += ' || PP Contact: ';
                            text += '<b>' + this.pp.firstName + ' ' + this.pp.lastName + '</b>';
                            if (this.pp.ehCaseNumber)
                                text += ' EH Case Id:' + this.pp.ehCaseNumber;
                            else
                                text += ' Not from EH';

                            if (this.pp.adverseParty == 'No')
                                text += ' (Not Adverse)';
                            else if(this.pp.adverseParty == 'Yes')
                                text += ' (Adverse)';
                            else
                                text += ' (Unknown Adversity)';
                            $('#conflict-status').append('<div class="ml-5">' + text + '</div>')
                        });
                    } else {
                        $('#conflict-status').html('No conflicts found');
                    }
                })
                .fail(function(e){
                    console.log(e);
                    $('#conflict-status').html(e.responseText);
                });
        });
    });
</script>
</body>
</html>