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
                    <input type="hidden" id="productId" />
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
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                    </tr>
                </tbody>
            </table>
        </main>
    </div>
@endsection

@section('jscript')
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const productForm = document.getElementById('productForm');

            productForm.addEventListener('submit', (event) => {
                event.preventDefault();

                const formData = new FormData(productForm);
                const data = Object.fromEntries(formData);

                console.log(data['productId']);

                const url = data['productId'] ? `product/{id}/update` : '/product/save';
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
                    })
                    .catch(error => {
                        console.error(error);
                    });

            });
        });
    </script>
@endsection
