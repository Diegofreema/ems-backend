<div class="container">
    
<?php $settings = $this->request->getSession()->read('settings')?>
    <div class="card o-hidden border-0 shadow-lg my-5">
           
        <div class="card-body p-0">
            <!-- Nested Row within Card Body -->
            
            <div class="row">
                <br />
<!--            <div class="col-md-3 d-sm-none d-md-block d-none d-sm-block">
             <?= $this->Html->image($settings->logo,['class'=>'img-fluid px-3 px-sm-4 mt-3 mb-4 float-left','style'=>'width: 190px; width: 190px;'])?>
            </div>     -->
                <!--          <div class="col-lg-5 d-none d-lg-block bg-register-image"></div>-->
                <div class="col-lg-12">
                     <?= $this->Html->image('android-chrome-192x192.png',['class'=>'img-fluid px-3 px-sm-4 mt-3 mb-4 float-left','style'=>'width: 160px; width: 160px;'])?>
           
                    <div class="p-5">
                        <div class="col-auto">
                            
                            <button class="btn btn-info float-right" title="click to check your application status"
                                    data-toggle="modal" data-target="#myModal" >Check Application Status</button>
                 </div>
                          <span class="d-block d-sm-none d-none d-sm-block d-md-none"> <br /> <br />    <br />  <br /> </span>
                      <div class="text-center">
                          
                            <h1 class="h4 text-gray-900 mb-4"><?=SCHOOL ?> <br />Requirements & Guide To The Application Process</h1>
                        </div>
                          
                        <div><br /> <br />
                            Academic Programmes at <?=SCHOOL ?> is broadly divided into three major areas:  <br />
                            <br />
                            <ul><li><?= $this->Html->link(__('Distance Learning Programmes'), ['action' => 'newcdlapplicant'],
        ['title'=>'apply for a distance learning programe']) ?></li>  <br />
                                <li><?= $this->Html->link(__('Undergraduate Programmes'), ['action' => 'newapplicant'], 
    ['title'=>'apply for an undergraduate programe']) ?></li>  <br />
                                <li>Postgraduate Programmes - Coming soon!</li>  <br />
                            </ul>
                             <br />
                            
                            <b>  Undergraduate Programmes:</b><br />
                           Candidates seeking admission into a First Year programme of any degree course 
                            in the Claretian University of Nigeria must be 
                            qualified for admission before the beginning of the session in which they wish 
                            to be admitted.
                            <br /> <br />
                            <b>  General Requirements</b><br /> <br />
                            The minimum age for admission is 16 years. Candidates are admitted into the
                            University degree programmes through a competitive entrance examination 
                            (Unified Tertiary Matriculation Examination -UTME) organized by the 
                            Joint Admissions and Matriculation Board (JAMB).<br /> <br />
Candidate must in addition to having an acceptable score in the Joint Admissions and Matriculation 
Examination, have at least a minimum of 5 credit passes at the SSCE or equivalent obtained at not 
more than 2 sittings in relevant subjects including English and Mathematics.<br /> <br />
The B. Agriculture programme, requires five credit passes in SSCE/WASC/GCE ‘O’ Level which must 
include Chemistry, Biology/ Agricultural Science, Mathematics, Physics and English Language. 
However, every applicant must have attempted Physics and Mathematics at WASC/SSCE/GCE O’ Level. 
<br />The exact requirements of different programmes which may be modified from time to time by the 
university Senate could be obtained from the University Registry as well as JAMB’s information 
publications for potential students.<br /> <br />
<b>Direct Entry</b><br /> <br />
Admission is based on a combination of ‘O’ Level results with the following qualifications: 
 <br />I. OND/ND (Distinction) <br />
ii. HND (Credit) <br />
iii. NCE (Double Major) at Credit Level <br />
iv. OND/ND (Credit) <br />
v. NCE (Double Major/Merit Level) <br />

 <br />Candidates with qualifications as in (i), (ii) and (iii) as listed above may be 
 admitted into the 200 level while those as in (iv) or (v) may be admitted into the 100 level.
 Holders of the ND with a minimum of upper credit passes in addition to O level credit passes are 
 normally admitted into the 200 level of the programme while holders of HND certificates may be 
 admitted into the 300 level except for the Doctor of Veterinary Medicine programme.

The degree duration shown for most programmes is for UTME candidates. 
Candidates with ND and HND or their equivalents spend fewer years in their programmes 
(as stated above), e.g. four or three years, respectively, in the B. Agric. compared to five years 
for UTME candidates.

<br /><br /><b>Distance Learning Programmes:</b><br /><br />
Candidates applying for any certificate course programme MUST have the following:<br /><br />
<li>Last certificate obtained showing a pass in English language (Preferably secondary school certificates)</li><br />
<li>NVQ Level I, II III as needed</li><br />
<li>At least 5years working experiences in the field of study.</li><br />
<li>Certified guarantor/Sponsor’s letter </li> <br />
<li>Access to farmland attested to by traditional ruler (for selected programmes in Agriculture)</li>.<br />

<br />Candidates applying for Diploma programmes in CUN must have the following:<br /><br />
<li>Minimum of WASC/GCE or SSS certificate with a Pass in English Language; NVQ Level III or IV</li>
<li>Three (3) Years working Experiences; Guarantor/Sponsor’s letter</li>
<li>Access to farmland attested to by traditional ruler (for selected programmes in Agriculture).</li>

<br /><br /><?= $this->Html->link(__('Apply Now - Distance Learning'), ['action' => 'newcdlapplicant'],
        ['class' => 'btn btn-primary btn-user btn-block','title'=>'apply for a distance learning programe']) ?>

<br /><br />The CDCLE Admissions Unit (CDCLEadmissisons@claretianuniversity.edu.ng) will be happy 
to provide you with all required information regarding admission to all our programmes<br /><br />

<br /><br /><b>Postgraduate Studies:</b><br /><br />
Admission into postgraduate studies at The Claretian University has not commenced.
Please direct all queries to admissions@claretianuniversity.edu.ng


<br /><br /><b>Application Process Requirements:</b><br /><br />
In order to have a successful application, all candidates are advised to adhere to the below 
guidelines:<br />
1. Have a valid and accessible email address: all communications between the candidate and the 
school is via this email address that is provided at the point of application, therefore, 
candidates are to use their valid email addresses as this also becomes their username for logging into 
the Student Information System if eventually admitted.<br />
2. Pay Application Fee: Your application is not complete until the application fee is paid and all
applications without a completed application fee payment would not be considered
(see below for how to pay fees)<br />
3. Upload all relevant results: all relevant results must be uploaded: O' Level,
JAMB and any other result you wish to present during the physical screening. 
All document to be uploaded must be less than 1mb in size.<br />
4. Accurately fill the application form, upload your passport and cross-check your data before you hit the submit button.<br />
5. Be ready to pay the application fee online using an ATM card<br /><br />

    
<b>How To Pay Application Fee</b><br /><br />
When you submit your application form, the system generates your application fee payment invoice.<br />
On the invoice, you will see your personal and application details.<br />
On the lower part of the invoice page you will see a green and a yellow payment 
buttons(Paystack and Interswitch respectively), 
when you click on either of this buttons, it'll take you to the payment gateway<br />
Enter your ATM card details and make payment, after a successful payment, 
you will be redirected back to the site.<br />
Print the application form payment receipt and bring it along with you whenever
you are coming for the post UTME/physical screening<br />
<br /><br />

If you have previously submitted your application but could not make payment, 
please <?=
$this->Html->link(__('CLICK HERE'), ['controller' => 'Students', 'action' => 'getincompleteapplicant'],
        [ 'title' => 'complete pending application'])
?> and provide your application number to complete your application.
<br /><br />

                            <br />For Further enquiries call/WhatsApp: 07036614567 or send a mail to info@claretianuniversity.edu.ng<br />
                        </div>  
                       
                        
 <br /> <br />
 
 <?= $this->Html->link(__('Apply Now - Undergraduate'), ['action' => 'newapplicant'], 
    ['class' => 'btn btn-primary btn-user btn-block','title'=>'apply for an undergraduate programe']) ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
     </div>

  <!-- The Modal -->
<div class="modal fade" id="myModal">
  <div class="modal-dialog">
    <div class="modal-content">

      <!-- Modal Header -->
      <div class="modal-header bg bg-info">
          <h4 class="modal-title" style="color: white; align-self: center">Check Your Application Status</h4>
        <button type="button" class="close" data-dismiss="modal">&times;</button>
      </div>

      <!-- Modal body -->
      <div class="modal-body">
         <?= $this->Form->create(null,['url'=>['controller'=>'Students','action'=>'checkstatus'],'id'=>'statuscheck']) ?>
          <div class="col-sm-12 mb-3 mb-sm-0">
<?= $this->Form->control('application_no', ['label' => false, 'placeholder' => 'Enter your application Number',
      'class' => 'form-control form-control-user2', 'required','id'=>'application_id'])
?>
                                </div>
          
          <div class="col-sm-12 mb-3 mb-sm-0" id="res">
              
          </div>
          
          <br /> <br />
          <?= $this->Form->button('Check Status', ['class' => 'btn btn-primary btn-sm','onClick'=>'submitCheckForm()']) ?>
<?= $this->Form->end() ?>
      </div>

      <!-- Modal footer -->
      <div class="modal-footer">
        <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
      </div>

    </div>
  </div>
</div>
   
    
    
    <script language="javascript" type="text/javascript">
    function submitCheckForm() {
       var application_no = document.getElementById('application_id').value;
      // alert(application_no);
   
     $.ajax({
        url: '../Students/checkstatus/'+application_no,
        method: 'GET',
        dataType: 'text',
        success: function(response) {
            console.log(response);
            document.getElementById('res').innerHTML = "";
            document.getElementById('res').innerHTML = response;
            //location.href = redirect;
            
        }
    });   
    event.preventDefault();
    }
</script>




