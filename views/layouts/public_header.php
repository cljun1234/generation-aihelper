<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AI Tools</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f8f9fa;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        .sidebar {
            width: 280px;
            background-color: #ffffff;
            border-right: 1px solid #e9ecef;
            height: 100vh;
            position: fixed;
            top: 0;
            left: 0;
            padding: 20px;
            overflow-y: auto;
            z-index: 1000;
        }
        .main-content {
            margin-left: 280px;
            padding: 30px;
        }
        .category-link {
            display: block;
            padding: 10px 15px;
            color: #495057;
            text-decoration: none;
            border-radius: 8px;
            margin-bottom: 5px;
            transition: background 0.2s;
        }
        .category-link:hover, .category-link.active {
            background-color: #e9ecef;
            color: #000;
            font-weight: 600;
        }
        .category-link i {
            width: 24px;
            color: #6c757d;
        }
        .tool-card {
            background: #fff;
            border-radius: 12px;
            border: 1px solid #e9ecef;
            transition: all 0.3s ease;
            cursor: pointer;
            height: 100%;
        }
        .tool-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 20px rgba(0,0,0,0.05);
            border-color: #dee2e6;
        }
        .tool-icon-wrapper {
            width: 60px;
            height: 60px;
            background: #f8f9fa;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 28px;
            color: #333;
            margin-bottom: 20px;
        }
        .search-bar {
            background: #fff;
            border: 1px solid #e9ecef;
            border-radius: 8px;
            padding: 10px 15px;
            width: 300px;
        }
    </style>
</head>
<body>
