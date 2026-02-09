// $(document).ready(function () {
//     $('#searchPurchaseProduct').on('keyup', function () {
//         let query = $(this).val();

//         if (query.length > 2) { // Fetch only if at least 3 characters are typed
//             $.ajax({
//                 url: `${location.origin}/search/products`,
//                 type: 'GET',
//                 data: { search: query },
//                 success: function (data) {
//                     let resultsDropdown = $('#searchResults');
//                     resultsDropdown.empty();
//                     console.log("resultsDropdown - ", resultsDropdown);

//                     if (data.length > 0) {
//                         $.each(data, function (index, product) {
//                             resultsDropdown.append(`<a href="#" class="dropdown-item selectProduct" 
//                                 data-id="${product.id}" 
//                                 data-name="${product.name}" 
//                                 data-price="${product.price}">
//                                 ${product.name} - $${product.price}
//                             </a>`);
//                         });

//                         resultsDropdown.show();
//                     } else {
//                         resultsDropdown.hide();
//                     }
//                 }
//             });
//         } else {
//             $('#searchResults').hide();
//         }
//     });

//     // Handle product selection from dropdown
//     $(document).on('click', '.selectProduct', function (e) {
//         e.preventDefault();
//         let productId = $(this).data('id');
//         let productName = $(this).data('name');
//         let productPrice = $(this).data('price');

//         // Hide search results
//         $('#searchResults').hide();

//         // Add product as a new row
//         addProductRow(productId, productName, productPrice);
//     });

//     // Function to add a new row to the purchase table
//     function addProductRow(productId, productName, productPrice) {
//         let newRow = `
//             <tr>
//                 <td>
//                     <input type="hidden" name="products[]" value="${productId}">
//                     ${productName}
//                 </td>
//                 <td><input type="number" class="form-control quantity" name="quantities[]" value="1" min="1" required></td>
//                 <td><input type="number" class="form-control price" name="prices[]" value="${productPrice}" step="0.01"></td>
//                 <td><input type="number" class="form-control discount" name="discounts[]" value="0" min="0"></td>
//                 <td><input type="number" class="form-control tax" name="taxes[]" value="0" min="0"></td>
//                 <td><input type="text" class="form-control total" name="totals[]" readonly></td>
//                 <td><button type="button" class="btn btn-danger removeRow">X</button></td>
//             </tr>
//         `;

//         $('#purchaseItems').append(newRow);
//     }

//     // Remove row
//     $(document).on('click', '.removeRow', function () {
//         $(this).closest('tr').remove();
//     });
// });




























// $(document).ready(function () {
//     $('#searchPurchaseProduct').on('keyup', function () {
//         let query = $(this).val();
//         let searchBox = $(this);

//         if (query.length > 2) {
//             $.ajax({
//                 url: `${location.origin}/search/products`,
//                 type: 'GET',
//                 data: { search: query },
//                 success: function (data) {
//                     let resultsDropdown = $('#searchResults');
//                     resultsDropdown.empty();

//                     if (data.length > 0) {
//                         $.each(data, function (index, product) {
//                             resultsDropdown.append(`
//                                 <a href="#" class="dropdown-item selectProduct" 
//                                     data-id="${product.id}" 
//                                     data-name="${product.name}" 
//                                     data-price="${product.price}">
//                                     ${product.name} - $${product.price}
//                                 </a>
//                             `);
//                         });

//                         // Position the dropdown
//                         let offset = searchBox.offset();
//                         resultsDropdown.css({
//                             top: searchBox.outerHeight(),
//                             left: 0,
//                             width: searchBox.outerWidth(),
//                         });

//                         resultsDropdown.show();
//                     } else {
//                         resultsDropdown.hide();
//                     }
//                 }
//             });
//         } else {
//             $('#searchResults').hide();
//         }
//     });

//     $(document).on('click', '.selectProduct', function (e) {
//         e.preventDefault();
//         let productId = $(this).data('id');
//         let productName = $(this).data('name');
//         let productPrice = $(this).data('price');

//         $('#searchResults').hide();
//         addProductRow(productId, productName, productPrice);
//     });

//     function addProductRow(productId, productName, productPrice) {
//         let newRow = `
//             <tr>
//                 <td>
//                     <input type="hidden" name="products[]" value="${productId}">
//                     ${productName}
//                 </td>
//                 <td><input type="number" class="form-control quantity" name="quantities[]" value="1" min="1" required></td>
//                 <td><input type="number" class="form-control price" name="prices[]" value="${productPrice}" step="0.01"></td>
//                 <td><input type="number" class="form-control discount" name="discounts[]" value="0" min="0"></td>
//                 <td><input type="number" class="form-control tax" name="taxes[]" value="0" min="0"></td>
//                 <td><input type="text" class="form-control total" name="totals[]" readonly></td>
//                 <td><button type="button" class="btn btn-danger removeRow">X</button></td>
//             </tr>
//         `;

//         $('#purchaseItems').append(newRow);
//     }
// });
