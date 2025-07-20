<div class="modal fade" id="shiftsModal">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">{{ __("Shifts modal")}}</h4>
                <button type="button" class="close close-modal" data-dismiss="modal" aria-hidden="true">×</button>
            </div>


            <div class="modal-body">
                <div class="row">
                    <table class="table jobs-table" id ="shifts-table">
                        <thead>
                        <tr class="fw-bold text-muted bg-light">
                            <th class="ps-4 rounded-start">Id</th>
                            <th class="min-w-125px">Start date</th>
                            <th class="min-w-125px">End Date</th>
                            <th class="min-w-200px">Start Time</th>
                            <th class="min-w-150px">End time</th>
                        </tr>
                        </thead>
                    </table>
                </div>
            </div>
            <div class="modal-footer">
                <button class="btn btn-white close-modal" data-bs-dismiss="modal" >{{__('Close')}}</button>
            </div>
        </div>
    </div>
</div>
@push('scripts')
    <script>
        $('.close-modal').click(function (e){
            e.preventDefault();
            $('#shiftsModal').modal('hide');
        });
    </script>
@endpush
