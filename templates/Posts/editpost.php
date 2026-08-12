<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title mb-0">Forum</h4>
            </div>
            <div class="card-body">
                <h4 class="card-title">Add Post</h4>
                <?= $this->Form->create($post) ?>
                <div class="row">
                    <div class="col-md-12">
                        <div class="form-group row">
                            <label class="col-lg-3 col-form-label">Title</label>
                            <div class="col-lg-9">
                                <?= $this->Form->control('title', ['label' => false, 'class' => 'form-control', 'required']) ?>    

                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-lg-3 col-form-label">Details</label>
                            <div class="col-lg-9">
                                <?= $this->Form->control('postdetails', ['label' => false, 'class' => 'form-control', 'required', 'type' => 'textarea', 'class' => 'summernote']) ?>

                            </div>
                        </div>
                     
                        <div class="form-group row">
                            <label class="col-lg-3 col-form-label">Select Category</label>
                            <div class="col-lg-9">
                                <?= $this->Form->control('postcategory_id', ['options' => $postcategories, 'label' => false, 'empty' => 'Select Category', 'class' => 'form-control']) ?>

                            </div>
                        </div>


                    </div>

                </div>

                <div class="text-right">
                    <button type="submit" class="btn btn-primary">Submit</button>
                </div>
                <?= $this->Form->end() ?>
            </div>
        </div>
    </div>
</div>

