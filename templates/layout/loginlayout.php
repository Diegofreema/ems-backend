<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0">
        <meta name="description" content="<?= SCHOOL ?>">
        <meta name="keywords" content="BENJAMIN UWAJUMOGU STATE COLLEGE OF EDUCATION, IHITTE/UBOMA, BUSCED">
        <meta name="author" content="<?= SCHOOL ?>">
        <meta name="robots" content="noindex, dofollow">
        
        <?= $this->Html->meta('icon') ?>
        <?= $this->Html->charset() ?>
        
        <title>
            <?php 
            mb_internal_encoding('UTF-8');
            mb_http_output('UTF-8');
            echo (!isset($title)) ? $this->fetch("title") : $title;
            ?> | <?= SCHOOL ?>
        </title>
        
        <!-- Bootstrap CSS & Icons -->
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" />
        <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet" />
        
        <!-- Bootstrap CSS -->
        <?= $this->Html->css(['tcss']) ?>
        
        <?= $this->fetch('meta') ?>
        <?= $this->fetch('css') ?>
    </head>
    
    <body class="account-page">
        <!-- Main Wrapper -->
        <div class="main-wrapper">
            <?= $this->Flash->render() ?>
            <?= $this->fetch('content') ?>
            
            <!-- <div class="col-md-12">
                <center> 
                    Powered By <a target="_blank" title="Netpro international Limited" href="https://www.netpro.africa">
                        Netpro International Limited
                    </a> 
                </center>
            </div> -->
        </div>
        <!-- /Main Wrapper -->
        
        <!-- jQuery -->
        <?= $this->Html->script([
            '../assets/js/jquery-3.2.1.min',
            '../assets/js/popper.min',
            '../assets/js/bootstrap.min',
            '../assets/js/app',
            'select2.full.min',
            'bootstrap-datepicker.min'
        ]) ?>
        
        <?= $this->fetch('script') ?>
        
        <script>
            $(document).ready(function () {
                $(".select2_single").select2({
                    placeholder: "Select One",
                    allowClear: true
                });
                
                $(".select2_group").select2({});
                
                $(".select2_multiple").select2({
                    // maximumSelectionLength: 14,
                    // placeholder: "With Max Selection limit 14",
                    allowClear: true
                });
            }); 
            
            //Date picker
            $('#datepicker').datepicker({
                autoclose: true
            });
        </script>
        
        <script>                
            $(window).on('load', function(){
                var delayMs = 1500; // delay in milliseconds
                
                setTimeout(function(){
                    $('#myModal1').modal('show');
                }, delayMs);
            });    
        </script>
        
        <div class="modal fade" id="m-Modal1">
            <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
                <div class="modal-content">
                    <div class="modal-header" style="background-color: #0080B9">
                        <h4 class="modal-title">
                            <b>ATTENTION ALL RETURNING STUDENTS</b>
                        </h4>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">×</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        If you are owing fees(any fee at all) from previous sessions, kindly pay up all outstanding fees
                        before paying any fee for the new session.<br /><br /><br />
                        Signed,<br /> 
                        Management.
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-primary mx-auto" data-dismiss="modal">Close</button>
                    </div>
                </div>
            </div>
        </div>         
    </body>
</html>