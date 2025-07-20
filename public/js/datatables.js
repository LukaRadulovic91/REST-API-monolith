function initAdvanceTable(data, filterData) {
    if (!data) {
        return;
    }
    let formData;
    if (!$.fn.DataTable.isDataTable(data.selector)) {
        let domDefined = false;
        if (typeof data.dom !== 'undefined') {
            domDefined = true;
        }
        let defaults = {
            "oSearch": {
                "bSmart": false,
                "bRegex": true
            },
            columns: data.columns,
            ordering: true,
            stateSave: true,
            autoWidth: false,
            processing: true,
            colReorder: false,
            lengthChange: true,
            serverSide: true,
            responsive: {
                details: {
                    renderer: function (api, rowIdx, columns) {
                        var data = $.map(columns, function (col, i) {
                            return col.hidden ?
                                '<tr data-dt-row="' + col.rowIndex + '" data-dt-column="' + col.columnIndex + '">' +
                                '<td>' + col.title + ':' + '</td> ' +
                                '<td>' + col.data + '</td>' +
                                '</tr>' :
                                '';
                        }).join('');
                        return data ?
                            $('<table/>').append(data) :
                            false;
                    }
                }
            },
            pageLength: 10,
            select: {
                info: false,
                style: 'os',
                className: 'row-selected'
            },
            buttons: renderAdvanceButtons(data),
            order: [
                [0, 'asc']
            ],
        };



        $(document).on('click', '.default-state', function(e) {
            stopReloadEvent = true;
            setDefaultProperties(defaults, data)
            stopReloadEvent = false;
        });
        $('#logout-form').on('submit', localStorage.clear());

        let datatableParams = Object.assign({}, defaults, data);

        datatableParams.ajax = {
            url: data.ajax.toString(),
            type: 'POST',
            headers: {
                'X-CSRF-TOKEN':  $('meta[name="csrf-token"]').attr('content')
            },
            data: {
                formData,
            },
        };
        //Initialize DataTables with our params
        $(data.selector).DataTable(datatableParams);
        advanceTableInstance = $(data.selector).DataTable();
        //fill multiview settings variable
        defaultAdvanceSetup = defaults;
        originalAdvanceTableSetup = data;
        // Replace for button views the label with the selected view name
        return $(data.selector).DataTable();
    }
    else {
        advanceTableInstance.ajax.reload()
    }
}

function renderAdvanceButtons(tableData, selectedView = 'Views') {
    let buttons = [
        'column_picker',
        'reload',
        'export_to',
    ];
    //Save State
    buttons.push({
        extend: 'collection',
        autoClose: true,
        text: "<i class='fal fa-lg fa-stream  p-r-5'></i> " + selectedView,
        className: "filteredViews m-l-4",
        buttons: [
            {
                text: "<div class='dropdown-divider'></div>",
            },
            {
                text: "<div class='dropdown-divider'></div>",
            },
        ]
    });
    return buttons;
}
