<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0">
        <meta name="description" content="<?= SCHOOL ?>">
        <meta name="keywords" content="LIVING TEMPLE ACADEMY, NEW OWERRI">
        <meta name="author" content="<?= SCHOOL ?>">
        <meta name="robots" content="noindex, dofollow">
        
        <?= $this->Html->meta('icon') ?>
        
        <!-- Favicon -->
        <link rel="shortcut icon" type="image/x-icon" href="../img/favicon.ico">
        
        <?= $this->Html->charset() ?>
        
        <title>
            <?php
            mb_internal_encoding('UTF-8');
            mb_http_output('UTF-8');
            echo (!isset($title)) ? $this->fetch("title") : $title;
            ?> | <?= SCHOOL ?>
        </title>

        <!-- Favicon -->
        <link rel="shortcut icon" type="image/x-icon" href="../img/favicon.ico">
        
<link href="https://fonts.googleapis.com/css2?family=Caprasimo&display=swap" rel="stylesheet">
        
        <!-- Bootstrap CSS -->
        <?= $this->Html->css([
            '../assets/css/bootstrap.min', 
            '../assets/css/font-awesome.min', 
            'dataTables.bootstrap4.min',
            'select2.min', 
            'bootstrap-datetimepicker.min', 
            '../assets/plugins/summernote/summernote',
            '../assets/css/line-awesome.min', 
            '../assets/plugins/morris/morris',
            'select2.min', 
            '../assets/css/style', 
            'datatables.min', 
            'buttons.bootstrap4.min',
            '../assets/css/teacher-custom'
        ]) ?>

        <?= $this->fetch('meta') ?>
        <?= $this->fetch('css') ?>

        <?php
        $admin = $this->request->getSession()->read('admin');
        // debug(json_encode($admin, JSON_PRETTY_PRINT)); exit;
        // $privileg_ids = [];
        // foreach ($admin->privileges as $privilege) {
        //     array_push($privileg_ids, $privilege->id);
        // }
        $user = $this->request->getSession()->read('usersinfo');
        $settings = $this->request->getSession()->read('settings');
        ?>
        
        <style>
            @media Print {
                .DontPrint {
                    display: none;
                }
                .page-wrapper {
                    margin: 0 !important;
                }
            }
        </style>
    </head>

    <body>
        <!-- Main Wrapper -->
        <div class="main-wrapper">
            <!-- Header -->
            <div class="header DontPrint">
                <!-- Logo -->
                <div class="header-left">
                    <a href="#" class="logo">
                        <?= $this->Html->image($settings->logo, ['alt' => 'BUSCED', 'width' => 47]) ?>
                        <!-- <img src="../assets/img/favicon-32x32.png" width="40" height="40" alt=""> -->
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

                <a id="mobile_btn" class="mobile_btn" href="#sidebar">
                    <i class="fa fa-bars"></i>
                </a>

                <!-- Header Menu -->
                <ul class="nav user-menu DontPrint">
                    <!-- Search -->
                    <!-- <li class="nav-item">
                                                                    <div class="top-nav-search">
                                                                            <a href="javascript:void(0);" class="responsive-search">
                                                                                    <i class="fa fa-search"></i>
                                                                       </a>
                                                                            <form action="search.html">
                                                                                    <input class="form-control" type="text" placeholder="Search here">
                                <button class="btn" type="submit">
                                    <i class="fa fa-search"></i>
                                </button>
                                                                            </form>
                                                                    </div>
                    </li> -->
                    <!-- /Search -->

                    <!-- Flag -->
                    <!-- <li class="nav-item dropdown has-arrow flag-nav">
                                                                    <a class="nav-link dropdown-toggle" data-toggle="dropdown" href="#" role="button">
                                                                            <img src="../assets/img/flags/us.png" alt="" height="20"> <span>English</span>
                                                                    </a>
                                                                    <div class="dropdown-menu dropdown-menu-right">
                                                                            <a href="javascript:void(0);" class="dropdown-item">
                                                                                    <img src="../assets/img/flags/us.png" alt="" height="16"> Coming Soon
                                                                            </a>
                                                                    </div>
                    </li> -->
                    <!-- /Flag -->

                    <!-- Notifications -->
                    <!-- <li class="nav-item dropdown">
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
                                                    <p class="noti-details">
                                                        <span class="noti-title">John Doe</span> added new task 
                                                        <span class="noti-title">Patient appointment booking</span>
                                                    </p>
                                                    <p class="noti-time">
                                                        <span class="notification-time">4 mins ago</span>
                                                    </p>
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
                                                    <p class="noti-details">
                                                        <span class="noti-title">Tarah Shropshire</span> changed the task name 
                                                        <span class="noti-title">Appointment booking with payment gateway</span>
                                                    </p>
                                                    <p class="noti-time">
                                                        <span class="notification-time">6 mins ago</span>
                                                    </p>
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
                                                    <p class="noti-details">
                                                        <span class="noti-title">Misty Tison</span> added 
                                                        <span class="noti-title">Domenic Houston</span> and 
                                                        <span class="noti-title">Claire Mapes</span> to project 
                                                        <span class="noti-title">Doctor available module</span>
                                                    </p>
                                                    <p class="noti-time">
                                                        <span class="notification-time">8 mins ago</span>
                                                    </p>
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
                                                    <p class="noti-details">
                                                        <span class="noti-title">Rolland Webber</span> completed task 
                                                        <span class="noti-title">Patient and Doctor video conferencing</span>
                                                    </p>
                                                    <p class="noti-time">
                                                        <span class="notification-time">12 mins ago</span>
                                                    </p>
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
                                                    <p class="noti-details">
                                                        <span class="noti-title">Bernardo Galaviz</span> added new task 
                                                        <span class="noti-title">Private chat module</span>
                                                    </p>
                                                    <p class="noti-time">
                                                        <span class="notification-time">2 days ago</span>
                                                    </p>
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
                    </li> -->
                    <!-- /Notifications -->

                    <!-- Message Notifications -->
                    <!-- <li class="nav-item dropdown">
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
                    </li> -->
                    <!-- /Message Notifications -->

                    <li class="nav-item dropdown has-arrow main-drop">
                        <a href="#" class="dropdown-toggle nav-link" data-toggle="dropdown">
                            <span class="user-img">
                                <?= $this->html->image($admin->adminphoto, ['alt' => $admin->surname]) ?>

                                <span class="status online"></span></span>
                            <span><?= ucfirst($admin->surname) ?></span>
                        </a>
                        <div class="dropdown-menu">
                            <?= $this->Html->link('My Profile', ['controller' => 'Admins', 'action' => 'adminprofile', $this->GenerateUrl('my profile')], ['title' => 'admin dashboard', 'class' => 'dropdown-item'])
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
                        <?= $this->Html->link('My Profile', ['controller' => 'Admins', 'action' => 'adminprofile', $this->GenerateUrl('my profile')], ['title' => 'admin dashboard', 'class' => 'dropdown-item'])
                        ?>

                        <?= $this->Html->link('Logout', ['controller' => 'Users', 'action' => 'logout'], ['title' => 'logout', 'class' => 'dropdown-item'])
                        ?>
                    </div>
                </div>
                <!-- /Mobile Menu -->

            </div>
            <!-- /Header -->

            <!-- Sidebar -->
            <div class="sidebar DontPrint" id="sidebar">
                <div class="sidebar-inner slimscroll DontPrint">
                    <div id="sidebar-menu" class="sidebar-menu DontPrint">
                        <ul>
                            <li class="menu-title"> 
                                <!-- <span>Main</span> -->
                            </li>
                            <li>
                                <?= $this->Html->link(
                                    $this->Html->tag('i', '', array('class' => 'la la-dashboard')) . ' <span>Dashboard</span>', 
                                    array('controller' => 'Users', 'action' => 'dashboard'), 
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

                            <!-- <li class="submenu">
                                <a href="#" class="noti-dot"><i class="la la-user"></i> <span> LMS</span> <span class="menu-arrow"></span></a>
                                <ul style="display: none;">
                                    <li><a target="blank" href="https://meet.google.com">Live Class(Google)<span class="badge badge-pill bg-primary float-right">1</span></a></li>
                                    <li><a target="blank" href="https://zoom.us">Live Class(Zoom)<span class="badge badge-pill bg-primary float-right">2</span></a></li>

                                    <li><a target="blank" href="https://classroom.google.com">Google Classroom<span class="badge badge-pill bg-primary float-right">3</span></a></li>
                                                                                                    <li><?= $this->Html->link('Manage Exams', ['controller' => 'Exams', 'action' => 'index'], ['title' => 'manage exams']) ?></li>
                                                                                               <li><?= $this->Html->link('Manage Quizzes', ['controller' => 'Quizzes', 'action' => 'index'], ['title' => 'manages quizzes']) ?></li>

                                    <li>
                                        <?= $this->Html->link('Learning Resources', ['controller' => 'eresources', 'action' => 'manageresources'], ['title' => 'manage eresources']) ?>
                                    </li>
                                    <li>
                                        <?= $this->Html->link('Library Management', ['controller' => 'Books', 'action' => 'index'], ['title' => 'manage library books']) ?>
                                    </li>

                                </ul>
                            </li> -->
<!--                            <li class="submenu">
                                <a href="#" class="noti-dot"><i class="la la-user"></i> <span> HRMS</span> <span class="menu-arrow"></span></a>
                                <ul style="display: none;">
                                                                                                    <li><?= $this->Html->link('New Employee', ['controller' => 'Employees', 'action' => 'newemployee'], ['title' => 'add new employee']) ?></li>
                                        <li><?= $this->Html->link('View Employee', ['controller' => 'Employees', 'action' => 'index'], ['title' => 'manages employee']) ?></li>
                                   <li><?= $this->Html->link('Manage Departments', ['controller' => 'Employees', 'action' => 'departments'], ['title' => 'manage departments']) ?></li>
                                        <li><?= $this->Html->link('Manage Grades', ['controller' => 'Employees', 'action' => 'staffgrades'], ['title' => 'manages grades']) ?></li>
                                       <li><?= $this->Html->link('Manage Payslips', ['controller' => 'Payslips', 'action' => 'index'], ['title' => 'manages payslips']) ?></li>
                                    
                                    <li><a href="/" target="blank">HRMS<span class="badge badge-pill bg-primary float-right">1</span></a></li>
                                </ul>
                            </li>-->
                            <!--							<li> 
                                                                                            <a href="#"><i class="la la-users"></i> <span>News</span></a>
                                                                                    </li>-->
                            
                                <li class="submenu">
                                    <a href="#"><i class="la la-newspaper-o"></i> <span> Manage Reports</span> <span class="menu-arrow"></span></a>
                                    <ul style="display: none;">
                                        <li>
                                            <?= $this->Html->link('View Invoices', ['controller' => 'Invoices', 'action' => 'index'], ['title' => 'view invoices']) ?>

                                        </li>
                                         <li>
                                            <?= $this->Html->link('Manage Expenses', ['controller' => 'Spendings', 'action' => 'index'], ['title' => 'view expenses']) ?>

                                        </li> 
                                        <li>
                                            <?= $this->Html->link('Attendance Reports', ['controller' => 'AdminAttendances', 'action' => 'index'], ['title' => 'view attendance reports']) ?>

                                        </li>
                                        <li>
                                            <?= $this->Html->link('Result Approvals', ['controller' => 'Results', 'action' => 'pendingApproval'], ['title' => 'approve pending results']) ?>

                                        </li>
                                        <li>
                                            <?= $this->Html->link('View Transactions', ['controller' => 'Transactions', 'action' => 'index'], ['title' => 'view transactions']) ?>

                                        </li>
                                        <!-- <li>
                                            <?= $this->Html->link('Pending Transactions', ['controller' => 'Transactions', 'action' => 'pendingtransactions'], ['title' => 'pending transactions']) ?>

                                        </li>
                                        <li>
                                            <?= $this->Html->link('Verify Payment', ['controller' => 'Transactions', 'action' => 'requeryfailedpayment'], ['title' => 'verify and update paystack payment']) ?>
                                        </li> -->
                                        <li>
                                            <?= $this->Html->link(' Payment Logs', ['controller' => 'Paylogs', 'action' => 'index'], ['title' => ' payment logs']) ?>
                                        </li>

                                        <!-- <li>
                                            <?= $this->Html->link('Business Intel', ['controller' => 'Admins', 'action' => 'businessinteligence'], ['title' => 'view business intelligense']) ?>

                                        </li> -->
                                        <li>
                                            <?= $this->Html->link('Result Analytics', ['controller' => 'Admins', 'action' => 'resultanalytics'], ['title' => 'view results analytics']) ?>

                                        </li>
                                      <li>
                                            <?= $this->Html->link('Finance Analytics', ['controller' => 'Admins', 'action' => 'financialanalytics'], ['title' => 'view financial analytics']) ?>

                                        </li>

                                    </ul>
                                </li>


                            

                                <li class="submenu">
                                    <a href="#"><i class="la la-users"></i> <span> Manage Admins</span> <span class="menu-arrow"></span></a>
                                    <ul style="display: none;">
                                        <li>
                                            <?= $this->Html->link('Manage Admins', ['controller' => 'Users', 'action' => 'manageadmins'], ['title' => 'manage admins']) ?>

                                        </li>
                                        <!-- <li>
                                            <?= $this->Html->link('New Admin', ['controller' => 'Admins', 'action' => 'newadmin'], ['title' => 'add new admin']) ?>

                                        </li> -->
                                        <li>
                                            <?= $this->Html->link('Manage Teachers', ['controller' => 'Teachers', 'action' => 'manageteachers'], ['title' => 'Manage Teachers']) ?>
                                        </li>
                                        <li>
                                            <?= $this->Html->link('Assign Subjects', ['controller' => 'Teachers', 'action' => 'assignsubjectstoteacher'], ['title' => 'Assign subjects']) ?>
                                        </li>
                                        <!-- <li>
                                            <?= $this->Html->link('Manage Privileges', ['controller' => 'Privileges', 'action' => 'index'], ['title' => 'manage privileges']) ?>
                                        </li> -->
                                        <!-- <li>
                                            <?= $this->Html->link('Delete Email Address', ['controller' => 'Users', 'action' => 'checkandremoveemail'], ['title' => 'delete email address']) ?>
                                        </li> -->
                                    </ul>
                                </li>
                            
                                <!-- <li class="submenu">
                                    <a href="#"><i class="la la-users"></i> <span> Manage Roles</span> <span class="menu-arrow"></span></a>
                                    <ul style="display: none;">
                                        <li>
                                            <?= $this->Html->link('Manage Roles', ['controller' => 'Roles', 'action' => 'manageroles'], ['title' => 'manage roles']) ?>

                                        </li>


                                    </ul>
                                </li> -->
                            
                                <li class="submenu">
                                    <a href="#"><i class="la la-file-o"></i> <span> Manage Results</span> <span class="menu-arrow"></span></a>
                                    <ul style="display: none;">
                                        <li>
                                            <?= $this->Html->link('Manage Results', ['controller' => 'Results', 'action' => 'manageresults'], ['title' => 'manage results']) ?>

                                        </li>
                                        <li>
                                            <?= $this->Html->link('Upload Results', ['controller' => 'Results', 'action' => 'uploadresults'], ['title' => 'upload results']) ?>

                                        </li>
                                        <li>
                                            <?= $this->Html->link('Examination Sheet', ['controller' => 'Results', 'action' => 'examinationsheet'], ['title' => 'examination sheet']) ?>

                                        </li>
                                        <!-- <li>
                                            <?= $this->Html->link('Upload Course Results', ['controller' => 'Results', 'action' => 'uploadcourseresults'], ['title' => 'upload course results']) ?>

                                        </li> -->
                                       <!-- <li>
                                            <?= $this->Html->link('Approve Results', ['controller' => 'Approvedresults', 'action' => 'add'], ['title' => 'approve a recent result']) ?>

                                        </li> -->
                                        <!-- <li>
                                            <?= $this->Html->link(' Manage Approved Results', ['controller' => 'Approvedresults', 'action' => 'index'], ['title' => 'manage approved results']) ?>

                                        </li> -->
                             <!-- <li>
                                            <?= $this->Html->link('Manage Transcript', ['controller' => 'Results', 'action' => 'managetranscript'], ['title' => 'manage transcript']) ?>

                                        </li> -->
                                        
                                        <!-- <li>
                                            <?= $this->Html->link(' Remove Result', ['controller' => 'Results', 'action' => 'removeresult'], ['title' => 'remove results']) ?>

                                        </li> -->
                                        

                                    </ul>
                                </li>
                                

                                <!-- <li class="submenu">
                                    <a href="#"><i class="la la-dashcube"></i> <span> Hostel </span> <span class="menu-arrow"></span></a>
                                    <ul style="display: none;">
                                        <li><?= $this->Html->link('Manage Hostel', ['controller' => 'Hostels', 'action' => 'index'], ['title' => 'manage hostels']) ?></li>
                                        <li><?= $this->Html->link('Manage Hostel Rooms', ['controller' => 'Hostelrooms', 'action' => 'index'], ['title' => 'manage hostel rooms']) ?></li> 
                                        <li>
                                            <?= $this->Html->link('Assign Hostel Rooms', ['controller' => 'Hostelrooms', 'action' => 'assignroom'], ['title' => 'assign hostel rooms']) ?>
                                        </li>
                                        <li>
                                            <?= $this->Html->link('Accommodation Registry', ['controller' => 'Hostelrooms', 'action' => 'studentrooms'], ['title' => 'view students and rooms']) ?>
                                        </li>
                                    </ul>
                                </li>
                                 <li class="submenu">
                                    <a href="#"><i class="la la-dashcube"></i> <span> Notifications </span> <span class="menu-arrow"></span></a>
                                    <ul style="display: none;">
                                        <li><?= $this->Html->link('Manage Notifications', ['controller' => 'Notifications', 'action' => 'index'], ['title' => 'manage notifications']) ?></li>
                                        <li><?= $this->Html->link('New Notice', ['controller' => 'Notifications', 'action' => 'newnotification'], ['title' => 'post new notice']) ?></li> 
                                      
                                        </li>
                                    </ul>
                                </li> -->
                                
                                <li class="submenu">
                                    <a href="#"><i class="la la-bank"></i> <span> Finance</span> <span class="menu-arrow"></span></a>
                                    <ul style="display: none;">
                                        <li>
                                            <?= $this->Html->link('Manage Fees', ['controller' => 'Fees', 'action' => 'managefees'], ['title' => 'manage fees', 'class' => 'collapse-item']) ?>

                                        </li>
                                        <li>
                                            <?= $this->Html->link('Generate Invoice', ['controller' => 'Students', 'action' => 'getstudents'], ['title' => 'get students for generating pay ids']) ?>

                                        </li>
                                        <li>
                                            <?= $this->Html->link('Assign Fee', ['controller' => 'Students', 'action' => 'assignfee'], ['title' => 'assign fee to students']) ?>
                                        </li>
                                        <li>
                                            <?= $this->Html->link('Collect Fee', ['controller' => 'CollectFees', 'action' => 'index'], ['title' => 'manually collect fees from students/parents']) ?>
                                        </li>
                                        <!-- <li>
                                            <?= $this->Html->link('Verify RRR', ['controller' => 'Admins', 'action' => 'checkrrr'], ['title' => 'verify RRR paymentents']) ?>
                                        </li> -->

                                    </ul>
                                </li>
                                
                                <li class="submenu">
                                    <a href="#"><i class="la la-pagelines"></i> <span> Manage Academics</span> <span class="menu-arrow"></span></a>
                                    <ul style="display: none;">
                                        <!-- <li>
                                            <?= $this->Html->link('Manage Faculties', ['controller' => 'Faculties', 'action' => 'managefaculties'], ['title' => 'manage faculties']) ?>

                                        </li> -->
                                        <li>
                                            <?= $this->Html->link('Manage Classes', ['controller' => 'Departments', 'action' => 'managedepartments'], ['title' => 'manage classes']) ?>

                                        </li>
                                        <li>
                                            <?= $this->Html->link('Manage Class Arms', ['controller' => 'ClassArms', 'action' => 'index'], ['title' => 'manage class arms']) ?>

                                        </li>
                                        <!-- <li>
                                            <?= $this->Html->link('Manage Programes', ['controller' => 'Programes', 'action' => 'manageprogrames'], ['title' => 'manage programes']) ?>
                                        </li> -->
                                        <!-- <li>
                                            <?= $this->Html->link('Course Assignment', ['controller' => 'Courseassignments', 'action' => 'index'], ['title' => 'Manage course assignments']) ?>
                                        </li> -->
                                        <!-- <li><?= $this->Html->link('Grade Book', ['controller' => 'Constants', 'action' => 'index'], ['title' => 'manage grade book', 'class' => 'collapse-item']) ?></li> -->
                                        <li>
                                            <?= $this->Html->link('Manage Subjects', ['controller' => 'Subjects', 'action' => 'managesubjects'], ['title' => 'Manage Subject']) ?>
                                        </li>
                                        <!--                                        <li>
                                        <?= $this->Html->link('Manage Topics', ['controller' => 'Topics', 'action' => 'managetopics'], ['title' => 'Manage Topics']) ?>
                                                                                </li>-->
                                        <li>
                                            <?= $this->Html->link('Manage Sessions', ['controller' => 'Sessions', 'action' => 'managesessions'], ['title' => 'manage sessions']) ?>
                                        </li>
                                        <li>
                                            <?= $this->Html->link('Manage Terms', ['controller' => 'Semesters', 'action' => 'managesemesters'], ['title' => 'manage terms']) ?>
                                        </li>
                                        <!-- <li>
                                            <?= $this->Html->link('Manage Classes', ['controller' => 'Admins', 'action' => 'manageclasses'], ['title' => 'manage classes']) ?>
                                        </li>
                                        <li>
                                            <?= $this->Html->link('Update Conditions & A.L', ['controller' => 'Admisionconditions', 'action' => 'editcondition', 1], ['title' => 'update admision policies']) ?>
                                        </li>
                                        <li>
                                            <?= $this->Html->link('How To Pay Fees', ['controller' => 'Admisionconditions', 'action' => 'editcondition', 2], ['title' => 'update acceptance letter']) ?>
                                        </li>
                                        <li>
                                            <?= $this->Html->link('Update Admission Letter', ['controller' => 'Admisionconditions', 'action' => 'editcondition', 3], ['title' => 'update admission letter']) ?>
                                        </li>
                                        <li>
                                            <?= $this->Html->link('Manage T.Requests', ['controller' => 'Admins', 'action' => 'managetranscriptorders'], ['title' => 'manage transcript requests']) ?>
                                        </li> -->

                                    </ul>
                                </li>
<!--                                <li class="submenu">
                                    <a href="#"><i class="la la-file-o"></i> <span> Course Registration</span> <span class="menu-arrow"></span></a>
                                    <ul style="display: none;">
                                        <li>
                                            <?= $this->Html->link('Course Registrants', ['controller' => 'Courseregistrations', 'action' => 'coursestudents'], ['title' => 'view students for this courses for the semester']) ?>

                                        </li>


                                    </ul>
                                </li>-->
                                <!-- <li class="submenu">
                                    <a href="#"><i class="la la-file-o"></i> <span> Time Table</span> <span class="menu-arrow"></span></a>
                                    <ul style="display: none;">
                                        <li>
                                            <?= $this->Html->link('Manage Time Table', ['controller' => 'Timetables', 'action' => 'index'], ['title' => 'manage time tables']) ?>

                                        </li>


                                    </ul>
                                </li> -->
                                
                                <li class="submenu">
                                    <a href="#"><i class="la la-angellist"></i> <span> Manage Students</span> <span class="menu-arrow"></span></a>
                                    <ul style="display: none;">
                                        <li>
                                            <?= $this->Html->link('List Students', ['controller' => 'Students', 'action' => 'managestudents'], ['title' => 'Manage Students']) ?>

                                        </li>
                                        <li>
                                            <?= $this->Html->link('Add Student', ['controller' => 'Students', 'action' => 'newstudent'], ['title' => 'Manage Students']) ?>

                                        </li>
                                        
                                        <li>
                                            <?= $this->Html->link('Import Students', ['controller' => 'Students', 'action' => 'importstudents'], ['title' => 'bulk import Students']) ?>
                                        </li>
                                        <li>
                                            <?= $this->Html->link('Students Promotion', ['controller' => 'Students', 'action' => 'getstudentsforpromotion'], ['title' => 'promote Students']) ?>
                                        </li>
                                        <!-- <li>
                                            <?= $this->Html->link('Add old Student', ['controller' => 'Students', 'action' => 'addoldstudent'], ['title' => 'add old student with reg number']) ?>
                                        </li> -->
                                        <!-- <li>
                                            <?= $this->Html->link('Search', ['controller' => 'Students', 'action' => 'searchreport'], ['title' => 'search Students']) ?>
                                        </li> -->
                                        
                                            <!-- <li>
                                                <?= $this->Html->link('Assign Email', ['controller' => 'Students', 'action' => 'studentswithoutemail'], ['title' => 'assign school email to stdents']) ?>
                                            </li>
                                            <li>
                                                <?= $this->Html->link('Student IDs', ['controller' => 'Students', 'action' => 'generateids'], ['title' => 'generate ID for students']) ?>
                                            </li>
                                            <li>
                                                <?= $this->Html->link('Special Candidates', ['controller' => 'Students', 'action' => 'spcanidates'], ['title' => 'view candidates with special arrangements']) ?>
                                            </li> -->
                                        
                                    </ul>
                                </li>

                                <li class="submenu">
                                    <a href="#"><i class="la la-money"></i> <span> Manage Admission </span> <span class="menu-arrow"></span></a>
                                    <ul style="display: none;">
                                        <li><?= $this->Html->link('Direct Admission', ['controller' => 'Students', 'action' => 'newstudent'], ['title' => 'Direct admision']) ?></li>
                                        <li><?= $this->Html->link('Applicant List', ['controller' => 'Students', 'action' => 'manageapplicants'], ['title' => 'Manage Applicants']) ?></li>
                                        <li><?= $this->Html->link('Application Form', ['controller' => 'Students', 'action' => 'newapplicant'], ['title' => 'Manage Applicants']) ?></li>
                                        <!-- <li><?= $this->Html->link('Modes of Admission', ['controller' => 'Modes', 'action' => 'index'], ['title' => 'Manage Admission modes']) ?></li> -->
                                        <li><?= $this->Html->link('Manage Parents', ['controller' => 'Admins', 'action' => 'viewparents'], ['title' => 'Manage Parents']) ?></li>

                                    </ul>
                                </li>
<!--                                <li class="submenu">
                                    <a href="#"><i class="la la-user"></i> <span> Sponsorships </span> <span class="menu-arrow"></span></a>
                                    <ul style="display: none;">
                                        <li><?= $this->Html->link('Manage Sponsors', ['controller' => 'Sponsors', 'action' => 'index'], ['title' => 'Manage sponsors']) ?></li>
                                        <li>
                                            <?= $this->Html->link('Manage Sponsorships', ['controller' => 'Sponsorships', 'action' => 'index'], ['title' => 'view sponsorships']) ?>
                                        </li>
                                        <li>
                                            <?= $this->Html->link('Sponsorship Payments', ['controller' => 'Sponsorshippayments', 'action' => 'index'], ['title' => 'view sponsorship payments']) ?>
                                        </li>
                                                                                <li>
                                        <?= $this->Html->link('Notifications', ['controller' => 'Notifications', 'action' => 'index'], ['title' => 'Manage Notifications']) ?>
                                                                                </li>

                                    </ul>
                                </li>-->

                                     
                                <!--                                 <li class="submenu">
                                                                    <a href="#"><i class="la la-newspaper-o"></i> <span> News & Events </span> <span class="menu-arrow"></span></a>
                                                                    <ul style="display: none;">
                                                                        <li><?= $this->Html->link('Manage News', ['controller' => 'News', 'action' => 'managenews'], ['title' => 'Manage news']) ?></li>
                                                                       <li><?= $this->Html->link('Manage Events', ['controller' => 'Events', 'action' => 'manageevents'], ['title' => 'Manage events']) ?></li>
                                
                                                                    </ul>
                                                                </li>-->


<!--                                                              <li class="submenu">
                                                                    <a href="#"><i class="la la-pie-chart"></i> <span> Forum </span> <span class="menu-arrow"></span></a>
                                                                    <ul style="display: none;">
                                                                        <li><?= $this->Html->link('Manage Forum', ['controller' => 'Posts', 'action' => 'manageposts'], ['title' => 'Manage Forum']) ?></li>
                                
                                                                    </ul>
                                                                </li>-->
                                                                <!-- <li class="submenu">
                                                                    <a href="#"><i class="la la-pie-chart"></i> <span> Manage Polls </span> <span class="menu-arrow"></span></a>
                                                                    <ul style="display: none;">
                                                                        <li><?= $this->Html->link('Add Candidates', ['controller' => 'Admins', 'action' => 'addcandidate'], ['title' => 'add candidate']) ?></li>
                                 <li><?= $this->Html->link('Manage Candidates', ['controller' => 'Admins', 'action' => 'managecandidates'], ['title' => 'add candidate']) ?></li>
                                                          <li><?= $this->Html->link('Manage Positions', ['controller' => 'Admins', 'action' => 'addposition'], ['title' => 'add position']) ?></li>  
                                                           <li><?= $this->Html->link('View Votes', ['controller' => 'Admins', 'action' => 'viewvotes'], ['title' => 'view votes']) ?></li>   
                                                                    </ul>
                                                                </li> -->
                           

                             <!-- <li class="submenu">
                                    <a href="#"><i class="la la-users"></i> <span> Manage Letters</span> <span class="menu-arrow"></span></a>
                                    <ul style="display: none;">
                                        <li>
                                            <?= $this->Html->link('Manage Letters', ['controller' => 'Letters', 'action' => 'index'], ['title' => 'manage letters']) ?>

                                        </li>


                                    </ul>
                                </li> -->
                                <li class="submenu">
                                    <a href="#"><i class="la la-graduation-cap"></i> <span> Setting </span> <span class="menu-arrow"></span></a>
                                    <ul style="display: none;">
                                        <li>
                                            <?= $this->Html->link('Activity Logs', ['controller' => 'Logs', 'action' => 'index'], ['title' => 'view activity logs']) ?>


                                        </li>
                                        <li>
                                            <?= $this->Html->link('System Settings', ['controller' => 'Settings', 'action' => 'editsettings', 1], ['title' => 'update system system']) ?>

                                        </li>



                                    </ul>
                                </li>

                            <!-- <li> 
                                <a target="blank" href="https://mail.google.com" class="fa fa-envelope-o" title="check my school mail">&nbsp;Check Mail</a>

                            </li> -->
                            <li>
                                <?= $this->Html->link(
                                    $this->Html->tag('i', '', array('class' => 'la la-power-off')) . ' <span>Logout</span>', 
                                    array('controller' => 'Users', 'action' => 'logout', $user['id']), 
                                    array('escape' => false)
                                ) ?>
                            </li>





                            <!--							<li class="submenu">
                                                                                            <a href="#"><i class="la la-crosshairs"></i> <span> Goals </span> <span class="menu-arrow"></span></a>
                                                                                            <ul style="display: none;">
                                                                                                    <li><a href="goal-tracking.html"> Goal List </a></li>
                                                                                                    <li><a href="goal-type.html"> Goal Type </a></li>
                                                                                            </ul>
                                                                                    </li>
                                                                                    <li class="submenu">
                                                                                            <a href="#"><i class="la la-edit"></i> <span> Training </span> <span class="menu-arrow"></span></a>
                                                                                            <ul style="display: none;">
                                                                                                    <li><a href="training.html"> Training List </a></li>
                                                                                                    <li><a href="trainers.html"> Trainers</a></li>
                                                                                                    <li><a href="training-type.html"> Training Type </a></li>
                                                                                            </ul>
                                                                                    </li>-->
                            <!--							<li><a href="promotion.html"><i class="la la-bullhorn"></i> <span>Promotion</span></a></li>
                                                                                    <li><a href="resignation.html"><i class="la la-external-link-square"></i> <span>Resignation</span></a></li>
                                                                                    <li><a href="termination.html"><i class="la la-times-circle"></i> <span>Termination</span></a></li>
                                                                                    <li class="menu-title"> 
                                                                                            <span>Administration</span>
                                                                                    </li>
                                                                                    <li> 
                                                                                            <a href="assets.html"><i class="la la-object-ungroup"></i> <span>Assets</span></a>
                                                                                    </li>-->
                            <!--							<li class="submenu">
                                                                                            <a href="#"><i class="la la-briefcase"></i> <span> Jobs </span> <span class="menu-arrow"></span></a>
                                                                                            <ul style="display: none;">
                                                                                                    <li><a href="jobs.html"> Manage Jobs </a></li>
                                                                                                    <li><a href="job-applicants.html"> Applied Candidates </a></li>
                                                                                            </ul>
                                                                                    </li>-->
                            <!--							<li> 
                                                                                            <a href="knowledgebase.html"><i class="la la-question"></i> <span>Knowledgebase</span></a>
                                                                                    </li>
                                                                                    <li> 
                                                                                            <a href="activities.html"><i class="la la-bell"></i> <span>Activities</span></a>
                                                                                    </li>-->
                            <!--							<li> 
                                                                                            <a href="users.html"><i class="la la-user-plus"></i> <span>Users</span></a>
                                                                                    </li>
                                                                                    <li> 
                                                                                            <a href="settings.html"><i class="la la-cog"></i> <span>Settings</span></a>
                                                                                    </li>-->
                            <li class="menu-title"> 
                                <!-- <span>Pages</span> -->
                            </li>
                            <li class="submenu">
                                <a href="#"><i class="la la-user"></i> <span> Profile </span> <span class="menu-arrow"></span></a>
                                <ul style="display: none;">
                                    <li><?= $this->Html->link('My Profile', ['controller' => 'Users', 'action' => 'myprofile', $this->GenerateUrl('my profile')], ['title' => 'my profile'])
                                ?></li>
                                    <li><?= $this->Html->link('Update Profile', ['controller' => 'Users', 'action' => 'updateprofile'], ['title' => 'update profile'])
                                ?></li>
                                </ul>
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
                    '../assets/plugins/raphael/raphael.min', '../assets/js/chart', 'employerscript',
                    '../assets/js/app', 'datatables.min', 'vfs_fonts', 'pdfmake.min', 'buttons.bootstrap4.min',
                    'summernote.init', 'select2.full.min', 'ckeditor/ckeditor'])
        ?>
        <?= $this->fetch('script') ?>


        <script>
//            $(document).ready(function () {
//                $('#myTable').DataTable();
//            });

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
            
            //Date picker for current term ends
            $('#currenttermends').datepicker({
                autoclose: true,
                format: 'yyyy-mm-dd'
            });
            
            //Date picker for next term begins
            $('#nexttermbegins').datepicker({
                autoclose: true,
                format: 'yyyy-mm-dd'
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