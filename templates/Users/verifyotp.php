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
               <h1>WELCOME TO LIVING TEMPLE ACADEMY</h1>
                    <p>Every Student, a Unique Treasure</p>
                <?= $this->Html->image("home3.png", ['alt' => SCHOOL, 'class' => 'img-fluid mt-3 rounded-2']) ?>
            </div>
        </div>

        <!-- Right OTP Verification Panel -->
        <div class="col-md-6 d-flex align-items-center justify-content-center py-4">
            <div class="login-box p-4 shadow rounded bg-white w-100" style="max-width: 420px;">
                <div class="text-center mb-4">
                    <a href="/">
                        <?= $this->Html->image("logolta.png", ['alt' => SCHOOL, 'style' => 'width: 80px;']) ?>
                    </a>
                </div>
                <h5 class="text-center mb-3">Verify OTP Code</h5>
                <p class="text-center text-muted small mb-4">
                    We've sent a 6-digit OTP code to <strong><?= h($user->username) ?></strong><br>
                    Please enter the code below to continue.
                </p>

                <?= $this->Form->create(null) ?>
                <div class="mb-3">
                    <div class="form-group">
                        <label>OTP Code</label>
                        <?= $this->Form->control('otp_code', [
                            'label' => false,
                            'class' => 'form-control text-center',
                            'required',
                            'type' => 'text',
                            'placeholder' => 'Enter 6-digit code',
                            'maxlength' => '6',
                            'style' => 'font-size: 18px; letter-spacing: 3px; font-family: monospace;'
                        ]) ?>
                    </div>
                </div>

                <div class="d-grid mb-3">
                    <button type="submit" class="btn btn-primary" style="background-color: rgba(0, 0, 128, 0.9);">
                        Verify OTP
                    </button>
                </div>

                <div class="text-center">
                    <p class="text-muted small">
                        Didn't receive the code? 
                        <a href="<?= $this->Url->build(['action' => 'forgotpassword']) ?>" class="text-decoration-none">
                            Request new code
                        </a>
                    </p>
                    <?= $this->Html->link(' Back to login', ['controller' => 'Users', 'action' => 'login'], ['title' => 'back to login', 'class' => 'text-decoration-none']) ?>
                </div>
                <?= $this->Form->end() ?>
            </div>
        </div>
    </div>
</div>

<script>
// Auto-focus on OTP input
document.addEventListener('DOMContentLoaded', function() {
    const otpInput = document.querySelector('input[name="otp_code"]');
    if (otpInput) {
        otpInput.focus();
    }
});

// Format OTP input (numbers only)
document.querySelector('input[name="otp_code"]').addEventListener('input', function(e) {
    // Remove non-numeric characters
    this.value = this.value.replace(/[^0-9]/g, '');
    
    // Limit to 6 digits
    if (this.value.length > 6) {
        this.value = this.value.slice(0, 6);
    }
});
</script>
