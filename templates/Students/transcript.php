<!-- Main Wrapper -->
<style>
    .lback {
  background-image: url("../img/DSC_2176.JPG"); /* The image used */
  background-color: #cccccc; /* Used if the image is unavailable */
  height: 630px; /* You must set a specified height */
  background-position: center; /* Center the image */
  background-repeat: no-repeat; /* Do not repeat the image */
  background-size: cover; /* Resize the background image to cover the entire container */
}
</style>
        <div class="main-wrapper lback">
		
			<div class="account-content">
				
				<div class="container">
				
					<!-- Account Logo -->
					<div class="account-logo" style="margin-bottom: -40px;">
                                             
<a href="/"><?=  $this->Html->image('android-chrome-512x512.png',['alt'=>'CUN']) ?>  </a>
					</div>
					<!-- /Account Logo -->
					<br /><br /><br />
					<div class="account-box">
						<div class="account-wrapper">
							<h3 class="account-title">Transcript Request</h3>
							<p class="account-subtitle">Provide your Registration Number to continue</p>
							
							<!-- Account Form -->
							 <?= $this->Form->create(null) ?>
								<div class="form-group">
									<label>Registration Number</label>
								<?=$this->Form->control('regno',['label'=>false,'class'=>'form-control','required','placeholder'=>'enter your registration  number'])?>  
								</div>
								<div class="form-group text-center">
									<button class="btn btn-primary account-btn" type="submit">Submit</button>
								</div>
								
							<?= $this->Form->end() ?>
							<!-- /Account Form -->
							
						</div>
					</div>
				</div>
			</div>
        </div>
		<!-- /Main Wrapper -->