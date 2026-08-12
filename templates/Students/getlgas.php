<?php echo $this->Form->control('lga_id', ['label' => 'Local Government Area', 'options' => $lgas,
                                          'class' => 'select2_multiple form-control form-control-user2', 'required','empty'=>'Select LGA','id'=>'lga'])
                                    ?>
