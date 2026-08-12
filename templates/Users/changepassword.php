<div class="container-fluid">
        <div class="row min-vh-100">

            <!-- Mobile Title and Caption -->
            <div class="d-md-none text-center px-4 pt-3 mt-5">
                <h1 class="h4 fw-bold" style="color: #330165;">Welcome to the BUSCED Portal</h1>
                <p class="text-secondary small">Your gateway to excellence in teacher education, innovation, and
                    academic resources.</p>
            </div>


            <!-- Left Branding Panel -->
            <div class="col-md-6 d-none d-md-flex flex-column justify-content-center align-items-center text-white text-center"
                style="background-color: #330165;">
                <div class="px-4">
                    <h1>Welcome to the BUSCED Portal</h1>
                    <p>Your gateway to excellence in teacher education, innovation, and academic resources.</p>

                <?=  $this->Html->image("auth.jpg",['alt'=>SCHOOL,'class'=>'img-fluid mt-3 rounded-2']) ?> 
                </div>
            </div>

            <!-- Right Reset Form Panel -->
            <div class="col-md-6 d-flex align-items-center justify-content-center py-4">
                <div class="login-box p-4 shadow rounded bg-white w-100" style="max-width: 420px;">
                    <div class="text-center mb-4">
                     <?=  $this->Html->image("loginlogo.png",['alt'=>SCHOOL,'style'=>'width: 80px;']) ?>
                        <h5 class="mt-3">Reset Password</h5>
                        <p class="text-muted small">Enter your new password below.</p>
                    </div>
                     <?= $this->Form->create(null) ?> <?= $this->Flash->render() ?>
                    <div>
                        <!-- New Password Field -->
                        <div class="form-group">
									<label>New Password</label>
								<?=$this->Form->control('password',['label'=>false,'class'=>'form-control','required','type'=>'password','placeholder'=>'choose password'])?>  
								</div>

                        <!-- Confirm Password Field -->
                        <div class="form-group">
									<label>Repeat Password</label>
								<?=$this->Form->control('cpassword',['label'=>false,'class'=>'form-control','required','type'=>'password','placeholder'=>'repeat password'])?>  
								</div>


                        <div class="d-grid mb-3">
                            <button type="submit" class="btn btn-primary">Reset Password</button>
                        </div>

                        <div class="text-center">
                           <a href="https://busced.online/users/login" class="text-decoration-none"><i class="bi bi-arrow-left"></i> Back to
                                login</a>
                        </div>
                           <?= $this->Form->end() ?>
                    </div>
                </div>

            </div>
        </div>
                
                 <script>
            function togglePassword(fieldId, toggleBtn) {
                const passwordField = document.getElementById(fieldId);
                const icon = toggleBtn.querySelector('i');

                if (passwordField.type === 'password') {
                    passwordField.type = 'text';
                    icon.classList.remove('bi-eye');
                    icon.classList.add('bi-eye-slash');
                } else {
                    passwordField.type = 'password';
                    icon.classList.remove('bi-eye-slash');
                    icon.classList.add('bi-eye');
                }
            }

            // Password match check (unchanged)
            document.querySelector('form').addEventListener('submit', function (e) {
                const newPassword = document.getElementById('newPassword').value;
                const confirmPassword = document.getElementById('confirmPassword').value;

                if (newPassword !== confirmPassword) {
                    e.preventDefault();
                    alert('Passwords do not match.');
                }
            });
        </script>