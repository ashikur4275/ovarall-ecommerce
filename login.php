<?php
$pageTitle = 'Login';
require_once 'includes/config.php';

// Redirect if already logged in
if (isLoggedIn()) {
    redirect('index.php');
}

// Handle login
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    
    $db = getDB();
    $stmt = $db->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch();
    
    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['user_name'] = $user['name'];
        $_SESSION['user_email'] = $user['email'];
        $_SESSION['user_phone'] = $user['phone'];
        $_SESSION['user_role'] = $user['role'];
        
        setFlash('success', 'Welcome back, ' . $user['name'] . '!');
        
        // Redirect to intended page or dashboard
        $redirect = isset($_SESSION['redirect_after_login']) ? $_SESSION['redirect_after_login'] : 'index.php';
        unset($_SESSION['redirect_after_login']);
        redirect($redirect);
    } else {
        $error = 'Invalid email or password';
    }
}

require_once 'includes/header.php';
?>

<section class="py-5">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-6 col-lg-5">
                <div class="card shadow-sm border-0">
                    <div class="card-body p-5">
                        <div class="text-center mb-4">
                            <h2 class="mb-2">Welcome Back</h2>
                            <p class="text-muted">Sign in to your OVARALL account</p>
                        </div>
                        
                        <?php if (isset($error)): ?>
                            <div class="alert alert-danger"><?php echo $error; ?></div>
                        <?php endif; ?>
                        
                        <form method="POST" action="">
                            <div class="mb-3">
                                <label class="form-label">Email Address</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="fas fa-envelope"></i></span>
                                    <input type="email" name="email" class="form-control" placeholder="you@example.com" required>
                                </div>
                            </div>
                            
                            <div class="mb-3">
                                <label class="form-label">Password</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="fas fa-lock"></i></span>
                                    <input type="password" name="password" class="form-control" placeholder="••••••••" required>
                                </div>
                            </div>
                            
                            <div class="d-flex justify-content-between align-items-center mb-4">
                                <div class="form-check">
                                    <input type="checkbox" class="form-check-input" id="remember">
                                    <label class="form-check-label" for="remember">Remember me</label>
                                </div>
                                <a href="forgot-password.php" class="text-primary">Forgot password?</a>
                            </div>
                            
                            <button type="submit" class="btn btn-primary-custom w-100 btn-lg">Sign In</button>
                        </form>
                        
                        <div class="text-center mt-4">
                            <p class="text-muted">Don't have an account? <a href="register.php" class="text-primary">Create one</a></p>
                        </div>
                        
                        <hr class="my-4">
                        
                        <div class="text-center">
                            <p class="text-muted mb-3">Or sign in with</p>
                            <div class="d-flex justify-content-center gap-2">
                                <a href="#" class="btn btn-outline-secondary"><i class="fab fa-google"></i></a>
                                <a href="#" class="btn btn-outline-secondary"><i class="fab fa-facebook-f"></i></a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<?php require_once 'includes/footer.php'; ?>
