@extends('layouts.master',
[
    'breadcrumbs' => true,
    'title' =>'Feedbacks'
]
)
@php
    $is_active = [1 => 'Candidate', 0 => 'Client'];
@endphp
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
                <table class="table feedbacks-table align-middle gs-0 gy-4" id ="feedbacks-table">
                    <!--begin::Table head-->
                    <thead>
                    <tr class="fw-bold text-muted bg-light">
                        <th class="ps-4 min-w-325px rounded-start">Client/Candidate Name</th>
                        <th class="min-w-125px">Feedback text</th>
{{--                        <th class="min-w-125px">Timestamp</th>--}}
                        <th class="min-w-200px">Related Job</th>
                        <th class="min-w-150px">Feedback Details</th>
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
                selector: '#feedbacks-table',
                saveStateAPI: false,
                ajax    : "{{route('job-ads.feedbacks-fetch')}}",
                pageLength : 5   ,
                columns: [
                    {data: 'full_name',
                        render: function (data, type, row) {
                            return row.full_name ;
                        },
                        filtering: {
                            name: 'is_client',
                            label: 'By role',
                            type: 'checkbox',
                            options: "{{json_encode($is_active)}}"
                        }
                    },
                    {data: 'feedback',
                        render: function (data, type, row) {
                            return row.feedback ;
                        }
                    },
                    // {data: 'request_status',
                    // },
                    {data: 'title'},
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
                csrfToken : '{{ csrf_token() }}',
                columnDefs: [{ defaultContent: "-", "targets": '_all' }],
            });
        });
    </script>
@endpush
