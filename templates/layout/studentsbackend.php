<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0">
        <meta name="description" content="Imo State Judiciary">
        <meta name="keywords" content="Imo state, Agriculture, University, University of Agriculture, Food, Tertiary Institutions in Imo state">
        <meta name="author" content="Imo state University of Agriculture and Environmental Sciences">
        <meta name="robots" content="noindex, dofollow">
        <?= $this->Html->meta('icon') ?>
        <!-- Favicon -->
        <link rel="shortcut icon" type="image/x-icon" href="../img/favicon.ico">
        <?= $this->Html->charset() ?>
        <title> <?php
            mb_internal_encoding('UTF-8');
            mb_http_output('UTF-8');
            echo (!isset($title)) ? $this->fetch("title") : $title;
            ?> |  <?= SCHOOL ?></title>

        <!-- Favicon -->
        <link rel="shortcut icon" type="image/x-icon" href="../img/favicon.ico">

        <!-- Bootstrap CSS -->
        <?=
        $this->Html->css(['../assets/css/bootstrap.min', '../assets/css/font-awesome.min', '../assets/css/dataTables.bootstrap4.min',
            'select2.min', 'bootstrap-datetimepicker.min', '../assets/plugins/summernote/summernote',
            '../assets/css/line-awesome.min', '../assets/plugins/morris/morris', '../assets/plugins/morris/morris',
            'select2.min', '../assets/css/style', '../assets/css/backend-custom'])
        ?>
        <?= $this->fetch('meta') ?>
        <?= $this->fetch('css') ?>

        <?php
        $user = $this->request->getSession()->read('usersinfo');
        $settings = $this->request->getSession()->read('settings');
        $student = $this->request->getSession()->read('student');
        $is_owing = $this->request->getSession()->read('is_owing');
        ?>
<style>
    @media Print
	{
		.DontPrint
		{
			display:none;
		}
	}
</style>

    </head>

    <body>
        <!-- Main Wrapper -->
        <div class="main-wrapper">

            <!-- Header -->
            <div class="header">

                <!-- Logo -->
                <div class="header-left">
                    <a href="#" class="logo">
                        <?= $this->Html->image($settings->logo, ['alt' => 'UAES','width'=>47]) ?>
                    </a>
                </div>
                <!-- /Logo -->

                <a id="toggle_btn" href="javascript:void(0);">
                    <span class="bar-icon">
                        <span></span>
                        <span></span>
                        <span></span>
                    </span>
                </a>

                <!-- Header Title -->
                <div class="page-title-box">
                    <!-- <h3><?= SCHOOL ?></h3> -->
                    <h3><?= ucfirst($settings->name) . ', ' . ucfirst($settings->address) ?></h3>
                </div>
                <!-- /Header Title -->

                <a id="mobile_btn" class="mobile_btn" href="#sidebar"><i class="fa fa-bars"></i></a>

                <!-- Header Menu -->
                <ul class="nav user-menu">

                    <!-- Search -->
                    <!--					<li class="nav-item">
                                                                    <div class="top-nav-search">
                                                                            <a href="javascript:void(0);" class="responsive-search">
                                                                                    <i class="fa fa-search"></i>
                                                                       </a>
                                                                            <form action="search.html">
                                                                                    <input class="form-control" type="text" placeholder="Search here">
                                                                                    <button class="btn" type="submit"><i class="fa fa-search"></i></button>
                                                                            </form>
                                                                    </div>
                                                            </li>-->
                    <!-- /Search -->

                    <!-- Flag -->
                    <!--					<li class="nav-item dropdown has-arrow flag-nav">
                                                                    <a class="nav-link dropdown-toggle" data-toggle="dropdown" href="#" role="button">
                                                                            <img src="../assets/img/flags/us.png" alt="" height="20"> <span>English</span>
                                                                    </a>
                                                                    <div class="dropdown-menu dropdown-menu-right">
                                                                            <a href="javascript:void(0);" class="dropdown-item">
                                                                                    <img src="../assets/img/flags/us.png" alt="" height="16"> Coming Soon
                                                                            </a>
                                                                            
                                                                    </div>
                                                            </li>-->
                    <!-- /Flag -->

                    <!-- Notifications -->
                    <!--					<li class="nav-item dropdown">
                                                                    <a href="#" class="dropdown-toggle nav-link" data-toggle="dropdown">
                                                                            <i class="fa fa-bell-o"></i> <span class="badge badge-pill">3</span>
                                                                    </a>
                                                                    <div class="dropdown-menu notifications">
                                                                            <div class="topnav-dropdown-header">
                                                                                    <span class="notification-title">Notifications</span>
                                                                                    <a href="javascript:void(0)" class="clear-noti"> Clear All </a>
                                                                            </div>
                                                                            <div class="noti-content">
                                                                                    <ul class="notification-list">
                                                                                            <li class="notification-message">
                                                                                                    <a href="activities.html">
                                                                                                            <div class="media">
                                                                                                                    <span class="avatar">
                                                                                                                            <img alt="" src="assets/img/profiles/avatar-02.jpg">
                                                                                                                    </span>
                                                                                                                    <div class="media-body">
                                                                                                                            <p class="noti-details"><span class="noti-title">John Doe</span> added new task <span class="noti-title">Patient appointment booking</span></p>
                                                                                                                            <p class="noti-time"><span class="notification-time">4 mins ago</span></p>
                                                                                                                    </div>
                                                                                                            </div>
                                                                                                    </a>
                                                                                            </li>
                                                                                            <li class="notification-message">
                                                                                                    <a href="activities.html">
                                                                                                            <div class="media">
                                                                                                                    <span class="avatar">
                                                                                                                            <img alt="" src="../assets/img/profiles/avatar-03.jpg">
                                                                                                                    </span>
                                                                                                                    <div class="media-body">
                                                                                                                            <p class="noti-details"><span class="noti-title">Tarah Shropshire</span> changed the task name <span class="noti-title">Appointment booking with payment gateway</span></p>
                                                                                                                            <p class="noti-time"><span class="notification-time">6 mins ago</span></p>
                                                                                                                    </div>
                                                                                                            </div>
                                                                                                    </a>
                                                                                            </li>
                                                                                            <li class="notification-message">
                                                                                                    <a href="activities.html">
                                                                                                            <div class="media">
                                                                                                                    <span class="avatar">
                                                                                                                            <img alt="" src="../assets/img/profiles/avatar-06.jpg">
                                                                                                                    </span>
                                                                                                                    <div class="media-body">
                                                                                                                            <p class="noti-details"><span class="noti-title">Misty Tison</span> added <span class="noti-title">Domenic Houston</span> and <span class="noti-title">Claire Mapes</span> to project <span class="noti-title">Doctor available module</span></p>
                                                                                                                            <p class="noti-time"><span class="notification-time">8 mins ago</span></p>
                                                                                                                    </div>
                                                                                                            </div>
                                                                                                    </a>
                                                                                            </li>
                                                                                            <li class="notification-message">
                                                                                                    <a href="activities.html">
                                                                                                            <div class="media">
                                                                                                                    <span class="avatar">
                                                                                                                            <img alt="" src="../assets/img/profiles/avatar-17.jpg">
                                                                                                                    </span>
                                                                                                                    <div class="media-body">
                                                                                                                            <p class="noti-details"><span class="noti-title">Rolland Webber</span> completed task <span class="noti-title">Patient and Doctor video conferencing</span></p>
                                                                                                                            <p class="noti-time"><span class="notification-time">12 mins ago</span></p>
                                                                                                                    </div>
                                                                                                            </div>
                                                                                                    </a>
                                                                                            </li>
                                                                                            <li class="notification-message">
                                                                                                    <a href="activities.html">
                                                                                                            <div class="media">
                                                                                                                    <span class="avatar">
                                                                                                                            <img alt="" src="../assets/img/profiles/avatar-13.jpg">
                                                                                                                    </span>
                                                                                                                    <div class="media-body">
                                                                                                                            <p class="noti-details"><span class="noti-title">Bernardo Galaviz</span> added new task <span class="noti-title">Private chat module</span></p>
                                                                                                                            <p class="noti-time"><span class="notification-time">2 days ago</span></p>
                                                                                                                    </div>
                                                                                                            </div>
                                                                                                    </a>
                                                                                            </li>
                                                                                    </ul>
                                                                            </div>
                                                                            <div class="topnav-dropdown-footer">
                                                                                    <a href="activities.html">View all Notifications</a>
                                                                            </div>
                                                                    </div>
                                                            </li>-->
                    <!-- /Notifications -->

                    <!-- Message Notifications -->
                    <!--					<li class="nav-item dropdown">
                                                                    <a href="#" class="dropdown-toggle nav-link" data-toggle="dropdown">
                                                                            <i class="fa fa-comment-o"></i> <span class="badge badge-pill">8</span>
                                                                    </a>
                                                                    <div class="dropdown-menu notifications">
                                                                            <div class="topnav-dropdown-header">
                                                                                    <span class="notification-title">Messages</span>
                                                                                    <a href="javascript:void(0)" class="clear-noti"> Clear All </a>
                                                                            </div>
                                                                            <div class="noti-content">
                                                                                    <ul class="notification-list">
                                                                                            <li class="notification-message">
                                                                                                    <a href="chat.html">
                                                                                                            <div class="list-item">
                                                                                                                    <div class="list-left">
                                                                                                                            <span class="avatar">
                                                                                                                                    <img alt="" src="../assets/img/profiles/avatar-09.jpg">
                                                                                                                            </span>
                                                                                                                    </div>
                                                                                                                    <div class="list-body">
                                                                                                                            <span class="message-author">Richard Miles </span>
                                                                                                                            <span class="message-time">12:28 AM</span>
                                                                                                                            <div class="clearfix"></div>
                                                                                                                            <span class="message-content">Lorem ipsum dolor sit amet, consectetur adipiscing</span>
                                                                                                                    </div>
                                                                                                            </div>
                                                                                                    </a>
                                                                                            </li>
                                                                                            <li class="notification-message">
                                                                                                    <a href="chat.html">
                                                                                                            <div class="list-item">
                                                                                                                    <div class="list-left">
                                                                                                                            <span class="avatar">
                                                                                                                                    <img alt="" src="../assets/img/profiles/avatar-02.jpg">
                                                                                                                            </span>
                                                                                                                    </div>
                                                                                                                    <div class="list-body">
                                                                                                                            <span class="message-author">John Doe</span>
                                                                                                                            <span class="message-time">6 Mar</span>
                                                                                                                            <div class="clearfix"></div>
                                                                                                                            <span class="message-content">Lorem ipsum dolor sit amet, consectetur adipiscing</span>
                                                                                                                    </div>
                                                                                                            </div>
                                                                                                    </a>
                                                                                            </li>
                                                                                            <li class="notification-message">
                                                                                                    <a href="chat.html">
                                                                                                            <div class="list-item">
                                                                                                                    <div class="list-left">
                                                                                                                            <span class="avatar">
                                                                                                                                    <img alt="" src="../assets/img/profiles/avatar-03.jpg">
                                                                                                                            </span>
                                                                                                                    </div>
                                                                                                                    <div class="list-body">
                                                                                                                            <span class="message-author"> Tarah Shropshire </span>
                                                                                                                            <span class="message-time">5 Mar</span>
                                                                                                                            <div class="clearfix"></div>
                                                                                                                            <span class="message-content">Lorem ipsum dolor sit amet, consectetur adipiscing</span>
                                                                                                                    </div>
                                                                                                            </div>
                                                                                                    </a>
                                                                                            </li>
                                                                                            <li class="notification-message">
                                                                                                    <a href="chat.html">
                                                                                                            <div class="list-item">
                                                                                                                    <div class="list-left">
                                                                                                                            <span class="avatar">
                                                                                                                                    <img alt="" src="../assets/img/profiles/avatar-05.jpg">
                                                                                                                            </span>
                                                                                                                    </div>
                                                                                                                    <div class="list-body">
                                                                                                                            <span class="message-author">Mike Litorus</span>
                                                                                                                            <span class="message-time">3 Mar</span>
                                                                                                                            <div class="clearfix"></div>
                                                                                                                            <span class="message-content">Lorem ipsum dolor sit amet, consectetur adipiscing</span>
                                                                                                                    </div>
                                                                                                            </div>
                                                                                                    </a>
                                                                                            </li>
                                                                                            <li class="notification-message">
                                                                                                    <a href="chat.html">
                                                                                                            <div class="list-item">
                                                                                                                    <div class="list-left">
                                                                                                                            <span class="avatar">
                                                                                                                                    <img alt="" src="../assets/img/profiles/avatar-08.jpg">
                                                                                                                            </span>
                                                                                                                    </div>
                                                                                                                    <div class="list-body">
                                                                                                                            <span class="message-author"> Catherine Manseau </span>
                                                                                                                            <span class="message-time">27 Feb</span>
                                                                                                                            <div class="clearfix"></div>
                                                                                                                            <span class="message-content">Lorem ipsum dolor sit amet, consectetur adipiscing</span>
                                                                                                                    </div>
                                                                                                            </div>
                                                                                                    </a>
                                                                                            </li>
                                                                                    </ul>
                                                                            </div>
                                                                            <div class="topnav-dropdown-footer">
                                                                                    <a href="chat.html">View all Messages</a>
                                                                            </div>
                                                                    </div>
                                                            </li>-->
                    <!-- /Message Notifications -->
                    <li class="nav-item dropdown has-arrow main-drop">
                        <a href="#" class="dropdown-toggle nav-link" data-toggle="dropdown">
                            <span class="user-img">
                                <?= $this->html->image('../student_files/' . $student->passporturl, ['alt' => $student->fname]) ?>

                                <span class="status online"></span></span>
                            <span><?= ucfirst($user['lname'] . ' ' . $user['fname']) ?></span>
                        </a>
                        <div class="dropdown-menu">
                            <?= $this->Html->link('My Profile', ['controller' => 'Students', 'action' => 'viewprofile', $this->GenerateUrl('my profile')], ['title' => 'my profile', 'class' => 'dropdown-item'])
                            ?>


                            <?= $this->Html->link('Logout', ['controller' => 'Users', 'action' => 'logout', $user['id']], ['title' => 'logout', 'class' => 'dropdown-item'])
                            ?>

                        </div>
                    </li>
                </ul>
                <!-- /Header Menu -->

                <!-- Mobile Menu -->
                <div class="dropdown mobile-user-menu">
                    <a href="#" class="nav-link dropdown-toggle" data-toggle="dropdown" aria-expanded="false"><i class="fa fa-ellipsis-v"></i></a>
                    <div class="dropdown-menu dropdown-menu-right">
                        <?= $this->Html->link('My Profile', ['controller' => 'Students', 'action' => 'myprofile', $this->GenerateUrl('my profile')], ['title' => 'my profile', 'class' => 'dropdown-item'])
                        ?>

                        <?= $this->Html->link('Logout', ['controller' => 'Users', 'action' => 'logout', $user['id']], ['title' => 'logout', 'class' => 'dropdown-item'])
                        ?>
                    </div>
                </div>
                <!-- /Mobile Menu -->

            </div>
            <!-- /Header -->

            <!-- Sidebar -->
            <div class="sidebar" id="sidebar">
                <div class="sidebar-inner slimscroll">
                    <div id="sidebar-menu" class="sidebar-menu">
                        <ul>
                            <li class="menu-title"> 
                                <!-- <span>Main</span> -->
                            </li>
                            <li>
                                <?= $this->Html->link(
                                    $this->Html->tag('i', '', array('class' => 'la la-dashboard')) . ' <span>Dashboard</span>', 
                                    array('controller' => 'Students', 'action' => 'dashboard'), 
                                    array('escape' => false)
                                ) ?>
                                    </li>

                            <!--							<li class="submenu">
                                                                                            <a href="#"><i class="la la-cube"></i> <span> Apps</span> <span class="menu-arrow"></span></a>
                                                                                            <ul style="display: none;">
                                                                                                    <li><a href="chat.html">Chat</a></li>
                                                                                                    <li class="submenu">
                                                                                                            <a href="#"><span> Calls</span> <span class="menu-arrow"></span></a>
                                                                                                            <ul style="display: none;">
                                                                                                                    <li><a href="voice-call.html">Voice Call</a></li>
                                                                                                                    <li><a href="video-call.html">Video Call</a></li>
                                                                                                                    <li><a href="outgoing-call.html">Outgoing Call</a></li>
                                                                                                                    <li><a href="incoming-call.html">Incoming Call</a></li>
                                                                                                            </ul>
                                                                                                    </li>
                                                                                                    <li><a href="events.html">Calendar</a></li>
                                                                                                    <li><a href="contacts.html">Contacts</a></li>
                                                                                                    <li><a href="inbox.html">Email</a></li>
                                                                                                    <li><a href="file-manager.html">File Manager</a></li>
                                                                                            </ul>
                                                                                    </li>-->
                            <!--							<li class="menu-title"> 
                                                                                            <span>Employees</span>
                                                                                    </li>
                                                                                    <li class="submenu">
                                                                                            <a href="#" class="noti-dot"><i class="la la-user"></i> <span> Employees</span> <span class="menu-arrow"></span></a>
                                                                                            <ul style="display: none;">
                                                                                                    <li><a href="employees.html">All Employees</a></li>
                                                                                                    <li><a href="holidays.html">Holidays</a></li>
                                                                                                    <li><a href="leaves.html">Leaves (Admin) <span class="badge badge-pill bg-primary float-right">1</span></a></li>
                                                                                                    <li><a href="leaves-employee.html">Leaves (Employee)</a></li>
                                                                                                    <li><a href="leave-settings.html">Leave Settings</a></li>
                                                                                                    <li><a href="attendance.html">Attendance (Admin)</a></li>
                                                                                                    <li><a href="attendance-employee.html">Attendance (Employee)</a></li>
                                                                                                    <li><a href="departments.html">Departments</a></li>
                                                                                                    <li><a href="designations.html">Designations</a></li>
                                                                                                    <li><a href="timesheet.html">Timesheet</a></li>
                                                                                                    <li><a href="overtime.html">Overtime</a></li>
                                                                                            </ul>
                                                                                    </li>-->
                            <!--							<li> 
                                                                                            <a href="#"><i class="la la-users"></i> <span>News</span></a>
                                                                                    </li>-->
                            <?php 
                           // if ($user['role_id'] == 2 & empty($is_owing)) 
                             //   {
                                ?>
                            <!-- <li class="submenu">
                                    <a href="#"><i class="la la-newspaper-o"></i> <span> LMS</span> <span class="menu-arrow"></span></a>
                                    <ul style="display: none;">
                               <li><a target="blank" href="https://meet.google.com">Live Class(G-Meet)<span class="badge badge-pill bg-primary float-right">1</span></a></li>
                             <li><a target="blank" href="https://zoom.us">Live Class(Zoom)<span class="badge badge-pill bg-primary float-right">2</span></a></li>
                             
                                        <li><a target="blank" href="https://classroom.google.com">Google Classroom<span class="badge badge-pill bg-primary float-right">3</span></a></li>
                                     
                              <li>
                                        <?= $this->Html->link(__(' Learning Resources'), ['controller' => 'Students', 'action' => 'findresources'], ['title' => 'find resources']) ?>
                                       
                                    </li>
                                    <li>
                                        <?= $this->Html->link(__(' Library Resources'), ['controller' => 'Books', 'action' => 'findbooks'], ['title' => 'find resources']) ?>
                                       
                                    </li>
                                       <li>
                                            <?= $this->Html->link(__(' My Lessons'), ['controller' => 'Students', 'action' => 'mycourses'], ['title' => 'view assigned courese']) ?>

                                        </li>
                                        <li>
                                            <?= $this->Html->link(__(' My Quizzes'), ['controller' => 'Quizzes', 'action' => 'myquizzes'], [ 'title' => 'view assigned quizzes']) ?>

                                        </li>
                                        <li>
                                            <?= $this->Html->link(__(' My Assignments'), ['controller' => 'Results', 'action' => 'myresults'], [ 'title' => 'check assignments']) ?>
                                        </li>
                                        <li>
                                            <?= $this->Html->link(__(' My Exams'), ['controller' => 'Exams', 'action' => 'myexams'], ['title' => 'my exams']) ?>
                                        </li>

                                    </ul>
                                </li> -->
                                <li class="submenu">
                                    <a href="#"><i class="la la-newspaper-o"></i> <span> Academics</span> <span class="menu-arrow"></span></a>
                                    <ul style="display: none;">
                                        <!-- <li>
                                            <?= $this->Html->link(__(' My Time Table'), ['controller' => 'Timetables', 'action' => 'mytimetable'], ['title' => 'view time table']) ?>

                                        </li> -->
                                        <li>
                                            <?= $this->Html->link(__(' My Subjects'), ['controller' => 'Students', 'action' => 'mycourses'], ['title' => 'view assigned subjects']) ?>

                                        </li>
                                        <!-- <li>
                                            <?= $this->Html->link(__(' Course Materials'), ['controller' => 'Students', 'action' => 'coursematerials'], [ 'title' => 'course materials']) ?>

                                        </li> -->
                                        <li>
                                            <?= $this->Html->link(__(' My Results'), ['controller' => 'Results', 'action' => 'myresults'], [ 'title' => 'check results']) ?>
                                        </li>
                                        <li>
                                            <?= $this->Html->link(__(' My Assignments'), ['controller' => 'Assignments', 'action' => 'myassignments'], [ 'title' => 'check assignments']) ?>
                                        </li>

                                    </ul>
                                </li>


                            <?php 
                            
                          //  } 
                            ?>


                            <li class="submenu">
                                <a href="#"><i class="la la-users"></i> <span> My Account</span> <span class="menu-arrow"></span></a>
                                <ul style="display: none;">
                                    <?php //if ($user['role_id'] == 2 && empty($is_owing)) { 
                                        ?>
<!--                                        <li>
                                            <?= $this->Html->link(__(' View Profile'), ['controller' => 'Students', 'action' => 'viewprofile'], ['title' => 'view my profile']) ?>

                                        </li>-->
                                        <li>
                                            <?= $this->Html->link(__(' My Invoices'), ['controller' => 'Students', 'action' => 'myinvoices'], ['title' => 'view my invoices']) ?>

                                        </li>
                                        
                                         <!-- <li>
                                            <?= $this->Html->link(__(' Sponsored fees'), ['controller' => 'Sponsorships', 'action' => 'sponsoredfees'], ['title' => 'view my invoices']) ?>

                                        </li> -->

                                    <?php // } 
                                    ?>
                                </ul>
                            </li>
                            <?php if ($user['role_id'] == 2 & empty($is_owing)) { ?>
<!--                                <li class="submenu">
                                    <a href="#"><i class="la la-users"></i> <span> Messaging</span> <span class="menu-arrow"></span></a>
                                    <ul style="display: none;">
                                        <li>
                                            <?= $this->Html->link(__(' Contact Admin'), ['controller' => 'Studentmessages', 'action' => 'newmessage'], ['title' => 'contact system admin']) ?>

                                        </li>
                                        <li>
                                            <?= $this->Html->link(__(' Contact Lecturer'), ['controller' => 'Students', 'action' => 'contactlecturer'], ['title' => 'contact lecturer']) ?>
                                        </li>


                                    </ul>
                                </li>-->
                            <?php } if ($user['role_id'] == 2) { ?>
                                <!-- <li class="submenu">
                                    <a href="#"><i class="la la-file-o"></i> <span> Course Registration</span> <span class="menu-arrow"></span></a>
                                    <ul style="display: none;">
                                        <li>
                                            <?= $this->Html->link('Register Courses', ['controller' => 'Courseregistrations', 'action' => 'register'], ['title' => 'register courses for the semester']) ?>

                                        </li>
                                        <li>
                                            <?= $this->Html->link('manage Courses', ['controller' => 'Courseregistrations', 'action' => 'registeredcourses'], ['title' => 'my registered courses']) ?>

                                        </li>

                                    </ul>
                                </li> -->
                                <?php
                            }
                            if ($user['role_id'] == 2) {
                                ?>

                                <!-- <li class="submenu">
                                    <a href="#"><i class="la la-dashcube"></i> <span> Admission </span> <span class="menu-arrow"></span></a>
                                    <ul style="display: none;">
                                        <li><?= $this->Html->link('Print Acceptance Letter', ['controller' => 'Students', 'action' => 'getacceptanceletter'], ['title' => 'print acceptance letter']) ?></li>
                                        <li><?= $this->Html->link('Print Admission Letter', ['controller' => 'Students', 'action' => 'getadmissionletter'], ['title' => 'print admission letter']) ?></li>

                                </li>
                            </ul>
                            </li> -->
                            <?php }
                        ?>
                            
                            <!-- <li class="submenu">
                                <a href="#"><i class="la la-users"></i> <span> Forum/Notice</span> <span class="menu-arrow"></span></a>
                                <ul style="display: none;">

                                      <li>
                                            <?= $this->Html->link(__(' Posts'), ['controller' => 'Posts', 'action' => 'index'], ['title' => 'view posts']) ?>

                                        </li>
                                        
                                        <li>
                                            <?= $this->Html->link(__(' Noticess'), ['controller' => 'Notifications', 'action' => 'notices'], ['title' => 'view notoices']) ?>

                                        </li>
                                       
                                </ul>
                            </li> -->
                              <!-- <li class="submenu">
                                                                    <a href="#"><i class="la la-pie-chart"></i> <span>  Polls </span> <span class="menu-arrow"></span></a>
                                                                    <ul style="display: none;">
                                                                        <li><?= $this->Html->link('View Election', ['controller' => 'Students', 'action' => 'viewelections'], ['title' => 'view elections']) ?></li>
                                  </ul>
                                                                </li>
                             <li> 
                                 <a target="blank" href="https://mail.google.com" class="fa fa-envelope-o" title="check my school mail">&nbsp;Check Mail</a>

                        </li> -->

<!--                        <li class="menu-title"> 
                            <span>Pages</span>
                        </li>-->
                        <li class="submenu">
                            <a href="#"><i class="la la-user"></i> <span> Profile </span> <span class="menu-arrow"></span></a>
                            <ul style="display: none;">
                                <li><?= $this->Html->link('My Profile', ['controller' => 'Students', 'action' => 'viewprofile', $this->GenerateUrl('my profile')], ['title' => 'my profile'])
?></li>
                                <li><?= $this->Html->link('Update Profile', ['controller' => 'Students', 'action' => 'updateprofile'], ['title' => 'update profile'])
?></li>
                            </ul>
                        </li>

                        <li>
                            <?= $this->Html->link(
                                $this->Html->tag('i', '', array('class' => 'la la-power-off')) . ' <span>Logout</span>', 
                                array('controller' => 'Users', 'action' => 'logout', $user['id']), 
                                array('escape' => false)
                            ) ?>
                        </li>


                        </ul>
                    </div>
                </div>
            </div>
            <!-- /Sidebar -->

            <!-- Page Wrapper -->
            <div class="page-wrapper">
                <br />
                <?= $this->Flash->render() ?>

<?= $this->fetch('content') ?>			

            </div>
            <!-- /Page Wrapper -->

        </div>
        <!-- /Main Wrapper -->
        <?=
        $this->Html->script(
                ['../assets/js/jquery-3.2.1.min', '../assets/js/popper.min', '../assets/js/bootstrap.min',
                    '../assets/js/jquery.slimscroll.min', 'moment.min', 'bootstrap-datetimepicker.min',
                    'jquery.dataTables.min', 'dataTables.bootstrap4.min', '../assets/plugins/morris/morris.min', '../assets/plugins/summernote/summernote.min',
                    '../assets/plugins/raphael/raphael.min', '../assets/js/chart', '../assets/js/chart',
                    '../assets/js/app','datatables.min',
                    'summernote.init', 'select2.full.min'])
        ?>
<?= $this->fetch('script') ?>
        <script type="text/javascript" src="https://login.remita.net/payment/v1/remita-pay-inline.bundle.js"></script> 

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
            $(document).ready(function () {
                var handleDataTableButtons = function () {
                    if ($("#myTable").length) {
                        $("#myTable").DataTable({
                            dom: "Bfrtip",
                            buttons: [
                                {
                                    extend: "copy",
                                    className: "btn-sm"
                                },
                                {
                                    extend: "csv",
                                    className: "btn-sm"
                                },
                                {
                                    extend: "excel",
                                    className: "btn-sm"
                                },
                                {
                                    extend: "pdfHtml5",
                                    className: "btn-sm"
                                },
                                {
                                    extend: "print",
                                    className: "btn-sm"
                                },
                            ],
                            responsive: true
                        });
                    }
                };

                TableManageButtons = function () {
                    "use strict";
                    return {
                        init: function () {
                            handleDataTableButtons();
                        }
                    };
                }();

                $('#datatable').dataTable();

                $('#datatable-keytable').DataTable({
                    keys: true
                });

                $('#datatable-responsive').DataTable();

                $('#datatable-scroller').DataTable({
                    ajax: "js/datatables/json/scroller-demo.json",
                    deferRender: true,
                    scrollY: 380,
                    scrollCollapse: true,
                    scroller: true
                });

                $('#datatable-fixed-header').DataTable({
                    fixedHeader: true
                });

                var $datatable = $('#datatable-checkbox');

                $datatable.dataTable({
                    'order': [[1, 'asc']],
                    'columnDefs': [
                        {orderable: false, targets: [0]}
                    ]
                });
                $datatable.on('draw.dt', function () {
                    $('input').iCheck({
                        checkboxClass: 'icheckbox_flat-green'
                    });
                });

                TableManageButtons.init();
            });
        </script>

    </body>
</html>