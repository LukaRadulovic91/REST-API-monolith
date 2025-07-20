@extends('layouts.master',
[
    'breadcrumbs' => true,
    'title' =>'Candidate profile'
]
)
@push('css')
    <style>
        .file-icon {
            width: 70px;
            height: 70px;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 1px 1px 5px 1px #888888;
            border: 2px solid #fff;
            border-radius: 6px;
            background-color: #ffffff; /* Boja pozadine ikonice */
        }

        .d-flex {
            display: flex;
        }

        .align-items-start {
            align-items: flex-start;
        }

        .file-icon {
            margin-right: 10px; /* Dodajte odgovarajući razmak između ikonice i teksta */
        }

        .message-body:last-child{
            margin-bottom: 0px !important;
        }

        .messages-container {
            padding: 2rem !important;
        }

        .card-footer {
            padding: 1rem !important;
        }

        .nav-line-tabs.nav-line-tabs-2x .nav-item .nav-link
        {
            caret-color: transparent;
        }

    </style>
@endpush
@php
    $systemPhoneNumber = '+13653633403';
@endphp
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
                                    @if(!isset($candidate->user->user_image_path))
                                        <img src="{{asset('images/blank.png')}}" alt="image">
                                    @else
                                        <img src="{{asset($candidate->user->user_image_path)}}" alt="image">
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
                                            <a href="#" class="text-gray-900 text-hover-primary fs-2 fw-bold me-1"> {{$candidate->user->first_name}} {{$candidate->user->last_name}}</a>
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
                                            <span href="#" class="d-flex align-items-center text-gray-400 text-hover-primary me-5 mb-2">
                                                 <i class="fas fa-map-marker-alt fa-lg me-1 mr-3"></i>
                                                {{$candidate->user->address}}
                                            </span>
                                            <span href="#" class="d-flex align-items-center text-gray-400 text-hover-primary mb-2">
                                                <i class="fas fa-envelope fa-lg mr-3"></i>
                                                {{$candidate->user->email}}
                                            </span>
                                        </div>


                                        <!--end::Info-->
                                    </div>
                                    <div class="d-flex my-4">
                                        <div class="d-flex my-4">
                                            @if($candidate->user->profile_status_id === \App\Enums\ProfileStatuses::REJECTED)
                                                <button class="btn btn-success approve-action btn-sm align-self-start mr-2">Approve</button>
                                            @elseif ($candidate->user->profile_status_id === \App\Enums\ProfileStatuses::PENDING_REVIEW)
                                                <button class="btn btn-success approve-action btn-sm align-self-start mr-2">Approve</button>
                                                <button class="btn btn-danger reject-action btn-sm align-self-start ml-2">Reject</button>
                                            @endif
                                        </div>
                                    </div>
                                    <!--end::User-->
                                </div>
                                <div class="d-flex my-4">
                                    <span class="d-flex align-items-center text-center text-white mb-1 ml-2
                                        @if($candidate->user->profile_status_id === \App\Enums\ProfileStatuses::APPROVED)
                                            bg-success
                                         @elseif($candidate->user->profile_status_id === \App\Enums\ProfileStatuses::REJECTED)
                                            bg-danger
                                         @else
                                            bg-primary
                                         @endif
                                        p-2 rounded small">
                                            <i class="ki-duotone ki-sms fs-2"></i>
                                           {{\App\Enums\ProfileStatuses::getDescription($candidate->user->profile_status_id)}}
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
                                   data-bs-toggle="tab" data-bs-target="#messages" type="button" role="tab" aria-controls="messages" aria-selected="false" >
                                    Messages
                                </p>
                            </li>
                        </ul>
                        <!--begin::Navs-->
                    </div>
                </div>
                <!--end::Navbar-->
                <!--begin::details View-->
                <div class="tab-content" id="myTabContent">
                    <div class="tab-pane fade show active card mb-5 mb-xl-10" id="details" role="tabpanel" aria-labelledby="details-tab">
                    <!--begin::Card header-->
                    <div class="card-header cursor-pointer">
                        <!--begin::Card title-->
                        <div class="card-title m-0 mb-3">
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
                            <label class="col-lg-4 fw-semibold text-muted">Phone number</label>
                            <!--end::Label-->
                            <!--begin::Col-->
                            <div class="col-lg-8">
                                <span class="fw-bold fs-6 text-gray-800">{{$candidate->user->phone_number}}</span>
                            </div>
                            <!--end::Col-->
                        </div>
                        <!--end::Row-->
                        <!--begin::Input group-->
                        <div class="row mb-7">
                            <!--begin::Label-->
                            <label class="col-lg-4 fw-semibold text-muted">Transportation</label>
                            <!--end::Label-->
                            <!--begin::Col-->
                            <div class="col-lg-8 fv-row">
                                <span class="fw-semibold text-gray-800 fs-6">{{\App\Enums\Transportations::getTransportation($candidate->transportation)}}</span>
                            </div>
                            <!--end::Col-->
                        </div>
                        <!--end::Input group-->
                        <!--begin::Input group-->
                        <div class="row mb-7">
                            <!--begin::Label-->
                            <label class="col-lg-4 fw-semibold text-muted">Title</label>
                            <!--end::Label-->
                            <!--begin::Col-->
                            <div class="col-lg-8 fv-row">
                                @foreach($candidate->positions as $position)
                                    <span class="fw-semibold text-gray-800 fs-6">{{$position->title}}</span>
                                    @if(!$loop->last)
                                        <span>,</span>
                                    @endif
                                @endforeach
                            </div>
                            <!--end::Col-->
                        </div>
                        <!--end::Input group-->
                        <!--begin::Input group-->
                        <div class="row mb-7">
                            <!--begin::Label-->
                            <label class="col-lg-4 fw-semibold text-muted">Desired Positions</label>
                            <!--end::Label-->
                            <!--begin::Col-->
                            <div class="col-lg-8">
                                @foreach($candidate->desired_positions as $position)
                                    <span class="fw-semibold text-gray-800 fs-6">{{\App\Enums\JobAdTypes::getType($position)}} </span>
                                    @if(!$loop->last)
                                        <span>,</span>
                                    @endif
                                @endforeach
                            </div>
                            <!--end::Col-->
                        </div>
                        <!--end::Input group-->
                        <div class="row mb-7">
                            <!--begin::Label-->
                            <label class="col-lg-4 fw-semibold text-muted">Address</label>
                            <!--end::Label-->
                            <!--begin::Col-->
                            <div class="col-lg-8">
                                <span class="fw-semibold text-gray-800 fs-6">{{$candidate->user->city}}, {{$candidate->user->province}}, {{$candidate->user->postal_code}}</span>
                            </div>
                            <!--end::Col-->
                        </div>
                        <!--begin::Input group-->
                        <div class="row mb-7">
                            <!--begin::Label-->
                            <label class="col-lg-4 fw-semibold text-muted">Resume</label>
                            <div class="col-lg-8">
                                @if(isset($candidate->candidate_cv[0]))
                                    <a href="{{asset($candidate->candidate_cv[0]->file_path)}}" target="_blank" rel="noopener noreferrer">{{$candidate->candidate_cv[0]->name}}</a>
                                @endif
                            </div>
                            <!--end::Label-->
                            <!--begin::Col-->
                            <div class="col-lg-8">
                                <span class="fw-semibold text-gray-800 fs-6"></span>
                            </div>
                            <!--end::Col-->
                        </div>
                        <!--end::Input group-->
                        <!--begin::Input group-->
                        <div class="row mb-7">
                            <!--begin::Label-->
                            <label class="col-lg-4 fw-semibold text-muted">Certifications</label>
                            <!--end::Label-->
                            <!--begin::Col-->
                            <div class="col-lg-8 d-flex flex-wrap">
                                @foreach($candidate->candidate_certificates as $certificates)
                                    <div class="m-2 ml-5 mr-2" style="max-width: 100px;" >
                                        <div class="d-flex flex-column align-items-start">
                                            <a href="{{ asset($certificates->file_path) }}" target="_blank" rel="noopener noreferrer" class="text-decoration-none">
                                                <div class="file-icon">
                                                    <img src="{{ asset('images/PDF.png') }}" alt="PDF Icon"> <!-- PNG ikonica za PDF -->
                                                </div>
                                            </a>
                                            <p class="m-0 mt-1 text-center" style="max-width: 90px">{{$certificates->name}}</p>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                            <!--end::Col-->
                        </div>
                        <!--end::Input group-->
                        <!--begin::Input group-->
                        <div class="row mb-7">
                            <!--begin::Label-->
                            <label class="col-lg-4 fw-semibold text-muted">Languages</label>
                            <!--end::Label-->
                            <!--begin::Col-->
                            <div class="col-lg-8">
                                @foreach($candidate->languages as $language)
                                    <span class="fw-semibold text-gray-800 fs-6">{{$language->title}} </span>
                                    @if(!$loop->last)
                                        <span>,</span>
                                    @endif
                                @endforeach
                            </div>
                            <!--end::Col-->
                        </div>
                        <!--end::Input group-->
                        <div class="row mb-7">
                            <!--begin::Label-->
                            <label class="col-lg-4 fw-semibold text-muted">Software Skills</label>
                            <!--end::Label-->
                            <!--begin::Col-->
                            <div class="col-lg-8">
                                @foreach($candidate->user->softwares as $software)
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
                    <div class="tab-pane fade" id="messages" role="tabpanel" aria-labelledby="messages-tab">
                        <!--begin::Card header-->

                        <!--begin::Card header-->
                        <!--begin::Card body-->
                        <div class="flex-lg-row-fluid">
                            <!--begin::Messenger-->
                            <div class="card" id="kt_chat_messenger">
                                <!--begin::Card header-->
                                <div class="card-header" id="kt_chat_messenger_header">
                                    <!--begin::Title-->
                                    <div class="card-title">
                                        <!--begin::User-->
                                        <div class="d-flex justify-content-center flex-column me-3">
                                            <!--end::Info-->
                                        </div>
                                        <!--end::User-->
                                    </div>
                                    <!--end::Title-->
                                </div>
                                <!--end::Card header-->
                                <!--begin::Card body-->

                                <div class=" messages-container" id="kt_chat_messenger_body">
                                    <!--begin::Messages-->
                                    <div class="scroll-y me-n5 pe-5 h-400px"
                                         data-kt-element="messages"
                                         data-kt-scroll="true"
                                         data-kt-scroll-activate="{default: false, lg: true}"
                                         id="messages-container">
                                        @foreach($messages as $message)
                                            @php
                                                 $createdAt = new DateTime($message->created_at);
                                                 $currentTime = new DateTime();

                                                 $timeDifference = $currentTime->diff($createdAt);

                                                 $timeUnits = [
                                                     'day' => $timeDifference->days,
                                                     'hour' => $timeDifference->h,
                                                     'minute' => $timeDifference->i,
                                                 ];

                                                 $output = array_reduce(array_keys($timeUnits), function($carry, $unit) use ($timeUnits) {
                                                    $value = $timeUnits[$unit];
                                                    if ($value > 0) {
                                                        $carry[] = ($value > 1) ? "$value $unit" . 's' : "$value $unit";
                                                    }
                                                    return $carry;
                                                 }, []);
                                            @endphp
                                        @if($message->direction == 'received')
                                        <!--begin::Message(in)-->
                                        <div class="d-flex justify-content-start mb-5 message-body">
                                            <!--begin::Wrapper-->
                                            <div class="d-flex flex-column align-items-start">
                                                <!--begin::User-->
                                                <div class="d-flex align-items-center mb-2">
                                                    <!--begin::Avatar-->
                                                    <div class="symbol symbol-35px symbol-circle">
                                                        <img alt="Pic" src="{{asset($candidate->user->user_image_path)}}">
                                                    </div>
                                                    <!--end::Avatar-->
                                                    <!--begin::Details-->
                                                    <div class="ms-3">
                                                        <a href="#" class="fs-5 fw-bold text-gray-900 text-hover-primary me-1">{{$candidate->user->first_name}} {{$candidate->user->last_name}}</a>
                                                        <span class="text-muted fs-7 mb-1">{{  implode(', ', $output) ?: '0 minutes' }}</span>
                                                    </div>
                                                    <!--end::Details-->
                                                </div>
                                                <!--end::User-->
                                                <!--begin::Text-->
                                                <div class="p-5 rounded bg-light-info text-dark fw-semibold mw-lg-400px text-start"
                                                     data-kt-element="message-text">
                                                    {{ $message->body }}
                                                </div>
                                                <!--end::Text-->
                                            </div>
                                            <!--end::Wrapper-->
                                        </div>
                                        <!--end::Message(in)-->
                                        @endif

                                        @if($message->direction == 'sent')
                                        <!--begin::Message(template for out)-->
                                        <div class="d-flex justify-content-end mb-5 message-body" data-kt-element="template-out">
                                            <!--begin::Wrapper-->
                                            <div class="d-flex flex-column align-items-end">
                                                <!--begin::User-->
                                                <div class="d-flex align-items-center mb-2">
                                                    <!--begin::Details-->
                                                    <div class="me-3">
                                                        <span class="text-muted fs-7 mb-1">{{  implode(', ', $output) ?: '0 minutes' }}</span>
                                                        <a href="#" class="fs-5 fw-bold text-gray-900 text-hover-primary ms-1">You</a>
                                                    </div>
                                                    <!--end::Details-->
                                                    <!--begin::Avatar-->
                                                    <div class="symbol symbol-35px symbol-circle">
                                                        <img alt="Pic" src="{{asset(auth()->user()->user_image_path)}}">
                                                    </div>
                                                    <!--end::Avatar-->
                                                </div>
                                                <!--end::User-->
                                                <!--begin::Text-->
                                                <div class="p-5 rounded bg-light-primary text-dark fw-semibold mw-lg-400px text-end"
                                                     data-kt-element="message-text">
                                                    {{ $message->body }}
                                                </div>
                                                <!--end::Text-->
                                            </div>
                                            <!--end::Wrapper-->
                                        </div>
                                        <!--end::Message(template for out)-->
                                        @endif

                                        @endforeach
                                    </div>
                                    <!--end::Messages-->
                                </div>
                                <!--end::Card body-->
                                <!--begin::Card footer-->
                                <div class="card-footer  d-flex justify-content-between" id="kt_chat_messenger_footer">
                                    <!--begin::Input-->
                                    <form class="d-flex" style="width: 100%;">
                                        <input
                                            class="form-control form-control-flush"
                                            style="height: 100%;"
                                            id="nevena-textarea"
                                            rows="1"
                                            data-kt-element="input"
                                            type="text"
                                            placeholder="Type a message">
                                        <!--end::Input-->
                                        <!--begin:Toolbar-->
                                        <div class="d-flex flex-stack">
                                            <!--begin::Send-->
                                            <button
                                                class="btn btn-primary send-message ml-4"
                                                type="submit"
                                                data-kt-element="send"
                                            >
                                                Send
                                            </button>
                                            <!--end::Send-->
                                        </div>
                                    </form>
                                    <!--end::Toolbar-->
                                </div>
                                <!--end::Card footer-->
                            </div>
                            <!--end::Messenger-->
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
        $(document).ready(function () {
            var urlParams = new URLSearchParams(window.location.search);

            if (urlParams.has('tab')) {

                var tabId = urlParams.get('tab');

                var element = document.getElementById(tabId);
                if (element) {
                    element.click();
                    $.ajax({
                        url: "{{ route('users.read-messages', ['user' => $candidate->user->id]) }}",
                        method: 'PUT',
                        headers: {
                            'X-CSRF-TOKEN':  $('meta[name="csrf-token"]').attr('content')
                        }
                    }).done(function (response) {
                        $('.bullet-dot').removeClass('animation-blink');
                    })

                    urlParams.delete('tab');
                    var newUrl = window.location.pathname + '?' + urlParams.toString();
                    history.replaceState({}, '', newUrl);
                }
            }
            $('.approve-action').on('click', function (event) {
                $.ajax({
                    url: "{{ route('users.approve-user', ['user' => $candidate->user->id]) }}",
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
                    url: "{{ route('users.reject-user', ['user' => $candidate->user->id]) }}",
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

            // send and get messages
            $('.send-message').on('click', function (e) {
                e.preventDefault();

                $.ajax({
                    url: "{{ route('twilio.send-message') }}",
                    type: 'GET',
                    data: {
                        messageText: $('#nevena-textarea').val(),
                        candidatePhoneNumber: "{{ $candidate->user->phone_number }}"
                    }
                }).done(function (response) {

                    $.ajax({
                        url: "{{ route('twilio.get-message-by-candidate', ['candidate' => $candidate->id]) }}",
                        type: 'GET'
                    }).done(function (response) {

                        let userPhoto = @json( asset(auth()->user()->user_image_path) );

                        let messageDiv =
                            `<div class="d-flex justify-content-end mb-10" data-kt-element="template-out">
                                <div class="d-flex flex-column align-items-end">
                                    <div class="d-flex align-items-center mb-2">
                                        <div class="me-3">
                                            <span class="text-muted fs-7 mb-1">${response.created_at}</span>
                                            <a href="#" class="fs-5 fw-bold text-gray-900 text-hover-primary ms-1"></a>
                                        </div>
                                        <div class="symbol symbol-35px symbol-circle">
                                            <img alt="Pic" src="${userPhoto}">
                                        </div>
                                    </div>

                                    <div class="p-5 rounded bg-light-primary text-dark fw-semibold mw-lg-400px text-end"
                                        data-kt-element="message-text">
                                        ${response.body}
                                    </div>
                                </div>
                            </div>`;

                        $('#messages-container').append(messageDiv);
                        $('#nevena-textarea').val('');
                        $('#messages-container').scrollTop($('#messages-container').prop('scrollHeight'));
                    });

                    toastr.success('Message has been successfully sent!');
                }).fail(function (response) {
                    // if (response.responseJSON.errors) {
                    //     toastr.error(response.responseJSON.errors, response.responseJSON.errors.resolution_date, {timeOut: 3000});
                    // }
                }).always(function () {
                    // buttonLocker.unlock();
                });
            });
        });

        function scrollToBottom()
        {
            $(document).scrollTop($(document).height());
        }

        $("#messages-tab").click(function() {
            scrollToBottom();
            $('#messages-container').scrollTop($('#messages-container').prop('scrollHeight'));
        });
    </script>
@endpush
