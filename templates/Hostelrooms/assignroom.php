<?php
$userdata = $this->request->getSession()->read('usersinfo');
?>

<!-- Page Content -->
<div class="content container-fluid">
    <!-- Page Header -->
    <div class="page-header">
        <div class="row">
            <div class="col-sm-12">
                <h3 class="page-title">Assign Students to Hostel Room</h3>
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><?= $this->Html->link(' Dashboard', ['controller' => 'Users', 'action' => 'dashboard', $this->GenerateUrl('Dashboard')], ['title' => 'Dashboard']) ?></li>
                    <li class="breadcrumb-item"><?= $this->Html->link(' Manage Hostel Rooms', ['controller' => 'Hostelrooms', 'action' => 'index'], ['title' => 'manage hostel rooms']) ?></li>
                    <li class="breadcrumb-item active">Assign Students</li>
                </ul>
            </div>
        </div>
    </div>
    <!-- /Page Header -->

    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title mb-0">
                        <i class="fa fa-user-plus text-primary"></i> Room Assignment Form
                    </h4>
                </div>
                <div class="card-body">
                    <?= $this->Form->create(null, ['id' => 'assignRoomForm']) ?>
                    
                    <div class="row">
                        <!-- Hostel Selection -->
                        <div class="col-md-4">
                            <div class="form-group">
                                <label class="font-weight-bold">Select Hostel <span class="text-danger">*</span></label>
                                <?= $this->Form->control('hostel_id', [
                                    'label' => false,
                                    'options' => $hostels, 
                                    'empty' => 'Choose Hostel',
                                    'required' => true,
                                    'class' => 'form-control select2',
                                    'onChange' => 'getrooms(this.value)',
                                    'value' => $selectedHostel ?? null
                                ]) ?>
                            </div>
                        </div>
                        
                        <!-- Room Selection -->
                        <div class="col-md-4">
                            <div class="form-group">
                                <label class="font-weight-bold">Select Room <span class="text-danger">*</span></label>
                                <?= $this->Form->control('hostelroom_id', [
                                    'label' => false,
                                    'options' => $hostelrooms,
                                    'empty' => 'Choose Room',
                                    'required' => true,
                                    'class' => 'form-control select2',
                                    'id' => 'rooms',
                                    'value' => $selectedRoom ? $selectedRoom->id : null
                                ]) ?>
                            </div>
                        </div>
                        
                        <!-- Available Beds Display -->
                        <div class="col-md-4">
                            <div class="form-group">
                                <label class="font-weight-bold">Available Beds</label>
                                <div class="form-control-static">
                                    <span id="availableBeds" class="badge badge-info">-</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <!-- Student Selection -->
                        <div class="col-md-12">
                            <div class="form-group">
                                <label class="font-weight-bold">Select Students <span class="text-danger">*</span></label>
                                <small class="form-text text-muted">You can select multiple students. Only students without rooms and who have paid ICT fees are shown.</small>
                                <?= $this->Form->control('student_ids', [
                                    'label' => false,
                                    'options' => $studentsList,
                                    'multiple' => true,
                                    'required' => true,
                                    'class' => 'form-control select2_multiple',
                                    'size' => 10
                                ]) ?>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Room Information Display -->
                    <div class="row mt-3" id="roomInfo" style="display: none;">
                        <div class="col-md-12">
                            <div class="alert alert-info">
                                <h6><i class="fa fa-info-circle"></i> Room Information</h6>
                                <div id="roomDetails"></div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Selected Students Summary -->
                    <div class="row mt-3" id="selectedStudentsInfo" style="display: none;">
                        <div class="col-md-12">
                            <div class="alert alert-success">
                                <h6><i class="fa fa-users"></i> Selected Students Summary</h6>
                                <div id="studentsSummary"></div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row mt-4">
                        <div class="col-md-12">
                            <div class="form-group">
                                <?= $this->Form->button('📝 Assign Students to Room', [
                                    'class' => 'btn btn-primary btn-lg btn-block',
                                    'id' => 'submitBtn'
                                ]) ?>
                                
                                <?= $this->Html->link('<i class="fa fa-arrow-left"></i> Back to Rooms', 
                                    ['action' => 'index'], 
                                    ['class' => 'btn btn-secondary btn-lg btn-block mt-2', 'escape' => false]
                                ) ?>
                            </div>
                        </div>
                    </div>
                    
                    <?= $this->Form->end() ?>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    // Initialize Select2
    $('.select2').select2({
        theme: 'bootstrap4',
        width: '100%'
    });
    
    $('.select2_multiple').select2({
        theme: 'bootstrap4',
        width: '100%',
        placeholder: 'Select students...'
    });
    
    // Load room details if room is pre-selected
    <?php if ($selectedRoom): ?>
    console.log('Pre-selected room ID:', <?= $selectedRoom->id ?>);
    console.log('Selected room data:', <?= json_encode($selectedRoom) ?>);
    setTimeout(function() {
        getRoomDetails(<?= $selectedRoom->id ?>);
    }, 100);
    <?php endif; ?>
    
    // Handle room selection change
    $('#rooms').on('change', function() {
        var roomId = $(this).val();
        if (roomId) {
            getRoomDetails(roomId);
        } else {
            $('#roomInfo').hide();
            $('#availableBeds').text('-');
        }
    });
    
    // Handle student selection change
    $('select[name="student_ids[]"]').on('change', function() {
        updateStudentsSummary();
    });
    
    // Form validation
    $('#assignRoomForm').on('submit', function(e) {
        var roomId = $('select[name="hostelroom_id"]').val();
        var studentIds = $('select[name="student_ids[]"]').val();
        
        if (!roomId) {
            e.preventDefault();
            alert('Please select a room.');
            return false;
        }
        
        if (!studentIds || studentIds.length === 0) {
            e.preventDefault();
            alert('Please select at least one student.');
            return false;
        }
        
        // Check if selected students exceed available beds
        var availableBeds = parseInt($('#availableBeds').text());
        if (studentIds.length > availableBeds) {
            e.preventDefault();
            alert('Cannot assign ' + studentIds.length + ' students. Only ' + availableBeds + ' beds available in this room.');
            return false;
        }
        
        // Show confirmation
        if (!confirm('Are you sure you want to assign ' + studentIds.length + ' student(s) to this room?')) {
            e.preventDefault();
            return false;
        }
        
        // Disable submit button to prevent double submission
        $('#submitBtn').prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> Assigning...');
    });
});

function getrooms(hostelId) {
    if (!hostelId) {
        $('#rooms').html('<option value="">Choose Room</option>');
        $('#roomInfo').hide();
        return;
    }
    
    $.ajax({
        url: '<?= $this->Url->build(['controller' => 'Hostelrooms', 'action' => 'getrooms']) ?>/' + hostelId,
        method: 'GET',
        dataType: 'text',
        success: function(response) {
            $('#rooms').html(response);
            $('#roomInfo').hide();
            $('#availableBeds').text('-');
        },
        error: function() {
            alert('Error loading rooms. Please try again.');
        }
    });
}

function getRoomDetails(roomId) {
    console.log('Getting room details for room ID:', roomId);
    $.ajax({
        url: '<?= $this->Url->build(['controller' => 'Hostelrooms', 'action' => 'getroomdetails']) ?>/' + roomId,
        method: 'GET',
        dataType: 'json',
        success: function(data) {
            console.log('Room details received:', data);
            // Handle both direct response and wrapped response
            var roomData = data.response || data;
            var availableBeds = roomData.available_beds - roomData.occupiedbeds;
            $('#availableBeds').text(availableBeds);
            
            var roomDetails = '<strong>Room:</strong> ' + roomData.room_number + 
                            ' | <strong>Floor:</strong> ' + roomData.floor +
                            ' | <strong>Total Beds:</strong> ' + roomData.available_beds +
                            ' | <strong>Occupied:</strong> ' + roomData.occupiedbeds +
                            ' | <strong>Available:</strong> ' + availableBeds;
            
            $('#roomDetails').html(roomDetails);
            $('#roomInfo').show();
        },
        error: function(xhr, status, error) {
            console.log('Error getting room details:', error);
            console.log('Status:', status);
            console.log('Response:', xhr.responseText);
            $('#roomInfo').hide();
            $('#availableBeds').text('-');
        }
    });
}

function updateStudentsSummary() {
    var selectedStudents = $('select[name="student_ids[]"] option:selected');
    if (selectedStudents.length > 0) {
        var summary = '<strong>Selected:</strong> ' + selectedStudents.length + ' student(s)<br>';
        summary += '<strong>Students:</strong> ';
        
        var studentNames = [];
        selectedStudents.each(function() {
            studentNames.push($(this).text());
        });
        
        summary += studentNames.join(', ');
        $('#studentsSummary').html(summary);
        $('#selectedStudentsInfo').show();
    } else {
        $('#selectedStudentsInfo').hide();
    }
}
</script>