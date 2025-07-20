function initAdvanceDataTable(config) {
    let encap = encapsulatedDatatable(config);
    encap.registerListener(config.selector);
    encap.mapFilters(config.columns, config.selector);
    return encap.initAdvanceTable(config, encap.serializeFilterData(config.selector));
}

function encapsulatedDatatable() {
    var defaultAdvanceSetup;
    var originalAdvanceTableSetup;
    var saveStateAPI = false;
    var selectedAdvanceState = undefined;
    var defaultAdvanceState = false;
    var customDefinedAdvanceTableBtns;
    var allAdvanceStates;
    var advanceTableInstance;
    var filterDataSelected;
    var stopReloadEvent = false;
    var filtersInitData;
    var updateAdvanceRow;
    var deleteAdvanceRow;
    var renameAdvanceRow;
    var dateRangePickerData = {};

    function mapFilters(columns, selector) {
        let map = (columns.map(filters => ({ filter: filters.filtering }))).filter(item => item.filter);
        renderFilterAccordian(selector)
        $.each(map, function (key, value) {
            renderFilters(
                value.filter.type,
                {
                    'name': value.filter.name,
                    'label': value.filter.label,
                    'table_id': selector,
                    'options': value.filter.options,
                    'class': value.filter.class
                },
                selector,
                dateRangePickerData
            )
        })
        //init filter data for state filter rendering
        filtersInitData = map
    }

    function getAdvanceTableSettings() {
        return {
            'defaults': defaultAdvanceSetup,
            'data': originalAdvanceTableSetup
        }
    }

    function registerListener(tableSelector) {
        //listener for filter changes
        $(document).on('change',
            tableSelector + '-accordian' + ' :input[type="text"],' +
            tableSelector + '-accordian' + ' :input[type="radio"],' +
            tableSelector + '-accordian' + ' :input[type="hidden"],' +
            tableSelector + '-accordian' + ' select', function () {
                if (!stopReloadEvent) {
                    if (advanceTableInstance) {
                        $(originalAdvanceTableSetup.selector).DataTable().clear().destroy()
                    }
                    let data = serializeFilterData(tableSelector);
                    filterDataSelected = data;
                    initAdvanceTable(originalAdvanceTableSetup, data)
                }
            });

        //listener for saving state to db
        $(document).on('click', 'button[data-table-id="' + tableSelector.replace('#', '') + '"]', function () {
            saveNewAdvanceState(tableSelector)
        })

        //MANAGE STATE LISTENERS

        //OVERRIDE-UPDATE
        $(document).on('click', '.override-message-' + tableSelector.replace('#',''), function () {
            let stateId = $(this).attr('data-state-id')
            overrideMessage(stateId, tableSelector)
        })

        $(document).on('click', '.update-advance-state-' + tableSelector.replace('#',''), function () {
            let stateId = $(this).attr('data-state-id')
            updateAdvanceStateInDB(stateId)
        })

        $(document).on('click', '.cancel-update-advance-state-' + tableSelector.replace('#',''), function () {
            let stateId = $(this).attr('data-state-id')
            cancelAdvanceUpdate(stateId)
        })

        //RENAME
        $(document).on('click', '.rename-message-' + tableSelector.replace('#',''), function () {
            let stateId = $(this).attr('data-state-id')
            renameMessage(stateId, tableSelector)
        })

        $(document).on('click', '.rename-advance-state-' + tableSelector.replace('#',''), function () {
            let stateId = $(this).attr('data-state-id')
            renameAdvanceStateInDB(stateId)
        })

        $(document).on('click', '.cancel-rename-advance-state-' + tableSelector.replace('#',''), function () {
            let stateId = $(this).attr('data-state-id')
            cancelAdvanceRename(stateId)
        })

        //DELETE
        $(document).on('click', '.delete-message-' + tableSelector.replace('#',''), function () {
            let stateId = $(this).attr('data-state-id')
            deleteMessage(stateId, tableSelector)
        })

        $(document).on('click', '.delete-advance-state-' + tableSelector.replace('#',''), function () {
            let stateId = $(this).attr('data-state-id')
            deleteAdvanceState(stateId)
        })

        $(document).on('click', '.cancel-delete-advance-state-' + tableSelector.replace('#',''), function () {
            let stateId = $(this).attr('data-state-id')
            cancelAdvanceDelete(stateId)
        })

        //listener for setting default state
        $(document).on('change', '#select-default-view-state-' + tableSelector.replace('#','') , function (e) {
            setDefaultAdvanceState($(this))
        })

        //CLEAR ACTIVE FILTERS
        $(document).on('click', '.active-option-clear-'+  tableSelector.replace('#',''), function(e) {
            clearActiveFilters( tableSelector.replace('#',''))
        });
    }

    function serializeFilterData(tableSelector) {
        let data = $(tableSelector + '-accordian' + ' :input[type="text"],' +
            tableSelector + '-accordian' + ' :input[type="radio"],' +
            tableSelector + '-accordian' + ' :input[type="hidden"]').serializeArray();
        let datase = []
        $(tableSelector + '-accordian' + ' select').each(function () {
            datase.push({
                name: $(this).attr('name'),
                value: $(this).val()
            })
        });

        let result = $.merge(data, datase)

        let obj = {}
        result.map(x => {
            return Object.assign(obj, { [x.name]: x.value });
        });

        filterDataSelected = obj;
        return obj;
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
                fetchState(tableData.selector),
                {
                    text: "<div class='dropdown-divider'></div>",
                },
                {
                    text: "<div class='dropdown-divider'></div>",
                }
            ]
        });
        return buttons;
    }
    function setNewAdvanceSelectedState(stateId) {
        selectedAdvanceState = stateId;
        defaultAdvanceState = defaultAdvanceSetup;
        localStorage.setItem('DataTables_' + originalAdvanceTableSetup.selector.replace('#', '') + '_' + window.location.pathname + "_selectedState", stateId);
        changeAdvanceTableState();
    }
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


            if(checkIfLocalStorageChanged(data)) {
                setDatatableProperties(defaults, data)
                renderAdvanceButtons(data, 'Custom')
            }

            $(document).on('click', '.default-state', function(e) {
                stopReloadEvent = true;
                setDefaultProperties(defaults, data)
                stopReloadEvent = false;
            });
            $('#logout-form').on('submit', localStorage.clear());

            let datatableParams = Object.assign({}, defaults, data);

            formData = filterDataSelected

            datatableParams.ajax = {
                url: data.ajax.toString(),
                type: 'POST',
                headers: {
                    'X-CSRF-TOKEN': data.csrfToken
                },
                data: {
                    formData
                }
            };
            //Initialize DataTables with our params
            $(data.selector).DataTable(datatableParams);
            advanceTableInstance = $(data.selector).DataTable();
            //fill multiview settings variable
            defaultAdvanceSetup = defaults;
            originalAdvanceTableSetup = data;
            // Replace for button views the label with the selected view name
            if (data.saveStateAPI !== 'undefined' && data.saveStateAPI === true) {
                var selectedView = allAdvanceStates.filter(s => s.id == selectedAdvanceState).map(s => s.name_en);
                var selectedViewFromAdvanceState = allAdvanceStates.filter(s => s.id == selectedAdvanceState)[0];
                var stateObj = JSON.parse(selectedViewFromAdvanceState.state);
                var localStorageObj = JSON.parse(localStorage.getItem('DataTables_' + data.selector.replace('#', '') + '_' + window.location.pathname + '_filtering')).filtering;

                if(checkIfFilterChanged(stateObj, localStorageObj)) {
                    selectedView = __("Custom");
                }
                $('.filteredViews').html('<i class="fal fa-lg fa-stream p-r-5"></i> ' + ((selectedView.length) ? selectedView : 'Views'));
            }
            checkIfFiltersExist(data.selector);
            return $(data.selector).DataTable();
        }
        else {
            advanceTableInstance.ajax.reload()
        }
    }

    function setDefaultProperties(defaults, data) {
        localStorage.removeItem('DataTables_' +data.selector.replace('#', '') +  '_' + window.location.pathname + "_filtering")
        localStorage.removeItem('DataTables_' +data.selector.replace('#', '') +  '_' + window.location.pathname)
        localStorage.removeItem('DataTables_' +data.selector.replace('#', '') +  '_' + window.location.pathname + "_selectedState")

        if(localStorage.getItem('DataTables_' + data.selector.replace('#', '') + '_' + window.location.pathname + '_newState')) {
            defaults.order = JSON.parse(localStorage.getItem('DataTables_' + data.selector.replace('#', '') + '_' + window.location.pathname + '_newState')).order
            defaults.pageLength = JSON.parse(localStorage.getItem('DataTables_' + data.selector.replace('#', '') + '_' + window.location.pathname + '_newState')).length
        } else {
            defaults.order = [0, 'asc'];
            defaults.pageLength = 10;
        }

        localStorage.removeItem('DataTables_' +data.selector.replace('#', '') +  '_' + window.location.pathname + "_newState")
    }

    function checkIfLocalStorageChanged(data) {
        if(localStorage.getItem('DataTables_' + data.selector.replace('#', '') + '_' + window.location.pathname)) {
            return true;
        }
        return false;
    }

    function checkIfFilterChanged(stateObj, localStorageStateString)
    {
        stateString = JSON.stringify(stateObj.filtering);
        localStorageString = JSON.stringify(localStorageStateString);

        if (stateString === localStorageString) {
            return false;
        } else {
            return true;
        }
    }

    function setDatatableProperties(defaults, data) {
        defaults.pageLength = JSON.parse(localStorage.getItem('DataTables_' + data.selector.replace('#', '') + '_' + window.location.pathname)).length
        defaults.order = JSON.parse(localStorage.getItem('DataTables_' + data.selector.replace('#', '') + '_' + window.location.pathname)).order
    }

    function isNullish(filterDataSelected) {
        if(Object.values(filterDataSelected).every(value => value === null) ){
            return false;
        }else {
            return true;
        }
    }

    function checkIfFiltersExist(selector) {
        if(isNullish(filterDataSelected) && Object.values(filterDataSelected).some(value => value.length)) {
            $(".active-option-clear-"+ selector.replace('#', '')).css('display', 'inline');
        }else {
            $(".active-option-clear-"+ selector.replace('#', '')).css('display', 'none');
        }
    }

    function clearActiveFilters(selector) {
        stopReloadEvent = true;

        $.each($(".active-filter-"+ selector.replace('#', '')).children('#delete-filter-label'), function (i, val) {
            $(".active-filter-"+ selector.replace('#', '')).children('#delete-filter-label').trigger('click');
        })

        if(advanceTableInstance) {
            $(originalAdvanceTableSetup.selector).DataTable().clear().destroy()
        }
        let data = serializeFilterData();
        filterDataSelected = [];

        initAdvanceTable(originalAdvanceTableSetup, data)
        stopReloadEvent = false;
    }

    //
    // State handle functions
    //
    function changeAdvanceTableState() {
        if (selectedAdvanceState) {
            let selectedFiltersFromState = allAdvanceStates.filter(s => s.id === parseFloat(selectedAdvanceState)).map(s => s.state);
            filterDataSelected = JSON.parse(selectedFiltersFromState)['filtering']
            //fill filter data from selected state
            fillFilterDataFromState(filterDataSelected)
        }

        let formData = filterDataSelected
        let tableID = originalAdvanceTableSetup.selector;
        var advanceTableInstance = $(tableID).DataTable();
        let settings = getAdvanceTableSettings(tableID);
        var selectedView = allAdvanceStates.filter(s => s.id === selectedAdvanceState).map(s => s.name_en);
        let defaultBtns = renderAdvanceButtons(originalAdvanceTableSetup, selectedView);
        let datatableParams = Object.assign({}, settings.defaults, settings.data);
        datatableParams.buttons = defaultBtns;
        // When user change the selected view and we need to reload it
        // clone the customDefined Buttons because we don't want to copy the reference
        if (customDefinedAdvanceTableBtns) {
            var mergedButtons = Object.values(Object.assign({}, customDefinedAdvanceTableBtns));
            defaultBtns.forEach(btn => mergedButtons.push(btn));
            datatableParams.buttons = mergedButtons;
        }
        advanceTableInstance.clear().destroy();

        localStorage.removeItem('DataTables_' +tableID.replace('#', '') +  '_' + window.location.pathname + '_filtering');
        localStorage.removeItem('DataTables_' +tableID.replace('#', '') +  '_' + window.location.pathname);

        datatableParams.ajax = {
            url: settings.data.ajax.toString(),
            type: 'POST',
            data: {
                formData
            }
        };
        advanceTableInstance = $(tableID).DataTable(datatableParams);
        checkIfFiltersExist(tableID);
    }
    function saveNewAdvanceState(tableID) {
        let new_state = localStorage.getItem('DataTables_' + tableID.replace('#', '') + '_' + window.location.pathname);
        let parseState = $.parseJSON(new_state);
        parseState.search['search'] = ''
        let data = serializeFilterData(tableID);
        let bundle = Object.assign(parseState, { 'filtering': data });
        let name = $('#new-state-advance-name-en-' + tableID.replace('#', '')).val()
        $.ajax({
            type: 'POST',
            url: '/datatable/state',
            data: {
                'state': JSON.stringify(bundle),
                'table_id': tableID + '_' + window.location.pathname.replace(/\/\d+/g, ''),
                'name_en': name,
                'name_nl': name,
                'name_de': name,
            },
            success: function (data) {
                $('div.invalid-feedback').remove();
                $('input').removeClass('is-invalid');
                toastr.success(__('Datatable state saved'), 'SUCCESS', {
                    timeOut: 3000
                });
                $('#modal-advance-state-' + tableID.replace('#', '')).modal('hide');
                selectedAdvanceState = data.id;
                allAdvanceStates.push({
                    'name_en': name,
                    'name_de': name,
                    'name_nl': name,
                    'id': data.id,
                    'state': JSON.stringify(bundle)
                });
                changeAdvanceTableState();
            },
            error: function (data) {
                $('div.invalid-feedback').remove();
                $('input').removeClass('is-invalid');
                $.each(data.responseJSON.errors, function (key, value) {
                    let column = 'new-advance-state-' + key.replace('_', '-') + '-' + tableID;
                    $('input[name="' + column + '"]').addClass("is-invalid");
                    $('input[name="' + column + '"]').after('<div class="invalid-feedback">' + value + '</div>');
                });
            }
        });
    }

    function updateAdvanceStateInDB(id) {
        //check if state selected is system default set by suneti admin
        let systemDefault = allAdvanceStates.filter(s => s.id === parseFloat(id)).map(s => s.systemDefault);
        if (systemDefault[0] === true) {
            toastr.warning(__('Cannot update system defaults'), 'WARNING', {
                timeOut: 3000
            });
            return;
        }
        let tableID = originalAdvanceTableSetup.selector
        //serialize data and get setting from local storage
        state = localStorage.getItem('DataTables_' + tableID.replace('#', '') + '_' + window.location.pathname);
        let parseState = $.parseJSON(state);
        parseState.search['search'] = ''
        let data = serializeFilterData(tableID);
        let bundle = Object.assign(parseState, { 'filtering': data });
        $.ajax({
            type: 'PUT',
            url: '/datatable/state',
            async: false,
            data: {
                'id': id,
                'state': bundle
            },
            success: function (data) {
                allAdvanceStates = data;
                toastr.success(__('Datatable state updated'), 'SUCCESS', {
                    timeOut: 3000
                });
            }
        });
        cancelAdvanceUpdate(id)
        if (parseFloat(id) !== parseFloat(selectedAdvanceState)) {
            setNewAdvanceSelectedState(id)
            return;
        }

    }

    function clearLocalStorage() {
        localStorage.removeItem('DataTables_' +originalAdvanceTableSetup.selector.replace('#', '') +  '_' + window.location.pathname + "_filtering");
        localStorage.removeItem('DataTables_' +originalAdvanceTableSetup.selector.replace('#', '') +  '_' + window.location.pathname);
        localStorage.removeItem('DataTables_' +originalAdvanceTableSetup.selector.replace('#', '') +  '_' + window.location.pathname + "_selectedState");
        localStorage.removeItem('DataTables_' +originalAdvanceTableSetup.selector.replace('#', '') +  '_' + window.location.pathname + "_newState");
    }

    function setDefaultAdvanceState(checkbox) {
        let id = checkbox.val()
        //check if state selected is system default set by suneti admin
        let systemDefault = allAdvanceStates.filter(s => s.id === parseInt(id)).map(s => s.systemDefault);
        if (systemDefault[0] === true) {
            toastr.warning(__('Cannot update/override system default state'), 'WARNING', {
                timeOut: 3000
            });
            checkbox.prop('checked', false);
            return;
        }
        //unselect the rest of the
        $('.default-state-change').not(checkbox).prop('checked', false);
        $.ajax({
            type: 'GET',
            url: '/datatable/set-default',
            data: {
                'table_id': originalAdvanceTableSetup.selector + '_' + window.location.pathname.replace(/\/\d+/g, ''),
                'state_id': id,
                'default': checkbox.is(":checked")
            },
            success: function (data) {
                toastr.success(__('Datatable state set as default'), 'SUCCESS', {
                    timeOut: 3000
                });
            }
        });
        clearLocalStorage();
        window.location.reload()
        //if you unset view as default remove check from the checkbox
        if (checkbox.is(":checked") === false) {
            checkbox.prop('checked', false);
        }
    }
    function deleteAdvanceState(id) {
        $.ajax({
            type: 'GET',
            url: '/datatable/destroy',
            data: {
                'state_id': id
            },
            success: function (data) {
                toastr.success(__('Datatable state deleted succesfully'), 'SUCCESS', {
                    timeOut: 3000
                });
            }
        });
        deleteAdvanceRow = '';
        //trigger change to datatable
        changeAdvanceTableState();
    }
    function renameAdvanceStateInDB(id) {
        let name = $('#new_state_name').val()
        $.ajax({
            type: 'GET',
            url: '/datatable/rename',
            data: {
                'state_id': id,
                'name': name
            },
            success: function (data) {
                toastr.success(__('Datatable state renamed succesfully'), 'SUCCESS', {
                    timeOut: 3000
                });
            }
        });
        renameAdvanceRow = '';
        //trigger change to datatable
        changeAdvanceTableState();
    }
    function saveNewAdvanceStateModal(tableID) {
        $('#modal-advance-state-' + tableID.replace('#', '')).modal('show');
    }
    function fetchState(tableID) {
        let table = tableID + '_' + window.location.pathname.replace(/\/\d+/g, '');
        let buttons = [];
        let trHTML = '';
        $('#table-advance-state-manage-' + tableID.replace('#', '') + ' > tbody').empty();
        //append tr to table
        $('#table-advance-state-manage-' + tableID.replace('#', '')).append(trHTML);
        return buttons;
    }
    function fillFilterDataFromState(filterData) {
        stopReloadEvent = true
        hideAllRadioOptions()
        $.each(filterData, function (key, val) {
            let type = filtersInitData.filter(s => s.filter.name === key).map(s => s.filter.type);
            //case for timepicker NOTE: refactor this part
            if (type.length === 0) {
                type = filtersInitData.filter(s => (s.filter.name + '_from') === key).map(s => s.filter.type);
            }
            if (type.length === 0) {
                type = filtersInitData.filter(s => (s.filter.name + '_to') === key).map(s => s.filter.type);
            }
            fillFIlterByType(type, val, key)
        })
        stopReloadEvent = false;
    }
    function fillFIlterByType(type, value, name) {
        if (type[0] === 'select') {
            if (value.length) {
                $("[name='" + name + "']").val(value).trigger('change');
                return;
            }
            $("[name='" + name + "']").val('').trigger('change');
        }
        else if (type[0] === 'checkbox') {
            if (value) {
                $('input[name="' + name + '"]').val('')
                $('input[data-filter-name="' + name + '"]:checked').each(function () {
                    $(this).prop('checked', false).trigger('change');
                });
                $.each(value.split(','), function (key, val) {
                    $('[data-filter-name="' + name + '"][data-filter-option="' + val + '"]').prop('checked', true).trigger('change');
                })
                $('input[name="' + name + '"]').val(value)
                return;
            }
            $('input[data-filter-name="' + name + '"]:checked').each(function () {
                $(this).prop('checked', false).trigger('change');
            });
            $('input[name="' + name + '"]').val('')
        }
        else if (type[0] === 'rangepicker') {
            if (value) {
                let fromTo = value.split(',')
                $("#" + name + "_id").data("ionRangeSlider").update({
                    from: fromTo[0],
                    to: fromTo[1]
                });
                clearFilterLabel(name);
                let ids = [];
                ids.push({
                    name: (fromTo[0] + ' - ' + fromTo[1]),
                    value: (fromTo[0] + ' - ' + fromTo[1]),
                    filterSection: name,
                    type: 'rangeslider'
                });
                updateBlobsToLabel(ids, name, selector)
                $("[name='" + name + "']").val(value).trigger('change');
                return;
            }
            clearFilterLabel(name);
            hideActiveFilter(name)
            let options = filtersInitData.filter(s => s.filter.name === name).map(s => s.filter.options);
            $("#" + name + "_id").data("ionRangeSlider").update({
                from: options[0]['from'],
                to: options[0]['to']
            });
        }
        else if (type[0] === 'daterange') {
            if (dateRangePickerData[name] !== undefined && dateRangePickerData[name][value] !== undefined  ) {
                let f= dateRangePickerData[name][value][0];
                let t = dateRangePickerData[name][value][1];
                $("#" + name).daterangepicker('destroy');
                $("#" + name).daterangepicker({
                    startDate: f,
                    endDate: t,
                    ranges: dateRangePickerData[name],
                    locale: {
                        format: 'DD-M-YYYY'
                    }
                });
                let range = dateRangePickerData[name][value];

                if (range) {
                    $('input[name="'+ name +'"]').data('daterangepicker').chosenLabel = value;
                }

                $('input[name="'+ name +'"]').val(value);

                $('input[name="'+ name +'"]').change();
                return;
            } else  if (value) {
                let fromToDate = value.split(' - ')
                let from = fromToDate[0].split("-")
                let f = new Date(from[2], from[1] - 1, from[0])
                let to = fromToDate[1].split("-")
                let t = new Date(to[2], to[1] - 1, to[0])
                $("#" + name).daterangepicker('destroy');
                $("#" + name).daterangepicker({
                    startDate: f,
                    endDate: t,
                    ranges: dateRangePickerData[name],
                    locale: {
                        format: 'DD-M-YYYY'
                    }
                });
                return;
            }
            $("#" + name).daterangepicker({
                ranges: dateRangePickerData[name],
                locale: {
                    format: 'DD-M-YYYY'
                }
            });
            $("#" + name).val('').change()
        }
        else if (type[0] === 'timepicker') {
            if (value) {
                $("[name='" + name + "']").val(value).trigger('change');
                return;
            }
            $("[name='" + name + "']").val('').trigger('change');
            hideActiveFilter(name.replace('_to', ''))
            clearFilterLabel(name.replace('_to', ''));
        }
        else if (type[0] === 'radio') {
            if (value) {
                $("input[name=" + name + "][value=" + value + "]").attr('checked', true).trigger('change');
                return;
            }
        }
    }
    function overrideMessage(stateId, tableID) {
        //stop users from opening multiple update dialogs
        if (deleteAdvanceRow || updateAdvanceRow || renameAdvanceRow) {
            toastr.warning(__('You need to finish first action you selected!'), 'WARNING', {
                timeOut: 3000
            });
            return;
        }
        //check if state selected is system default set by suneti admin
        let systemDefault = allAdvanceStates.filter(s => s.id === parseFloat(stateId)).map(s => s.systemDefault);
        if (systemDefault[0] === true) {
            toastr.warning(__('Cannot update/override system default state'), 'WARNING', {
                timeOut: 3000
            });
            return;
        }
        let displayMessage = __('Are you sure you want to override this view?');
        if (selectedAdvanceState === parseFloat(stateId)) {
            displayMessage = __('Are you sure you want to update this view?');
        }
        updateAdvanceRow = $('#table-row-state-' + stateId).clone();
        //stop tooltip from showing after clicking on some of action buttons
        $('[data-toggle="tooltip"]').tooltip("hide");
        let confirmation =
            `<tr id="advance-update-confirmation-${stateId}">
          <td colspan="3"><div style="text-align: center">
             <span width="60%" style="min-height: 28px;display: inline-flex;align-items: center;">${displayMessage}</span>
             <span width="40%" class="pull-right"><a href="#" data-state-id="${stateId}" class="btn btn-primary btn-sm update-advance-state-${tableID.replace('#', '')}">Save</a> <a href="#" data-state-id="${stateId}" class="btn btn-secondary btn-sm cancel-update-advance-state-${tableID.replace('#', '')}">Cancel</a></span>
          </div> </td>
      </tr>`;
        $('#table-row-state-' + stateId).replaceWith(confirmation);
    }
    function cancelAdvanceUpdate(stateId) {
        $('#advance-update-confirmation-' + stateId).replaceWith(updateAdvanceRow);
        updateAdvanceRow = '';
        //enable tooltips after cancel action
        $("[rel='tooltip']").tooltip();
    }
    function deleteMessage(stateId, tableID) {
        deleteAdvanceState
        //stop users from opening multiple update dialogs
        if (deleteAdvanceRow || updateAdvanceRow || renameAdvanceRow) {
            toastr.warning(__('You need to finish first action you selected!'), 'WARNING', {
                timeOut: 3000
            });
            return;
        }
        //check if state selected is system default set by suneti admin
        let systemDefault = allAdvanceStates.filter(s => s.id === stateId).map(s => s.systemDefault);
        if (systemDefault[0] === true) {
            toastr.warning(__('Cannot delete system default state'), 'WARNING', {
                timeOut: 3000
            });
            return;
        }
        //stop user from deleteing currently selected state
        if (stateId === selectedAdvanceState) {
            toastr.warning(__('Cannot delete currently selected state!'), 'WARNING', {
                timeOut: 3000
            });
            return;
        }
        deleteAdvanceRow = $('#table-row-state-' + stateId).clone();
        //stop tooltip from showing after clicking on some of action buttons
        $('[data-toggle="tooltip"]').tooltip("hide");
        let confirmation =
            `<tr id="advance-delete-confirmation-${stateId}">
          <td colspan="3"><div style="text-align: center">
             <span width="60%" style="min-height: 28px;display: inline-flex;align-items: center;">${__('Are you sure you want to delete this view?')}</span>
             <span width="40%" class="pull-right">
               <a href="#" data-state-id="${stateId}" class="btn btn-danger btn-sm delete-advance-state-${tableID.replace('#', '')}">Delete</a>
               <a href="#" data-state-id="${stateId}" class="btn btn-secondary btn-sm cancel-delete-advance-state-${tableID.replace('#', '')}">cancle</a>
             </span>
          </div> </td>
        </tr>`;
        $('#table-row-state-' + stateId).replaceWith(confirmation);
    }
    function cancelAdvanceDelete(stateId) {
        $('#advance-delete-confirmation-' + stateId).replaceWith(deleteAdvanceRow);
        deleteAdvanceRow = '';
        //enable tooltips after cancel action
        $("[rel='tooltip']").tooltip();
    }
    function renameMessage(stateId, tableID) {
        //stop users from opening multiple update dialogs
        if (deleteAdvanceRow || updateAdvanceRow || renameAdvanceRow) {
            toastr.warning(__('You need to finish first action you selected!'), 'WARNING', {
                timeOut: 3000
            });
            return;
        }
        //check if state selected is system default set by suneti admin
        let systemDefault = allAdvanceStates.filter(s => s.id === stateId).map(s => s.systemDefault);
        if (systemDefault[0] === true) {
            toastr.warning(__('Cannot update/override system default state'), 'WARNING', {
                timeOut: 3000
            });
            return;
        }
        let name = allAdvanceStates.filter(s => s.id === parseFloat(stateId)).map(s => s.name_en);
        renameAdvanceRow = $('#table-row-state-' + stateId).clone();
        //stop tooltip from showing after clicking on some of action buttons
        $('[data-toggle="tooltip"]').tooltip("hide");
        let confirmation =
            `<tr id="advance-rename-confirmation-${stateId}">
          <td colspan="3"><div style="text-align: center">
             <span width="60%" style="min-height: 28px;display: inline-flex;align-items: center;"><input type="text" name="new_state_name" id="new_state_name" class="form-control" value="${name}"> </span>
             <span width="40%" class="pull-right"><a href="#" data-state-id="${stateId}" class="btn btn-primary btn-sm rename-advance-state-${tableID.replace('#', '')}">Save</a> <a href="#" data-state-id="${stateId}" class="btn btn-secondary btn-sm cancel-rename-advance-state-${tableID.replace('#', '')}">cancle</a></span>
          </div> </td>
       </tr>`;
        $('#table-row-state-' + stateId).replaceWith(confirmation);
    }
    function cancelAdvanceRename(stateId) {
        $('#advance-rename-confirmation-' + stateId).replaceWith(renameAdvanceRow);
        renameAdvanceRow = '';
        //enable tooltips after cancel action
        $("[rel='tooltip']").tooltip();
    }
    function hideAllRadioOptions() {
        $('#filter-table input:radio').each(function () {
            let selectorName = $(this).attr('name')
            let value = $(this).val()
            let filterOption = selectorName + ':' + value
            $('input[name="' + selectorName + '"]').prop('checked', false);
            $(`.active-filter[data-filter-section="${selectorName}"],[data-filter-value="${value}"]`).parent().parent().css('display', 'none');
            $(`.active-filter[data-filter-section="${selectorName}"],[data-filter-value="${value}"]`).remove();
        });
        return;
    }
    return {
        mapFilters: mapFilters,
        serializeFilterData: serializeFilterData,
        initAdvanceTable: initAdvanceTable,
        registerListener: registerListener
    };
}
