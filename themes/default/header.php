<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $title ?? 'CRM V1'; ?></title>
    
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
    <script src="https://cdn.tailwindcss.com"></script>

    <style>
        body { font-family: Arial, sans-serif; margin: 0; padding: 0; background-color: #f4f4f4; }
        .container { width: 80%; margin: 20px auto; background-color: #fff; padding: 20px; border-radius: 8px; box-shadow: 0 0 10px rgba(0, 0, 0, 0.1); }
        header { background-color: #333; color: #fff; padding: 10px 0; text-align: center; }
        nav ul { list-style: none; padding: 0; }
        nav ul li { display: inline; margin-right: 20px; }
        nav ul li a { color: #fff; text-decoration: none; }
        footer { text-align: center; padding: 20px; background-color: #333; color: #fff; position: fixed; bottom: 0; width: 100%; }
    </style>
</head>
<body>
    <header>
        <nav>
            <ul>
                <li><a href="/">Home</a></li>
                <li><a href="/admin">Admin</a></li>
                <li><a href="/login">Login</a></li>
            </ul>
        </nav>
    </header>


