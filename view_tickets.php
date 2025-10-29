<?php
session_start();
include '../database/db_connect.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: ../../login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Get user info (optional, depending on usage)
$user_sql = "SELECT * FROM user_info WHERE employee_id = ?";
$stmt = $conn->prepare($user_sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$user_result = $stmt->get_result();
$current_user = $user_result->fetch_assoc();

// Fetch all ticket entries for the logged-in user, including status
$tickets_sql = "SELECT ticket_id, lab_room, description, priority, attachment_filename, upload_note, date_submitted, status 
                FROM ticket WHERE employee_id = ? ORDER BY date_submitted DESC";

$stmt = $conn->prepare($tickets_sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$tickets_result = $stmt->get_result();
?>



<!DOCTYPE html>
<html lang="en">
<meta charset="utf-8" />
<meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0, minimal-ui" />
<meta http-equiv="X-UA-Compatible" content="IE=edge" />
<meta name="description" content="." />
<meta name="keywords" content="." />
<meta name="author" content="Sniper 2025" />
<link href="https://fonts.googleapis.com/css2?family=Open+Sans:wght@300;400;500;600&display=swap" rel="stylesheet" />
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
<link rel="stylesheet" href="../dist/assets/images/user">
<link rel="stylesheet" href="../assets/fonts/phosphor/duotone/style.css" />
<link rel="stylesheet" href="../assets/fonts/tabler-icons.min.css" />
<link rel="stylesheet" href="../assets/fonts/feather.css" />
<link rel="stylesheet" href="../assets/fonts/fontawesome.css" />
<link rel="stylesheet" href="../assets/fonts/material.css" />
<link rel="stylesheet" href="../assets/css/style.css" id="main-style-link" />
<link rel="stylesheet" href="../assets/css/plugins/requester.css" />
<link rel="stylesheet" href="../assets/css/plugins/view_tickets.css" />
<script src="https://kit.fontawesome.com/a076d05399.js" crossorigin="anonymous"></script>

<head>
    <title>Employee Dashboard</title>
    <!-- Include your CSS files here -->
</head>

<body>
    <!-- SIDEBAR
  <div class="sidebar" id="sidebar">
    <div class="sidebar-header">
      <h2><i class="fas fa-desktop"></i> MainTix</h2>
    </div>
    <div class="user-info">
      <img src="../dist/assets/images/user/avatar-2.jpg" alt="User" class="user-img">
      <div>
        <h6>Jan Lewis Agas</h6>
        <span>User</span>
      </div>
    </div>
    <hr>
    <div class="sidebar-footer">
      <h6>NAVIGATION</h6>
      <ul class="nav flex-column">
        <li><a href="#" class="active" data-tab="labs"><i class="fas fa-laptop"></i> Computer Labs</a></li>
        <li><a href="#" data-tab="requests"><i class="fas fa-chart-line"></i> View Requests</a></li>
      </ul>
    </div>

    <div class="sidebar-footer">
      <h6>SETTINGS</h6>
      <ul class="nav flex-column">
        <li><a href="#" data-tab="about"><i class="fas fa-cog"></i> About</a></li>
        <li><a href="#" data-tab="darkmode"><i class="fas fa-moon"></i> Dark Mode</a></li>
        <li><a href="#" data-tab="logout"><i class="fas fa-sign-out-alt"></i> Log-Out</a></li>
      </ul>
    </div>
  </div> -->
  

    <!-- [ Pre-loader ] start -->
    <div class="loader-bg fixed inset-0 bg-white dark:bg-themedark-cardbg z-[1034]">
        <div class="loader-track h-[5px] w-full inline-block absolute overflow-hidden top-0">
            <div class="loader-fill w-[300px] h-[5px] bg-primary-500 absolute top-0 left-0 animate-[hitZak_0.6s_ease-in-out_infinite_alternate]"></div>
        </div>
    </div>
    <!-- [ Pre-loader ] End -->
    <!-- [ Sidebar Menu ] start -->
    <?php include '../includes/requester_sidebar.php'; ?>
    <!-- [ Sidebar Menu ] end -->
    <?php include '../includes/header.php'; ?>

   <!-- MAIN CONTENT -->
<div class="pc-container">
    <div class="pc-content">

        <!--page header-->
        <div class="page-header">
            <div class="page-block">
                <div class="page-header-title">
                    <h5 class="mb-0 font-medium">VIEW REQUESTS</h5>
                </div>
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><a href="../requester/dashboard.php">REQUEST FORM</a></li>
                    <li class="breadcrumb-item active">VIEW REQUESTS</li>
                </ul>
            </div>
        </div>

        <!--request-->
        <div class="container">
            <div class="request-list mt-4" id="requestList">

                <?php if ($tickets_result->num_rows > 0): ?>
                    <?php while ($ticket = $tickets_result->fetch_assoc()): ?>

                        <?php 
                            $status = trim($ticket['status']);
                            $status_upper = strtoupper($status);

                            switch ($status_upper) {
                                case 'PENDING':
                                    $badge_class = 'bg-secondary';
                                    break;
                                case 'IN PROGRESS':
                                    $badge_class = 'bg-warning text-dark';
                                    break;
                                case 'RESOLVED':
                                    $badge_class = 'bg-success';
                                    break;
                                default:
                                    $badge_class = 'bg-secondary';
                                    break;
                            }
                        ?>

                        <div class="card mb-3 shadow-sm">
                            <div class="row g-0 p-3 align-items-start">

                                <!--info-->
                                <div class="info-left col-12 col-md-9">

                                    <div class="ticket-info-row">
                                        <i class="fas fa-ticket-alt"></i>
                                        <p><strong>Ticket #:</strong> <?= htmlspecialchars($ticket['ticket_id']) ?></p>
                                    </div>

                                    <div class="ticket-info-row">
                                        <i class="fas fa-door-open"></i>
                                        <p><strong>Room:</strong> <?= htmlspecialchars($ticket['lab_room']) ?></p>
                                    </div>

                                    <div class="ticket-info-row">
                                        <i class="fas fa-file-alt"></i>
                                        <p><strong>Report:</strong> <?= htmlspecialchars($ticket['description']) ?></p>
                                    </div>

                                    <div class="ticket-info-row">
                                        <i class="fas fa-exclamation-circle"></i>
                                        <p><strong>Priority:</strong> <?= htmlspecialchars($ticket['priority']) ?></p>
                                    </div>

                                    <?php if (!empty($ticket['upload_note'])): ?>
                                        <div class="ticket-info-row">
                                            <i class="fas fa-sticky-note"></i>
                                            <p><strong>Note:</strong> <?= htmlspecialchars($ticket['upload_note']) ?></p>
                                        </div>
                                    <?php endif; ?>

                                    <?php if (!empty($ticket['attachment_filename'])): ?>
                                        <div class="ticket-info-row">
                                            <i class="fas fa-paperclip"></i>
                                            <p>
                                                <strong>Attachment:</strong> 
                                                <a href="../uploads/<?= urlencode($ticket['attachment_filename']) ?>" target="_blank">View Attachment</a>
                                            </p>
                                        </div>
                                    <?php else: ?>
                                        <div class="ticket-info-row">
                                            <i class="fas fa-paperclip"></i>
                                            <p><strong>Attachment:</strong> No file uploaded</p>
                                        </div>
                                    <?php endif; ?>

                                    <?php if (!empty($ticket['software_specify'])): ?>
                                        <div class="ticket-info-row">
                                            <i class="fas fa-download"></i>
                                            <p><strong>Software Not Installed:</strong> <?= htmlspecialchars($ticket['software_specify']) ?></p>
                                        </div>
                                    <?php endif; ?>

                                    <?php if (!empty($ticket['other_specify'])): ?>
                                        <div class="ticket-info-row">
                                            <i class="fas fa-question-circle"></i>
                                            <p><strong>Other Issue:</strong> <?= htmlspecialchars($ticket['other_specify']) ?></p>
                                        </div>
                                    <?php endif; ?>

                                </div>
                    
                                <div class="info-right col-12 col-md-3 text-end">
                                    <p class="small text-muted mb-2"><?= date('M d, Y H:i', strtotime($ticket['date_submitted'])) ?></p>
                                    <p>
                                        <strong>Status:</strong> 
                                        <span class="badge <?= $badge_class ?> rounded-3"><?= htmlspecialchars($status) ?></span>
                                    </p>

                                    <?php if ($status_upper === 'PENDING'): ?>
                                        <div class="mt-auto pt-5 text-end">
                                            <button class="btn btn-danger btn-sm delete-request mt-3 align-self-end" data-id="<?= $ticket['ticket_id'] ?>">
                                                <i class="fas fa-trash-alt"></i> Delete
                                            </button>
                                        </div>
                                    <?php endif; ?>
                                </div>
                       

                            </div>
                        </div>

                    <?php endwhile; ?>

                <?php else: ?>
                    <div class="d-flex justify-content-center align-items-center" style="height: 60vh;">
                        <div class="text-center">
                            <i class="fas fa-inbox fa-6x text-muted mb-3"></i>
                            <h4 class="fw-semibold text-secondary">No Tickets Found</h4>
                            <p class="text-muted mb-0">
                                <?php 
                                    if (isset($error_msg)) {
                                        echo "Error: " . htmlspecialchars($error_msg);
                                    } else {
                                        echo "There are currently no submitted tickets.";
                                    }
                                ?>
                            </p>
                        </div>
                    </div>
                <?php endif; ?>

            </div>
        </div>
    

    </div>
</div>

<!-- Required Js -->
 <script src="../assets/js/requester/view_tickets.js"></script>
 <script src="../assets/js/requester/requester.js"></script>
 <script src="../assets/js/plugins/simplebar.min.js"></script>
 <script src="../assets/js/plugins/popper.min.js"></script>
 <script src="../assets/js/icon/custom-icon.js"></script>
 <script src="../assets/js/plugins/feather.min.js"></script>
 <script src="../assets/js/component.js"></script>
 <script src="../assets/js/theme.js"></script>
 <script src="../assets/js/script.js"></script>
 <?php include '../includes/footer.php'; ?>

</body>

</html>
<?php  ?>


