<section class="page-title court-title" style="margin-bottom: -85px;">
            <div class="container">
                <div class="row">
                    <div class="col col-xs-12">
                        <h2>Welcome to the class</h2>
                        <!--<p>Gregor then turned to look out the window at the weather</p>-->
                    </div>
                </div> <!-- end row -->
            </div> <!-- end container -->
        </section>   


<section class="about-section section-padding">
            <div class="container">
                <div class="row">
        <div class="col-md-8">
              <div class="blog-content">
                
                <div class="card-body">
                    <div class="col-12" style="padding-top: 25px;">
         
                <div class="embed-responsive embed-responsive-16by9">
                    <?php if(!empty($liveclass->meetinglink)){  ?>
                         <iframe width="920" height="615" allow="camera; microphone" allowfullscreen="allowfullscreen"
                                 src="<?=$liveclass->meetinglink  ?>">
</iframe>  
         
                    <?php }else{
                        echo "Sorry, this session has either been closed or yet to be started";
                    } ?>
                  
                </div>
     
            </div>
                </div>
            </div>
        </div>
    </div>
            </div>    </section>