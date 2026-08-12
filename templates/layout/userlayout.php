<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0">
        <meta name="description" content="Imo State Judiciary">
        <meta name="keywords" content="Imo state, Judiciary, law, court, legal, courts in Imo state">
        <meta name="author" content="Imo state judiciary">
        <meta name="robots" content="noindex, dofollow">
        <?= $this->Html->meta('icon') ?>
        <title> <?php
            mb_internal_encoding('UTF-8');
            mb_http_output('UTF-8');
            echo (!isset($title)) ? $this->fetch("title") : $title;
            ?> | <?= SCHOOL ?> </title>

        <!-- Favicon -->
 
<!--        <link rel="shortcut icon" type="image/x-icon" href="../../../img/favicon.ico">-->

        <!-- Bootstrap CSS -->
        <?=
        $this->Html->css(['../assets/css/bootstrap.min', '../assets/css/font-awesome.min', 'dataTables.bootstrap4.min',
            'select2.min', 'bootstrap-datepicker.min', '../assets/plugins/summernote/summernote',
            '../assets/css/line-awesome.min', '../assets/plugins/morris/morris', '../assets/plugins/morris/morris',
            'select2.min', '../assets/css/style'])
        ?>
        <?= $this->fetch('meta') ?>
        <?= $this->fetch('css') ?>

        <?php
        $lawyer = $this->request->getSession()->read('lawyer');
// debug(json_encode($admin, JSON_PRETTY_PRINT)); exit;
        ?>

    </head>

    <body>
        <!-- Main Wrapper -->
        <div class="main-wrapper">

            <!-- Header -->
            <div class="header">

                <!-- Logo -->
                <div class="header-left">
                    <a href="#" class="logo">
                        <?= $this->Html->image($settings->logo, ['alt' => 'UAES','width'=>40,'height'=>40]) ?>
<!--						<img src="../assets/img/logo.png" width="40" height="40" alt="">-->
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
                    <h3>Imo State Judiciary Information System</h3>
                </div>
                <!-- /Header Title -->

                <a id="mobile_btn" class="mobile_btn" href="#sidebar"><i class="fa fa-bars"></i></a>

                <!-- Header Menu -->
                <ul class="nav user-menu">

                    <!-- Search -->
<!--                    <li class="nav-item">
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
                    <li class="nav-item dropdown has-arrow flag-nav">
                        <a class="nav-link dropdown-toggle" data-toggle="dropdown" href="#" role="button">
                            <img src="../assets/img/flags/us.png" alt="" height="20"> <span>Support Line</span>
                        </a>
                        <div class="dropdown-menu dropdown-menu-right">
                            <a href="javascript:void(0);" class="dropdown-item">
                                <?= $this->Html->image('Imojudiciaryverysmall.png', ['alt' => 'Imo State Judiciary','height'=>16]) ?>
                               +234 904 6795 968
                            </a>

                        </div>
                         
                    </li>
                    <!-- /Flag -->

                    <!-- Notifications -->
                    <!--                    <li class="nav-item dropdown">
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
                    <!--                    <li class="nav-item dropdown">
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
                                <?= $this->html->image($lawyer->photo, ['alt' => $lawyer->firstname]) ?>

                                <span class="status online"></span></span>
                            <span><?= $lawyer->firstname ?></span>
                        </a>
                        <div class="dropdown-menu">
                            <?= $this->Html->link('My Profile', ['controller' => 'Lawyers', 'action' => 'myprofile', $this->GenerateUrl('my profile')], ['title' => 'admin dashboard', 'class' => 'dropdown-item'])
                            ?>

                            <?= $this->Html->link('Logout', ['controller' => 'Users', 'action' => 'logout'], ['title' => 'logout', 'class' => 'dropdown-item'])
                            ?>

                        </div>
                    </li>
                </ul>
                <!-- /Header Menu -->

                <!-- Mobile Menu -->
                <div class="dropdown mobile-user-menu">
                    <a href="#" class="nav-link dropdown-toggle" data-toggle="dropdown" aria-expanded="false"><i class="fa fa-ellipsis-v"></i></a>
                    <div class="dropdown-menu dropdown-menu-right">
                        <li><?= $this->Html->link('My Profile', ['controller' => 'Lawyers', 'action' => 'myprofile', $this->GenerateUrl('my profile')], ['title' => 'admin dashboard', 'class' => 'dropdown-item'])
                            ?></li>
                        <!--                        <a class="dropdown-item" href="settings.html">Settings</a>-->
                        <?= $this->Html->link('Logout', ['controller' => 'Users', 'action' => 'logout'], ['title' => 'logout', 'class' => 'dropdown-item']) ?>
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
                                <span>Main</span>
                            </li>
                            <li class="submenu">
                                <a href="#"><i class="la la-dashboard"></i> <span> Dashboard</span> <span class="menu-arrow"></span></a>
                                <ul style="display: none;">
                                    <li>
                                        <?= $this->Html->link('My Dashboard', ['controller' => 'Users', 'action' => 'dashboard'], ['title' => 'admin dashboard'])
                                        ?>

                                    </li>

                                </ul>
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
                            <li class="submenu">
                                <a href="#"><i class="la la-newspaper-o"></i> <span> e-Filing</span> <span class="menu-arrow"></span></a>
                                <ul style="display: none;">
                                    <li>
                                        <?= $this->Html->link(' File A New Case', ['controller' => 'Lawyers', 'action' => 'casefiler'], ['title' => 'file originating summon']) ?>

                                    </li>
                                     <li>
                                        <?= $this->Html->link(' My Cases', ['controller' => 'Lawyers', 'action' => 'managecases'], ['title' => 'manage my cases']) ?>

                                    </li>
                                    <li>
                                        <?= $this->Html->link(' Search Cases', ['controller' => 'Lawyers', 'action' => 'searchcases'], ['title' => 'search my cases']) ?>

                                    </li>
                                    <li>
                                        <?= $this->Html->link(' File a Document', ['controller' => 'Lawyers', 'action' => 'newaffidavit'], ['title' => 'file affidavit, applications, motions etc']) ?>

                                    </li>
                                  <li>
                                        <?= $this->Html->link(' My Filings', ['controller' => 'Lawyers', 'action' => 'otherfilings'], ['title' => 'view other filings']) ?>

                                    </li>
                                   
                                    <li>
                                        <?= $this->Html->link(' Forms', ['controller' => 'Forms', 'action' => 'getforms'], ['title' => 'download forms']) ?>

                                    </li>
                                </ul>
                            </li>

<!--                            <li class="submenu">
                                <a href="#"><i class="la la-users"></i> <span> Probate</span> <span class="menu-arrow"></span></a>
                                <ul style="display: none;">
                                    <li>
                                        <?= $this->Html->link(' New application', ['controller' => 'Probates', 'action' => 'newadmin'], ['title' => 'add new admin']) ?>

                                    </li>
                                    <li>
                                        <?= $this->Html->link(' Manage Applications', ['controller' => 'Probates', 'action' => 'manageadmins'], ['title' => 'manage news']) ?>

                                    </li>

                                </ul>
                            </li>-->

<li class="submenu">
                                <a href="#"><i class="la la-dashcube"></i> <span> e-Court </span> <span class="menu-arrow"></span></a>
                                <ul style="display: none;">
                                    <li><?= $this->Html->link(' My e-Court Sessions', ['controller' => 'Lawyers', 'action' => 'myecourtsessions'], ['title' => 'e-Court sessions']) ?></li>
                                   

                                </ul>
                            </li>
                            
                            <li class="submenu">
								<a href="javascript:void(0);"><i class="la la-money"></i> <span>Payments/Invoices</span> <span class="menu-arrow"></span></a>
								<ul style="display: none;">
									<li class="submenu">
										<a href="javascript:void(0);"> <span>Make Payments</span> <span class="menu-arrow"></span></a>
										<ul style="display: none;">
											<li><?= $this->Html->link(' Make A Payment', ['controller' => 'Lawyers', 'action' => 'costpayment'], ['title' => 'cost payment']) ?></li>
<!--											<li class="submenu">
												<a href="javascript:void(0);"> <span> Fines</span> <span class="menu-arrow"></span></a>
												<ul style="display: none;">
													<li><a href="javascript:void(0);">Level 3</a></li>
													<li><a href="javascript:void(0);">Level 3</a></li>
												</ul>
											</li>-->
<!--											<li><?= $this->Html->link(' Fines', ['controller' => 'Lawyers', 'action' => 'costpayment'], ['title' => 'fines payment']) ?></li>-->
										</ul>
									</li>
									<li><?= $this->Html->link(' My Invoices', ['controller' => 'Transactions', 'action' => 'myinvoices'], ['title' => 'my invoices']) ?></li>
                                    <li><?= $this->Html->link(' My Payments', ['controller' => 'Transactions', 'action' => 'mypayments'], ['title' => 'my payments']) ?></li>
								</ul>
							</li>

<!--                            <li class="submenu">
                                <a href="#"><i class="la la-money"></i> <span> Payments/Invoices </span> <span class="menu-arrow"></span></a>
                                <ul style="display: none;">
                                    <li><?= $this->Html->link(' My Invoices', ['controller' => 'Transactions', 'action' => 'myinvoices'], ['title' => 'my invoices']) ?></li>
                                    <li><?= $this->Html->link(' My Payments', ['controller' => 'Transactions', 'action' => 'mypayments'], ['title' => 'my payments']) ?></li>

                                </ul>
                            </li>-->

<!--                            <li class="submenu">
                                <a href="#"><i class="la la-money"></i> <span> Marriage Registry </span> <span class="menu-arrow"></span></a>
                                <ul style="display: none;">
                                    <li><?= $this->Html->link(' New Application', ['controller' => 'Admins', 'action' => 'useraccounts'], ['title' => 'user accounts']) ?></li>
                                    <li><?= $this->Html->link(' Manage Applications', ['controller' => 'Admins', 'action' => 'lawyers'], ['title' => 'layers and users']) ?></li>

                                </ul>
                            </li>-->
                            <!--		<li> 
                                                    <a href="policies.html"><i class="la la-file-pdf-o"></i> <span>Policies</span></a>
                                            </li>
                                            <li class="submenu">
                                                    <a href="#"><i class="la la-pie-chart"></i> <span> Reports </span> <span class="menu-arrow"></span></a>
                                                    <ul style="display: none;">
                                                            <li><a href="expense-reports.html"> Expense Report </a></li>
                                                            <li><a href="invoice-reports.html"> Invoice Report </a></li>
                                                    </ul>
                                            </li>-->


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



                            <li class="menu-title"> 
                                <!-- <span>Pages</span> -->
                            </li>
                            <li class="submenu">
                                <a href="#"><i class="la la-user"></i> <span> Profile </span> <span class="menu-arrow"></span></a>
                                <ul style="display: none;">
                                    <li><?= $this->Html->link('My Profile', ['controller' => 'Lawyers', 'action' => 'myprofile', $this->GenerateUrl('my profile')], ['title' => 'my profile'])
                                        ?></li>
                                   
        <li><?= $this->Html->link('Change Password', ['controller' => 'Users', 'action' => 'updatepassword'], ['title' => 'change my password'])
                                        ?></li>
 <li><?= $this->Html->link('Logout', ['controller' => 'Users', 'action' => 'logout'], ['title' => 'logout'])
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
                    '../assets/js/jquery.slimscroll.min', 'moment.min', 'bootstrap-datepicker.min',
                    'jquery.dataTables.min', 'dataTables.bootstrap4.min', '../assets/plugins/morris/morris.min', '../assets/plugins/summernote/summernote.min',
                    '../assets/plugins/raphael/raphael.min', '../assets/js/chart', 'assets/js/chart',
                    '../assets/js/app',
                    'summernote.init', 'select2.full.min'])
        ?>
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
            
               $('#enddate').datepicker({
                autoclose: true
            });
            
               $('#startdate').datepicker({
                autoclose: true
            });

        </script>

    </body>
</html>