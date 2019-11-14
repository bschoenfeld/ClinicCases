
<?php

session_start();
require('../lib/php/auth/session_check.php');

?>

<html>

<head>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
</head>

<body>

<h4>Manage Practice Panther</h4>

<div>
    <button class="btn btn-primary m-3" id='sync-button'>Prepare Sync (no data will be modified)</button>
    <div class="m-3" id='sync-details'></div>
    <button class="btn btn-warning m-3" style='display:none' id='sync-run-button'>Run All Tasks (Practice Panther will be modified)</button>
    <div class="m-3" id='sync-status'></div>
</div>

<script
  src="https://code.jquery.com/jquery-3.4.1.min.js"
  integrity="sha256-CSXorXvZcTkaix6Yvo6HppcZGetbYMGWSFlBw8HfCJo="
  crossorigin="anonymous"></script>

<script>
    function runSyncTask(tasks, name, next) {
        var allDone = true;

        $.each(tasks, function(i) {
            var task = this;
            if(task.done) return true;

            allDone = false;

            $.post(name + '.php', task)
                .done(function() {
                    task.done = true;
                    $('#sync-status').html(name + ' - ' + (i+1) + ' of ' + tasks.length);
                    runSyncTask(tasks, name, next);
                })
                .fail(function(e){
                    console.log(e);
                    $('#sync-status').html(e.responseText);
                });
            return false;
        });

        if(allDone) next();
    }

    function runNextAdd() {
        runSyncTask(window.toSync.adds, 'add', runNextModify);
    }

    function runNextModify() {
        runSyncTask(window.toSync.modifies, 'modify', runNextDelete);
    }

    function runNextDelete() {
        runSyncTask(window.toSync.deletes, 'delete', syncComplete);
    }

    function syncComplete() {

    }

    $(function() {

        $('#sync-button').click(function() {
            window.toSync = null;

            $('#sync-details').html("Preparing Sync");
            $('#sync-run-button').hide();

            $.get('sync.php')
                .done(function(data){
                    $('#sync-details').html('Sync Tasks To Run: <br>');
                    $('#sync-details').append(data.adds.length + ' contacts will be added <br>');
                    $('#sync-details').append(data.modifies.length + ' contacts will be modifed <br>');
                    $('#sync-details').append(data.deletes.length + ' contacts will be deleted <br>');
                    $('#sync-run-button').show();
                    window.toSync = data;
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
    });
</script>
</body>
</html>