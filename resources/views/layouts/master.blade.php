<!DOCTYPE html>

<html lang="en">
<!--begin::Head-->
@include('fixed.head')
<!--end::Head-->
<!--begin::Body-->
<body id="kt_app_body" data-kt-app-layout="dark-sidebar" data-kt-app-header-fixed="true"
      data-kt-app-sidebar-enabled="true" data-kt-app-sidebar-fixed="true" data-kt-app-sidebar-hoverable="true"
      data-kt-app-sidebar-push-header="true" data-kt-app-sidebar-push-toolbar="true"
      data-kt-app-sidebar-push-footer="true" data-kt-app-toolbar-enabled="true" class="app-default">
<!--begin::Theme mode setup on page load-->
<!--end::Theme mode setup on page load-->


<!--begin::App-->
<div class="d-flex flex-column flex-root app-root" id="kt_app_root">
    <!--begin::Page-->
    <div class="app-page flex-column flex-column-fluid" id="kt_app_page">

        <!--begin::Header-->
        @include('fixed.header')
        <!--end::Header-->

        <div class="app-wrapper flex-column flex-row-fluid" id="kt_app_wrapper">

            <!--begin::Sidebar-->
            @include('fixed.sidebar')
            <!--end::Sidebar-->

            <!--begin::Main-->
            <div class="app-main flex-column flex-row-fluid" id="kt_app_main">

                <!--begin::Content wrapper-->
                <div class="d-flex flex-column flex-column-fluid">
                    <!--begin::Breadcrumbs -->
                    @if('breadcrumbs')
                        @include('fixed.breadcrumbs')
                    @endif
                    <!--end::Breadcrumbs-->
                    @yield('content')
                </div>
                <!--end::Content wrapper-->

                <!--begin::Footer-->
                @include('fixed.footer')
                <!--end::Footer-->
            </div>
            <!--end:::Main-->

        </div>



    </div>
    <!--end::Page-->
</div>
<!--end::App-->

<script>var hostUrl = "assets/";</script>
<!--begin::Global Javascript Bundle(mandatory for all pages)-->
<script src="{{asset('plugins/global/plugins.bundle.js')}}"></script>
<script src="{{asset('js/scripts.bundle.js')}}"></script>
{{--<script src = "{{asset('js/datatables.js')}}"></script>--}}
<script src = "{{asset('js/advance-datatables.js')}}"></script>
<script src = "{{asset('js/advance-datatables-filter.js')}}"></script>
@stack('scripts')
<!--end::Global Javascript Bundle-->
<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.4/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
<script src="{{ asset('plugins/custom/select2/dist/js/select2.full.min.js') }}"></script>
<script src="{{asset('plugins/custom/daterangepicker/js/daterangepicker.js')}}"></script>
<script src="{{ asset('js/toastr.js') }}"></script>


{{--<!--end::Vendors Javascript-->--}}
{{--<!--begin::Custom Javascript(used for this page only)-->--}}
{{--<script src="assets/js/widgets.bundle.js"></script>--}}
{{--<script src="assets/js/custom/widgets.js"></script>--}}
{{--<script src="assets/js/custom/apps/chat/chat.js"></script>--}}
{{--<script src="assets/js/custom/utilities/modals/upgrade-plan.js"></script>--}}
{{--<script src="assets/js/custom/utilities/modals/create-app.js"></script>--}}
{{--<script src="assets/js/custom/utilities/modals/new-target.js"></script>--}}
{{--<script src="assets/js/custom/utilities/modals/users-search.js"></script>--}}
<!--end::Custom Javascript-->
<!--end::Javascript-->
</body>
<!--end::Body-->
</html>
