<div class="modal fade mx-auto" id="shiftsModalForPaymentDays">
    <div class="modal-dialog modal-lg">
        <div class="modal-content"  style="max-width: 400px; margin: auto;">
            <div class="modal-header">
                <h4 class="modal-title">{{ __("Payment")}}</h4>
                <button type="button" class="close close-modal" data-dismiss="modal" aria-hidden="true">×</button>
            </div>
            <div class="modal-body">
                <div id="error-message" class="text-danger"></div>
                <form action="{{ route('job-ads.payment',['user'=>$jobAd->client->user->id,'jobAd'=>$jobAd->id]) }}" method="POST" class="ml-auto" id="paymentForm">
                    <input type="hidden" name="_token" value="{{ csrf_token() }}">
                    <input type="hidden" id="payment_days" name="payment_days" value="">
                    <div class="row">
                        <table class="table payment-table">
                            <thead>
                                <tr class="fw-bold text-muted bg-light">
                                    <th>Payment Days</th>
                                    <th>Selection</th>
                                </tr>
                            </thead>
                            <tbody>
                            @foreach($payment_days as $key => $day)
                                @php
                                    $price = \App\Enums\PaymentDays::fromValue($key)->value();
                                    $priceWithTaxes = \App\Enums\PaymentDays::fromValue($key)->valueWithTaxes();
                                @endphp
                                <tr data-toggle="radio-click">
                                    <td class="payment-option">{{ $day }}  {{ $priceWithTaxes }} </td>
                                    <td class="payment-selection"><input type="radio" name="payment_days" value="{{ $price }}"></td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-success btn-sm align-self-start mr-2 payment">Make a payment</button>
                        <button class="btn btn-white close-modal" data-bs-dismiss="modal" >{{__('Close')}}</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@push('scripts')
    <script>
        $('.close-modal').click(function (e){
            e.preventDefault();
            $('#shiftsModalForPaymentDays').modal('hide');
        });

        $('.payment').click(function (e) {
            var selectedPaymentDays = $('input[name=payment_days]:checked').val();
            if (!selectedPaymentDays) {
                $('#error-message').text('Please select payment days.').show();
                return;
            }
            $('#error-message').hide();
            $('#payment_days').val(selectedPaymentDays);
            $('#paymentForm').submit();
        });

        $('body').on('click', '[data-toggle="radio-click"]', function (e){
            e.stopPropagation();
            $(this).find('input[type="radio"]').prop('checked', true);
        })

    </script>
@endpush
