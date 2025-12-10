<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- Page title will be dynamically inserted using Blade -->
    <title>@yield('title')</title>
    
    <!-- Bootstrap 5 CSS CDN -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <style>
        /* Body styling */
        body {
            background-color: #f5f6fa; /* Light gray background */
            font-family: 'Segoe UI', sans-serif; /* Clean font */
        }

        /* Custom container with top margin */
        .container-custom {
            margin-top: 40px;
        }

        /* Card styling */
        .card {
            border-radius: 10px; /* Rounded corners */
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1); /* Soft shadow */
        }

        /* Search bar layout */
        .search-bar {
            display: flex;
            justify-content: space-between; /* Space between input and button */
            align-items: center; /* Vertical alignment */
            margin-bottom: 20px; /* Space below search bar */
        }

        /* Search input field width */
        .search-bar input {
            width: 250px;
        }

        /* Status badges */
        .badge-active {
            background-color: #28a745; /* Green for active */
        }

        .badge-deleted {
            background-color: #dc3545; /* Red for deleted */
        }

        /* Table header styling */
        table th {
            background-color: #f8f9fa; /* Light gray background */
        }

        /* Button styles */
        .btn-show {
            background-color: #0d6efd; /* Blue */
            color: #fff; /* White text */
        }

        .btn-show:hover {
            background-color: #0b5ed7; /* Darker blue on hover */
        }

        .btn-edit {
            background-color: #ffc107; /* Yellow */
            color: #fff; /* White text */
        }

        .btn-edit:hover {
            background-color: #e0a800; /* Darker yellow on hover */
        }

        .btn-delete {
            background-color: #dc3545; /* Red */
            color: #fff; /* White text */
        }

        .btn-delete:hover {
            background-color: #c82333; /* Darker red on hover */
        }

        /* Pagination alignment */
        .pagination {
            justify-content: center; /* Center the pagination */
        }

        /* Responsive design for smaller screens */
        @media(max-width:768px) {
            .search-bar {
                flex-direction: column; /* Stack elements vertically */
                gap: 10px; /* Space between elements */
                align-items: flex-start; /* Align left */
            }

            .search-bar input {
                width: 100%; /* Full width on mobile */
            }
        }

        /* Pagination custom styling */
        .pagination {
            display: flex;
            gap: 6px; /* Space between page items */
        }

        .page-item .page-link {
            border-radius: 8px !important; /* Rounded corners */
            padding: 8px 14px; /* Padding inside links */
            font-size: 14px; /* Font size */
            border: 1px solid #dee2e6; /* Border color */
            transition: all 0.2s ease-in-out; /* Smooth hover effect */
        }

        .page-item.active .page-link {
            background-color: #0d6efd; /* Active page blue */
            border-color: #0d6efd;
            color: white;
            box-shadow: 0 2px 8px rgba(13,110,253,0.3); /* Shadow for active page */
        }

        .page-item .page-link:hover {
            background-color: #e9f2ff; /* Light blue hover */
            border-color: #0d6efd; /* Border remains blue */
            color: #0d6efd; /* Text color blue */
        }

        .page-item.disabled .page-link {
            background-color: #f2f2f2; /* Gray for disabled */
            color: #b5b5b5;
            pointer-events: none; /* Disable click */
        }
    </style>
</head>

<body>
    <!-- Main container with custom top margin -->
    <div class="container container-custom">
        <!-- Page header will be dynamically inserted using Blade -->
        <h1 class="text-center mb-4">@yield('header')</h1>
        
        <!-- Page content will be dynamically inserted using Blade -->
        @yield('content')
    </div>

    <!-- Bootstrap 5 JS Bundle CDN -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>
