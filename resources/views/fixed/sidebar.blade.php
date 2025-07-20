
    <!--begin::Sidebar-->
    <div id="kt_app_sidebar" class="app-sidebar flex-column" data-kt-drawer="true" data-kt-drawer-name="app-sidebar"  style="background-color: #5A294D;" data-kt-drawer-activate="{default: true, lg: false}" data-kt-drawer-overlay="true" data-kt-drawer-width="225px" data-kt-drawer-direction="start" data-kt-drawer-toggle="#kt_app_sidebar_mobile_toggle">
        <!--begin::Logo-->
        <div class="app-sidebar-logo px-6" id="kt_app_sidebar_logo">
            <!--begin::Logo image-->
            <a href="{{route('home')}}">
                <img alt="Logo" src="{{asset('images/logos/login-logo.png')}}" class="h-55px app-sidebar-logo-default" />
                <img alt="Logo" src="{{asset('images/logos/login-logo.png')}}" class="h-20px app-sidebar-logo-minimize" />
            </a>
            <!--end::Logo image-->
            <div id="kt_app_sidebar_toggle" class="app-sidebar-toggle btn btn-icon btn-shadow btn-sm btn-color-muted btn-active-color-primary h-30px w-30px position-absolute top-50 start-100 translate-middle rotate" data-kt-toggle="true" data-kt-toggle-state="active" data-kt-toggle-target="body" data-kt-toggle-name="app-sidebar-minimize">
                <i class="ki-duotone ki-black-left-line fs-3 rotate-180">
                    <span class="path1"></span>
                    <span class="path2"></span>
                </i>
            </div>
            <!--end::Sidebar toggle-->
        </div>
        <!--end::Logo-->
        <!--begin::sidebar menu-->
        <div class="app-sidebar-menu overflow-hidden flex-column-fluid">
            <!--begin::Menu wrapper-->
            <div id="kt_app_sidebar_menu_wrapper" class="app-sidebar-wrapper">
                <!--begin::Scroll wrapper-->
                <div id="kt_app_sidebar_menu_scroll" class="scroll-y my-5 mx-3" data-kt-scroll="true" data-kt-scroll-activate="true" data-kt-scroll-height="auto" data-kt-scroll-dependencies="#kt_app_sidebar_logo, #kt_app_sidebar_footer" data-kt-scroll-wrappers="#kt_app_sidebar_menu" data-kt-scroll-offset="5px" data-kt-scroll-save-state="true">
                    <!--begin::Menu-->
                    <div class="menu menu-column menu-rounded menu-sub-indention fw-semibold fs-6" id="#kt_app_sidebar_menu" data-kt-menu="true" data-kt-menu-expand="false">
                        <!--begin:Menu item-->
                        <div class="menu-item here show menu-accordion">
                            <!--begin:Menu link-->
                            <span class="menu-link">
                                <span class="menu-icon">
                                    <i class="ki-duotone ki-element-11 fs-2">
                                        <span class="path1"></span>
                                        <span class="path2"></span>
                                        <span class="path3"></span>
                                        <span class="path4"></span>
                                    </i>
                                </span>
                                <span class="menu-title">
                                      <a class="menu-link active" href="{{route('home')}}" style="background-color: rgba(255, 255, 255, 0.2)">
                                            <span class="menu-title">Job Orders</span>
                                        </a>
                                </span>
							</span>
                            <span class="menu-link">
                                <span class="menu-icon">
                                    <i class="ki-duotone ki-color-swatch fs-2">
                                        <span class="path1"></span>
                                        <span class="path2"></span>
                                        <span class="path3"></span>
                                        <span class="path4"></span>
                                        <span class="path5"></span>
                                        <span class="path6"></span>
                                        <span class="path7"></span>
                                        <span class="path8"></span>
                                        <span class="path9"></span>
                                        <span class="path10"></span>
                                        <span class="path11"></span>
                                        <span class="path12"></span>
                                        <span class="path13"></span>
                                        <span class="path14"></span>
                                        <span class="path15"></span>
                                        <span class="path16"></span>
                                        <span class="path17"></span>
                                        <span class="path18"></span>
                                        <span class="path19"></span>
                                        <span class="path20"></span>
                                        <span class="path21"></span>
                                    </i>
                                </span>
                                <span class="menu-title">
                                      <a class="menu-link active" href="{{route('candidates.index')}}" style="background-color: rgba(255, 255, 255, 0.2)">
                                            <span class="menu-title">Candidates</span>
                                        </a>
                                </span>
							</span>

                            <span class="menu-link">
                               <span class="menu-icon">
													<i class="ki-duotone ki-user fs-2">
														<span class="path1"></span>
														<span class="path2"></span>
													</i>
												</span>
                                <span class="menu-title">
                                      <a class="menu-link active" href="{{route('clients.index')}}" style="background-color: rgba(255, 255, 255, 0.2)">
                                            <span class="menu-title">Clients/Companies</span>
                                        </a>
                                </span>
							</span>

                            <span class="menu-link">
                              <span class="menu-icon">
                                 <i class="fas fa-comment fs-2"></i>
                            </span>
                                <span class="menu-title">
                                  <a class="menu-link active" href="{{route('job-ads.show-feedbacks')}}" style="background-color: rgba(255, 255, 255, 0.2)">
                                            <span class="menu-title">Feedback</span>
                                  </a>
                                </span>
							</span>
                            <!--end:Menu link-->
                        </div>
                        <!--end:Menu item-->
                    </div>
                    <!--end::Menu-->
                </div>
                <!--end::Scroll wrapper-->
            </div>
            <!--end::Menu wrapper-->
        </div>
        <!--end::sidebar menu-->
        <!--begin::Footer-->

        <!--end::Footer-->
    </div>


