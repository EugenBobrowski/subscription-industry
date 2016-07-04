"use strict";

(function ($) {

    var $import_form, $upload_field, $import_table, $import_text;

    $(document).ready(function () {
        $import_form = $('#import_form');
        $upload_field = $('#import_csv');
        $import_table = $('#import_table');
        $import_text = $('#import_text');

        theTable.table = $import_table;

        $upload_field.change(function () {

            var $this = $(this);
            var files = $this.get(0).files;

            for (var i = 0, numFiles = files.length; i < numFiles; i++) {
                var file = files[i];

                //Only plain text
                if (!file.type.match('csv')) continue;

                var reader = new FileReader();

                reader.onloadend = function () {

                    theTable.done = function () {
                        $this.emptyAtfUpload();
                    };

                    theTable.insertCSV(csv2object(reader.result));


                };

                //Read the text file
                reader.readAsText(file);

            }

        });
        $import_text.change(function () {

            var $this = $(this),
                Content = $this.val(),
                csv = {},
                emails = Content.split(/(\n|,|\s)/)
                    .map(function (val) {
                        return val.trim();
                    })
                    .filter(function (val) {
                        if (validateEmail(val)) return true;
                    });

            emails.forEach(function (val, id) {
                csv[id] = {
                    name: '',
                    email: val,
                    groups: '',
                    status: false,
                    last_send: ''
                };
            });
            theTable.done = function () {
                $import_text.val('');
            };
            theTable.insertCSV(csv);

        });
        $import_form.on('submit', importBuddies.onsubmit);
    });

    var importBuddies = {};

    importBuddies.onsubmit = function (e) {
        e.preventDefault();

        importBuddies.this = $(this);
        importBuddies.prepare_ajax_data();
        importBuddies.ajax();

    };

    importBuddies.prepare_ajax_data = function () {

        var data = {};

        data.data = [];

        importBuddies.checked = importBuddies.this.find('input.subscriber:checked');
        importBuddies.checked.each(function () {
            var $this = $(this);
            var val = JSON.parse($this.val());
            data.data.push(val);
            console.log('data', $this.data('email'));
        });

        data.unconfirmed2all = importBuddies.this.find('[name=set_unconfirm]:checked').val();
        data.send2unconfirmed = importBuddies.this.find('[name=confirm]:checked').val();
        data.action = 'do_import';

        importBuddies.ajax_data = data;

        console.log(importBuddies.ajax_data);
    };

    importBuddies.ajax = function () {
        $.post(si_admin_ajax.ajax_url, importBuddies.ajax_data, importBuddies.callback);
    };

    importBuddies.callback = function (response) {
        console.log('callback', response);

        var report = JSON.parse(response);

        console.log(report);

        importBuddies.checked.each(function () {
            var $this = $(this),
                val = JSON.parse($this.val()),
                $name = $this.parents('tr').find('.name');

            $this.prop('checked', false);
            $this.prop('disabled', true);

            if (report.imported.indexOf(val.email) > -1) {
                $name.append(' <em>Imported</em>');
                
            }
            /**
             * ToDo: for others types
             */
            //"no_mail":0,"wrong_mails":[],"existing":[],"imported":["yullis2008@gmail.com"]}

        });

        for(var type in report) {
            if (type == 'imported') {
                report.imported.forEach(function (val) {
                    var $field = $import_table.find('input[data-email="'+val+'"]');
                    $field.addClass('dfslkjgldfskgjlfgkj');
                    console.log($field);
                });
                $import_table.find('[data]')
            }
        }

    };

    var theTable = {};

    theTable.insertCSV = function (csvObj) {

        if (theTable.table == undefined) return;

        theTable.emptyLine = theTable.table.find('.empty-line');
        theTable.emptyLine.hide();


        var data = {
            'action': 'check_import',
            'data': csvObj
        };
        jQuery.post(si_admin_ajax.ajax_url, data, function (response) {

            csvObj = JSON.parse(response);

            for (var line_id in csvObj) {

                theTable.insertLine(csvObj[line_id]);

            }

            theTable.showLines();

        });


    };

    theTable.insertLine = function (line) {

        if (!validateEmail(line.email)) return;


        var newLine = theTable.emptyLine.clone();
        var status = false;

        if (line.status == 'Confirmed' || (line.status && line.status != 'Unconfirmed' && line.status && line.status != 'false')) status = true;

        newLine.removeClass('empty-line');
        newLine.find('.name').html('<strong>' + line.name + '</strong>');
        newLine.find('.name').prepend(line.gravatar);
        newLine.find('.email').html('<a href="mailto:' + line.email + '">' + line.email + '</a>');
        newLine.find('.group').text(line.groups);
        newLine.find('.status').html((status) ? '<strong class="confirmed">Confirmed</strong>' : '<strong class="unconfirmed">Unconfirmed</strong>');
        newLine.find('.last-send').text(line.last_send);

        if (line.exists != undefined && line.exists) {
            newLine.find('.name').append(' <em>Already exists</em> ');
            newLine.addClass('exists');
        } else {
            newLine.find('input.subscriber').prop('disabled', false).val(JSON.stringify(line)).data('email', line.email);
        }

        newLine.insertBefore(theTable.emptyLine).hide();


    };

    theTable.showLines = function () {
        var lines = theTable.table.find('tr').filter(':hidden:not(.empty-line)');
        theTable.showOneLine(lines);
    };
    theTable.showOneLine = function (lines) {

        lines.filter(':first').show('slow');

        var last = lines.filter(':hidden');
        if (last.length < 1) {
            if (theTable.done != undefined) theTable.done();
            return;
        }

        setTimeout(function () {
            theTable.showOneLine(last);
        }, 10)
    };

    function validateEmail(email) {
        var re = /^(([^<>()[\]\\.,;:\s@\"]+(\.[^<>()[\]\\.,;:\s@\"]+)*)|(\".+\"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
        return re.test(email);
    }

    function csv2object(csv) {
        var lines = csv.split("\n").map(function (val) {
                return val.split(",");
            }
            ),
            headers = lines.shift(),
            parsedCsv = {};

        lines.forEach(function (line, line_id) {
            var object = {};
            headers.forEach(function (value, index) {
                object[value] = line[index]
            });
            parsedCsv[line_id] = object;
        });
        return parsedCsv;
    }
})(jQuery);