<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>TECHZONE Admin Dashboard</title>
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <!-- Custom Bootstrap-like Utilities (No Bootstrap used) -->
    <link rel="stylesheet" href="../../css/admin/bootstrap-shim.css">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    <!-- Custom CSS -->
    <link rel="stylesheet" href="../../css/admin/app.css">
    <link rel="stylesheet" href="../../css/admin/nav.css">
    <link rel="stylesheet" href="../../css/admin/header.css">
    <link rel="stylesheet" href="../../css/admin/product.css">
</head>

<body>
    <div class="admin-layout">
        <!-- Sidebar -->
        <?php include __DIR__ . '/nav.php'; ?>

        <!-- Main Content -->
        <div class="admin-main">
            <!-- Header -->
            <?php include __DIR__ . '/header.php'; ?>

            <!-- Page Content -->
            <div class="admin-content p-4">
                <?php 
                    if (isset($pageContent) && file_exists($pageContent)) {
                        include $pageContent;
                    } else {
                        if (file_exists(__DIR__ . '/../products/index.php')) {
                            include __DIR__ . '/../products/index.php';
                        }
                    }
                ?>
            </div>
            <!-- Footer -->
            <?php include __DIR__ . '/footer.php'; ?>
        </div>
    </div>

   
</body>

</html>