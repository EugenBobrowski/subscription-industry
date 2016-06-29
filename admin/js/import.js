"use strict";

(function ($) {

    var $upload_field, $import_table;

    $(document).ready(function () {

        $upload_field = $('#import_csv');
        $import_table = $('#import_table');

        $upload_field.change(function () {

            var $this = $(this);
            var files = $this.get(0).files;

            for (var i = 0, numFiles = files.length; i < numFiles; i++) {
                var file = files[i];

                //Only plain text
                if (!file.type.match('csv')) continue;

                var reader = new FileReader();

                reader.onloadend = function () {

                    importTable.table = $import_table;

                    importTable.done = function () {
                        $this.emptyAtfUpload();
                    };

                    importTable.insertCSV(csv2object(reader.result));


                };

                //Read the text file
                reader.readAsText(file);

            }

        });
        $('#import_form').on('submit', function (e) {
            e.preventDefault();

            var $this = $(this),
                data = {};
            
            console.log($upload_field.val());

            data.data = [];

            $this.find('input.subscriber:checked').each(function () {
                data.data.push(JSON.parse($(this).val()));
            });
            data.unconfirmed2all = $this.find('[name=set_unconfirm]:checked').val();
            data.send2unconfirmed = $this.find('[name=confirm]:checked').val();
            console.log(data);
        });
    });

    var importTable = {};

    importTable.insertCSV = function (csvObj) {

        if (importTable.table == undefined) return;

        importTable.emptyLine = importTable.table.find('.empty-line');
        importTable.emptyLine.hide();


        var data = {
            'action': 'check_import',
            'data' : csvObj
        };
        
        jQuery.post(si_admin_ajax.ajax_url, data, function(response) {

            csvObj = JSON.parse(response);

            for (var line_id in csvObj) {

                importTable.insertLine(csvObj[line_id]);

            }

            importTable.showLines();

        });




    };

    importTable.insertLine = function (line) {

        if (!validateEmail(line.email)) return;



        var newLine = importTable.emptyLine.clone();
        var status = false;

        if (line.status == 'Confirmed' || (line.status && line.status != 'Unconfirmed')) status = true;

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
            newLine.find('input.subscriber').prop('disabled', false).val(JSON.stringify(line));
        }

        newLine.insertBefore(importTable.emptyLine).hide();


    };

    importTable.showLines = function () {
        var lines = importTable.table.find('tr').filter(':hidden:not(.empty-line)');
        importTable.showOneLine(lines);
    };
    importTable.showOneLine = function (lines) {

        lines.filter(':first').show('slow');

        var last = lines.filter(':hidden');
        if (last.length < 1) {
            if (importTable.done != undefined) importTable.done();
            return;
        }

        setTimeout(function () {
            importTable.showOneLine(last);
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