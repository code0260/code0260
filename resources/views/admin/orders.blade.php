@extends('layouts.admin')

<style>
    .modal-backdrop {
        background-color: rgba(0, 0, 0, 0.1) !important;
        /* Light grey backdrop */
    }
</style>

<style>
    .modal-dialog-centered {
        display: flex;
        align-items: center;
        justify-content: center;
        height: 100%;
        margin: 0;
    }

    .modal-content {
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
        /* Adjust shadow */
        border-radius: 8px;
        /* Optional rounded corners */
    }
</style>

@section('content')
    <div class="main-content-inner">
        <div class="main-content-wrap">
            <div class="flex items-center flex-wrap justify-between gap20 mb-27">
                <h3>Orders</h3>
                <ul class="breadcrumbs flex items-center flex-wrap justify-start gap10">
                    <li>
                        <a href="{{ route('admin.index') }}">
                            <div class="text-tiny">Dashboard</div>
                        </a>
                    </li>
                    <li>
                        <i class="icon-chevron-right"></i>
                    </li>
                    <li>
                        <div class="text-tiny">Orders</div>
                    </li>
                </ul>
            </div>

            <div class="wg-box">
                <div class="flex items-center justify-between gap10 flex-wrap">
                    <div class="wg-filter flex-grow">
                        <form class="form-search flex-grow" id="order-search-form">
                            <fieldset class="name">
                                <input type="text" placeholder="Search Orders..." class="show-search" name="name"
                                    id="search-input-order" tabindex="2" autocomplete="off">
                            </fieldset>

                            <div class="box-content-search">
                                <ul id="box-content-search-order"></ul>
                            </div>
                        </form>

                    </div>
                </div>
                <div class="wg-table table-all-user">
                    <div class="table-responsive">
                        <table class="table table-striped table-bordered">
                            <thead>
                                <tr>
                                    <th style="width:70px">ID Number</th>
                                    <th class="text-center">Client's Name</th>
                                    <th class="text-center">Phone</th>
                                    <th class="text-center">Total</th>
                                    <th class="text-center">Status</th>
                                    <th class="text-center">Order Date</th>
                                    <th class="text-center">Total Items</th>
                                    <th class="text-center">Delivered On</th>
                                    <th class="text-center">Canceled On</th>
                                    <th class="text-center">Notes</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($orders as $order)
                                    <tr>

                                        <td class="text-center">{{ $order->id }}</td>
                                        <td class="text-center">{{ $order->name }}</td>
                                        <td class="text-center">{{ $order->phone }}</td>
                                        <td class="text-center">${{ $order->subtotal }}</td>

                                        <td class="text-center">
                                            @if ($order->status == 'delivered')
                                                <span class="badge bg-success">Delivered</span>
                                            @elseif($order->status == 'canceled')
                                                <span class="badge bg-danger">Canceled</span>
                                            @else
                                                <span class="badge bg-warning">Ordered</span>
                                            @endif

                                            {{ $order->status }}
                                        </td>
                                        <td class="text-center">{{ $order->created_at }}</td>
                                        <td class="text-center">{{ $order->orderItems->count() }}</td>
                                        <td class="text-center">
                                            {{ $order->delivered_date ? \Carbon\Carbon::parse($order->delivered_date)->format('Y-m-d H:i:s') : 'N/A' }}
                                        </td>
                                        <td class="text-center">
                                            {{ $order->status == 'canceled' ? \Carbon\Carbon::parse($order->canceled_date)->format('Y-m-d H:i:s') : 'N/A' }}
                                            <!-- عرض تاريخ الإلغاء في العمود الجديد -->
                                        </td>
                                        <td class="text-center">
                                            <a href="javascript:void(0)" class="edit-note"
                                                data-order-id="{{ $order->id }}" data-note="{{ $order->note }}">
                                                {{ $order->note ? $order->note : 'Add Note' }}
                                            </a>
                                        </td>

                                        <td class="text-center">
                                            <a href="{{ route('admin.order.details', ['order_id' => $order->id]) }}">
                                                <div class="list-icon-function view-icon">
                                                    <div class="item eye">
                                                        <i class="icon-eye"></i>
                                                    </div>
                                                </div>
                                            </a>
                                            <div>
                                                <a href="{{ route('user.order.edit', ['order_id' => $order->id]) }}"
                                                    class="ms-2" title="Edit Order">
                                                    <i class="fa fa-edit text-primary" style="font-size: 1.2rem;"></i>
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>

                        </table>
                    </div>
                </div>
                <div class="divider"></div>
                <div class="flex items-center justify-between flex-wrap gap10 wgp-pagination">
                    {{ $orders->links('pagination::bootstrap-5') }}
                </div>
            </div>
        </div>

        <div class="modal fade" id="editNoteModal" tabindex="-1" aria-labelledby="editNoteModalLabel" aria-hidden="true"
            data-bs-backdrop="false">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="editNoteModalLabel">Edit Note</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <form id="edit-note-form">
                            <div class="mb-3">
                                <label for="order-note" class="form-label">Note</label>
                                <textarea class="form-control" id="order-note" rows="3"></textarea>
                            </div>
                            <input type="hidden" id="order-id">
                        </form>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="button" class="btn btn-primary" id="save-note-btn">Save Note</button>
                    </div>
                </div>
            </div>
        </div>
    @endsection




    @push('scripts')
        <script>
            $(function() {
                $("#search-input-order").on("keyup", function() {
                    var searchQuery = $(this).val();

                    // إذا كان النص المدخل أكبر من حرفين، نرسل طلب البحث
                    if (searchQuery.length > 2) {
                        $.ajax({
                            type: "GET",
                            url: "{{ route('admin.orders.search') }}", // تأكد من أن الـ route صحيح
                            data: {
                                query: searchQuery
                            },
                            dataType: 'json',
                            success: function(data) {
                                $("#box-content-search-order").html(''); // مسح النتائج القديمة

                                if (data.length === 0) {
                                    $("#box-content-search-order").append(
                                        '<li>No results found.</li>'); // إذا لم توجد نتائج
                                } else {
                                    $.each(data, function(index, item) {
                                        if (item && item.name && item
                                            .id) { // تحقق من وجود item وخصائصه
                                            var link =
                                                "{{ route('admin.order.details', ['order_id' => ':order_id']) }}";
                                            link = link.replace(':order_id', item.id);

                                            // تنسيق عرض الأوامر بشكل جميل
                                            $("#box-content-search-order").append(`
                                    <li>
                                        <div class="order-item flex items-center gap-10 p-2">
                                            <div class="image">
                                                <img src="{{ asset('images/order-icon.png') }}" alt="Order" class="img-thumbnail" width="50" height="50">
                                            </div>
                                            <div class="order-info flex flex-col">
                                                <a href="${link}" class="name text-bold">${item.name}</a>
                                                <span class="order-id text-muted">Order ID: ${item.id}</span>
                                                <span class="status badge badge-success">${item.status}</span>
                                            </div>
                                        </div>
                                    </li>
                                `);
                                        } else {
                                            console.warn("Invalid item data: ",
                                                item); // لو كانت البيانات غير صحيحة
                                        }
                                    });
                                }
                            },
                            error: function() {
                                console.error("Failed to fetch search results.");
                            }
                        });
                    } else {
                        $("#box-content-search-order").html(''); // إذا كان النص أقل من 3 حروف، نغلق النتائج
                    }
                });
            });
        </script>

        <script>
            $(document).ready(function() {
                // Open the modal when clicking on the note
                $(document).on('click', '.edit-note', function() {
                    const orderId = $(this).data('order-id');
                    const note = $(this).data('note');

                    // Set values in the modal
                    $('#order-id').val(orderId);
                    $('#order-note').val(note);

                    // Show the modal
                    $('#editNoteModal').modal('show');
                });

                // Save the note
                $('#save-note-btn').on('click', function() {
                    const orderId = $('#order-id').val();
                    const note = $('#order-note').val();

                    // Send the updated note to the server
                    $.ajax({
                        type: 'POST',
                        url: '{{ route('admin.orders.updateNote') }}', // Adjust the route name accordingly
                        data: {
                            _token: '{{ csrf_token() }}',
                            order_id: orderId,
                            note: note
                        },
                        success: function(response) {
                            // Update the note in the table
                            $(`.edit-note[data-order-id="${orderId}"]`).text(note || 'Add Note');

                            // Hide the modal
                            $('#editNoteModal').modal('hide');


                        },
                        error: function() {
                            alert('Failed to update the note.');
                        }
                    });
                });
            });
        </script>
    @endpush
