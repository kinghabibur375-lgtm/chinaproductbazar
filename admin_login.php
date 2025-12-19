<?php
session_start();
require 'config.php';

$msg = "";

if (isset($_POST['login'])) {
    $username = trim($_POST['username']);
    $password = $_POST['password'];

    if (!empty($username) && !empty($password)) {
        $stmt = $pdo->prepare("SELECT * FROM admins WHERE username = ?");
        $stmt->execute([$username]);
        $user = $stmt->fetch();

        // Verify Password
        if ($user && (password_verify($password, $user['password']) || $password === $user['password'])) {
            $_SESSION['admin_logged_in'] = true;
            $_SESSION['admin_id'] = $user['id'];
            $_SESSION['admin_username'] = $user['username'];
            
            // --- FIX: Redirect to Admin Panel, NOT Index ---
            header("Location: admin_panel.php");
            exit;
        } else {
            $msg = "Incorrect Username or Password";
        }
    } else {
        $msg = "Please enter all fields";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Portal | Login</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        /* STANDALONE STYLES - Won't break if main CSS is messy */
        * { margin: 0; padding: 0; box-sizing: border-box; font-family: 'Poppins', sans-serif; }
        
        body {
            height: 100vh;
            width: 100%;
            display: flex;
            justify-content: center;
            align-items: center;
            /* Cool Animated Gradient */
            background: linear-gradient(135deg, #0f0c29, #302b63, #24243e);
            background-size: 400% 400%;
            animation: gradientBG 15s ease infinite;
            overflow: hidden;
            color: white;
        }

        @keyframes gradientBG {
            0% { background-position: 0% 50%; }
            50% { background-position: 100% 50%; }
            100% { background-position: 0% 50%; }
        }

        /* Floating Shapes for Glass Effect */
        .shape {
            position: absolute;
            border-radius: 50%;
            filter: blur(80px);
            z-index: 1;
        }
        .shape-1 { top: -100px; left: -100px; width: 300px; height: 300px; background: #00e5ff; opacity: 0.6; }
        .shape-2 { bottom: -100px; right: -100px; width: 350px; height: 350px; background: #ff0055; opacity: 0.5; }

        /* THE GLASS CARD */
        .login-card {
            position: relative;
            z-index: 10;
            width: 400px;
            padding: 50px 40px;
            background: rgba(255, 255, 255, 0.05); /* See-through white */
            backdrop-filter: blur(15px);            /* The Frost Effect */
            -webkit-backdrop-filter: blur(15px);
            border-radius: 20px;
            border: 1px solid rgba(255, 255, 255, 0.1);
            box-shadow: 0 25px 50px rgba(0, 0, 0, 0.5);
            text-align: center;
        }

        .login-card h2 {
            margin-bottom: 30px;
            font-size: 2rem;
            letter-spacing: 1px;
            text-transform: uppercase;
            font-weight: 700;
        }

        .input-group { position: relative; margin-bottom: 25px; }

        .input-group i {
            position: absolute;
            left: 15px; top: 50%;
            transform: translateY(-50%);
            color: #fff;
            font-size: 1.1rem;
        }

        .input-group input {
            width: 100%;
            padding: 15px 15px 15px 45px;
            background: rgba(255, 255, 255, 0.1);
            border: 1px solid rgba(255, 255, 255, 0.1);
            outline: none;
            border-radius: 30px;
            color: #fff;
            font-size: 1rem;
            transition: 0.3s;
        }

        .input-group input::placeholder { color: rgba(255, 255, 255, 0.6); }

        .input-group input:focus {
            background: rgba(255, 255, 255, 0.2);
            box-shadow: 0 0 15px rgba(0, 229, 255, 0.3);
            border-color: #00e5ff;
        }

        .btn-login {
            width: 100%;
            padding: 15px;
            background: linear-gradient(90deg, #00e5ff, #0099ff);
            border: none;
            border-radius: 30px;
            color: #fff;
            font-size: 1.1rem;
            font-weight: 600;
            cursor: pointer;
            transition: 0.3s;
            text-transform: uppercase;
            letter-spacing: 1px;
            box-shadow: 0 5px 15px rgba(0, 229, 255, 0.4);
        }

        .btn-login:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(0, 229, 255, 0.6);
        }

        .error-msg {
            background: rgba(255, 23, 68, 0.2);
            color: #ff4081;
            padding: 12px;
            border-radius: 8px;
            margin-bottom: 20px;
            font-size: 0.9rem;
            border: 1px solid rgba(255, 23, 68, 0.4);
        }

        .back-link {
            margin-top: 25px;
            display: block;
            color: rgba(255, 255, 255, 0.5);
            text-decoration: none;
            font-size: 0.9rem;
            transition: 0.3s;
        }
        .back-link:hover { color: #fff; }
    </style>
</head>
<body>

    <div class="shape shape-1"></div>
    <div class="shape shape-2"></div>

    <div class="login-card">
        <h2>Admin Panel</h2>
        
        <?php if($msg): ?>
            <div class="error-msg"><i class="fa-solid fa-triangle-exclamation"></i> <?= $msg ?></div>
        <?php endif; ?>

        <form method="POST">
            <div class="input-group">
                <i class="fa-solid fa-user"></i>
                <input type="text" name="username" placeholder="Username" required autocomplete="off">
            </div>
            
            <div class="input-group">
                <i class="fa-solid fa-lock"></i>
                <input type="password" name="password" placeholder="Password" required>
            </div>

            <button type="submit" name="login" class="btn-login">Login</button>
        </form>

        <a href="index.php" class="back-link">Return to Website</a>
    </div>

</body>
</html>