@extends('layouts.master',
[
    'breadcrumbs' => true,
    'title' =>'Clients profile'
]
)
@section('content')
    <!--begin::Content wrapper-->
    <div class="d-flex flex-column flex-column-fluid">
        <!--begin::Toolbar-->
        <div id="kt_app_toolbar" class="app-toolbar py-3 py-lg-6">
            <!--begin::Toolbar container-->
            <div id="kt_app_toolbar_container" class=" mr-2 ml-2 d-flex flex-stack">
            </div>
            <!--end::Toolbar container-->
        </div>
        <!--end::Toolbar-->
        <!--begin::Content-->
        <div id="kt_app_content" class="app-content flex-column-fluid">
            <!--begin::Content container-->
            <div id="kt_app_content_container" class=" mr-2 ml-2 ">
                <!--begin::Navbar-->
                <div class="card mb-10 mb-xl-12">
                    <div class="card-body pt-9 pb-0">
                        <!--begin::Details-->
                        <div class="d-flex flex-wrap flex-sm-nowrap">
                            <!--begin: Pic-->
                            <div class="me-7 mb-4">
                                <div class="symbol symbol-100px symbol-lg-160px symbol-fixed position-relative">
                                    @if(!isset($client->user->user_image_path))
                                        <img src="{{asset('images/blank.png')}}" alt="image">
                                    @else
                                        <img src="{{asset($client->user->user_image_path)}}" alt="image">
                                    @endif
                                    <div class="position-absolute translate-middle bottom-0 start-100 mb-6 bg-success rounded-circle border border-4 border-body h-20px w-20px"></div>
                                </div>
                            </div>
                            <!--end::Pic-->
                            <!--begin::Info-->
                            <div class="flex-grow-1">
                                <!--begin::Title-->
                                <div class="d-flex justify-content-between align-items-start flex-wrap mb-2">
                                    <!--begin::User-->
                                    <div class="d-flex flex-column">
                                        <!--begin::Name-->
                                        <div class="d-flex align-items-center mb-2">
                                            <a href="#" class="text-gray-900 text-hover-primary fs-2 fw-bold me-1"> {{$client->user->first_name}} {{$client->user->last_name}}</a>
                                            <a href="#">
                                                <i class="ki-duotone ki-verify fs-1 text-primary">
                                                    <span class="path1"></span>
                                                    <span class="path2"></span>
                                                </i>
                                            </a>
                                        </div>
                                        <!--end::Name-->
                                        <!--begin::Info-->
                                        <div class="d-flex flex-wrap fw-semibold fs-6 mb-4 pe-2">
                                            <span  class="d-flex align-items-center text-gray-400 text-hover-primary me-5 mb-2">
                                                <i class="fas fa-user-circle fa-lg mr-3"></i>
                                                {{$client->dentist_name}}
                                            </span>
                                            <span  class="d-flex align-items-center text-gray-400 text-hover-primary me-5 mb-2">
                                               <i class="fas fa-map-marker-alt fa-lg me-1 mr-3"></i>
                                                {{$client->office_address}}
                                            </span>
                                            <span class="d-flex align-items-center text-gray-400 text-hover-primary mb-2">
                                                <i class="fas fa-envelope fa-lg mr-3"></i>
                                                {{$client->user->email}}
                                            </span>

                                        </div>


                                        <!--end::Info-->
                                    </div>
                                    <div class="d-flex my-4">
                                        @if($client->user->profile_status_id === \App\Enums\ProfileStatuses::REJECTED)
                                            <button class="btn btn-success approve-action  btn-sm align-self-start mr-2">Approve</button>
                                        @elseif ($client->user->profile_status_id === \App\Enums\ProfileStatuses::PENDING_REVIEW)
                                            <button class="btn btn-success approve-action  btn-sm align-self-start mr-2">Approve</button>
                                            <button class="btn btn-danger reject-action btn-sm align-self-start ml-2">Reject</button>
                                        @endif
                                    </div>
                                    <!--end::User-->
                                </div>
                                <div class="d-flex my-4">
                                        <span class="d-flex align-items-center text-center text-white mb-1 ml-1
                                            @if($client->user->profile_status_id === \App\Enums\ProfileStatuses::APPROVED)
                                                bg-success
                                             @elseif($client->user->profile_status_id === \App\Enums\ProfileStatuses::REJECTED)
                                                bg-danger
                                             @else
                                                bg-primary
                                             @endif
                                            p-2 rounded small">
                                                <i class="ki-duotone ki-sms fs-2"></i>
                                               {{\App\Enums\ProfileStatuses::getDescription($client->user->profile_status_id)}}
                                        </span>
                                </div>
                                <!--end::Title-->
                            </div>
                            <!--end::Info-->
                        </div>
                        <!--end::Details-->
                        <!--begin::Navs-->
                        <ul class="nav nav-tabs nav-line-tabs nav-line-tabs-2x border-transparent fs-5 fw-bold" id="myTab"  role="tablist">
                            <!--begin::Nav item-->
                            <li class="nav-item mt-2">
                                <p class="nav-link text-active-primary ms-0 me-10 py-5 active" id="details-tab"
                                   data-bs-toggle="tab" data-bs-target="#details" type="button" role="tab" aria-controls="details" aria-selected="true" >
                                    Overview
                                </p>
                            </li>
                            <!--end::Nav item-->
                            <li class="nav-item mt-2">
                                <p class="nav-link text-active-primary ms-0 me-10 py-5" id="messages-tab"
                                   data-bs-toggle="tab" data-bs-target="#jobAds" type="button" role="tab" aria-controls="jobAds" aria-selected="false" >
                                    Job Orders
                                </p>
                            </li>
                        </ul>
                        <!--begin::Navs-->
                    </div>
                </div>
                <!--end::Navbar-->
                <!--begin::details View-->
                <div class="tab-content" id="myTabContent">
                    <div  class="tab-pane fade show active card mb-5 mb-xl-10" id="details" role="tabpanel" aria-labelledby="details-tab">
                    <!--begin::Card header-->
                    <div class="card-header cursor-pointer">
                        <!--begin::Card title-->
                        <div class="card-title m-0 mb-2">
                            <h3 class="fw-bold m-0">Profile Details</h3>
                        </div>
                        <!--end::Card title-->
                    </div>
                    <!--begin::Card header-->
                    <!--begin::Card body-->
                    <div class="card-body p-9">
                        <!--begin::Row-->
                        <div class="row mb-7">
                            <!--begin::Label-->
                            <label class="col-lg-4 fw-semibold text-muted">Title</label>
                            <!--end::Label-->
                            <!--begin::Col-->
                            <div class="col-lg-8">
                                <span class="fw-bold fs-6 text-gray-800">{{$client->title}}</span>
                            </div>
                            <!--end::Col-->
                        </div>
                        <!--end::Row-->
                        <!--begin::Input group-->
                        <div class="row mb-7">
                            <!--begin::Label-->
                            <label class="col-lg-4 fw-semibold text-muted">Company name</label>
                            <!--end::Label-->
                            <!--begin::Col-->
                            <div class="col-lg-8 fv-row">
                                <span class="fw-semibold text-gray-800 fs-6">{{$client->company_name}}</span>
                            </div>
                            <!--end::Col-->
                        </div>
                        <!--end::Input group-->
                        <!--begin::Input group-->
                        <div class="row mb-7">
                            <!--begin::Label-->
                            <label class="col-lg-4 fw-semibold text-muted">Province</label>
                            <!--end::Label-->
                            <!--begin::Col-->
                            <div class="col-lg-8 fv-row">
                                <span class="fw-semibold text-gray-800 fs-6">{{$client->user->province}}</span>
                            </div>
                            <!--end::Col-->
                        </div>
                        <!--end::Input group-->
                        <!--begin::Input group-->
                        <div class="row mb-7">
                            <!--begin::Label-->
                            <label class="col-lg-4 fw-semibold text-muted">City</label>
                            <!--end::Label-->
                            <!--begin::Col-->
                            <div class="col-lg-8">
                                <span class="fw-semibold text-gray-800 fs-6">{{$client->user->city}}</span>
                            </div>
                            <!--end::Col-->
                        </div>
                        <!--end::Input group-->
                        <div class="row mb-7">
                            <!--begin::Label-->
                            <label class="col-lg-4 fw-semibold text-muted">Postal code</label>
                            <!--end::Label-->
                            <!--begin::Col-->
                            <div class="col-lg-8">
                                <span class="fw-semibold text-gray-800 fs-6">{{$client->user->postal_code}}</span>
                            </div>
                            <!--end::Col-->
                        </div>
                        <!--begin::Input group-->
                        <div class="row mb-7">
                            <!--begin::Label-->
                            <label class="col-lg-4 fw-semibold text-muted">Office phone number</label>
                            <!--end::Label-->
                            <!--begin::Col-->
                            <div class="col-lg-8">
                                <span class="fw-semibold text-gray-800 fs-6">{{$client->office_number}}</span>
                            </div>
                            <!--end::Col-->
                        </div>
                        <!--end::Input group-->
                        <!--begin::Input group-->
                        <div class="row mb-7">
                            <!--begin::Label-->
                            <label class="col-lg-4 fw-semibold text-muted">Parking</label>
                            <!--end::Label-->
                            <!--begin::Col-->
                            <div class="col-lg-8">
                                @if($client->free_parking)
                                    <span class="fw-bold fs-6 text-gray-800">&#10003;</span> <!-- Checkmark -->
                                @else
                                    <span class="fw-bold fs-6 text-danger">&#10008;</span> <!-- Red Cross -->
                                @endif
                            </div>
                            <!--end::Col-->
                        </div>
                        <!--end::Input group-->
                        <!--begin::Input group-->
                        <div class="row mb-7">
                            <!--begin::Label-->
                            <label class="col-lg-4 fw-semibold text-muted">Ultrasonic</label>
                            <!--end::Label-->
                            <!--begin::Col-->
                            <div class="col-lg-8">
                                @if($client->ultrasonic_cavitron)
                                    <span class="fw-bold fs-6 text-gray-800">&#10003;</span> <!-- Checkmark -->
                                @else
                                    <span class="fw-bold fs-6 text-danger">&#10008;</span> <!-- Red Cross -->
                                @endif
                            </div>
                            <!--end::Col-->
                        </div>
                        <!--end::Input group-->
                        <div class="row mb-7">
                            <!--begin::Label-->
                            <label class="col-lg-4 fw-semibold text-muted">Radiography</label>
                            <!--end::Label-->
                            <!--begin::Col-->
                            <div class="col-lg-8">
                                @if($client->digital_x_ray)
                                    <span class="fw-bold fs-6 text-gray-800">&#10003;</span> <!-- Checkmark -->
                                @else
                                    <span class="fw-bold fs-6 text-danger">&#10008;</span> <!-- Red Cross -->
                                @endif
                            </div>
                            <!--end::Col-->
                        </div>
                        <div class="row mb-7">
                            <!--begin::Label-->
                            <label class="col-lg-4 fw-semibold text-muted">Recall time</label>
                            <!--end::Label-->
                            <!--begin::Col-->
                            <div class="col-lg-8">
                                <span class="fw-semibold text-gray-800 fs-6"> {{\App\Enums\RecallTime::getMinutes($client->recall_time)}} mins</span>
                            </div>
                            <!--end::Col-->
                        </div>

                        <div class="row mb-7">
                            <!--begin::Label-->
                            <label class="col-lg-4 fw-semibold text-muted">Softwares</label>
                            <!--end::Label-->
                            <!--begin::Col-->
                            <div class="col-lg-8">

                               @foreach($client->user->softwares as $software)
                                    <span class="fw-semibold text-gray-800 fs-6">{{$software->title}} </span>
                                    @if(!$loop->last)
                                        <span>,</span>
                                    @endif
                               @endforeach
                            </div>
                            <!--end::Col-->
                        </div>
                    </div>
                    <!--end::Card body-->
                </div>
                    <div class="tab-pane fade" id="jobAds" role="tabpanel" aria-labelledby="jobAds-tab">
                        <div id="kt_table_customers_payment_wrapper" class="dataTables_wrapper dt-bootstrap4 no-footer p-3">
                            <div class="table-responsive">
                                <table class="table align-middle table-row-dashed gy-5 dataTable no-footer" id="job-ads-table">
                                    <thead>
                                    <tr class="fw-bold text-muted ">
                                        <th class="ps-4 min-w-325px rounded-start">Title</th>
                                        <th class="min-w-150px">Date</th>
                                        <th class="min-w-150px">No of candidates applied </th>
                                        <th class="min-w-150px">Details</th>
                                    </tr>
                                    </thead>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!--end::Content container-->
        </div>
        <!--end::Content-->
    </div>
@endsection

@push('scripts')
    <script>
        $(document).ready(function (){
            $('.approve-action').on('click', function (){
                $.ajax({
                    url: "{{ route('users.approve-user', ['user' => $client->user->id]) }}",
                    method: 'PUT',
                    headers: {
                        'X-CSRF-TOKEN':  $('meta[name="csrf-token"]').attr('content')
                    }
                }).done(function (response) {
                    toastr.success('User approved successfully!');
                    setTimeout(function() {
                        location.reload();
                    }, 700);
                })
            });

            $('.reject-action').on('click', function (){
                $.ajax({
                    url: "{{ route('users.reject-user', ['user' => $client->user->id]) }}",
                    method: 'PUT',
                    headers: {
                        'X-CSRF-TOKEN':  $('meta[name="csrf-token"]').attr('content')
                    }
                }).done(function (response) {
                    toastr.success('User rejected successfully!');
                    setTimeout(function() {
                        location.reload();
                    }, 700);
                })
            });
            $('#job-ads-table').DataTable({
                ajax: "{{ route('clients.get-job-ads', ['client' => $client->id]) }}",
                pageLength : 10   ,
                columns: [
                    { data: 'position',
                        render: function (data, type, row) {
                            return row.position;
                        }
                    },
                    {
                        data: 'created',
                        render: function (data, type, row) {
                            var dateToShow = row.shift_start_date ? row.shift_start_date : row.created;
                            if (row.shift_start_date) {
                                return  dateToShow + `<span class="badge badge-success badge-pill ml-4"><a href="#" class="show-link"  id = "showShiftsModal" data-id = "${row.id}" style="text-decoration: none; color: inherit;"><i class="fas fa-search" style="color:white;"></i></a></span>`;
                            } else {
                                return dateToShow;
                            }
                        }
                    },
                    {
                        "data": "applicant_count",
                        "render": function(data, type, row) {
                            if (type === 'display' && data === 0) {
                                return '-';
                            }
                            return data;
                        }
                    },
                    {
                        data: null,
                        render: function (data, type, row) {
                            var showRoute = "{{ route('job-ads.show', ['jobAd' => ':id']) }}".replace(':id', data.id);

                            return '<a href="' + showRoute + '" class="badge badge-primary show-link">Show</a>';
                        },
                        className: "text-center",
                    }
                ]
            });
        });

    </script>
@endpush
