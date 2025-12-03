<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title')</title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f5f6fa;
            font-family: 'Segoe UI', sans-serif;
        }

        .container-custom {
            margin-top: 40px;
        }

        .card {
            border-radius: 10px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
        }

        .search-bar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }

        .search-bar input {
            width: 250px;
        }

        .badge-active {
            background-color: #28a745;
        }

        .badge-deleted {
            background-color: #dc3545;
        }

        table th {
            background-color: #f8f9fa;
        }

        .btn-show {
            background-color: #0d6efd;
            color: #fff;
        }

        .btn-show:hover {
            background-color: #0b5ed7;
        }

        .btn-edit {
            background-color: #ffc107;
            color: #fff;
        }

        .btn-edit:hover {
            background-color: #e0a800;
        }

        .btn-delete {
            background-color: #dc3545;
            color: #fff;
        }

        .btn-delete:hover {
            background-color: #c82333;
        }

        .pagination {
            justify-content: center;
        }

        @media(max-width:768px) {
            .search-bar {
                flex-direction: column;
                gap: 10px;
                align-items: flex-start;
            }

            .search-bar input {
                width: 100%;
            }
        }

.pagination {
    display: flex;
    gap: 6px;
}

.page-item .page-link {
    border-radius: 8px !important;
    padding: 8px 14px;
    font-size: 14px;
    border: 1px solid #dee2e6;
    transition: all 0.2s ease-in-out;
}

.page-item.active .page-link {
    background-color: #0d6efd;
    border-color: #0d6efd;
    color: white;
    box-shadow: 0 2px 8px rgba(13,110,253,0.3);
}

.page-item .page-link:hover {
    background-color: #e9f2ff;
    border-color: #0d6efd;
    color: #0d6efd;
}

.page-item.disabled .page-link {
    background-color: #f2f2f2;
    color: #b5b5b5;
    pointer-events: none;
}


    </style>
</head>

<body>
    <div class="container container-custom">
        <h1 class="text-center mb-4">@yield('header')</h1>
        @yield('content')
    </div>

    <!-- Bootstrap 5 JS Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>