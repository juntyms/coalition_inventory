@extends('layout.app')

@section('content')
    <div class="container">
        <main>
            <div class="py-5 text-center">
                <h3>Product</h3>
            </div>
            <div class="col-md-7">
                <form id="productForm">
                    @csrf
                    <input type="hidden" name="productId" id="productId" />
                    <div class="form-group">
                        <label for="name">Product Name</label>
                        <input type="text"name="name" id="name" class="form-control" required />
                    </div>
                    <div class="form-group">
                        <label for="quantity">Quantity in Stock</label>
                        <input type="number" name="quantity" id="quantity" class="form-control" required />
                    </div>
                    <div class="form-group">
                        <label for="price">Price per Item</label>
                        <input type="number" name="price" id="price" class="form-control" required />
                    </div>

                    <button type="submit" class="btn btn-primary mt-3"> Save</button>

                </form>
            </div>
            <hr />
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>Product Name</th>
                        <th>Quantity in Stock</th>
                        <th>Price Per Item</th>
                        <th>Datetime submitted</th>
                        <th>Total Value</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody id="productList">
                    @foreach ($products as $product)
                        <tr data-id="{{ $product['id'] }}">
                            <td>{{ $product['name'] }}</td>
                            <td class="text-center">{{ $product['quantity'] }}</td>
                            <td class="text-end">{{ number_format($product['price'], 2) }}</td>
                            <td>{{ \Carbon\Carbon::parse($product['date_submitted'])->format('M d, Y h:i A') ?? '' }}</td>
                            <td class="text-end">{{ number_format($product['quantity'] * $product['price'], 2) }}</td>
                            <td>
                                <button class="btn btn-info" id="edit-btn" data-id="{{ $product['id'] }}"> Edit</button>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </main>
    </div>
@endsection

@section('jscript')
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const productForm = document.getElementById('productForm');
            const productList = document.getElementById('productList');

            productForm.addEventListener('submit', (event) => {
                event.preventDefault();

                const formData = new FormData(productForm);
                const data = Object.fromEntries(formData);

                //console.log(data['productId']);

                const url = data['productId'] ? `product/${data['productId']}/update` : '/product/save';
                const method = 'POST';

                fetch(url, {
                        method,
                        body: JSON.stringify(data),
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('[name="_token"]').value,
                        }
                    })
                    .then(response => response.json())
                    .then(data => {
                        console.log(data);
                        listProducts(data.products);
                    })
                    .catch(error => {
                        console.error(error);
                    });

            });

            productList.addEventListener('click', (event) => {
                if (event.target.id === 'edit-btn') {
                    const productId = event.target.dataset.id;
                    console.log(productId);
                    fetch(`/product/${productId}/edit`)
                        .then(response => response.json())
                        .then(data => {
                            if (data.product) {
                                document.getElementById('productId').value = data.product.id;
                                document.getElementById('name').value = data.product.name;
                                document.getElementById('quantity').value = data.product.quantity;
                                document.getElementById('price').value = data.product.price;
                            }
                        })
                        .catch(error => console.error('Error:', error));
                }
            });

            function listProducts(products) {
                productList.innerHTML = '';
                products.forEach(product => {
                    const row = `
                        <tr data-id="${product.id}">
                            <td>${product.name}</td>
                            <td>${product.quantity}</td>
                            <td>${product.price}</td>
                            <td>${new Date(product.date_submitted).toLocaleString('en-US', {
                                dateStyle: 'medium',
                                timeStyle: 'short',
                                timezone: 'UTC'
                            })}
                            </td>
                            <td>${product.quantity * product.price}</td>
                            <td><button class="btn btn-info" id="edit-btn" data-id="${product.id}"> Edit</button></td>
                        </tr>
                    `;
                    productList.insertAdjacentHTML('beforeend', row);
                });

            }
        });
    </script>
@endsection
