<?php
$userdata = $this->request->getSession()->read('usersinfo');
$userrole = $this->request->getSession()->read('usersroles');
?>
  <div class="content container-fluid">
    <!-- Page Header -->
     <div class="page-header">
            <div class="row">
                <div class="col-sm-12">
                    <h3 class="page-title">Topic Contents - <?= $subject->name ?></h3>
                    <ul class="breadcrumb">
                        <li class="breadcrumb-item"><?= $this->Html->link(' Dashboard', ['controller' => 'Teachers', 'action' => 'dashboard', $this->GenerateUrl('Teacher dashboard')], ['title' => 'Teacher dashboard'])
                            ?></li>
                        <li class="breadcrumb-item"><?= $this->Html->link('My Subjects', ['controller' => 'Teachers', 'action' => 'assignedcourses', $this->GenerateUrl('My Subjects')], ['title' => 'My Subjects'])
                            ?></li>
                        <li class="breadcrumb-item active">Topic Contents - <?= $subject->name ?></li>
                    </ul>
                </div>
            </div>
        </div>
        <!-- /Page Header -->
    <h1 class="h3 mb-0 text-gray-800">&nbsp;</h1>
  <!--/end d-sm-flex-->
  <div class="row">
  <!-- Pie Chart -->
  <div class="col-xl-4 col-lg-5 col-sm-12 col-md-12 col-xs-12">
    <div class="card shadow mb-4">
      <!-- Card Header - Dropdown -->
      <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
        <h6 class="m-0 font-weight-bold text-primary">Topics - Click on any topic to read</h6>
      </div>
      <!-- Card Body -->
      <div class="card-body">
         <?php foreach ($sub_contents as $topic) { ?>
          <button class="btn btn-info" style="margin: 6px;" onclick="getTopics(<?= $topic->id ?>)"><?= $topic->title ?> </button>
                              <?php } ?>
      </div>
      <!--/end card body-->
    </div>
    <!--/end card-->
  </div>
  <!--/end col-xl-4-->

  <!-- Area Chart -->
  <div class="col-xl-8 col-lg-7">
    <div class="card shadow mb-4">
      <!-- Card Header - Dropdown -->
      <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
        <h6 class="m-0 font-weight-bold text-primary">Topic Details</h6>
      </div>
      <!-- Card Body -->
      <div class="card-body" id="contents">
          

                           

                            Click on any topic to view the contents here


        <hr/>
       
      </div>
    </div>
    <!--/end card-->
  </div>
  <!--/end col-xl-8-->
</div></div>
        
<script>
    function getTopics(id) {
        // alert(id);
        $.ajax({
            url: '/imsced/topics/gettopics/' + id,
            method: 'GET',
            dataType: 'text',
            success: function (response) {
                //  console.log(response); return;
                document.getElementById('contents').innerHTML = "";
                document.getElementById('contents').innerHTML = response;
                //location.href = redirect;
            }
        });
    }
</script>