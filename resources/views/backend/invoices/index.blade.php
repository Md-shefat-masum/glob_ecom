@extends('backend.master')

@section('header_css')
    <link href="{{ url('dataTable') }}/css/jquery.dataTables.min.css" rel="stylesheet">
    <link href="{{ url('dataTable') }}/css/dataTables.bootstrap4.min.css" rel="stylesheet">
    <style>
        .dataTables_wrapper .dataTables_paginate .paginate_button {
            padding: 0px;
            border-radius: 4px;
        }

        table.dataTable tbody td:nth-child(1) {
            text-align: center !important;
            font-weight: 600;
        }

        table.dataTable tbody td:nth-child(2) {
            text-align: center !important;
        }

        table.dataTable tbody td:nth-child(3) {
            text-align: center !important;
        }

        table.dataTable tbody td:nth-child(4) {
            text-align: center !important;
        }

        table.dataTable tbody td:nth-child(5) {
            text-align: center !important;
        }

        table.dataTable tbody td:nth-child(6) {
            text-align: center !important;
        }

        table.dataTable tbody td:nth-child(7) {
            text-align: center !important;
        }

        table.dataTable tbody td:nth-child(8) {
            text-align: center !important;
        }

        tfoot {
            display: table-header-group !important;
        }

        tfoot th {
            text-align: center;
        }
    </style>
@endsection

@section('page_title')
    All Invoices
@endsection

@section('page_heading')
    View All Invoices
@endsection

@section('content')
    <div class="row">
        <div class="col-lg-12 col-xl-12">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h4 class="card-title mb-0">View All Invoices</h4>
                    </div>
                     <label id="customFilter">
                            <a href="{{url('create/new/order')}}" class="btn btn-primary btn-sm"
                                style="margin-left: 5px"><b><i class="fas fa-plus"></i> Pos System</b></a>
                        </label>
                    <div class="table-responsive">
                    
                        <table class="table table-bordered mb-0 data-table">
                            <thead>
                                <tr>
                                    <th class="text-center">SL</th>
                                    <th class="text-center">Invoice No</th>
                                    <th class="text-center">Order No</th>
                                    <th class="text-center">Customer</th>
                                    <th class="text-center">Phone</th>
                                    <th class="text-center">Invoice Date</th>
                                    <th class="text-center">Total</th>
                                    <th class="text-center">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <!-- Your table data goes here -->
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('footer_js')
    {{-- js code for data table --}}
    <script src="{{ url('dataTable') }}/js/jquery.validate.js"></script>
    <script src="{{ url('dataTable') }}/js/jquery.dataTables.min.js"></script>
    <script src="{{ url('dataTable') }}/js/dataTables.bootstrap4.min.js"></script>

    <script type="text/javascript">
        var table = $(".data-table").DataTable({
            processing: true,
            serverSide: true,
            ajax: "{{ route('ViewAllInvoices') }}",
            columns: [{
                    data: 'DT_RowIndex',
                    name: 'DT_RowIndex'
                },
                {
                    data: 'invoice_no',
                    name: 'invoice_no'
                },
                {
                    data: 'order_no',
                    name: 'order_no'
                },
                {
                    data: 'customer_name',
                    name: 'customer_name'
                },
                {
                    data: 'customer_phone',
                    name: 'customer_phone'
                },
                {
                    data: 'invoice_date',
                    name: 'invoice_date'
                },
                {
                    data: 'total',
                    name: 'total'
                },
                {
                    data: 'action',
                    name: 'action',
                    orderable: false,
                    searchable: false
                }
            ]
        });
        $(".dataTables_filter").append($("#customFilter"));
    </script>

    {{-- js code for invoice crud --}}
    <script>
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        // Function to print invoice inline without navigation
        function printInvoiceInline(invoiceId) {
            // Create a hidden iframe to load and print the content
            var printFrame = document.createElement('iframe');
            printFrame.style.position = 'absolute';
            printFrame.style.width = '0';
            printFrame.style.height = '0';
            printFrame.style.border = 'none';
            printFrame.style.visibility = 'hidden';
            
            document.body.appendChild(printFrame);
            
            // Load the printable content
            var printUrl = "{{ route('POSInvoiceContent', ':id') }}".replace(':id', invoiceId);
            printFrame.src = printUrl;
            
            printFrame.onload = function() {
                setTimeout(function() {
                    try {
                        printFrame.contentWindow.print();
                        console.log('Print command sent to iframe');
                        
                        // Remove iframe after printing
                        setTimeout(function() {
                            document.body.removeChild(printFrame);
                        }, 1000);
                    } catch (e) {
                        console.error('Print failed:', e);
                        // Fallback: open in new tab
                        window.open(printUrl, '_blank');
                        document.body.removeChild(printFrame);
                    }
                }, 500);
            };
        }

        // Function to print POS invoice directly
        function printPosInvoice(invoiceId) {
            // First approach: try opening with specific window parameters
            var printUrl = "{{ route('POSInvoicePrint', ':id') }}".replace(':id', invoiceId);
            
            // Try to open in a new window with specific parameters to avoid popup blockers
            var printWindow = window.open(printUrl, 'posInvoicePrint_' + invoiceId, 'width=800,height=600,scrollbars=yes,resizable=yes,menubar=no,toolbar=no,location=no,status=no');
            
            if (printWindow) {
                // Window opened successfully
                printWindow.focus();
                
                // Add event listener for when window loads
                printWindow.addEventListener('load', function() {
                    // Small delay to ensure content is fully loaded
                    setTimeout(function() {
                        printWindow.print();
                    }, 1000);
                });
                
                // Fallback in case load event doesn't fire
                setTimeout(function() {
                    if (printWindow && !printWindow.closed) {
                        try {
                            printWindow.print();
                        } catch(e) {
                            console.log('Print command failed:', e);
                        }
                    }
                }, 2000);
                
            } else {
                // Popup was blocked - try alternative approach
                if (confirm('Print window was blocked by your browser. Click OK to open the invoice in a new tab.')) {
                    window.open(printUrl, '_blank');
                }
            }
        }

        // Alternative quick print function that opens in new tab directly
        function printPosInvoiceQuick(invoiceId) {
            var printUrl = "{{ route('POSInvoicePrint', ':id') }}".replace(':id', invoiceId);
            
            // Open in new tab (more reliable than popup)
            var printTab = window.open(printUrl, '_blank');
            
            if (printTab) {
                printTab.focus();
                console.log('Print tab opened for invoice ID:', invoiceId);
            } else {
                alert('Please allow popups for this site to enable quick printing.');
            }
        }

        $('body').on('click', '.deleteBtn', function () {
            var invoiceSlug = $(this).data("id");
            if (confirm("Are You sure want to delete !")) {
                if (check_demo_user()) {
                    return false;
                }
                $.ajax({
                    type: "GET",
                    url: "{{ url('delete/invoice') }}" + '/' + invoiceSlug,
                    success: function (data) {
                        table.draw(false);
                        toastr.error("Deleted", "Deleted Successfully");
                    },
                    error: function (xhr) {
                        console.log('Error:', xhr.responseJSON.error);
                        if (xhr.responseJSON && xhr.responseJSON.error) {
                            toastr.error(xhr.responseJSON.error, "Error");
                        } else {
                            toastr.error("An unexpected error occurred", "Error");
                        }
                    }
                });
            }
        });
    </script>
@endsection
