@extends('layouts.master',
[
    'breadcrumbs' => true,
    'title' =>'Job Orders'
]
)
@section('content')
    @include('pages.modals.shifts-modal')
    <div class="card mb-5 mb-xl-8">
        <!--begin::Body-->
        <div class="card-body py-3">
            <!--begin::Table container-->
            <div class="table-responsive">
                <!--begin::Table-->
                <table class="table jobs-table align-middle gs-0 gy-4" id ="job-ads-table">
                    <!--begin::Table head-->
                    <thead>
                    <tr class="fw-bold text-muted bg-light">
                        <th class="ps-4 min-w-325px rounded-start">Id</th>
                        <th class="min-w-125px">Date</th>
                        <th class="min-w-200px">Company</th>
                        <th class="min-w-150px">Status</th>
                        <th class="min-w-150px">Job type</th>
                        <th class="min-w-150px">Title</th>
                        <th class="min-w-150px">Job Details</th>
                    </tr>
                    </thead>
                </table>
                <!--end::Table-->
            </div>
            <!--end::Table container-->
        </div>
        <!--begin::Body-->
    </div>
@endsection

@push('scripts')
    <script>
        $(document).ready(function() {
            $.fn.dataTable.ext.order['date-euro-pre'] = function (a) {
                var components = a.split('-');
                return components[2] + components[1] + components[0];
            };

            initAdvanceDataTable({
                selector: '#job-ads-table',
                saveStateAPI: false,
                ajax: "{{route('job-ads.fetch')}}",
                pageLength: 10,
                columns: [
                    {data: 'id', visible: false},
                    {
                        data: 'created',
                        type: 'date-euro',
                        render: function(data, type, row) {
                            if (type === 'display') {
                                // var rawDate = row.shift_start_date ? moment(row.shift_start_date, 'DD-MM-YYYY').format('DD-MM-YYYY') : moment(row.created, 'DD-MM-YYYY').format('DD-MM-YYYY');
                                var rawDate = row.shift_start_date ? moment(row.shift_start_date, 'DD-MM-YYYY').format('MM-DD-YYYY') : moment(row.created, 'DD-MM-YYYY').format('MM-DD-YYYY');
                                var rawDate = moment(rawDate).format('MMMM D, YYYY');
                                if (row.shift_start_date) {
                                    return rawDate + `<span class="badge badge-success badge-pill ml-4"><a href="#" class="show-link" id="showShiftsModal" data-id="${row.id}" style="text-decoration: none; color: inherit;"><i class="fas fa-search" style="color:white;"></i></a></span>`;
                                } else {
                                    return rawDate;
                                }
                            }

                            // var dateComponents = moment(row.sort_start_date, 'DD-MM-YYYY').format('DD-MM-YYYY');
                            var dateComponents = moment(row.sort_start_date, 'DD-MM-YYYY').format('MM-DD-YYYY');
                            var dateComponents = moment(rawDate).format('MMMM D, YYYY');
                            return dateComponents;
                        },
                        filtering: {
                            name: 'start_date',
                            label: 'Start date',
                            type: 'daterange',
                            options: { start_date: "", end_date: "" }
                        }
                    },
                    {data: 'company_name'},
                    {
                        data: 'status',
                        render    : function ( data, type, row ) {
                            var statusEnum = {!! json_encode(\App\Enums\JobAdStatus::toSelectArray()) !!};
                            return statusEnum[data]
                        },
                        filtering: {
                            name: 'statuses',
                            label: 'Statuses',
                            type: 'select',
                            options: "{{json_encode($statuses)}}"
                        }
                    },
                    {
                        data: 'category',
                        render: function (data, type, row) {
                            var statusEnum = {!! json_encode(\App\Enums\JobAdTypes::toSelectArray()) !!};
                            var jobType = statusEnum[data];
                            if (jobType.includes('Permanent')) {
                                return '<span class="badge badge-primary">' + jobType + '</span>';
                            } else if (jobType === 'Temporary') {
                                return '<span class="badge badge-success">' + jobType + '</span>';
                            } else {
                                return jobType;
                            }
                        },
                        filtering: {
                            name: 'category',
                            label: 'Job Type',
                            type: 'select',
                            options: "{{json_encode(\App\Enums\JobAdTypes::toSelectArray())}}"
                        }
                    },
                    {data: 'position'
                        ,
                        filtering: {
                            name: 'positions',
                            label: 'Title',
                            type: 'select',
                            options: "{{json_encode($positions)}}"
                        }
                    },
                    {
                        data: null,
                        render: function (data, type, row) {
                            var showRoute = "{{ route('job-ads.show', ['jobAd' => ':id']) }}".replace(':id', data.id);

                            return '<a href="' + showRoute + '" class="badge badge-primary show-link">Show</a>';
                        },
                        className: "text-center",
                        orderable: false,
                    }
                ],
                csrfToken: '{{ csrf_token() }}',
                columnDefs: [{defaultContent: "-", "targets": '_all'}],
            });

            $(document).on("click", "#showShiftsModal", function(e) {
                e.preventDefault();
                $('#shiftsModal').modal('show');
                let id = $(e.currentTarget).data('id');
                if ($.fn.DataTable.isDataTable('#shifts-table')) {
                    $('#shifts-table').DataTable().destroy();
                }
                $('#shifts-table').DataTable({
                    paging: true,
                    pageLength: 10,
                    ajax: {
                        url: "{{ route('job-ads.show-shifts', ['jobAd' => ':id']) }}".replace(':id', id),
                        type: 'GET',
                        data: {
                            _token: '{{ csrf_token() }}'
                        }
                    },
                    columns: [
                        {data: 'id', visible: false},
                        {data: 'start_date'},
                        {data: 'end_date'},
                        {data: 'start_time'},
                        {data: 'end_time'},
                    ],
                    columnDefs: [
                        {
                            targets: '_all',
                            defaultContent: "-"
                        }
                    ]
                });
            });
        });
    </script>
@endpush
