$(document).ready(function () {
    // Fetch Warehouse Rooms when Warehouse is selected
    $('#purchase_product_warehouse_id').change(function () {
        let warehouseId = $(this).val();
        console.log("Selected Warehouse ID: ", warehouseId);

        $('#purchase_product_warehouse_room_id').html('<option value="">Loading...</option>');
        $('#purchase_product_warehouse_room_cartoon_id').html('<option value="">Select Warehouse Room Cartoon</option>');

        $.ajax({
            url: "/get-warehouse-rooms",
            type: "GET",
            data: { warehouse_id: warehouseId },
            success: function (response) {
                console.log("Warehouse Rooms Response: ", response);

                let options = '<option value="">Select Warehouse Room</option>';
                response.forEach(room => {
                    options += `<option value="${room.id}">${room.title}</option>`;
                });
                $('#purchase_product_warehouse_room_id').html(options);
            },
            error: function (xhr, status, error) {
                console.error("Error fetching warehouse rooms: ", xhr.responseText);
            }
        });
    });

    // Fetch Warehouse Room Cartoons when Warehouse Room is selected
    $('#purchase_product_warehouse_room_id').change(function () {
        let warehouseId = $('#purchase_product_warehouse_id').val();
        let roomId = $(this).val();
        console.log("Selected Warehouse ID: ", warehouseId, " Room ID: ", roomId);

        $('#purchase_product_warehouse_room_cartoon_id').html('<option value="">Loading...</option>');

        $.ajax({
            url: "/get-warehouse-room-cartoons",
            type: "GET",
            data: { warehouse_id: warehouseId, warehouse_room_id: roomId },
            success: function (response) {
                console.log("Warehouse Room Cartoons Response: ", response);

                let options = '<option value="">Select Warehouse Room Cartoon</option>';
                response.forEach(cartoon => {
                    options += `<option value="${cartoon.id}">${cartoon.title}</option>`;
                });
                $('#purchase_product_warehouse_room_cartoon_id').html(options);
            },
            error: function (xhr, status, error) {
                console.error("Error fetching warehouse room cartoons: ", xhr.responseText);
            }
        });
    });
});
