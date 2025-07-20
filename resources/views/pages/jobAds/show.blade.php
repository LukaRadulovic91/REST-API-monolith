@extends('layouts.master',
[
    'breadcrumbs' => true,
    'title' =>'Job Ad Details'
]
)
@push('css')
    <style>
        .star-icon {
            font-size: 1.5rem !important;
            cursor: pointer;
        }
    </style>
@endpush
@section('content')
@include('pages.modals.payment-days-modal')
    <div id="kt_app_content_container" class="app-container ">
        <!--begin::Layout-->
        <div class="d-flex flex-column flex-xl-row">
            <!--begin::Sidebar-->
            <div class="flex-column flex-lg-row-auto w-100 w-xl-350px mb-10">
                <!--begin::Card-->
                <div class="card mb-5 mb-xl-8">
                    <!--begin::Card body-->
                    <div class="card-body pt-15">
                        <!--begin::Summary-->
                        <div class="d-flex flex-center flex-column mb-5">
                        </div>
                        <!--end::Summary-->
                        <!--begin::Details toggle-->
                        <div class="d-flex flex-stack fs-4 py-3">
                            <div class="fw-bold rotate collapsible" data-bs-toggle="collapse" href="#kt_customer_view_details" role="button" aria-expanded="false" aria-controls="kt_customer_view_details">Details
                                <span class="ms-2 rotate-180">

                            </span></div>
                        </div>
                        <!--end::Details toggle-->
                        <div class="separator separator-dashed my-3"></div>
                        <!--begin::Details content-->
                        <div id="kt_customer_view_details" class="collapse show">
                            <div class="py-5 fs-6">
                                <!--begin::Details item-->
                                <div class="fw-bold mt-5">Title</div>
                                <div class="text-gray-600">{{$jobAd->position->title}}</div>
                                <!--begin::Details item-->
                                <!--begin::Details item-->
                                <div class="fw-bold mt-5">Status</div>
                                <div class="text-gray-600">
                                    <a href="#" class="text-gray-600 text-hover-primary">{{\App\Enums\JobAdStatus::getDescription($jobAd->job_ad_status_id)}}</a>
                                </div>
                                <!--begin::Details item-->
                                <!--begin::Details item-->
                                <div class="fw-bold mt-5">Type of post</div>
                                <div class="text-gray-600">{{\App\Enums\JobAdTypes::getDescription($jobAd->job_ad_type)}}</div>
                                <!--begin::Details item-->
                                <!--begin::Details item-->
                                <div class="fw-bold mt-5">Job Ad date</div>
                                @php
                                    $formattedDate = optional($jobAd->shifts->first())->start_date
                                    ? Carbon\Carbon::createFromFormat('Y-m-d', $jobAd->shifts->first()->start_date)->isoFormat('MMMM D, YYYY')
                                    : Carbon\Carbon::createFromFormat('Y-m-d', $jobAd->permament_start_date)->isoFormat('MMMM D, YYYY');
                                @endphp
                                {{-- $jobAd->shifts->first() ? $jobAd->shifts->first()->start_date : $jobAd->permament_start_date --}}
                                <div class="text-gray-600">{{ $formattedDate }}</div>

                                <!--begin::Details item-->
                                <!--begin::Details item-->
                                @if($jobAd->job_ad_type === \App\Enums\JobAdTypes::TEMPORARY)
                                    <div class="fw-bold mt-5">Shift time</div>
                                    @foreach($jobAd->shifts as $shift)
                                        @php
                                           $startDateTime = DateTime::createFromFormat('H:i:s', $shift->start_time)->format('h:i A');
                                           $endDateTime = DateTime::createFromFormat('H:i:s', $shift->end_time)->format('h:i A');
                                        @endphp
                                        <div class="text-gray-600">
                                                {{$startDateTime}}  - {{$endDateTime}}
                                        </div>
                                    @endforeach
                                @endif
                                <!--begin::Details item-->
                                <!--begin::Details item-->
                                <div class="fw-bold mt-5">Address</div>
                                <div class="text-gray-600">{{$jobAd->client->office_address}}</div>
                                <!--begin::Details item-->

                                <div class="fw-bold mt-5">Contact person</div>
                                <div class="text-gray-600"><a href="{{route('clients.show', ['client' => $jobAd->client->id])}}"> {{$jobAd->client->user->first_name}} {{$jobAd->client->user->last_name}} </a></div>

                                <div class="fw-bold mt-5">Office name</div>
                                <div class="text-gray-600">{{$jobAd->client->company_name}}</div>

                                <div class="fw-bold mt-5">Lunch break</div>
                                <div class="text-gray-600">{{$jobAd->lunch_break ? 'Yes' : 'No'}}  {{$jobAd->lunch_break ? $jobAd->lunch_break_duration : ''}}</div>

                                <div class="fw-bold mt-5">Pay rate</div>
                                <div class="text-gray-600">{{$jobAd->pay_rate}} $ per hour</div>

                                <div class="fw-bold mt-5">Payment time</div>
                                <div class="text-gray-600">{{\App\Enums\PaymentTime::getDescription($jobAd->payment_time)}}</div>

                                <div class="fw-bold mt-5">Publish date</div>
                                {{-- \Carbon\Carbon::parse($jobAd->created_at)->format('Y-m-d') --}}
                                <div class="text-gray-600">{{ \Carbon\Carbon::parse($jobAd->created_at)->isoFormat('MMMM D, YYYY') }}</div>

                                <div class="fw-bold mt-5">Number of activity Days</div>
                                <div class="text-gray-600">{{ \Carbon\Carbon::parse($jobAd->created_at)->diffInDays() }}</div>

                            </div>
                        </div>
                        <!--end::Details content-->
                    </div>
                    <!--end::Card body-->
                </div>
                <!--end::Card-->
            </div>
            <div class="flex-lg-row-fluid ms-lg-15">
                <ul class="nav nav-custom nav-tabs nav-line-tabs nav-line-tabs-2x border-0 fs-4 fw-semibold mb-8" role="tablist">

                    <li class="nav-item" role="presentation">
                        <a class="nav-link text-active-primary pb-4 active"
                           data-bs-toggle="tab"
                           href="#kt_customer_view_overview_tab"
                           aria-selected="true"
                           role="tab">Overview
                        </a>
                    </li>
                    @if ($jobAd->job_ad_type === \App\Enums\JobAdTypes::PERMANENT_FULL_TIME ||$jobAd->job_ad_type === \App\Enums\JobAdTypes::PERMANENT_PART_TIME)
                        <li class="nav-item ms-auto  d-flex justify-content-center">
                            @if($jobAd->job_ad_status_id ===  \App\Enums\JobAdStatus::REJECTED)
                                <button class="btn btn-success approve-action  btn-sm align-self-start mr-2">Approve</button>
                            @elseif ($jobAd->job_ad_status_id ===   \App\Enums\JobAdStatus::PENDING_REVIEW)
                                <button class="btn btn-success approve-action  btn-sm align-self-start mr-2">Approve</button>
                                <button class="btn btn-danger reject-action btn-sm align-self-start ml-2">Reject</button>
                            @endif
                        </li>
                    @endif
                    @if(
                        (($jobAd->job_ad_type === \App\Enums\JobAdTypes::PERMANENT_FULL_TIME || $jobAd->job_ad_type === \App\Enums\JobAdTypes::PERMANENT_PART_TIME )
                         && $jobAd->job_ad_status_id === \App\Enums\JobAdStatus::COMPLETED)
                        || ($jobAd->job_ad_type === \App\Enums\JobAdTypes::TEMPORARY && $jobAd->job_ad_status_id === \App\Enums\JobAdStatus::COMPLETED)
                    )

                        {{-- STRIPE --}}

                        <li class="nav-item d-flex justify-content-end" role="presentation" style="margin-left: auto;">

                            {{-- EXAMPLE SETUP BUTTON --}}

{{--                            <form action="{{ route('api.job-ads.redirect-to-stripe-update-page' , ['user' => $jobAd->client->user->id]) }}" method="GET" class="ml-auto">--}}

{{--                                <div class="d-flex my-4">--}}
{{--                                    <button class="btn btn-success btn-sm align-self-start mr-2">Setup</button>--}}
{{--                                </div>--}}
{{--                            </form>--}}

                            {{-- EXAMPLE SETUP BUTTON --}}


                            <!--
                                <form action=" {{-- route('job-ads.payment',['user'=>$jobAd->client->user->id,'jobAd'=>$jobAd->id]) --}}"
                                      method="POST"
                                      class="ml-auto">

                                    <input type="hidden" name="_token" value="{{-- csrf_token() --}}">

                                    <div class="d-flex my-4">
                                        <button class="btn btn-success btn-sm align-self-start mr-2">Make a payment</button>
                                    </div>
                                </form>
                            -->
                                <div class="d-flex my-4">
                                    <button class="btn btn-success btn-sm align-self-start mr-2 payment-modal-for-days">Make a payment</button>
                                </div>

                        </li>

                        {{-- STRIPE --}}
                    @endif

                    @if (($jobAd->job_ad_type === \App\Enums\JobAdTypes::PERMANENT_FULL_TIME || $jobAd->job_ad_type === \App\Enums\JobAdTypes::PERMANENT_PART_TIME)&&
                        ($jobAd->job_ad_status_id !== \App\Enums\JobAdStatus::COMPLETED && $jobAd->job_ad_status_id !== \App\Enums\JobAdStatus::CANCELLED ))
                        <li class="nav-item ms-auto  d-flex justify-content-center">
                            <button class="btn btn-success complete-job-ad  btn-sm align-self-start mr-2">Complete job ad</button>
                        </li>
                    @endif
                </ul>

                <div class="tab-content" id="myTabContent">
                    <!--begin:::Tab pane-->
                    @if($jobAd->job_ad_status_id === \App\Enums\JobAdStatus::CANCELLED && !$candidateCancelled)
                        <p>Job ad cancelled by
                            <a href="{{route('clients.show', ['client' => $jobAd->client->id])}}"> {{$jobAd->client->user->first_name}} {{$jobAd->client->user->last_name}} </a>
                        </p>
                    @elseif($candidateCancelled)
                        <p>Job ad cancelled by
                            <a href="{{route('candidates.show', ['candidate' => $candidateCancelled->id])}}"> {{$candidateCancelled->first_name}} {{$candidateCancelled->last_name}}</a>.
                            Reason of cancellation is: {{$candidateCancelled->reason_of_cancellation}}
                        </p>
                    @endif
                    <div class="tab-pane fade show active" id="kt_customer_view_overview_tab" role="tabpanel">
                        <!--begin::Card-->
                        <div class="card pt-4 mb-6 mb-xl-9">
                            <!--begin::Card header-->
                            <div class="card-header border-0">
                                <!--begin::Card title-->
                                <div class="card-title">
                                    <h2>Candidates Applied</h2>
                                </div>
                                <!--end::Card title-->
                                <!--begin::Card toolbar-->
                                <!--end::Card toolbar-->
                            </div>
                            <!--end::Card header-->
                            <!--begin::Card body-->
                            <div class="card-body pt-0 pb-5">
                                <!--begin::Table-->
                                <div id="kt_table_customers_payment_wrapper" class="dataTables_wrapper dt-bootstrap4 no-footer"><div class="table-responsive">
                                        <table class="table align-middle table-row-dashed gy-5 dataTable no-footer" id="candidates_applied">
                                            <thead>
                                                <tr class="fw-bold text-muted ">
                                                    <th class="ps-4 min-w-325px rounded-start">Name</th>
                                                    <th class="min-w-150px">Recommended</th>
                                                    <th class="min-w-150px">Details</th>
                                                </tr>
                                            </thead>
                                        </table>
                                    </div>
                                </div>
                                <!--end::Table-->
                            </div>
                            <!--end::Card body-->
                        </div>
                        <!--end::Card-->
                    </div>
                    <!--end:::Tab pane-->
                </div>
                <!--end:::Tab content-->
            </div>
            <!--end::Content-->
        </div>

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
            let hasErrors =  @json($errors->any());
            hasErrors ? toastr.success(@json($errors->first())) : '';
            $('#candidates_applied').DataTable({
                ajax: "{{ route('job-ads.candidates-applied', ['jobAd' => $jobAd->id]) }}",
                columns: [
                    { data: 'first_name',
                        render: function (data, type, row) {
                        if(row.approved) {
                            return  row.first_name + ' ' + row.last_name + '<span class="badge badge-success ml-5"> Accepted </span>';
                        }
                        return row.first_name + ' ' + row.last_name;
                        }
                    },
                    {
                        data: 'candidate_id',
                        render: function (data, type, row) {
                            var starClass = row.recommended ? 'fas fa-star text-warning' : 'fas fa-star';

                            return '<i class="' + starClass + ' star-icon" data-id="' + row.candidate_id + '"  data-recommended="' + row.recommended + '"></i>';
                        },
                        className: "text-center",
                        orderable: false
                    },
                    {
                        data: 'candidate_id',
                        render: function (data, type, row) {
                            var showRoute = "{{ route('candidates.show', ['candidate' => ':id']) }}".replace(':id', row.candidate_id);
                            return '<a href="' + showRoute + '" class="badge badge-primary show-link">Show</a>';
                        },
                        className: "text-center",
                        orderable: false
                    }
                ]
            });

            $('.payment-modal-for-days').on('click', function (){
                $('#shiftsModalForPaymentDays').modal('show');
            });

            $('.approve-action').on('click', function (){
                $.ajax({
                    url: "{{ route('job-ads.approve-job-ad', ['jobAd' => $jobAd->id]) }}",
                    method: 'PUT',
                    headers: {
                        'X-CSRF-TOKEN':  $('meta[name="csrf-token"]').attr('content')
                    }
                }).done(function (response) {
                    toastr.success('Job Ad approved successfully!');
                    setTimeout(function() {
                        location.reload();
                    }, 700);
                })
            });
            $('.complete-job-ad').on('click', function (){
                $.ajax({
                    url: "{{ route('job-ads.complete-job-ad', ['jobAd' => $jobAd->id]) }}",
                    method: 'PUT',
                    headers: {
                        'X-CSRF-TOKEN':  $('meta[name="csrf-token"]').attr('content')
                    }
                }).done(function (response) {
                    toastr.success('Job Ad successfully completed!');
                    setTimeout(function() {
                        location.reload();
                    }, 700);
                })
            });


            $('.reject-action').on('click', function (){
                $.ajax({
                    url: "{{ route('job-ads.reject-job-ad', ['jobAd' => $jobAd->id]) }}",
                    method: 'PUT',
                    headers: {
                        'X-CSRF-TOKEN':  $('meta[name="csrf-token"]').attr('content')
                    }
                }).done(function (response) {
                    toastr.success('Job Ad rejected successfully!');
                    setTimeout(function() {
                        location.reload();
                    }, 700);
                })
            });

            $(document).on('click', '.star-icon', function () {
                var candidateId = $(this).data('id');
                var isRecommended = $(this).data('recommended');

                $.ajax({
                    url: "{{ route('candidates.recommend-candidate', ['candidate' => ':candidate', 'jobAd' => $jobAd->id]) }}".replace(':candidate', candidateId),
                    method: 'PUT',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    data: {
                        recommended: !isRecommended
                    },
                    success: function (response) {
                        isRecommended = !isRecommended;

                        $(this).data('recommended', isRecommended);

                        isRecommended ? toastr.success('Candidate recommended successfully!') :
                            toastr.success('The candidate recommendation has been revoked.');
                    }.bind(this),
                    error: function (error) {
                        console.error('Ajax action error:', error);
                        toastr.error('Error recommending candidate.');
                    }
                });

                $(this).toggleClass('text-warning');
            });

        });
    </script>
@endpush

