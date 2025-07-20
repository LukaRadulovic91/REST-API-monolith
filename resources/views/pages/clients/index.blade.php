@extends('layouts.master',
[
    'breadcrumbs' => true,
    'title' =>'Clients'
]
)
@section('content')
    <div class="card mb-5 mb-xl-8">
        <!--begin::Header-->
{{--        <div class="card-header border-0 pt-5">--}}
{{--            <h3 class="card-title align-items-start flex-column">--}}
{{--                <span class="card-label fw-bold fs-3 mb-1">New Arrivals</span>--}}
{{--                <span class="text-muted mt-1 fw-semibold fs-7">Over 500 new products</span>--}}
{{--            </h3>--}}
{{--            <div class="card-toolbar">--}}
{{--                <a href="#" class="btn btn-sm btn-light-primary">--}}
{{--                    <i class="ki-duotone ki-plus fs-2"></i>New Member</a>--}}
{{--            </div>--}}
{{--        </div>--}}
        <!--end::Header-->
        <!--begin::Body-->
        <div class="card-body py-3">
            <!--begin::Table container-->
            <div class="table-responsive">
                <!--begin::Table-->
                <table class="table client-table align-middle gs-0 gy-4" id ="client-table">
                    <!--begin::Table head-->
                    <thead>
                    <tr class="fw-bold text-muted bg-light">
                        <th class="ps-4 min-w-200px rounded-start">Company Name</th>
                        <th class="ps-4 min-w-200px rounded-start">Contact</th>
                        <th class="min-w-100px">Address</th>
                        <th class="min-w-100px">City</th>
                        <th class="min-w-150px">Office number </th>
                        <th class="min-w-150px">Cell Number</th>
                        <th class="min-w-100px">Email</th>
                        <th class="min-w-100px">Status</th>
                        <th class="min-w-100px">Job Details</th>
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
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
        $(document).ready(function() {

            initAdvanceDataTable({
                selector: '#client-table',
                saveStateAPI: false,
                ajax    : "{{route('clients.fetch')}}",
                pageLength : 10   ,
                columns: [
                    {data: 'company_name'},
                    {data: 'name'},
                    {data: 'address'},
                    {data: 'city'},
                    {data: 'office_number'},
                    {data: 'phone_number'},
                    {data: 'email'},
                    {data: 'status',
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
                        var showRoute = "{{ route('clients.show', ['client' => ':id']) }}".replace(':id', data.id);

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
