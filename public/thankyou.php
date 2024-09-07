<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Thank You - Mini Cafe POS</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f8f9fa;
        }
        .thank-you-container {
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            text-align: center;
        }
        .thank-you-card {
            padding: 40px;
            border-radius: 15px;
            background-color: #ffffff;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        }
        .thank-you-card h1 {
            font-size: 2.5rem;
            font-weight: 700;
            color: #28a745;
        }
        .thank-you-card p {
            font-size: 1.2rem;
            margin-bottom: 20px;
            color: #6c757d;
        }
    </style>
</head>
<body>

    <div class="container thank-you-container">
        <div class="thank-you-card">
            <h1>Thank You!</h1>
            <p>Your order has been successfully placed. We appreciate your business.</p>
            <a href="../frontend/" class="btn btn-outline-primary">Back to Orders</a>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>