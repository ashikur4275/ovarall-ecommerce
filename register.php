<?php
$pageTitle = 'Register';
require_once 'includes/config.php';

// Redirect if already logged in
if (isLoggedIn()) {
    redirect('index.php');
}

// Handle registration
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $phone = trim($_POST['phone']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    
    $errors = [];
    
    if (empty($name)) {
        $errors[] = 'Name is required';
    }
    
    if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = 'Valid email is required';
    }
    
    if (strlen($password) < 6) {
        $errors[] = 'Password must be at least 6 characters';
    }
    
    if ($password !== $confirm_password) {
        $errors[] = 'Passwords do not match';
    }
    
    // Check if email exists
    $db = getDB();
    $stmt = $db->prepare("SELECT id FROM users WHERE email = ?");
    $stmt->execute([$email]);
    if ($stmt->fetch()) {
        $errors[] = 'Email already registered';
    }
    
    if (empty($errors)) {
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
        
        $stmt = $db->prepare("INSERT INTO users (name, email, phone, password) VALUES (?, ?, ?, ?)");
        $stmt->execute([$name, $email, $phone, $hashedPassword]);
        
        setFlash('success', 'Registration successful! Please login.');
        redirect('login.php');
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
                            <h2 class="mb-2">Create Account</h2>
                            <p class="text-muted">Join OVARALL for exclusive deals</p>
                        </div>
                        
                        <?php if (!empty($errors)): ?>
                            <div class="alert alert-danger">
                                <ul class="mb-0">
                                    <?php foreach ($errors as $error): ?>
                                        <li><?php echo $error; ?></li>
                                    <?php endforeach; ?>
                                </ul>
                            </div>
                        <?php endif; ?>
                        
                        <form method="POST" action="">
                            <div class="mb-3">
                                <label class="form-label">Full Name *</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="fas fa-user"></i></span>
                                    <input type="text" name="name" class="form-control" placeholder="John Doe" required>
                                </div>
                            </div>
                            
                            <div class="mb-3">
                                <label class="form-label">Email Address *</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="fas fa-envelope"></i></span>
                                    <input type="email" name="email" class="form-control" placeholder="you@example.com" required>
                                </div>
                            </div>
                            
                            <div class="mb-3">
                                <label class="form-label">Phone Number</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="fas fa-phone"></i></span>
                                    <input type="tel" name="phone" class="form-control" placeholder="01XXXXXXXXX">
                                </div>
                            </div>
                            
                            <div class="mb-3">
                                <label class="form-label">Password *</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="fas fa-lock"></i></span>
                                    <input type="password" name="password" class="form-control" placeholder="••••••••" required minlength="6">
                                </div>
                                <small class="text-muted">Minimum 6 characters</small>
                            </div>
                            
                            <div class="mb-4">
                                <label class="form-label">Confirm Password *</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="fas fa-lock"></i></span>
                                    <input type="password" name="confirm_password" class="form-control" placeholder="••••••••" required>
                                </div>
                            </div>
                            
                            <div class="mb-4">
                                <div class="form-check">
                                    <input type="checkbox" class="form-check-input" id="terms" required>
                                    <label class="form-check-label" for="terms">
                                        I agree to the <a href="#">Terms of Service</a> and <a href="#">Privacy Policy</a>
                                    </label>
                                </div>
                            </div>
                            
                            <button type="submit" class="btn btn-primary-custom w-100 btn-lg">Create Account</button>
                        </form>
                        
                        <div class="text-center mt-4">
                            <p class="text-muted">Already have an account? <a href="login.php" class="text-primary">Sign in</a></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<?php require_once 'includes/footer.php'; ?>
