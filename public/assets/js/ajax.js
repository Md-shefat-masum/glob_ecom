$(document).ready(function() {
    // When the warehouse dropdown changes
    $('#product_warehouse_id').on('change', function() {
        let warehouseId = $(this).val();
        
        if (warehouseId) {
            // Send an AJAX POST request
            $.ajax({
                // url: "{{ route('get.product.warehouse.rooms') }}", 
                url: location.origin + "/get-product-warehouse-rooms",
                type: "POST",
                data: { product_warehouse_id: warehouseId },
                success: function(data) {
                    // Clear and populate the warehouse room dropdown
                    let $roomDropdown = $('#product_warehouse_room_id');
                    $roomDropdown.empty(); // Clear existing options
                    $roomDropdown.append('<option value="" disabled selected>Select Warehouse Room</option>');

                    $.each(data, function(index, room) {
                        $roomDropdown.append('<option value="' + room.id + '">' + room.title + '</option>');
                    });
                },
                error: function(xhr) {
                    console.error(xhr.responseText);
                    alert('An error occurred while fetching the warehouse rooms.');
                }
            });
        } else {
            // Clear the warehouse room dropdown if no warehouse is selected
            $('#product_warehouse_room_id').empty().append('<option value="" disabled selected>Select Warehouse Room</option>');
        }
    });
});
