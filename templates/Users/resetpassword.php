<div class="container-fluid">
    <div class="row min-vh-100">

        <!-- Mobile Title and Caption -->
        <div class="d-md-none text-center px-4 pt-3 mt-5">
            <h1 class="h4 fw-bold" style="color: rgba(0, 0, 128, 0.9);">Welcome to the T.S.S. Portal</h1>
            <p class="text-secondary small">Every Student a Unique Treasure to Unearth.</p>
        </div>

        <!-- Left Branding Panel -->
        <div class="col-md-6 d-none d-md-flex flex-column justify-content-center align-items-center text-white text-center"
            style="background-color: rgba(0, 0, 128, 0.9);">
            <div class="px-4">
                <h1>Welcome to the T.S.S. Portal</h1>
                <p>Every Student a Unique Treasure to Unearth.</p>
                <?= $this->Html->image("auth.jpg", ['alt' => SCHOOL, 'class' => 'img-fluid mt-3 rounded-2']) ?>
            </div>
        </div>

        <!-- Right Reset Password Panel -->
        <div class="col-md-6 d-flex align-items-center justify-content-center py-4">
            <div class="login-box p-4 shadow rounded bg-white w-100" style="max-width: 420px;">
                <div class="text-center mb-4">
                    <a href="/">
                        <?= $this->Html->image("loginlogo.png", ['alt' => SCHOOL, 'style' => 'width: 80px;']) ?>
                    </a>
                </div>
                <h5 class="text-center mb-3">Reset Password</h5>
                <p class="text-center text-muted small mb-4">
                    Please enter your new password for <strong><?= h($user->username) ?></strong>
                </p>

                <?= $this->Form->create(null) ?>
                <div class="mb-3">
                    <div class="form-group">
                        <label>New Password</label>
                        <?= $this->Form->control('password', [
                            'label' => false,
                            'class' => 'form-control',
                            'required',
                            'type' => 'password',
                            'placeholder' => 'Enter new password',
                            'id' => 'password'
                        ]) ?>
                    </div>
                </div>

                <div class="mb-3">
                    <div class="form-group">
                        <label>Confirm New Password</label>
                        <?= $this->Form->control('confirm_password', [
                            'label' => false,
                            'class' => 'form-control',
                            'required',
                            'type' => 'password',
                            'placeholder' => 'Confirm new password',
                            'id' => 'confirm_password'
                        ]) ?>
                    </div>
                </div>

                <div class="mb-3">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" id="showPassword">
                        <label class="form-check-label" for="showPassword">
                            Show passwords
                        </label>
                    </div>
                </div>

                <div class="d-grid mb-3">
                    <button type="submit" class="btn btn-primary" style="background-color: rgba(0, 0, 128, 0.9);">
                        Reset Password
                    </button>
                </div>

                <div class="text-center">
                    <?= $this->Html->link(' Back to login', ['controller' => 'Users', 'action' => 'login'], ['title' => 'back to login', 'class' => 'text-decoration-none']) ?>
                </div>
                <?= $this->Form->end() ?>
            </div>
        </div>
    </div>
</div>

<script>
// Show/hide password functionality
document.getElementById('showPassword').addEventListener('change', function() {
    const password = document.getElementById('password');
    const confirmPassword = document.getElementById('confirm_password');
    
    if (this.checked) {
        password.type = 'text';
        confirmPassword.type = 'text';
    } else {
        password.type = 'password';
        confirmPassword.type = 'password';
    }
});

// Password strength indicator (optional enhancement)
document.getElementById('password').addEventListener('input', function() {
    const password = this.value;
    const strength = getPasswordStrength(password);
    
    // You can add visual feedback here if needed
});

function getPasswordStrength(password) {
    let strength = 0;
    if (password.length >= 8) strength++;
    if (/[a-z]/.test(password)) strength++;
    if (/[A-Z]/.test(password)) strength++;
    if (/[0-9]/.test(password)) strength++;
    if (/[^A-Za-z0-9]/.test(password)) strength++;
    return strength;
}
</script>
