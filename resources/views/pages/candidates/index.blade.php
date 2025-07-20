@extends('layouts.master',
[
    'breadcrumbs' => true,
    'title' =>'Candidates'
]
)
@section('content')
    <div class="card mb-5 mb-xl-8">
        <!--begin::Body-->
        <div class="card-body py-3">
            <!--begin::Table container-->
            <div class="table-responsive">
                <!--begin::Table-->
                <table class="table candidate-table align-middle gs-0 gy-4" id ="candidate-table">
                    <!--begin::Table head-->
                    <thead>
                    <tr class="fw-bold text-muted bg-light">
                        <th class="ps-4 min-w-325px rounded-start">Name</th>
                        <th class="min-w-125px">Title</th>
                        <th class="min-w-125px">Address</th>
                        <th class="min-w-200px">Status</th>
                        <th class="min-w-150px">Request Date And Time</th>
                        <th class="min-w-150px">Candidate Details</th>
                    </tr>
                    </thead>
                    <!--end::Table head-->
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
            initAdvanceDataTable({
                selector: '#candidate-table',
                saveStateAPI: false,
                ajax    : "{{route('candidates.fetch')}}",
                pageLength : 25   ,
                columns: [
                    {data: 'name'},
                    {data: 'title',
                        filtering: {
                            name:'positions',
                            label:'Title',
                            type:'select',
                            options: "{{json_encode($positions)}}"
                        }
                    },
                    {data: 'address'},
                    {data: 'status'},
                    {data: 'request_status',
                        filtering: {
                            name:'statuses',
                            label:'Statuses',
                            type:'select',
                            options: "{{json_encode($statuses)}}"
                        }
                    },
                    {
                        data: null,
                        render: function (data, type, row) {
                            var showRoute = "{{ route('candidates.show', ['candidate' => ':id']) }}".replace(':id', row.id);

                            return '<a href="' + showRoute + '" class="badge badge-primary show-link">Show</a>';
                        },
                        className: "text-center",
                        orderable: false,
                    }
                ],
                csrfToken : '{{ csrf_token() }}',
                columnDefs: [{ defaultContent: "-", "targets": '_all' }],
            });
        });
    </script>
@endpush
