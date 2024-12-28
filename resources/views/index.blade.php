<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Coalition Laravel Skill Test</title>

    <!-- Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <link rel="stylesheet" href="/css/style.css">
</head>
<body>

    <div class="container mt-5  ">
        <div id="error-alert"></div>
        <h1 class="text-center">Coalition Laravel Skill Test</h1>
        <form id="stock-form" class="row" method="post">
            @csrf
            <div class="col-lg-4 mb-2">
                <div class="input-group">
                    <div class="input-group-prepend">
                        <span class="input-group-text">Product name</span>
                    </div>
                    <input type="text" class="form-control" id="product_name" name="product_name">
                </div>

            </div>
            <div class="col-lg-4 mb-2">
                <div class="input-group">
                    <div class="input-group-prepend">
                        <span class="input-group-text">Quantity in stock</span>
                    </div>
                    <input type="number" class="form-control" id="quantity_in_stock" name="quantity_in_stock">
                </div>

            </div>
            <div class="col-lg-4 mb-2">
                <div class="input-group">
                    <div class="input-group-prepend">
                        <span class="input-group-text">Price per item</span>
                    </div>
                    <input type="number" class="form-control" id="price_per_item" name="price_per_item">
                    <div class="input-group-append">
                        <button class="btn btn-primary">Submit</button>
                    </div>
                </div>
            </div>
        </form>
        <table class="table mt-3">
            <thead>
                <tr>
                    <th>Product name</th>
                    <th>Quantity in stock</th>
                    <th>Price per item</th>
                    <th>Datetime submitted</th>
                    <th>Total value number</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                @php
                $total = 0;
                @endphp
                @foreach ($data ?? [] as $i => $item)
                @php
                $total += $item['quantity_in_stock'] * $item['price_per_item'];
                @endphp
                <tr data-id="{{ $i }}">
                    <form method="get" class="edit-form">
                        <td class="product_name">{{ $item['product_name'] ?? null }}</td>
                        <td class="quantity_in_stock">{{ $item['quantity_in_stock'] ?? null }}</td>
                        <td class="price_per_item">{{ $item['price_per_item'] ?? null }}</td>
                        <td class="datetime_submitted">{{ date('m/d/y h:i A', strtotime($item['datetime_submitted'])) }}</td>
                        <td class="total_value">{{ $item['quantity_in_stock'] * $item['price_per_item'] ?? null }}</td>
                        <td>
                            <button type="button" class="btn btn-primary value" data-id="{{ $i }}" data-bs-toggle="modal" data-bs-target="#editModal"><i class="bi bi-pencil-square"></i></button>
                            <button type="button" class="btn btn-danger" data-id="{{ $i }}" data-bs-toggle="modal" data-bs-target="#deleteModal"><i class="bi bi-trash"></i></button>
                        </td>
                    </form>
                </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr>
                    <td colspan="4" class="text-right">Total:</td>
                    <td colspan="2">{{ $total ?? 0 }}</td>
                </tr>
            </tfoot>
        </table>
    </div>

    <div class="modal fade" id="editModal" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <form method="get" class="edit-form">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="editModalLabel">Edit</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        @csrf
                        <div class="mb-3">
                            <label for="product_name" class="form-label">Product name</label>
                            <input type="text" class="form-control" id="product_name" name="product_name">
                        </div>
                        <div class="mb-3">
                            <label for="quantity_in_stock" class="form-label">Quantity in stock</label>
                            <input type="number" class="form-control" id="quantity_in_stock" name="quantity_in_stock">
                        </div>
                        <div class="mb-3">
                            <label for="price_per_item" class="form-label">Price per item</label>
                            <input type="number" class="form-control" id="price_per_item" name="price_per_item">
                        </div>
                        <input type="hidden" id="id">
                        <button type="submit" class="btn btn-primary">Submit</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="deleteModalLabel">Delete <span id="delete-name"></span></h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p>Are you sure you want to delete this item?</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-danger delete" data-id="">Delete</button>
                </div>
            </div>
        </div>
    </div>


    <script src="https://code.jquery.com/jquery-3.5.1.min.js" integrity="sha256-9/aliU8dGd2tb6OSsuzixeV4y/faTqgFtohetphbbj0=" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
    <script src="/js/script.js"></script>
</body>
</html>
