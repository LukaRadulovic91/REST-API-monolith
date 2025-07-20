function renderFilterAccordian(selector) {
    let accordian = `<div class="card-accordion filters-accordion">
                             <div class="card  filter-card">
                                 <div class="card-header pointer-cursor filter-header collapsed" data-toggle="collapse" data-target="${selector}-dropdown" aria-expanded="false">
                                    <div class="card-content">
                                        <svg width="16" height="18" class="mb-1"  viewBox="0 0 12 12" fill="none" xmlns="http://www.w3.org/2000/svg">
                                            <g id="tabler-icon-filter">
                                            <path id="Vector" d="M2 2H10V3.086C9.99994 3.35119 9.89455 3.60551 9.707 3.793L7.5 6V9.5L4.5 10.5V6.25L2.26 3.786C2.09272 3.60196 2.00003 3.3622 2 3.1135V2Z" stroke="#99A1B7" stroke-linecap="round" stroke-linejoin="round"/>
                                            </g>
                                        </svg>
                                        <p  class="d-inline-flex mb-0 mt-1" style = "color: gray;">${'Filtering'}</p>
                                    </div>
                                    <i class="fa fa-fw fa-chevron-down mt-2"></i>
                                 </div>
                                 <div class="active-filters " id="${selector.replace('#', '')}-active-filters" data-table-id="">
                                     <span class="badge badge-danger active-option-clear active-option-clear-${selector.replace('#', '')}" style="cursor:pointer; font-size: 10.5px; margin-bottom: 3px; display: none">${'Clear all'}&nbsp;&nbsp;<i class="fal fa-times"></i></span>
                                 </div>
                                 <div id="${selector.replace('#', '')}-dropdown">
                                     <div class="card-body">
                                         <div class="row" id="${selector.replace('#', '')}-accordian" data-table-id="${selector}">
                                         </div>
                                     </div>
                                 </div>
                             </div>
                         </div>`;
    $(selector).parent().prepend(accordian);
}

function formatArray(options) {
    let array = options.replace('{', '').replace("}", '').replace(/&quot;/g, '"');
    let optionsList = {}
    $.each(array.split('","'), function (key, value) {
        splited = value.split(':')
        Object.assign(optionsList, {[splited[0].replace(/\"/g, '')]: splited[1].replace(/\"/g, '')});
    })

    return optionsList;
}

function renderActiveLabel(name, section, selector) {
    let label = '<span class="active-label" style="display: none;line-height: 40px;font-size: 14px;margin-left: 10px;">\n' +
        '          <strong>'+ name +':&nbsp</strong>\n' +
        '          <span class="active-label-'+ section +'"></span>\n' +
        '      </span>'
    $(selector + '-active-filters').prepend(label);
}

function select2render(options, selector) {
    let optionsList = formatArray(options.options)
    let classAttr = 'select2';
    if(options.class) {
        classAttr = options.class
    }
    let dropdown = '' +
        '<div class="form-group col-lg-3"> ' +
        '<label for="' + options.name + '">' + options.label + '</label>' +
        '<select id="' + options.name + '" name="' + options.name + '" ' +
        'class="form-control ' + classAttr + ' select2-hidden-accessible" multiple="" ' +
        'data-multiple="true"' +
        'data-placeholder="Select '+options.label+'" data-filter-section="'+ options.label +'" data-filter-type="select" data-close-on-select="true" ' +
        'tabindex="-1" aria-hidden="true">' +
        '</select> ' +
        '</div>';

    console.log(options.name)

    $(selector + '-accordian').append(dropdown);

    $.each(optionsList, function (key, value) {
        $('#'+ options.name).append('<option value="'+ key +'" data-text="'+ value +'">' + value + '</option>');
    })

    if(classAttr === 'select2') {
        $('#' + options.name).select2({width: '100%'});
    }

    renderActiveLabel(options.label, options.name, selector)

    $(document).on('change', '#' + options.name, function (e) {
        clearFilterLabel(options.name);
        ids = [];
        let selects = $(this).select2('data');
        for (let i=0;i<selects.length;i++) {
            let nameForLabel = selects[i].text
            //if we use options that contain html - this part cleans html so it can be used in a label
            if(options.class != 'select2') {
                nameForLabel = HTMLUnescape(nameForLabel)
            }
            ids.push({name: nameForLabel,value: selects[i].id, filterSection: options.name, type: 'select2'});
        }

        if(ids.length > 0) showActiveFilter(options.name)
        else hideActiveFilter(options.name)

        updateBlobsToLabel(ids, options.name, selector)
    });
}

function renderCheckbox(options, selector) {
    let optionsList = formatArray(options.options)

    let layout = '<div class="col-md-4 dt-filter">' +
        '  <label class="ml-1">\n' +
        '    <strong>'+ options.label +'</strong>\n' +
        '  </label>\n' +
        '<div class="col-md-12">\n' +
        '<div class="row" data-filter-column="'+ options.name +'" data-table-id="">\n' +
        '        <input name="'+ options.name +'" type="hidden" value="">\n' +
        '</div>\n' +
        '</div>\n' +
        '<div>';

    $(selector + '-accordian').append(layout);
    $.each(optionsList, function (key, value) {
        let checkboxId = ('id'+ options.name + '-' + key).replace(/\s/g, '_');
        let checkbox = '<div class="col-md-3 checkbox checkbox-css">\n' +
            '        <input id="'+ checkboxId +'" type="checkbox" name="'+value+'" data-filter-type="checkbox" class="dt-filter-checkbox '+options.name+'" ' +
            '         data-filter-section="'+ options.label +'" data-filter-name="'+ options.name +'" data-filter-option="'+ key +'" value="'+ key +'">\n' +
            '        <label for="' + checkboxId + '">'+value+'</label>\n' +
            '    </div>'
        $('div[data-filter-column=' + options.name +']').append(checkbox);
    })

    renderActiveLabel(options.label, options.name, selector)

    //on click event for labels
    $(document).on('change', '.' + options.name, function (e) {
        clearFilterLabel(options.name);
        ids = [];
        let selects = $('.' +options.name +':checkbox:checked');
        $.each(selects, function (key, value) {
            ids.push({name: $(value).attr('name'),value: $(value).val(), filterSection: options.name, type: 'checkbox'});
        });

        if(ids.length > 0) showActiveFilter(options.name)
        else hideActiveFilter(options.name)

        updateBlobsToLabel(ids, options.name, selector)
    });
}

function renderDaterange(options, selector, dateRangePickerData) {
    let daterange = '<div class="col-md-3">\n' +
        '                <div class="form-group" id="filter">\n' +
        '                     <label for="'+ options.name +'">'+ options.label +'</label>\n' +
        '                     <input class="form-control"  autocomplete="off" type="text" id="'+ options.name +'" name="'+ options.name +'" data-filter-type="daterange" data-filter-section="'+ options.label +'" value="'+ options.options.start_time + ' - ' + options.options.end_time +'" />\n' +
        '                 </div>\n' +
        '             </div>'
    $(selector + '-accordian').append(daterange);
    var f = moment();
    var t = moment();

    if(options.options.start_date) {
        let from = options.options.start_date.split("-")
        f = new Date(from[2], from[1] - 1, from[0])
        let to = options.options.end_date.split("-")
        t = new Date(to[2], to[1] - 1, to[0])
    }

    setDateRangePicker($('input[name="'+ options.name +'"]'), f, t, options.options.range)

    $('input[name="'+ options.name +'"]').wrap('<span class="deleteicon"></span>').after($('<i class="fa fa-times-circle clear-filter-label" aria-hidden="true"></i>').click(function() {
        $(this).prev('input').val('').trigger('change');
    }));

    renderActiveLabel(options.label, options.name, selector)

    $('input[name="'+ options.name +'"]').on('change', function () {
        if ( $(this).data('daterangepicker') !== undefined) {
            var chosenLabel = $(this).data('daterangepicker').chosenLabel;
            if (chosenLabel && chosenLabel !== 'Custom Range'  && chosenLabel !== 'Custom') {
                $(this).data('daterangepicker').ranges[chosenLabel];
                $(this).val(chosenLabel);
            } else if( chosenLabel === 'Custom Range' || chosenLabel === 'Custom' ){
                var startDate = $(this).data('daterangepicker').startDate;
                var endDate = $(this).data('daterangepicker').endDate;
                $(this).val(startDate.format('DD-MM-YYYY') + ' - ' + endDate.format('DD-MM-YYYY'));
            }
        }

        clearFilterLabel(options.name);
        let ids = [];
        if($(this).val() !== '') {
            ids.push({name:   $(this).val()  , value:$(this).val(),   filterSection: options.name, type: 'daterange'});
        }


        if(ids.length > 0) showActiveFilter(options.name)
        else hideActiveFilter(options.name)

        updateBlobsToLabel(ids, options.name, selector)
    });
    $('input[name="'+ options.name +'"]').change();

    if(!options.options.start_date) {
        $('input[name="'+ options.name +'"]').val('').change()
        $(this).parent().remove()
    }
    var dataObject = {};
    dateRangePickerData[options.name] = options.options.range;

}

function setDateRangePicker(element, startDate, endDate, range) {
    // $('#daterange-prev-date').html(endDate.format('YYYY/MM/DD') + ' - ' + moment().format('YYYY/MM/DD'));
    element.daterangepicker({
        dateFormat: "YYYY-MM-DD",
        startDate: startDate,
        endDate: endDate,
        showDropdowns: true, // Omogućite dropdowns za izbor meseca i godine
        showWeekNumbers: true,
        timePicker: false,
        timePickerIncrement: 1,
        timePicker12Hour: true,
        opens: 'right',
        drops: 'down',
        buttonClasses: ['btn', 'btn-sm'],
        applyClass: 'btn-primary',
        cancelClass: 'btn-default',
        separator: ' to ',
        numberOfMonths: 2, // Prikaz kalendara sa dva meseca
        locale: {
            applyLabel: 'Submit',
            cancelLabel: 'Cancel',
            fromLabel: 'From',
            toLabel: 'To',
            customRangeLabel: 'Custom',
            daysOfWeek: ['Su', 'Mo', 'Tu', 'We', 'Th', 'Fr', 'Sa'],
            monthNames: ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'],
            firstDay: 1,
            format: 'DD-M-YYYY'
        }
    }, function (startDate, endDate) {
        // Vaša logika kada se izabere datum
    });
}


function renderTimepicker(options, selector) {
    let timepickers = '<div class="col-md-3">\n' +
        '                <div class="row">\n' +
        '                    <div class="form-group col-md-6">\n' +
        '                        <label>'+ options.label +' from</label>\n' +
        '                        <div class="input-group">\n' +
        '                            <div class="input-group-prepend">\n' +
        '                                <span class="input-group-text"><i class="fal fa-calendar"></i></span>\n' +
        '                            </div>\n' +
        '                            <input name="'+ options.name +'_from" type="text" class="form-control '+options.name+'" data-filter-type="timepicker" data-filter-section="'+ options.label +'" placeholder="hh:mm" autocomplete="off" picker-type="time">\n' +
        '                        </div>\n' +
        '                    </div>\n' +
        '                    <div class="form-group col-md-6">\n' +
        '                        <label>'+ options.label +' To </label>\n' +
        '                        <div class="input-group">\n' +
        '                            <div class="input-group-prepend">\n' +
        '                                <span class="input-group-text"><i class="fal fa-calendar"></i></span>\n' +
        '                            </div>\n' +
        '                            <input name="'+ options.name +'_to" type="text" class="form-control '+options.name+'" placeholder="hh:mm" data-filter-type="timepicker" data-filter-section="'+ options.label +'" autocomplete="off" picker-type="time">\n' +
        '                        </div>\n' +
        '                    </div>\n' +
        '                </div>\n' +
        '            </div>';

    $(selector + '-accordian').append(timepickers);

    $("[picker-type='time']").each(function() {
        $(this).datetimepicker({
            todayHighlight: true         ,
            orientation   : 'bottom left',
            fontAwesome   : true         ,
            autoclose     : true         ,
            format        : 'hh:ii'      ,
            startView     : 0            ,
            maxView       : 0            ,
            minView       : 0            ,
            formatViewType: 'time',
        });
    });

    renderActiveLabel(options.label, options.name, selector)

    $('.'+ options.name).on('change', function () {
        clearFilterLabel(options.name);
        let ids = [];
        ids.push({
            name: ($('input[name="'+ options.name +'_from"]').val()+' - '+$('input[name="'+ options.name +'_to"]').val()),
            value: ($('input[name="'+ options.name +'_from"]').val()+' - '+$('input[name="'+ options.name +'_to"]').val()),
            filterSection: options.name,
            type: 'timepicker'
        });

        if(ids.length > 0) showActiveFilter(options.name)
        else hideActiveFilter(options.name)

        updateBlobsToLabel(ids, options.name, selector)
    });
}

function renderRangePicker(options, selector) {
    let picker = '<div class="col-md-3">\n' +
        '  <div class="form-group">\n' +
        '    <label>'+options.label+'</label>\n' +
        '    <input type="test" id="'+options.name+'_id" name="'+options.name+'_id" class="filter-qty_range" data-filter-type="rangeslider" data-filter-section="'+options.label+'" data-filter-value="'+options.label+'_value" value=""/>\n' +
        '    <input type="hidden" id="'+options.name+'_hidden" name="'+options.name+'" value=""/>\n' +
        '  </div>\n' +
        '  </div>';

    $(selector + '-accordian').append(picker);

    let slider = $('#'+ options.name + '_id');
    let hidden = $('#'+ options.name + '_hidden');
    renderActiveLabel(options.label, options.name, selector)
    slider.ionRangeSlider({
        type: "double",
        min: options.options.min,
        max: options.options.max,
        from: options.options.from,
        to: options.options.to,
        grid: true,
        skin:'flat',
        onFinish: function(data){
            minQty = data.from;
            maxQty = data.to;
            hidden.val(data.from + ','+ data.to).change()
            clearFilterLabel(options.name);
            let ids = [];
            ids.push({
                name: (data.from + ' - ' + data.to),
                value: (data.from + ' - ' + data.to),
                filterSection: options.name,
                type: 'rangeslider'
            });

            if(ids.length > 0) showActiveFilter(options.name)
            else hideActiveFilter(options.name)

            updateBlobsToLabel(ids, options.name, selector)
        }
    });
    slider.change()
}

function renderRadio(options, selector) {
    let radio = `
      <div class="form-group col-md-3">
          <label>${options.label}</label>
          <div id="${options.name}-options">
          </div>
      </div>
    `;

    $(selector + '-accordian').append(radio);

    $.each(options.options.options, function (key, value) {
        let option = `
        <div class="radio radio-css radio-inline">
          <input name="${options.name}" type="radio" value="${key}" data-label="${value}" id="${key}-id">
          <label for="${key}-id">${value}</label>
        </div>
    `;
        $('#' + options.name +'-options').append(option);
    })


    renderActiveLabel(options.label, options.name, selector)

    $('input[name="'+ options.name +'"]').on('change', function () {

        clearFilterLabel(options.name);
        let ids = [];
        if($(this).val() !== '') {
            ids.push({name:$(this).attr('data-label') , value: $(this).val(), filterSection: options.name, type: 'radio'});
        }

        if(ids.length > 0) showActiveFilter(options.name)
        else hideActiveFilter(options.name)

        updateBlobsToLabel(ids, options.name, selector)
    });

    if(options.options.defaultValue) {
        $("input[name=" + options.name + "][value=" + options.options.defaultValue + "]").attr('checked', true).trigger('change');
    }

}

function renderFilters(type, options, selector, dateRangePickerData) {
    switch(type) {
        case type = 'select':
            select2render(options, selector)
            break;
        case type = 'checkbox':
            renderCheckbox(options, selector)
            break;
        case type = 'daterange':
            renderDaterange(options, selector, dateRangePickerData)
            break;
        case type = 'timepicker':
            renderTimepicker(options, selector)
            break;
        case type = 'rangepicker':
            renderRangePicker(options, selector)
            break;
        case type = 'radio':
            renderRadio(options, selector)
            break;
        default:
            return;
    }
}

$(document).on('click', 'input:checkbox', function () {
    let checkboxFields = $(this).attr('data-filter-name')
    let selected = [];
    $('input[data-filter-name="'+checkboxFields+'"]:checked').each(function () {
        selected.push($(this).val());
    });
    $('input[name="'+checkboxFields+'"]').val(selected).change();
})

function clearFilterLabel(section){
    $('.active-option-'+ section).remove();
}

function createLabel(filterValue, filterSection, filterOption, type, selector){
    return `<span class="badge badge-success active-filter active-filter-${selector.replace("#", '')} active-option-${filterSection}"
          style="margin-left: 1px;"
          data-filter-section="${filterSection}"
          data-filter-value="${filterValue}"
              data-filter-type="${type}">
          ${filterOption}&nbsp;&nbsp;<i class="fal fa-times" id="delete-filter-label"></i>
        </span>`;
}

function updateBlobsToLabel(options, section, selector) {
    $.each(options, function (key, value) {
        let label = createLabel(value.value, value.filterSection, value.name, value.type, selector);
        $('.active-label-'+section).append(label);
        $('.active-label-'+section).parent().css('display', 'inline');
    });
}

function deSelect2(id, selector) {
    var $select = $('#' + selector);
    var idToRemove = id;
    var values = $select.val();
    if (values) {
        var i = values.indexOf(idToRemove);
        if (i >= 0) {
            values.splice(i, 1);
            $select.val(values).change();
        }
    }
}

$(document).on('click', '#delete-filter-label', function () {
    let value = $(this).parent().attr('data-filter-value')
    let type = $(this).parent().attr('data-filter-type')
    let section = $(this).parent().attr('data-filter-section')

    if(type === 'select2') {
        deSelect2(value, section)
        $(this).parent().remove()
    }
    else if(type === 'rangeslider') {
        let slider = $('#'+ section + '_id');
        slider.data("ionRangeSlider").reset();
        $(this).parent().parent().parent().css('display', 'none')
        $(this).parent().remove()
    }
    else if(type === 'checkbox') {
        let checkboxId = ('#id'+ section + '-' + value).replace(/\s/g, '_')
        $(checkboxId).trigger('click');
        $(this).parent().remove()
    }
    else if(type === 'timepicker') {
        $('input[name="'+ section +'_from"]').val('')
        $('input[name="'+ section +'_to"]').val('')
        $(this).parent().parent().parent().css('display', 'none')
        $(this).parent().remove()
    }
    else if(type === 'daterange') {
        $('input[name="'+ section +'"]').data('daterangepicker').chosenLabel = '';
        $('input[name="'+ section +'"]').val('').change()
        $(this).parent().remove()
    }
    else if(type === 'radio') {
        $('input[name="'+ section +'"]').prop('checked', false);
        $(this).parent().parent().parent().css('display', 'none')
        $(this).parent().remove()
    }
})

$(document).on('click', '.clear-filter-label', function (e) {
    e.preventDefault();
    var formControlElement = $(this).closest('.deleteicon').find('.form-control');
    formControlElement.data('daterangepicker').chosenLabel = '';
    formControlElement.val('');
    $('input[name="'+ formControlElement.attr('id') +'"]').change();
    // formControlElement.daterangepicker('destroy');
});


function showActiveFilter(section) {
    $('.active-label-'+ section).parent().css('display', 'inline')
}

function hideActiveFilter(section) {
    $('.active-label-'+ section).parent().css('display', 'none')
}

function HTMLUnescape(str) {
    return String(str)
        .replace(/&amp;/g, '&')
        .replace(/&quot;/g, '"')
        .replace(/&#39;/g, "'")
        .replace(/&lt;/g, '<')
        .replace(/&gt;/g, '>');
}
