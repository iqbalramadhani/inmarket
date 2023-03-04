<div class="modal-dialog" role="document">
    <div class="modal-content">
        <div class="modal-header">
            <h5 class="modal-title" id="exampleModalLabel">Batalkan Pesanan</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
        <div class="modal-body">
            Pesanan kamu akan dibatalkan
        </div>
        <div class="modal-footer">
            <a href="{{route('customer.cancel-order', ['order_code' => $order->code])}}" class="btn btn-primary">Batalkan Pesanan</a>
        </div>
    </div>
</div>
