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
                    <?=  $this->Html->image("home3.png",['alt'=>SCHOOL,'class'=>'img-fluid mt-3 rounded-2']) ?> 
                </div>
            </div>

            <!-- Right Forgot Password Panel -->
            <div class="col-md-6 d-flex align-items-center justify-content-center py-4">
                <div class="login-box p-4 shadow rounded bg-white w-100" style="max-width: 420px;">
                    <div class="text-center mb-4">
                        <a href="/">  
                            <?=  $this->Html->image("logolta.png",['alt'=>SCHOOL,'style'=>'width: 80px;']) ?>  
                        </a>
                    </div>
                    <h5 class="text-center mb-3">Forgot Password</h5>
                    <p class="text-center text-muted small mb-4">Enter your email address and we'll send you a 6-digit OTP code to reset your password.</p>

                    <?= $this->Form->create(null) ?> <?= $this->Flash->render() ?>
                    <div>
                        <div class="form-group">
                            <label>Email Address</label>
                            <?= $this->Form->control('username',['label'=>false,'class'=>'form-control','required','type'=>'email','placeholder'=>'email address']) ?>
                        </div>
                        <div> &nbsp; </div>

                        <div class="d-grid">
                            <button type="submit" class="btn btn-primary" style="background-color: rgba(0, 0, 128, 0.9);">Send OTP Code</button>
                        </div>

                        <div class="text-center mt-3">
                            <?= $this->Html->link(' Back to login', ['controller' => 'Users', 'action' => 'login'], ['title' => 'back to login', 'class' => 'text-decoration-none']) ?>
                        </div>
                    </div>
                    <?= $this->Form->end() ?>
                </div>
            </div>

        </div>
    </div>