<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?=PAGE_TITLE ? PAGE_TITLE.' | ' : ''?>BrickMMO Stats</title>
    <link rel="stylesheet" href="https://www.w3schools.com/w3css/4/w3.css">
    
    <style>
        :root {
            --primary-color: #ff6600;
            --primary-dark: #e55a00;
            --primary-light: #ff8533;
            --dark-color: #333333;
            --light-gray: #f8f9fa;
            --border-color: #dee2e6;
            --success-color: #28a745;
            --danger-color: #dc3545;
            --warning-color: #ffc107;
            --info-color: #17a2b8;
        }

        * {
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f5f5f5;
            color: var(--dark-color);
            line-height: 1.6;
        }

        .orange-theme {
            background-color: var(--primary-color) !important;
            color: white !important;
        }

        .orange-text {
            color: var(--primary-color) !important;
        }

        .main-header {
            background: linear-gradient(135deg, var(--primary-color) 0%, var(--primary-dark) 100%);
            color: white;
            padding: 20px 0;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }

        .main-header h1 {
            margin: 0;
            font-size: 28px;
            font-weight: 600;
            text-align: center;
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
        }

        .w3-card {
            box-shadow: 0 2px 8px rgba(0,0,0,0.1) !important;
            border: none !important;
            border-radius: 8px !important;
            overflow: hidden;
        }

        .w3-button {
            border-radius: 6px !important;
            font-weight: 500 !important;
            transition: all 0.3s ease !important;
            border: none !important;
        }

        .w3-button:hover {
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(0,0,0,0.15);
        }

        .w3-input {
            border-radius: 6px !important;
            border: 2px solid var(--border-color) !important;
            padding: 12px !important;
            font-size: 14px !important;
            transition: border-color 0.3s ease !important;
        }

        .w3-input:focus {
            border-color: var(--primary-color) !important;
            outline: none !important;
            box-shadow: 0 0 0 3px rgba(255, 102, 0, 0.1) !important;
        }

        .w3-select {
            border-radius: 6px !important;
            border: 2px solid var(--border-color) !important;
            padding: 12px !important;
        }

        .w3-table {
            border: none !important;
        }

        .w3-table th {
            background-color: var(--dark-color) !important;
            color: white !important;
            font-weight: 600 !important;
            padding: 15px 12px !important;
            border: none !important;
        }

        .w3-table td {
            padding: 12px !important;
            border-bottom: 1px solid var(--border-color) !important;
            border-left: none !important;
            border-right: none !important;
        }

        .w3-table tr:hover {
            background-color: var(--light-gray) !important;
        }

        .w3-bar {
            border-radius: 0 !important;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }

        .w3-bar .w3-bar-item {
            font-weight: 500 !important;
            transition: all 0.3s ease !important;
        }

        .page-title {
            color: var(--dark-color);
            font-size: 32px;
            font-weight: 700;
            margin: 30px 0 20px 0;
            padding-bottom: 10px;
            border-bottom: 3px solid var(--primary-color);
        }

        .stats-card {
            background: white;
            border-radius: 12px;
            padding: 25px;
            text-align: center;
            box-shadow: 0 4px 12px rgba(0,0,0,0.05);
            border: 1px solid var(--border-color);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        .stats-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(0,0,0,0.1);
        }

        .stats-number {
            font-size: 36px;
            font-weight: 700;
            color: var(--primary-color);
            margin: 10px 0;
            display: block;
        }

        .stats-label {
            font-size: 16px;
            font-weight: 600;
            color: var(--dark-color);
            margin: 0;
        }

        .stats-description {
            font-size: 14px;
            color: #6c757d;
            margin: 5px 0 0 0;
        }

        .section-header {
            background: linear-gradient(135deg, var(--primary-color) 0%, var(--primary-dark) 100%);
            color: white;
            padding: 15px 20px;
            margin: 0;
            font-size: 18px;
            font-weight: 600;
        }

        .w3-panel {
            border-radius: 8px !important;
            border: none !important;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1) !important;
        }

        .alert-success {
            background-color: #d4edda !important;
            color: #155724 !important;
            border-left: 4px solid var(--success-color) !important;
        }

        .alert-error {
            background-color: #f8d7da !important;
            color: #721c24 !important;
            border-left: 4px solid var(--danger-color) !important;
        }

        .form-section {
            background: white;
            border-radius: 12px;
            padding: 25px;
            margin: 20px 0;
            box-shadow: 0 2px 8px rgba(0,0,0,0.05);
            border: 1px solid var(--border-color);
        }

        .form-section h3 {
            color: var(--primary-color);
            margin-top: 0;
            font-weight: 600;
            font-size: 20px;
        }

        @media (max-width: 768px) {
            .container {
                padding: 15px;
            }
            
            .page-title {
                font-size: 24px;
            }
            
            .main-header h1 {
                font-size: 22px;
            }
            
            .stats-number {
                font-size: 28px;
            }
        }
    </style>
</head>
<body>
    <div class="main-header">
        <h1>BrickMMO Stats</h1>
    </div>

    <div class="container">
    