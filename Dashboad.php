<?php
    require_once 'assets/Controllers/AuthController.php';

    $auth = new AuthController();
    $data = $auth->getDashboardStatistics();

?>

<!doctype html>
<html lang="en" data-layout="vertical" data-topbar="light" data-sidebar="dark" data-sidebar-size="lg" data-sidebar-image="none" data-preloader="disable" data-theme="default" data-theme-colors="default">


<!-- Mirrored from themesbrand.com/velzon/html/master/index.php by HTTrack Website Copier/3.x [XR&CO'2014], Mon, 12 Aug 2024 07:44:28 GMT -->
<head>

    <meta charset="utf-8" />
    <title>Dashboard | Administration C.S.P.P.UNILU</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta content="Premium Multipurpose Admin & Dashboard Template" name="description" />
    <meta content="Themesbrand" name="author" />
    <!-- App favicon -->
    <link rel="shortcut icon" href="assets/images/logo_pp.png">

    <!-- jsvectormap css -->
    <link href="assets/libs/jsvectormap/css/jsvectormap.min.css" rel="stylesheet" type="text/css" />

    <!--Swiper slider css-->
    <link href="assets/libs/swiper/swiper-bundle.min.css" rel="stylesheet" type="text/css" />

    <!-- Layout config Js -->
    <script src="assets/js/layout.js"></script>
    <!-- Bootstrap Css -->
    <link href="assets/css/bootstrap.min.css" rel="stylesheet" type="text/css" />
    <!-- Icons Css -->
    <link href="assets/css/icons.min.css" rel="stylesheet" type="text/css" />
    <!-- App Css-->
    <link href="assets/css/app.min.css" rel="stylesheet" type="text/css" />
    <!-- custom Css-->
    <link href="assets/css/custom.min.css" rel="stylesheet" type="text/css" />

</head>

<body>

    <!-- Begin page -->
    <div id="layout-wrapper">

        <header id="page-topbar">
    <div class="layout-width">
        <div class="navbar-header">
            <div class="d-flex">
                <!-- LOGO -->
                <div class="navbar-brand-box horizontal-logo">
                    <a href="index.php" class="logo logo-dark">
                        <span class="logo-sm">
                            <img src="assets/images/logo-sm.png" alt="" height="22">
                        </span>
                        <span class="logo-lg">
                            <img src="assets/images/logo-dark.png" alt="" height="17">
                        </span>
                    </a>

                    <a href="index.php" class="logo logo-light">
                        <span class="logo-sm">
                            <img src="assets/images/logo-sm.png" alt="" height="22">
                        </span>
                        <span class="logo-lg">
                            <img src="assets/images/logo-light.png" alt="" height="17">
                        </span>
                    </a>
                </div>

                <button type="button" class="btn btn-sm px-3 fs-16 header-item vertical-menu-btn topnav-hamburger material-shadow-none" id="topnav-hamburger-icon">
                    <span class="hamburger-icon">
                        <span></span>
                        <span></span>
                        <span></span>
                    </span>
                </button>

                <!-- App Search-->
                <form class="app-search d-none d-md-block">
                    <div class="position-relative">
                        <input type="text" class="form-control" placeholder="Recherche..." autocomplete="off" id="search-options" value="">
                        <span class="mdi mdi-magnify search-widget-icon"></span>
                        <span class="mdi mdi-close-circle search-widget-icon search-widget-icon-close d-none" id="search-close-options"></span>
                    </div>
                    <div class="dropdown-menu dropdown-menu-lg" id="search-dropdown">
                        <div data-simplebar style="max-height: 320px;">
                            <!-- item-->
                            <div class="dropdown-header">
                                <h6 class="text-overflow text-muted mb-0 text-uppercase">Recherches recentes</h6>
                            </div>

                            <div class="dropdown-item bg-transparent text-wrap">
                                <a href="index.php" class="btn btn-soft-secondary btn-sm rounded-pill">how to setup <i class="mdi mdi-magnify ms-1"></i></a>
                                <a href="index.php" class="btn btn-soft-secondary btn-sm rounded-pill">buttons <i class="mdi mdi-magnify ms-1"></i></a>
                            </div>
                            <!-- item-->
                            <div class="dropdown-header mt-2">
                                <h6 class="text-overflow text-muted mb-1 text-uppercase">Pages</h6>
                            </div>

                            <!-- item-->
                            <a href="javascript:void(0);" class="dropdown-item notify-item">
                                <i class="ri-bubble-chart-line align-middle fs-18 text-muted me-2"></i>
                                <span>Analytics Dashboard</span>
                            </a>

                            <!-- item-->
                            <a href="javascript:void(0);" class="dropdown-item notify-item">
                                <i class="ri-lifebuoy-line align-middle fs-18 text-muted me-2"></i>
                                <span>Help Center</span>
                            </a>

                            <!-- item-->
                            <a href="javascript:void(0);" class="dropdown-item notify-item">
                                <i class="ri-user-settings-line align-middle fs-18 text-muted me-2"></i>
                                <span>My account settings</span>
                            </a>

                            <!-- item-->
                            <div class="dropdown-header mt-2">
                                <h6 class="text-overflow text-muted mb-2 text-uppercase">Membres</h6>
                            </div>

                            <div class="notification-list">
                                <!-- item -->
                                <a href="javascript:void(0);" class="dropdown-item notify-item py-2">
                                    <div class="d-flex">
                                        <img src="assets/images/users/avatar-2.jpg" class="me-3 rounded-circle avatar-xs" alt="user-pic">
                                        <div class="flex-grow-1">
                                            <h6 class="m-0">Angela Bernier</h6>
                                            <span class="fs-11 mb-0 text-muted">Manager</span>
                                        </div>
                                    </div>
                                </a>
                                <!-- item -->
                                <a href="javascript:void(0);" class="dropdown-item notify-item py-2">
                                    <div class="d-flex">
                                        <img src="assets/images/users/avatar-3.jpg" class="me-3 rounded-circle avatar-xs" alt="user-pic">
                                        <div class="flex-grow-1">
                                            <h6 class="m-0">David Grasso</h6>
                                            <span class="fs-11 mb-0 text-muted">Web Designer</span>
                                        </div>
                                    </div>
                                </a>
                                <!-- item -->
                                <a href="javascript:void(0);" class="dropdown-item notify-item py-2">
                                    <div class="d-flex">
                                        <img src="assets/images/users/avatar-5.jpg" class="me-3 rounded-circle avatar-xs" alt="user-pic">
                                        <div class="flex-grow-1">
                                            <h6 class="m-0">Mike Bunch</h6>
                                            <span class="fs-11 mb-0 text-muted">React Developer</span>
                                        </div>
                                    </div>
                                </a>
                            </div>
                        </div>

                        <div class="text-center pt-3 pb-1">
                            <a href="pages-search-results.php" class="btn btn-primary btn-sm">View All Results <i class="ri-arrow-right-line ms-1"></i></a>
                        </div>
                    </div>
                </form>
            </div>

            <div class="d-flex align-items-center">

                <div class="dropdown d-md-none topbar-head-dropdown header-item">
                    <button type="button" class="btn btn-icon btn-topbar material-shadow-none btn-ghost-secondary rounded-circle" id="page-header-search-dropdown" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        <i class="bx bx-search fs-22"></i>
                    </button>
                    <div class="dropdown-menu dropdown-menu-lg dropdown-menu-end p-0" aria-labelledby="page-header-search-dropdown">
                        <form class="p-3">
                            <div class="form-group m-0">
                                <div class="input-group">
                                    <input type="text" class="form-control" placeholder="Recherche ..." aria-label="Recipient's username">
                                    <button class="btn btn-primary" type="submit"><i class="mdi mdi-magnify"></i></button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>

                  

                <div class="ms-1 header-item d-none d-sm-flex">
                    <button type="button" class="btn btn-icon btn-topbar material-shadow-none btn-ghost-secondary rounded-circle" data-toggle="fullscreen">
                        <i class='bx bx-fullscreen fs-22'></i>
                    </button>
                </div>

                <div class="ms-1 header-item d-none d-sm-flex">
                    <button type="button" class="btn btn-icon btn-topbar material-shadow-none btn-ghost-secondary rounded-circle light-dark-mode">
                        <i class='bx bx-moon fs-22'></i>
                    </button>
                </div>



                <div class="dropdown ms-sm-3 header-item topbar-user">
                    <button type="button" class="btn material-shadow-none" id="page-header-user-dropdown" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        <span class="d-flex align-items-center">
                            <img class="rounded-circle header-profile-user" src="assets/images/logo_pp2.png" alt="Header Avatar">
                            <span class="text-start ms-xl-2">
                                <span class="d-none d-xl-inline-block ms-1 fw-medium user-name-text"><?php echo $_SESSION['username'] ?></span>
                                <span class="d-block fs-14 sidebar-user-name-sub-text"><i class="ri ri-circle-fill fs-10 text-success align-baseline"></i> <span class="align-middle">En ligne</span></span>
                            </span>
                        </span>
                    </button>
                    <div class="dropdown-menu dropdown-menu-end">
                        <!-- item-->
                        <h6 class="dropdown-header">Administration</h6>
                        <a class="dropdown-item" href="#"><i class="mdi mdi-account-circle text-muted fs-16 align-middle me-1"></i> <span class="align-middle">Profil</span></a>
                        <a class="dropdown-item" href="#"><i class="mdi mdi-logout text-muted fs-16 align-middle me-1"></i> <span class="align-middle" data-key="t-logout">Deconnexion</span></a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</header>

<!-- removeNotificationModal -->
<div id="removeNotificationModal" class="modal fade zoomIn" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close" id="NotificationModalbtn-close"></button>
            </div>
            <div class="modal-body">
                <div class="mt-2 text-center">
                    <lord-icon src="https://cdn.lordicon.com/gsqxdxog.json" trigger="loop" colors="primary:#f7b84b,secondary:#f06548" style="width:100px;height:100px"></lord-icon>
                    <div class="mt-4 pt-2 fs-15 mx-4 mx-sm-5">
                        <h4>Are you sure ?</h4>
                        <p class="text-muted mx-4 mb-0">Are you sure you want to remove this Notification ?</p>
                    </div>
                </div>
                <div class="d-flex gap-2 justify-content-center mt-4 mb-2">
                    <button type="button" class="btn w-sm btn-light" data-bs-dismiss="modal">Close</button>
                    <button type="button" class="btn w-sm btn-danger" id="delete-notification">Yes, Delete It!</button>
                </div>
            </div>

        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->
        <!-- ========== App Menu ========== -->
        <div class="app-menu navbar-menu">
            <!-- LOGO -->
            <div class="navbar-brand-box">
                <!-- Dark Logo-->
                <a href="index.php" class="logo logo-dark">
                    <span class="logo-sm">
                        <img src="assets/images/logo_pp2.png" alt="" height="22">
                    </span>
                    <span class="logo-lg">
                        <img src="assets/images/logo_pp2.png" alt="" height="17">
                    </span>
                </a>
                <!-- Light Logo-->
                <a href="index.php" class="logo logo-light">
                    <span class="logo-sm">
                        <img src="assets/images/logo_pp2.png" alt="" height="22">
                    </span>
                    <span class="logo-lg">
                        <img src="assets/images/logo_pp2.png" alt="" height="17">
                    </span>
                </a>
                <button type="button" class="btn btn-sm p-0 fs-20 header-item float-end btn-vertical-sm-hover" id="vertical-hover">
                    <i class="ri-record-circle-line"></i>
                </button>
            </div>
    
            <div class="dropdown sidebar-user m-1 rounded">
                <button type="button" class="btn material-shadow-none" id="page-header-user-dropdown" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    <span class="d-flex align-items-center gap-2">
                        <img class="rounded header-profile-user" src="assets/images/logo_pp2.png" alt="Header Avatar">
                        <span class="text-start">
                            <span class="d-block fw-medium sidebar-user-name-text"><?php echo $_SESSION['username'] ?></span>
                            <span class="d-block fs-14 sidebar-user-name-sub-text"><i class="ri ri-circle-fill fs-10 text-success align-baseline"></i> <span class="align-middle">En ligne</span></span>
                        </span>
                    </span>
                </button>
                <div class="dropdown-menu dropdown-menu-end">
                    <!-- item-->
                    <h6 class="dropdown-header">Bonjour <?php echo $_SESSION['username'] ?></h6>
                    <a class="dropdown-item" href="#"><i class="mdi mdi-account-circle text-muted fs-16 align-middle me-1"></i> <span class="align-middle">Profile</span></a>
                    <a class="dropdown-item" href="#"><i class="mdi mdi-logout text-muted fs-16 align-middle me-1"></i> <span class="align-middle" data-key="t-logout">Deconnexion</span></a>
                </div>
            </div>
            <div id="scrollbar">
                <div class="container-fluid">


                    <div id="two-column-menu">
                    </div>
                    <ul class="navbar-nav" id="navbar-nav">
                        <li class="menu-title"><span data-key="t-menu">Menu</span></li>
                        <li class="nav-item">
                            <a class="nav-link menu-link" href="#sidebarDashboards" data-bs-toggle="collapse" role="button" aria-expanded="false" aria-controls="sidebarDashboards">
                                <i class="ri-dashboard-2-line"></i> <span data-key="t-dashboards">Dashboards</span>
                            </a>
                            <div class="collapse menu-dropdown" id="sidebarDashboards">
                                <ul class="nav nav-sm flex-column">
                                   
                                    <li class="nav-item">
                                        <a href="#" class="nav-link" data-key="t-ecommerce"> Analyses </a>
                                    </li>
                                    <li class="nav-item">
                                        <a href="./assets/Paiements/Paiement.php" class="nav-link" data-key="t-ecommerce"> Paiement </a>
                                    </li>
                                    <li class="nav-item">
                                        <a href="./assets/Paiements/historique_paiement.php" class="nav-link" data-key="t-ecommerce"> Rapports </a>
                                    </li>
                                   
                                </ul>
                            </div>
                        </li> <!-- end Dashboard Menu -->
                    

                        

                       
                    </ul>
                </div>
                <!-- Sidebar -->
            </div>

            <div class="sidebar-background"></div>
        </div>
        <!-- Left Sidebar End -->
        <!-- Vertical Overlay-->
        <div class="vertical-overlay"></div>

        <!-- ============================================================== -->
        <!-- Start right Content here -->
        <!-- ============================================================== -->
        <div class="main-content">

            <div class="page-content">
                <div class="container-fluid">

                    <div class="row">
                        <div class="col">

                            <div class="h-100">
                                <div class="row mb-3 pb-1">
                                    <div class="col-12">
                                        <div class="d-flex align-items-lg-center flex-lg-row flex-column">
                                            <div class="flex-grow-1">
                                                <h4 class="fs-16 mb-1">Bonjour <?php echo $_SESSION['username'] ?></h4>
                                                <p class="text-muted mb-0">Voici vos analyses du jour.</p>
                                            </div>
                                            <div class="mt-3 mt-lg-0">
                                                <form action="javascript:void(0);">
                                                    <div class="row g-3 mb-0 align-items-center">
                                                       
                                                        <!--end col-->
                                                        <div class="col-auto">
                                                            <button type="button" class="btn btn-soft-info btn-icon waves-effect material-shadow-none waves-light layout-rightside-btn"><i class="ri-pulse-line"></i></button>
                                                        </div>
                                                        <!--end col-->
                                                    </div>
                                                    <!--end row-->
                                                </form>
                                            </div>
                                        </div><!-- end card header -->
                                    </div>
                                    <!--end col-->
                                </div>
                                <!--end row-->

                                <div class="row">
                                    <div class="col-xl-3 col-md-6">
                                        <!-- card -->
                                        <div class="card card-animate">
                                            <div class="card-body">
                                                <div class="d-flex align-items-center">
                                                    <div class="flex-grow-1 overflow-hidden">
                                                        <p class="text-uppercase fw-medium text-muted text-truncate mb-0"> Total de(s) conecté(s)</p>
                                                    </div>
                                                    <div class="flex-shrink-0">
                                                   
                                                    </div>
                                                </div>
                                                <div class="d-flex align-items-end justify-content-between mt-4">
                                                <div>
                                                    <h4 class="fs-22 fw-semibold ff-secondary mb-4">
                                                        Connecté :
                                                    </h4>
                                                    <ul>
                                                        <?php
                                                            if (isset($_SESSION['username'])) {
                                                                echo "<li>" . htmlspecialchars($_SESSION['username']) . "</li>";
                                                            } else {
                                                                echo "<li>Aucun utilisateur connecté</li>";
                                                            }
                                                        ?>
                                                    </ul>
                                                </div>
                                                    <div class="avatar-sm flex-shrink-0">
                                                        <span class="avatar-title bg-success-subtle rounded fs-3">
                                                            <i class="bx bx-user text-success"></i>
                                                        </span>
                                                    </div>
                                                </div>
                                            </div><!-- end card body -->
                                        </div><!-- end card -->
                                    </div><!-- end col -->

                                    <div class="col-xl-3 col-md-6">
                                        <!-- card -->
                                        <div class="card card-animate">
                                            <div class="card-body">
                                                <div class="d-flex align-items-center">
                                                    <div class="flex-grow-1 overflow-hidden">
                                                        <p class="text-uppercase fw-medium text-muted text-truncate mb-0">État des paiements</p>
                                                    </div>
                                                   
                                                </div>
                                                <div class="d-flex align-items-end justify-content-between mt-4">
                                                    <div>
                                                        <h4 class="fs-22 fw-semibold ff-secondary mb-4">
                                                            <?php echo $data['currentPayments']; ?> paiements
                                                        </h4>
                                                        <!--<ul>
                                                            <?php foreach ($paymentsByClass as $class => $total): ?>
                                                                <li><?php echo htmlspecialchars($class); ?> : <?php echo $total; ?> paiements</li>
                                                            <?php endforeach; ?>
                                                        </ul> -->
                                                    </div>
                                                    <div class="avatar-sm flex-shrink-0">
                                                        <span class="avatar-title bg-info-subtle rounded fs-3">
                                                            <i class="bx bx-credit-card text-info"></i>
                                                        </span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div><!-- end card -->
                                    </div><!-- end col -->

                                    <div class="col-xl-3 col-md-6">
                                        <!-- card -->
                                        <div class="card card-animate">
                                            <div class="card-body">
                                                <div class="d-flex align-items-center">
                                                    <div class="flex-grow-1 overflow-hidden">
                                                        <p class="text-uppercase fw-medium text-muted text-truncate mb-0">Heure</p>
                                                    </div>
                                                    <div class="flex-shrink-0">
                                                        
                                                    </div>
                                                </div>
                                                <div class="d-flex align-items-end justify-content-between mt-4">
                                                    <div>
                                                        <h4 id="real-time-clock" class="fs-22 fw-semibold ff-secondary mb-4"></h4>
                                                            <script>
                                                                function updateClock() {
                                                                    var now = new Date();
                                                                    var hours = String(now.getHours()).padStart(2, '0');
                                                                    var minutes = String(now.getMinutes()).padStart(2, '0');
                                                                    var seconds = String(now.getSeconds()).padStart(2, '0');
                                                                    document.getElementById('real-time-clock').textContent = hours + ':' + minutes + ':' + seconds;
                                                                }
                                                                setInterval(updateClock, 1000);
                                                            </script>
                                                        
                                                    </div>
                                                    <div class="avatar-sm flex-shrink-0">
                                                        <span class="avatar-title bg-warning-subtle rounded fs-3">
                                                            <i class="bx bx-time text-warning"></i>
                                                        </span>
                                                    </div>
                                                </div>
                                            </div><!-- end card body -->
                                        </div><!-- end card -->
                                    </div><!-- end col -->

                                    <div class="col-xl-3 col-md-6">
                                        <!-- card -->
                                        <div class="card card-animate">
                                        <div class="card-body">
                                            <div class="d-flex align-items-center">
                                                <div class="flex-grow-1 overflow-hidden">
                                                    <p class="text-uppercase fw-medium text-muted text-truncate mb-0"> Date</p>
                                                </div>
                                                <div class="flex-shrink-0">
                                                    <!-- Rien ici -->
                                                </div>
                                            </div>

                                            <div class="d-flex align-items-end justify-content-between mt-4">
                                                <div>
                                                    <h4 id="real-time-date" class="fs-22 fw-semibold ff-secondary mb-4"></h4>
                                                    <script>
                                                        function updateDate() {
                                                            var now = new Date();
                                                            var options = { year: 'numeric', month: 'long', day: 'numeric' };
                                                            document.getElementById('real-time-date').textContent = now.toLocaleDateString('fr-FR', options);
                                                        }
                                                        updateDate();
                                                        setInterval(updateDate, 60000); 
                                                    </script>
                                                </div>
                                                <div class="avatar-sm flex-shrink-0">
                                                    <span class="avatar-title bg-primary-subtle rounded fs-3" id="calendar-icon" style="cursor:pointer;">
                                                        <i class="bx bx-calendar text-primary"></i>
                                                    </span>
                                                </div>
                                            </div>

                                            <!-- Calendrier caché au départ -->
                                            <div id="calendar-popup" style="display:none; position:absolute; top: 100px; right: 20px; background:white; border:1px solid #ddd; padding:15px; border-radius:10px; box-shadow: 0 0 10px rgba(0,0,0,0.1); z-index:999;">
                                                <input type="date" class="form-control">
                                            </div>

                                        </div><!-- end card body -->

                                        <script>
                                            const calendarIcon = document.getElementById('calendar-icon');
                                            const calendarPopup = document.getElementById('calendar-popup');

                                            calendarIcon.addEventListener('click', () => {
                                                if (calendarPopup.style.display === 'none') {
                                                    calendarPopup.style.display = 'block';
                                                } else {
                                                    calendarPopup.style.display = 'none';
                                                }
                                            });

                                            // Fermer le calendrier si on clique en dehors
                                            document.addEventListener('click', function(event) {
                                                if (!calendarPopup.contains(event.target) && !calendarIcon.contains(event.target)) {
                                                    calendarPopup.style.display = 'none';
                                                }
                                            });
                                        </script>

                                        </div><!-- end card -->
                                    </div><!-- end col -->
                                </div> <!-- end row-->

                                <div class="row">
                                    <div class="col-xl-8">
                                        <div class="card">
                                            <div class="card-header border-0 align-items-center d-flex">
                                                <h4 class="card-title mb-0 flex-grow-1">Revenu des paiements</h4>
                                                
                                            </div><!-- end card header -->

                                            <div class="card-header p-0 border-0 bg-light-subtle">
                                                <div class="row g-0 text-center">
                                                    <div class="col-6 col-sm-3">
                                                        <?php
                                                            // Connexion MySQL
                                                            $host = 'localhost';
                                                            $db   = 'school';
                                                            $user = 'root'; 
                                                            $pass = '';

                                                            $mysqli = new mysqli($host, $user, $pass, $db);

                                                            // Vérifier si la connexion est bonne
                                                            if ($mysqli->connect_error) {
                                                                die('Erreur de connexion (' . $mysqli->connect_errno . ') ' . $mysqli->connect_error);
                                                            }
                                                            // Requête pour compter les élèves selon leur nom
                                                            $query = "SELECT COUNT(nom_eleve) as total FROM paiement"; // ON COMPTE LES NOMS

                                                            $result = $mysqli->query($query);

                                                            $currentPayments = 0;
                                                            if ($result) {
                                                                $row = $result->fetch_assoc();
                                                                $currentPayments = $row['total'];
                                                            }
                                                             
                                                            $tauxJour = 2886.15;

                                                            // Calcul du pourcentage
                                                            $resultat = ($currentPayments * 100) / $tauxJour;

                                                            // Choix de la couleur selon le résultat
                                                            $colorClass = ($resultat < 50) ? 'text-danger' : 'text-success';

                                                            // Choix de la description selon le résultat
                                                            $baisse = ($resultat < 50) ? 'Baisse' : 'Hausse';
                                                            
                                                        ?>
                                                        <div class="p-3 border border-dashed border-start-0">
                                                        <h5 class="mb-1 <?= $colorClass ?>">
                                                            <span class="counter-value" data-target="<?= number_format($resultat, 2) ?>">0</span>%
                                                        </h5>
                                                            <p class="text-muted mb-0"><?php echo $baisse ?></p>
                                                        </div>
                                                    </div>
                                                    <!--end col-->
                                                    <div class="col-6 col-sm-3">
                                                        <?php
                                                            if (date('d') == 5) {
                                                                $currentDate = date('d/m/Y'); // Date du jour
                                                                echo '<div class="p-3 border border-dashed border-start-0">';
                                                                echo '<h5 class="mb-1 text-sucess">Envoyer notification</h5>';
                                                                echo '<p class="text-muted mb-0">' . $currentDate . '</p>';
                                                                echo '</div>';
                                                            } else {
                                                                $currentDate = date('d/m/Y');
                                                                echo '<div class="p-3 border border-dashed border-start-0">';
                                                                echo '<h5 class="mb-1 text-danger">Aucune notification</h5>';
                                                                echo '<p class="text-muted mb-0">'. $currentDate . '</p>';
                                                                echo '</div>';
                                                            }
                                                        ?>
                                                    </div>
                                                    <!--end col-->
                                                    <div class="col-6 col-sm-3">
                                                        <div class="p-3 border border-dashed border-start-0">
                                                            <h5 class="mb-1"><span class="counter-value" data-target="367">0</span></h5>
                                                            <p class="text-muted mb-0">Remboursements</p>
                                                        </div>
                                                    </div>
                                                    <!--end col-->
                                                    <div class="col-6 col-sm-3">
                                                        <div class="p-3 border border-dashed border-start-0 border-end-0">
                                                            <h5 class="mb-1 text-success">
                                                                <span id="taux-jour" class="counter-value" data-target="0">0</span>CDF
                                                            </h5>
                                                            <p class="text-muted mb-0">Taux du jour</p>
                                                        </div>
                                                    </div>

                                                    <!--end col-->
                                                </div>
                                            </div><!-- end card header -->

                                            <div class="card-body p-0 pb-2">
                                            <div class="card-body p-0 pb-2">
                                                <?php
                                                    // Connexion MySQL
                                                    $host = 'localhost';
                                                    $db   = 'school';
                                                    $user = 'root'; 
                                                    $pass = '';

                                                    $mysqli = new mysqli($host, $user, $pass, $db);

                                                    // Vérifier si la connexion est bonne
                                                    if ($mysqli->connect_error) {
                                                        die('Erreur de connexion (' . $mysqli->connect_errno . ') ' . $mysqli->connect_error);
                                                    }

                                                    // Requête pour récupérer les montants de paiement
                                                    $result = $mysqli->query("SELECT montant_payer, date_paiement FROM paiement");

                                                    $series = [];
                                                    $months = [];

                                                    // Tableau pour convertir mois numérique en français
                                                    $mois_francais = [
                                                        '01' => 'Janvier', '02' => 'Février', '03' => 'Mars', '04' => 'Avril',
                                                        '05' => 'Mai', '06' => 'Juin', '07' => 'Juillet', '08' => 'Août',
                                                        '09' => 'Septembre', '10' => 'Octobre', '11' => 'Novembre', '12' => 'Décembre'
                                                    ];

                                                    while ($row = $result->fetch_assoc()) {
                                                        $date = $row['date_paiement'];
                                                        $mois_numero = date('m', strtotime($date)); // Extraire le mois
                                                        $mois_nom = $mois_francais[$mois_numero] ?? 'Inconnu'; // Chercher le mois français

                                                        $months[] = $mois_nom; // X-axis
                                                        $series[] = (float) $row['montant_payer']; // Y-axis
                                                    }

                                                    // Encoder en JSON pour JavaScript
                                                    $months_json = json_encode($months);
                                                    $series_json = json_encode($series);
                                                ?>
                                                    <div class="w-100">
                                                        <div id="customer_impression_charts" class="apex-charts" dir="ltr"></div>

                                                        <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
                                                        <script>
                                                        document.addEventListener("DOMContentLoaded", function () {
                                                            var options = {
                                                                chart: {
                                                                    type: 'bar',
                                                                    height: 350
                                                                },
                                                                series: [{
                                                                    name: 'Paiements',
                                                                    data: <?php echo $series_json; ?>
                                                                }],
                                                                xaxis: {
                                                                    categories: <?php echo $months_json; ?>,
                                                                    labels: {
                                                                        rotate: -45, 
                                                                        style: {
                                                                            fontSize: '12px',
                                                                            colors: '#6c757d'
                                                                        }
                                                                    }
                                                                },
                                                                colors: ['#34c38f'],
                                                            };

                                                            var chart = new ApexCharts(document.querySelector("#customer_impression_charts"), options);
                                                            chart.render();
                                                        });
                                                        </script>
                                                    </div>
                                                </div>

                                            </div><!-- end card body -->
                                        </div><!-- end card -->
                                    </div><!-- end col -->

                                    <div class="col-xl-4">
                                        <!-- card -->
                                        <div class="card card-height-100">
                                            <div class="card-header align-items-center d-flex">
                                                <h4 class="card-title mb-0 flex-grow-1">Sales by Locations</h4>
                                                <div class="flex-shrink-0">
                                                   
                                                </div>
                                            </div><!-- end card header -->

                                            <!-- card body -->
                                            <?php
                                                // Connexion MySQL
                                                $host = 'localhost';
                                                $db   = 'school';
                                                $user = 'root'; 
                                                $pass = '';

                                                $mysqli = new mysqli($host, $user, $pass, $db);

                                                if ($mysqli->connect_error) {
                                                    die('Erreur de connexion (' . $mysqli->connect_errno . ') ' . $mysqli->connect_error);
                                                }

                                                // Récupérer les paiements par classe
                                                $result = $mysqli->query("
                                                    SELECT classe_eleve, 
                                                        COUNT(CASE WHEN montant_payer > 0 THEN 1 END) AS payes, 
                                                        COUNT(*) AS total
                                                    FROM paiement
                                                    GROUP BY classe_eleve
                                                ");

                                                $classes = [];
                                                $paiements = [];

                                                while ($row = $result->fetch_assoc()) {
                                                    $classes[] = $row['classe_eleve'];
                                                    $pourcentage = $row['total'] > 0 ? round($row['payes'] + $row['payes']): 0;
                                                    $paiements[] = $pourcentage;
                                                }
                                                ?>

                                                <div class="card-body">
                                                    <h4 class="card-title mb-3">Avancement des paiements par classe</h4>

                                                    <?php foreach ($classes as $index => $classe): 
                                                        $pourcentage = $paiements[$index];
                                                        // Définir une couleur différente selon le pourcentage
                                                        if ($pourcentage >= 80) {
                                                            $barClass = 'bg-success';
                                                        } elseif ($pourcentage >= 50) {
                                                            $barClass = 'bg-info';
                                                        } elseif ($pourcentage >= 30) {
                                                            $barClass = 'bg-warning';
                                                        } else {
                                                            $barClass = 'bg-danger';
                                                        }
                                                    ?>
                                                        <div class="mb-3">
                                                            <div class="d-flex justify-content-between">
                                                                <span><strong><?php echo htmlspecialchars($classe); ?></strong></span>
                                                                <span><?php echo $pourcentage; ?>%</span>
                                                            </div>
                                                            <div class="progress" style="height: 8px;">
                                                                <div class="progress-bar progress-bar-striped <?php echo $barClass; ?>" role="progressbar" 
                                                                    style="width: <?php echo $pourcentage; ?>%;" aria-valuenow="<?php echo $pourcentage; ?>" 
                                                                    aria-valuemin="0" aria-valuemax="100"></div>
                                                            </div>
                                                        </div>
                                                    <?php endforeach; ?>
                                                </div>

                                              
                                            <!-- end card body -->
                                        </div>
                                        <!-- end card -->
                                    </div>
                                    <!-- end col -->
                                </div>

                                <div class="row">
                                <div class="col-xl-4">
                                    <div class="card card-height-100">
                                        <div class="card-header align-items-center d-flex">
                                            <h4 class="card-title mb-0 flex-grow-1">Répartition des paiements par mois</h4>
                                            <div class="flex-shrink-0">
                                                <div class="dropdown card-header-dropdown">
                                                    <a class="text-reset dropdown-btn" href="#" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                        <span class="text-muted">Options<i class="mdi mdi-chevron-down ms-1"></i></span>
                                                    </a>
                                                    <div class="dropdown-menu dropdown-menu-end">
                                                        <a class="dropdown-item" href="#" id="export-data">Télécharger</a>
                                                        <input type="file" id="import-file" style="display:none" accept=".csv">
                                                        <a class="dropdown-item" href="#" id="import-data">Importer</a>

                                                    </div>
                                                </div>
                                            </div>
                                        </div><!-- end card header -->

                                        <div class="card-body">
                                            <?php
                                                // Connexion MySQL
                                                $host = 'localhost';
                                                $db   = 'school';
                                                $user = 'root'; 
                                                $pass = '';

                                                $mysqli = new mysqli($host, $user, $pass, $db);

                                                if ($mysqli->connect_error) {
                                                    die('Erreur de connexion (' . $mysqli->connect_errno . ') ' . $mysqli->connect_error);
                                                }

                                                // Charger toutes les données
                                                $result = $mysqli->query("SELECT id, nom_eleve, classe_eleve, montant_payer, motif_paiement, date_paiement, payment_status FROM paiement");

                                                $mois_fr = [
                                                    '01' => 'Janvier',
                                                    '02' => 'Février',
                                                    '03' => 'Mars',
                                                    '04' => 'Avril',
                                                    '05' => 'Mai',
                                                    '06' => 'Juin',
                                                    '07' => 'Juillet',
                                                    '08' => 'Août',
                                                    '09' => 'Septembre',
                                                    '10' => 'Octobre',
                                                    '11' => 'Novembre',
                                                    '12' => 'Décembre'
                                                ];

                                                $payments_by_month = [];
                                                $full_payments = [];

                                                while ($row = $result->fetch_assoc()) {
                                                    // Paiements par mois
                                                    $mois = date('m', strtotime($row['date_paiement']));
                                                    $mois_text = $mois_fr[$mois];

                                                    if (!isset($payments_by_month[$mois_text])) {
                                                        $payments_by_month[$mois_text] = 0;
                                                    }
                                                    $payments_by_month[$mois_text] += (float) $row['montant_payer'];

                                                    // Paiements complets
                                                    $full_payments[] = $row;
                                                }

                                                // Préparer pour le JavaScript
                                                $categories_json = json_encode(array_keys($payments_by_month), JSON_UNESCAPED_UNICODE);
                                                $series_json = json_encode(array_values($payments_by_month));
                                                $full_payments_json = json_encode($full_payments, JSON_UNESCAPED_UNICODE);
                                            ?>

                                            <!-- Graphique -->
                                            <div id="store-visits-source" class="apex-charts" dir="ltr"></div>

                                            <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
                                            <script>
                                                document.addEventListener("DOMContentLoaded", function () {
                                                    var options = {
                                                        chart: {
                                                            type: 'donut',
                                                            height: 350
                                                        },
                                                        labels: <?php echo $categories_json; ?>,
                                                        series: <?php echo $series_json; ?>,
                                                        colors: ['#34c38f', '#50a5f1', '#f1b44c', '#f46a6a', '#556ee6', '#34c38f', '#50a5f1', '#f1b44c', '#f46a6a', '#556ee6', '#34c38f', '#50a5f1'],
                                                        legend: {
                                                            position: 'bottom'
                                                        }
                                                    };

                                                    var chart = new ApexCharts(document.querySelector("#store-visits-source"), options);
                                                    chart.render();

                                        

                                                    // Bouton Télécharger en CSV avec style amélioré
                                                    document.getElementById('export-data').addEventListener('click', function () {
                                                        var payments = <?php echo $full_payments_json; ?>;

                                                        // Titre avec des libellés lisibles
                                                        var csv = 'ID,Nom de l\'élève,Classe,Montant payé,Motif du paiement,Date de paiement,Statut du paiement\n';

                                                        payments.forEach(function(row) {
                                                            csv += [
                                                                row.id,
                                                                '"' + capitalizeWords(row.nom_eleve) + '"', // Capitaliser les noms
                                                                '"' + capitalizeWords(row.classe_eleve) + '"', // Capitaliser la classe
                                                                parseFloat(row.montant_payer).toFixed(2), // 2 chiffres après la virgule
                                                                '"' + capitalizeWords(row.motif_paiement) + '"',
                                                                formatDate(row.date_paiement), // Formater la date proprement
                                                                capitalizeWords(row.payement_status) // Exemple de capitalisation
                                                            ].join(';') + '\n'; // Séparateur ; pour éviter les problèmes avec des virgules
                                                        });

                                                        var hiddenElement = document.createElement('a');
                                                        hiddenElement.href = 'data:text/csv;charset=utf-8,' + encodeURIComponent(csv);
                                                        hiddenElement.target = '_blank';
                                                        hiddenElement.download = 'paiements_complet.csv';
                                                        hiddenElement.click();
                                                    });

                                                    // Fonction pour capitaliser chaque mot
                                                    function capitalizeWords(str) {
                                                        if (!str) return '';
                                                        return str.toLowerCase().replace(/\b\w/g, function(l) { return l.toUpperCase(); });
                                                    }

                                                    // Fonction pour reformater la date en format plus joli (ex: 06 Mai 2025)
                                                    function formatDate(dateStr) {
                                                        const mois_fr = ['Janvier', 'Février', 'Mars', 'Avril', 'Mai', 'Juin', 'Juillet', 'Août', 'Septembre', 'Octobre', 'Novembre', 'Décembre'];
                                                        const date = new Date(dateStr);
                                                        const day = String(date.getDate()).padStart(2, '0');
                                                        const month = mois_fr[date.getMonth()];
                                                        const year = date.getFullYear();
                                                        return `${day} ${month} ${year}`;
                                                    }


                                                    // Bouton Importer
                                                    document.getElementById('import-data').addEventListener('click', function () {
                                                        document.getElementById('import-file').click();
                                                    });

                                                    document.getElementById('import-file').addEventListener('change', function (e) {
                                                        var file = e.target.files[0];
                                                        if (!file) return;

                                                        var reader = new FileReader();
                                                        reader.onload = function (e) {
                                                            var content = e.target.result;
                                                            console.log('Contenu importé :', content);
                                                            // Tu peux ici ajouter du code pour parser et recharger les données
                                                        };
                                                        reader.readAsText(file);
                                                    });
                                                });
                                            </script>
                                        </div> <!-- end card body -->

                                    </div> <!-- .card-->
                                </div> <!-- .col-->


                                    <div class="col-xl-8">
                                        <div class="card">
                                            <div class="card-header align-items-center d-flex">
                                                <h4 class="card-title mb-0 flex-grow-1">Recent Orders</h4>
                                                <div class="flex-shrink-0">
                                                    
                                                </div>
                                            </div><!-- end card header -->
                                            <div class="card-body">
                                                <div class="table-responsive table-card">
                                                    <table class="table table-borderless table-centered align-middle table-nowrap mb-0">
                                                        <thead class="text-muted table-light">
                                                            <tr>
                                                                <th scope="col">Order ID</th>
                                                                <th scope="col">Customer</th>
                                                                <th scope="col">Product</th>
                                                                <th scope="col">Amount</th>
                                                                <th scope="col">Vendor</th>
                                                                <th scope="col">Status</th>
                                                                <th scope="col">Rating</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            <tr>
                                                                <td>
                                                                    <a href="apps-ecommerce-order-details.php" class="fw-medium link-primary">#VZ2112</a>
                                                                </td>
                                                                <td>
                                                                    <div class="d-flex align-items-center">
                                                                        <div class="flex-shrink-0 me-2">
                                                                            <img src="assets/images/users/avatar-1.jpg" alt="" class="avatar-xs rounded-circle material-shadow" />
                                                                        </div>
                                                                        <div class="flex-grow-1">Alex Smith</div>
                                                                    </div>
                                                                </td>
                                                                <td>Clothes</td>
                                                                <td>
                                                                    <span class="text-success">$109.00</span>
                                                                </td>
                                                                <td>Zoetic Fashion</td>
                                                                <td>
                                                                    <span class="badge bg-success-subtle text-success">Paid</span>
                                                                </td>
                                                                <td>
                                                                    <h5 class="fs-14 fw-medium mb-0">5.0<span class="text-muted fs-11 ms-1">(61 votes)</span></h5>
                                                                </td>
                                                            </tr><!-- end tr -->
                                                            <tr>
                                                                <td>
                                                                    <a href="apps-ecommerce-order-details.php" class="fw-medium link-primary">#VZ2111</a>
                                                                </td>
                                                                <td>
                                                                    <div class="d-flex align-items-center">
                                                                        <div class="flex-shrink-0 me-2">
                                                                            <img src="assets/images/users/avatar-2.jpg" alt="" class="avatar-xs rounded-circle material-shadow" />
                                                                        </div>
                                                                        <div class="flex-grow-1">Jansh Brown</div>
                                                                    </div>
                                                                </td>
                                                                <td>Kitchen Storage</td>
                                                                <td>
                                                                    <span class="text-success">$149.00</span>
                                                                </td>
                                                                <td>Micro Design</td>
                                                                <td>
                                                                    <span class="badge bg-warning-subtle text-warning">Pending</span>
                                                                </td>
                                                                <td>
                                                                    <h5 class="fs-14 fw-medium mb-0">4.5<span class="text-muted fs-11 ms-1">(61 votes)</span></h5>
                                                                </td>
                                                            </tr><!-- end tr -->
                                                            <tr>
                                                                <td>
                                                                    <a href="apps-ecommerce-order-details.php" class="fw-medium link-primary">#VZ2109</a>
                                                                </td>
                                                                <td>
                                                                    <div class="d-flex align-items-center">
                                                                        <div class="flex-shrink-0 me-2">
                                                                            <img src="assets/images/users/avatar-3.jpg" alt="" class="avatar-xs rounded-circle material-shadow" />
                                                                        </div>
                                                                        <div class="flex-grow-1">Ayaan Bowen</div>
                                                                    </div>
                                                                </td>
                                                                <td>Bike Accessories</td>
                                                                <td>
                                                                    <span class="text-success">$215.00</span>
                                                                </td>
                                                                <td>Nesta Technologies</td>
                                                                <td>
                                                                    <span class="badge bg-success-subtle text-success">Paid</span>
                                                                </td>
                                                                <td>
                                                                    <h5 class="fs-14 fw-medium mb-0">4.9<span class="text-muted fs-11 ms-1">(89 votes)</span></h5>
                                                                </td>
                                                            </tr><!-- end tr -->
                                                            <tr>
                                                                <td>
                                                                    <a href="apps-ecommerce-order-details.php" class="fw-medium link-primary">#VZ2108</a>
                                                                </td>
                                                                <td>
                                                                    <div class="d-flex align-items-center">
                                                                        <div class="flex-shrink-0 me-2">
                                                                            <img src="assets/images/users/avatar-4.jpg" alt="" class="avatar-xs rounded-circle material-shadow" />
                                                                        </div>
                                                                        <div class="flex-grow-1">Prezy Mark</div>
                                                                    </div>
                                                                </td>
                                                                <td>Furniture</td>
                                                                <td>
                                                                    <span class="text-success">$199.00</span>
                                                                </td>
                                                                <td>Syntyce Solutions</td>
                                                                <td>
                                                                    <span class="badge bg-danger-subtle text-danger">Unpaid</span>
                                                                </td>
                                                                <td>
                                                                    <h5 class="fs-14 fw-medium mb-0">4.3<span class="text-muted fs-11 ms-1">(47 votes)</span></h5>
                                                                </td>
                                                            </tr><!-- end tr -->
                                                            <tr>
                                                                <td>
                                                                    <a href="apps-ecommerce-order-details.php" class="fw-medium link-primary">#VZ2107</a>
                                                                </td>
                                                                <td>
                                                                    <div class="d-flex align-items-center">
                                                                        <div class="flex-shrink-0 me-2">
                                                                            <img src="assets/images/users/avatar-6.jpg" alt="" class="avatar-xs rounded-circle material-shadow" />
                                                                        </div>
                                                                        <div class="flex-grow-1">Vihan Hudda</div>
                                                                    </div>
                                                                </td>
                                                                <td>Bags and Wallets</td>
                                                                <td>
                                                                    <span class="text-success">$330.00</span>
                                                                </td>
                                                                <td>iTest Factory</td>
                                                                <td>
                                                                    <span class="badge bg-success-subtle text-success">Paid</span>
                                                                </td>
                                                                <td>
                                                                    <h5 class="fs-14 fw-medium mb-0">4.7<span class="text-muted fs-11 ms-1">(161 votes)</span></h5>
                                                                </td>
                                                            </tr><!-- end tr -->
                                                        </tbody><!-- end tbody -->
                                                    </table><!-- end table -->
                                                </div>
                                            </div>
                                        </div> <!-- .card-->
                                    </div> <!-- .col-->
                                </div> <!-- end row-->

                            </div> <!-- end .h-100-->

                        </div> <!-- end col -->

                      
                    </div>

                </div>
                <!-- container-fluid -->
            </div>
            <!-- End Page-content -->

            <footer class="footer">
                <div class="container-fluid">
                    <div class="row">
                        <div class="col-sm-6">
                            <script>document.write(new Date().getFullYear())</script> © C.S.P.P.UNILU
                        </div>
                        <div class="col-sm-6">
                            <div class="text-sm-end d-none d-sm-block">
                                Administration 
                            </div>
                        </div>
                    </div>
                </div>
            </footer>
        </div>
        <!-- end main content-->

    </div>
    <!-- END layout-wrapper -->



    <!--start back-to-top-->
    <button onclick="topFunction()" class="btn btn-danger btn-icon" id="back-to-top">
        <i class="ri-arrow-up-line"></i>
    </button>
    <!--end back-to-top-->

    <!--preloader-->
    <div id="preloader">
        <div id="status">
            <div class="spinner-border text-primary avatar-sm" role="status">
                <span class="visually-hidden">Loading...</span>
            </div>
        </div>
    </div>

    <div class="customizer-setting d-none d-md-block">
        <div class="btn-info rounded-pill shadow-lg btn btn-icon btn-lg p-2" data-bs-toggle="offcanvas" data-bs-target="#theme-settings-offcanvas" aria-controls="theme-settings-offcanvas">
            <i class='mdi mdi-spin mdi-cog-outline fs-22'></i>
        </div>
    </div>

    <!-- Theme Settings -->
    <div class="offcanvas offcanvas-end border-0" tabindex="-1" id="theme-settings-offcanvas">
        <div class="d-flex align-items-center bg-primary bg-gradient p-3 offcanvas-header">
            <h5 class="m-0 me-2 text-white">Options supplementaires</h5>

            <button type="button" class="btn-close btn-close-white ms-auto" id="customizerclose-btn" data-bs-dismiss="offcanvas" aria-label="Close"></button>
        </div>
        <div class="offcanvas-body p-0">
            <div data-simplebar class="h-100">
                <div class="p-4">
                   <h6 class="mb-0 fw-semibold text-uppercase">Disposition</h6>
                    <p class="text-muted">Choisissez votre disposition</p>


                    <div class="row gy-3">
                        <div class="col-4">
                            <div class="form-check card-radio">
                                <input id="customizer-layout01" name="data-layout" type="radio" value="vertical" class="form-check-input">
                                <label class="form-check-label p-0 avatar-md w-100 material-shadow" for="customizer-layout01">
                                    <span class="d-flex gap-1 h-100">
                                        <span class="flex-shrink-0">
                                            <span class="bg-light d-flex h-100 flex-column gap-1 p-1">
                                                <span class="d-block p-1 px-2 bg-primary-subtle rounded mb-2"></span>
                                                <span class="d-block p-1 px-2 pb-0 bg-primary-subtle"></span>
                                                <span class="d-block p-1 px-2 pb-0 bg-primary-subtle"></span>
                                                <span class="d-block p-1 px-2 pb-0 bg-primary-subtle"></span>
                                            </span>
                                        </span>
                                        <span class="flex-grow-1">
                                            <span class="d-flex h-100 flex-column">
                                                <span class="bg-light d-block p-1"></span>
                                                <span class="bg-light d-block p-1 mt-auto"></span>
                                            </span>
                                        </span>
                                    </span>
                                </label>
                            </div>
                            <h5 class="fs-13 text-center mt-2">Vertical</h5>
                        </div>
                        <div class="col-4">
                            <div class="form-check card-radio">
                                <input id="customizer-layout02" name="data-layout" type="radio" value="horizontal" class="form-check-input">
                                <label class="form-check-label p-0 avatar-md w-100 material-shadow" for="customizer-layout02">
                                    <span class="d-flex h-100 flex-column gap-1">
                                        <span class="bg-light d-flex p-1 gap-1 align-items-center">
                                            <span class="d-block p-1 bg-primary-subtle rounded me-1"></span>
                                            <span class="d-block p-1 pb-0 px-2 bg-primary-subtle ms-auto"></span>
                                            <span class="d-block p-1 pb-0 px-2 bg-primary-subtle"></span>
                                        </span>
                                        <span class="bg-light d-block p-1"></span>
                                        <span class="bg-light d-block p-1 mt-auto"></span>
                                    </span>
                                </label>
                            </div>
                            <h5 class="fs-13 text-center mt-2">Horizontal</h5>
                        </div>
                        <div class="col-4">
                            <div class="form-check card-radio">
                                <input id="customizer-layout03" name="data-layout" type="radio" value="twocolumn" class="form-check-input">
                                <label class="form-check-label p-0 avatar-md w-100 material-shadow" for="customizer-layout03">
                                    <span class="d-flex gap-1 h-100">
                                        <span class="flex-shrink-0">
                                            <span class="bg-light d-flex h-100 flex-column gap-1">
                                                <span class="d-block p-1 bg-primary-subtle mb-2"></span>
                                                <span class="d-block p-1 pb-0 bg-primary-subtle"></span>
                                                <span class="d-block p-1 pb-0 bg-primary-subtle"></span>
                                                <span class="d-block p-1 pb-0 bg-primary-subtle"></span>
                                            </span>
                                        </span>
                                        <span class="flex-shrink-0">
                                            <span class="bg-light d-flex h-100 flex-column gap-1 p-1">
                                                <span class="d-block p-1 px-2 pb-0 bg-primary-subtle"></span>
                                                <span class="d-block p-1 px-2 pb-0 bg-primary-subtle"></span>
                                                <span class="d-block p-1 px-2 pb-0 bg-primary-subtle"></span>
                                                <span class="d-block p-1 px-2 pb-0 bg-primary-subtle"></span>
                                            </span>
                                        </span>
                                        <span class="flex-grow-1">
                                            <span class="d-flex h-100 flex-column">
                                                <span class="bg-light d-block p-1"></span>
                                                <span class="bg-light d-block p-1 mt-auto"></span>
                                            </span>
                                        </span>
                                    </span>
                                </label>
                            </div>
                            <h5 class="fs-13 text-center mt-2">A deux colonnes</h5>
                        </div>
                        <!-- end col -->

                        
                    </div>

                    <div class="form-check form-switch form-switch-md mb-3 mt-4">
                        <input type="checkbox" class="form-check-input" id="sidebarUserProfile">
                        <label class="form-check-label" for="sidebarUserProfile">Avatar du profil utilisateur dans la barre latérale</label>
                    </div>

                    <h6 class="mt-4 mb-0 fw-semibold text-uppercase">Theme</h6>
                    <p class="text-muted">Chosir votre thème préféré.</p>

                    <div class="row">
                        <div class="col-6">
                            <div class="form-check card-radio">
                                <input id="customizer-theme01" name="data-theme" type="radio" value="default" class="form-check-input">
                                <label class="form-check-label p-0" for="customizer-theme01">
                                    <img src="../../assets/images/demo/default.png" alt="" class="img-fluid">
                                </label>
                            </div>
                            <h5 class="fs-13 text-center fw-medium mt-2">Default</h5>
                        </div>
                        <div class="col-6">
                            <div class="form-check card-radio">
                                <input id="customizer-theme02" name="data-theme" type="radio" value="saas" class="form-check-input">
                                <label class="form-check-label p-0" for="customizer-theme02">
                                    <img src="../../assets/images/demo/saas.png" alt="" class="img-fluid">
                                </label>
                            </div>
                            <h5 class="fs-13 text-center fw-medium mt-2">Sass</h5>
                        </div>
                        <div class="col-6">
                            <div class="form-check card-radio">
                                <input id="customizer-theme03" name="data-theme" type="radio" value="corporate" class="form-check-input">
                                <label class="form-check-label p-0" for="customizer-theme03">
                                    <img src="../../assets/images/demo/corporate.png" alt="" class="img-fluid">
                                </label>
                            </div>
                            <h5 class="fs-13 text-center fw-medium mt-2">Corporate</h5>
                        </div>
                        <div class="col-6">
                            <div class="form-check card-radio">
                                <input id="customizer-theme04" name="data-theme" type="radio" value="galaxy" class="form-check-input">
                                <label class="form-check-label p-0" for="customizer-theme04">
                                    <img src="../../assets/images/demo/galaxy.png" alt="" class="img-fluid">
                                </label>
                            </div>
                            <h5 class="fs-13 text-center fw-medium mt-2">Galaxy</h5>
                        </div>
                        <div class="col-6">
                            <div class="form-check card-radio">
                                <input id="customizer-theme05" name="data-theme" type="radio" value="material" class="form-check-input">
                                <label class="form-check-label p-0" for="customizer-theme05">
                                    <img src="../../assets/images/demo/material.png" alt="" class="img-fluid">
                                </label>
                            </div>
                            <h5 class="fs-13 text-center fw-medium mt-2">Material</h5>
                        </div>
                        <div class="col-6">
                            <div class="form-check card-radio">
                                <input id="customizer-theme06" name="data-theme" type="radio" value="creative" class="form-check-input">
                                <label class="form-check-label p-0" for="customizer-theme06">
                                    <img src="../../assets/images/demo/creative.png" alt="" class="img-fluid">
                                </label>
                            </div>
                            <h5 class="fs-13 text-center fw-medium mt-2">Creative</h5>
                        </div>
                        <div class="col-6">
                            <div class="form-check card-radio">
                                <input id="customizer-theme07" name="data-theme" type="radio" value="minimal" class="form-check-input">
                                <label class="form-check-label p-0" for="customizer-theme07">
                                    <img src="../../assets/images/demo/minimal.png" alt="" class="img-fluid">
                                </label>
                            </div>
                            <h5 class="fs-13 text-center fw-medium mt-2">Minimal</h5>
                        </div>
                        <div class="col-6">
                            <div class="form-check card-radio">
                                <input id="customizer-theme08" name="data-theme" type="radio" value="modern" class="form-check-input">
                                <label class="form-check-label p-0" for="customizer-theme08">
                                    <img src="../../assets/images/demo/modern.png" alt="" class="img-fluid">
                                </label>
                            </div>
                            <h5 class="fs-13 text-center fw-medium mt-2">Modern</h5>
                        </div>
                        <!-- end col -->
                        <div class="col-6">
                            <div class="form-check card-radio">
                                <input id="customizer-theme09" name="data-theme" type="radio" value="interactive" class="form-check-input">
                                <label class="form-check-label p-0" for="customizer-theme09">
                                    <img src="../../assets/images/demo/interactive.png" alt="" class="img-fluid">
                                </label>
                            </div>
                            <h5 class="fs-13 text-center fw-medium mt-2">Interactive</h5>
                        </div><!-- end col -->

                        <div class="col-6">
                            <div class="form-check card-radio">
                                <input id="customizer-theme10" name="data-theme" type="radio" value="classic" class="form-check-input">
                                <label class="form-check-label p-0" for="customizer-theme10">
                                    <img src="../../assets/images/demo/classic.png" alt="" class="img-fluid">
                                </label>
                            </div>
                            <h5 class="fs-13 text-center fw-medium mt-2">Classic</h5>
                        </div><!-- end col -->

                        <div class="col-6">
                            <div class="form-check card-radio">
                                <input id="customizer-theme11" name="data-theme" type="radio" value="vintage" class="form-check-input">
                                <label class="form-check-label p-0" for="customizer-theme11">
                                    <img src="../../assets/images/demo/vintage.png" alt="" class="img-fluid">
                                </label>
                            </div>
                            <h5 class="fs-13 text-center fw-medium mt-2">Vintage</h5>
                        </div><!-- end col -->
                    </div>

                    <h6 class="mt-4 mb-0 fw-semibold text-uppercase">Color Scheme</h6>
                    <p class="text-muted">Choose Light or Dark Scheme.</p>

                    <div class="colorscheme-cardradio">
                        <div class="row">
                            <div class="col-4">
                                <div class="form-check card-radio">
                                    <input class="form-check-input" type="radio" name="data-bs-theme" id="layout-mode-light" value="light">
                                    <label class="form-check-label p-0 avatar-md w-100 material-shadow" for="layout-mode-light">
                                        <span class="d-flex gap-1 h-100">
                                            <span class="flex-shrink-0">
                                                <span class="bg-light d-flex h-100 flex-column gap-1 p-1">
                                                    <span class="d-block p-1 px-2 bg-primary-subtle rounded mb-2"></span>
                                                    <span class="d-block p-1 px-2 pb-0 bg-primary-subtle"></span>
                                                    <span class="d-block p-1 px-2 pb-0 bg-primary-subtle"></span>
                                                    <span class="d-block p-1 px-2 pb-0 bg-primary-subtle"></span>
                                                </span>
                                            </span>
                                            <span class="flex-grow-1">
                                                <span class="d-flex h-100 flex-column">
                                                    <span class="bg-light d-block p-1"></span>
                                                    <span class="bg-light d-block p-1 mt-auto"></span>
                                                </span>
                                            </span>
                                        </span>
                                    </label>
                                </div>
                                <h5 class="fs-13 text-center mt-2">Blanc</h5>
                            </div>

                            <div class="col-4">
                                <div class="form-check card-radio dark">
                                    <input class="form-check-input" type="radio" name="data-bs-theme" id="layout-mode-dark" value="dark">
                                    <label class="form-check-label p-0 avatar-md w-100 bg-dark material-shadow" for="layout-mode-dark">
                                        <span class="d-flex gap-1 h-100">
                                            <span class="flex-shrink-0">
                                                <span class="bg-white bg-opacity-10 d-flex h-100 flex-column gap-1 p-1">
                                                    <span class="d-block p-1 px-2 bg-white bg-opacity-10 rounded mb-2"></span>
                                                    <span class="d-block p-1 px-2 pb-0 bg-white bg-opacity-10"></span>
                                                    <span class="d-block p-1 px-2 pb-0 bg-white bg-opacity-10"></span>
                                                    <span class="d-block p-1 px-2 pb-0 bg-white bg-opacity-10"></span>
                                                </span>
                                            </span>
                                            <span class="flex-grow-1">
                                                <span class="d-flex h-100 flex-column">
                                                    <span class="bg-white bg-opacity-10 d-block p-1"></span>
                                                    <span class="bg-white bg-opacity-10 d-block p-1 mt-auto"></span>
                                                </span>
                                            </span>
                                        </span>
                                    </label>
                                </div>
                                <h5 class="fs-13 text-center mt-2">Noir</h5>
                            </div>
                        </div>
                    </div>

                    <div id="sidebar-visibility">
                        <h6 class="mt-4 mb-0 fw-semibold text-uppercase">Sidebar Visibility</h6>
                        <p class="text-muted">Choose show or Hidden sidebar.</p>
                
                        <div class="row">
                            <div class="col-4">
                                <div class="form-check card-radio">
                                    <input class="form-check-input" type="radio" name="data-sidebar-visibility" id="sidebar-visibility-show" value="show">
                                    <label class="form-check-label p-0 avatar-md w-100 material-shadow" for="sidebar-visibility-show">
                                        <span class="d-flex gap-1 h-100">
                                            <span class="flex-shrink-0 p-1">
                                                <span class="bg-light d-flex h-100 flex-column gap-1 p-1">
                                                    <span class="d-block p-1 px-2 bg-primary-subtle rounded mb-2"></span>
                                                    <span class="d-block p-1 px-2 pb-0 bg-primary-subtle"></span>
                                                    <span class="d-block p-1 px-2 pb-0 bg-primary-subtle"></span>
                                                    <span class="d-block p-1 px-2 pb-0 bg-primary-subtle"></span>
                                                </span>
                                            </span>
                                            <span class="flex-grow-1">
                                                <span class="d-flex h-100 flex-column pt-1 pe-2">
                                                    <span class="bg-light d-block p-1"></span>
                                                    <span class="bg-light d-block p-1 mt-auto"></span>
                                                </span>
                                            </span>
                                        </span>
                                    </label>
                                </div>
                                <h5 class="fs-13 text-center mt-2">Show</h5>
                            </div>
                            <div class="col-4">
                                <div class="form-check card-radio">
                                    <input class="form-check-input" type="radio" name="data-sidebar-visibility" id="sidebar-visibility-hidden" value="hidden">
                                    <label class="form-check-label p-0 avatar-md w-100 px-2 material-shadow" for="sidebar-visibility-hidden">
                                        <span class="d-flex gap-1 h-100">
                                            <span class="flex-grow-1">
                                                <span class="d-flex h-100 flex-column pt-1 px-2">
                                                    <span class="bg-light d-block p-1"></span>
                                                    <span class="bg-light d-block p-1 mt-auto"></span>
                                                </span>
                                            </span>
                                        </span>
                                    </label>
                                </div>
                                <h5 class="fs-13 text-center mt-2">Hidden</h5>
                            </div>
                        </div>
                    </div>

                    <div id="layout-width"></div>

                    <div id="layout-position"></div>


                    <div class="row">
                        <div class="col-4">
                            <div class="form-check card-radio">
                                <input class="form-check-input" type="radio" name="data-topbar" id="topbar-color-light" value="light">
                                <label class="form-check-label p-0 avatar-md w-100 material-shadow" for="topbar-color-light">
                                    <span class="d-flex gap-1 h-100">
                                        <span class="flex-shrink-0">
                                            <span class="bg-light d-flex h-100 flex-column gap-1 p-1">
                                                <span class="d-block p-1 px-2 bg-primary-subtle rounded mb-2"></span>
                                                <span class="d-block p-1 px-2 pb-0 bg-primary-subtle"></span>
                                                <span class="d-block p-1 px-2 pb-0 bg-primary-subtle"></span>
                                                <span class="d-block p-1 px-2 pb-0 bg-primary-subtle"></span>
                                            </span>
                                        </span>
                                        <span class="flex-grow-1">
                                            <span class="d-flex h-100 flex-column">
                                                <span class="bg-light d-block p-1"></span>
                                                <span class="bg-light d-block p-1 mt-auto"></span>
                                            </span>
                                        </span>
                                    </span>
                                </label>
                            </div>
                            <h5 class="fs-13 text-center mt-2">Blanc</h5>
                        </div>
                        <div class="col-4">
                            <div class="form-check card-radio">
                                <input class="form-check-input" type="radio" name="data-topbar" id="topbar-color-dark" value="dark">
                                <label class="form-check-label p-0 avatar-md w-100 material-shadow" for="topbar-color-dark">
                                    <span class="d-flex gap-1 h-100">
                                        <span class="flex-shrink-0">
                                            <span class="bg-light d-flex h-100 flex-column gap-1 p-1">
                                                <span class="d-block p-1 px-2 bg-primary-subtle rounded mb-2"></span>
                                                <span class="d-block p-1 px-2 pb-0 bg-primary-subtle"></span>
                                                <span class="d-block p-1 px-2 pb-0 bg-primary-subtle"></span>
                                                <span class="d-block p-1 px-2 pb-0 bg-primary-subtle"></span>
                                            </span>
                                        </span>
                                        <span class="flex-grow-1">
                                            <span class="d-flex h-100 flex-column">
                                                <span class="bg-primary d-block p-1"></span>
                                                <span class="bg-light d-block p-1 mt-auto"></span>
                                            </span>
                                        </span>
                                    </span>
                                </label>
                            </div>
                            <h5 class="fs-13 text-center mt-2">Noir</h5>
                        </div>
                    </div>

                    <div id="sidebar-size">
                        

                        <div class="row">
                            <div class="col-4">
                                <div class="form-check sidebar-setting card-radio">
                                    <input class="form-check-input" type="radio" name="data-sidebar-size" id="sidebar-size-default" value="lg">
                                   
                                </div>
                              
                            </div>

                            <div class="col-4">
                                <div class="form-check sidebar-setting card-radio">
                                    <input class="form-check-input" type="radio" name="data-sidebar-size" id="sidebar-size-compact" value="md">
                                   
                                </div>
                                
                            </div>

                            <div class="col-4">
                                <div class="form-check sidebar-setting card-radio">
                                    <input class="form-check-input" type="radio" name="data-sidebar-size" id="sidebar-size-small" value="sm">
                                   
                                </div>
                             
                            </div>

                            <div class="col-4">
                                <div class="form-check sidebar-setting card-radio">
                                    <input class="form-check-input" type="radio" name="data-sidebar-size" id="sidebar-size-small-hover" value="sm-hover">
                                   
                                </div>
                               
                            </div>
                        </div>
                    </div>

                    <div id="sidebar-view">
                     

                    </div>
                    <div id="sidebar-color">
                      

                       

                        <div class="collapse" id="collapseBgGradient">
                           
                        </div>
                    </div>

                    <div id="sidebar-img">
                        <h6 class="mt-4 mb-0 fw-semibold text-uppercase">Sidebar Images</h6>
                        <p class="text-muted">Choose a image of Sidebar.</p>

                        <div class="d-flex gap-2 flex-wrap img-switch">
                            <div class="form-check sidebar-setting card-radio">
                                <input class="form-check-input" type="radio" name="data-sidebar-image" id="sidebarimg-none" value="none">
                                <label class="form-check-label p-0 avatar-sm h-auto" for="sidebarimg-none">
                                    <span class="avatar-md w-auto bg-light d-flex align-items-center justify-content-center">
                                        <i class="ri-close-fill fs-20"></i>
                                    </span>
                                </label>
                            </div>

                            <div class="form-check sidebar-setting card-radio">
                                <input class="form-check-input" type="radio" name="data-sidebar-image" id="sidebarimg-01" value="img-1">
                                <label class="form-check-label p-0 avatar-sm h-auto" for="sidebarimg-01">
                                    <img src="assets/images/sidebar/img-1.jpg" alt="" class="avatar-md w-auto object-fit-cover">
                                </label>
                            </div>	

                            <div class="form-check sidebar-setting card-radio">
                                <input class="form-check-input" type="radio" name="data-sidebar-image" id="sidebarimg-02" value="img-2">
                                <label class="form-check-label p-0 avatar-sm h-auto" for="sidebarimg-02">
                                    <img src="assets/images/sidebar/img-2.jpg" alt="" class="avatar-md w-auto object-fit-cover">
                                </label>
                            </div>
                            <div class="form-check sidebar-setting card-radio">
                                <input class="form-check-input" type="radio" name="data-sidebar-image" id="sidebarimg-03" value="img-3">
                                <label class="form-check-label p-0 avatar-sm h-auto" for="sidebarimg-03">
                                    <img src="assets/images/sidebar/img-3.jpg" alt="" class="avatar-md w-auto object-fit-cover">
                                </label>
                            </div>
                            <div class="form-check sidebar-setting card-radio">
                                <input class="form-check-input" type="radio" name="data-sidebar-image" id="sidebarimg-04" value="img-4">
                                <label class="form-check-label p-0 avatar-sm h-auto" for="sidebarimg-04">
                                    <img src="assets/images/sidebar/img-4.jpg" alt="" class="avatar-md w-auto object-fit-cover">
                                </label>
                            </div>
                        </div>
                    </div>

                    <div id="sidebar-color">
                       <h6 class="mt-4 mb-0 fw-semibold text-uppercase">Couleur principale</h6>
                        <p class="text-muted">Choisissez la couleur principale.</p>


                        <div class="d-flex flex-wrap gap-2">
                            <div class="form-check sidebar-setting card-radio">
                                <input class="form-check-input" type="radio" name="data-theme-colors" id="themeColor-01" value="default">
                                <label class="form-check-label avatar-xs p-0" for="themeColor-01"></label>
                            </div>
                            <div class="form-check sidebar-setting card-radio">
                                <input class="form-check-input" type="radio" name="data-theme-colors" id="themeColor-02" value="green">
                                <label class="form-check-label avatar-xs p-0" for="themeColor-02"></label>
                            </div>
                            <div class="form-check sidebar-setting card-radio">
                                <input class="form-check-input" type="radio" name="data-theme-colors" id="themeColor-03" value="purple">
                                <label class="form-check-label avatar-xs p-0" for="themeColor-03"></label>
                            </div>
                            <div class="form-check sidebar-setting card-radio">
                                <input class="form-check-input" type="radio" name="data-theme-colors" id="themeColor-04" value="blue">
                                <label class="form-check-label avatar-xs p-0" for="themeColor-04"></label>
                            </div>
                        </div>
                    </div>

                    <div id="preloader-menu">
                       
                    
                       
                    
                    </div>
                    <!-- end preloader-menu -->

                    <div id="body-img" style="display: none;">
                        <h6 class="mt-4 mb-0 fw-semibold text-uppercase">Image de fond</h6>
                        <p class="text-muted">Choisissez une image de fond pour le corps de la page.</p>
                
                        <div class="row">
                            <div class="col-4">
                                <div class="form-check sidebar-setting card-radio">
                                    <input class="form-check-input" type="radio" name="data-body-image" id="body-img-none" value="none">
                                    <label class="form-check-label p-0 avatar-md w-100" data-body-image="none" for="body-img-none">
                                        <span class="d-flex gap-1 h-100">
                                            <span class="flex-shrink-0">
                                                <span class="bg-light d-flex h-100 flex-column gap-1 p-1">
                                                    <span class="d-block p-1 px-2 bg-primary-subtle rounded mb-2"></span>
                                                    <span class="d-block p-1 px-2 pb-0 bg-primary-subtle"></span>
                                                    <span class="d-block p-1 px-2 pb-0 bg-primary-subtle"></span>
                                                    <span class="d-block p-1 px-2 pb-0 bg-primary-subtle"></span>
                                                </span>
                                            </span>
                                            <span class="flex-grow-1">
                                                <span class="d-flex h-100 flex-column">
                                                    <span class="bg-light d-block p-1"></span>
                                                    <span class="bg-light d-block p-1 mt-auto"></span>
                                                </span>
                                            </span>
                                        </span>
                                    </label>
                                </div>
                            </div>
                           
                
                           
                        </div>
                        <!-- end row -->
                    </div>

                </div>
            </div>

        </div>
        <div class="offcanvas-footer border-top p-3 text-center">
            <div class="row text-center">
                <div class="col-6 text-center">
                    <button type="button" class="btn btn-light w-100 text-center" id="reset-layout">Réinitialiser</button>
                </div>
               
            </div>
        </div>
    </div>

    <!-- JAVASCRIPT -->
    <script src="assets/libs/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="assets/libs/simplebar/simplebar.min.js"></script>
    <script src="assets/libs/node-waves/waves.min.js"></script>
    <script src="assets/libs/feather-icons/feather.min.js"></script>
    <script src="assets/js/pages/plugins/lord-icon-2.1.0.js"></script>
    <script src="assets/js/plugins.js"></script>

    <!-- apexcharts -->
    <script src="assets/libs/apexcharts/apexcharts.min.js"></script>

    <!-- Vector map-->
    <script src="assets/libs/jsvectormap/js/jsvectormap.min.js"></script>
    <script src="assets/libs/jsvectormap/maps/world-merc.js"></script>

    <!--Swiper slider js-->
    <script src="assets/libs/swiper/swiper-bundle.min.js"></script>

    <!-- Dashboard init -->
    <script src="assets/js/pages/dashboard-ecommerce.init.js"></script>

    <!-- App js -->
    <script src="assets/js/app.js"></script>

    <!-- TEST JS -->
     <script src="assets/js/test.js"></script>

    <!-- API CDF-->
    <script>
        async function mettreAJourTaux() {
        try {
            // Exemple avec une API de taux de change (à adapter selon ton besoin réel)
            const response = await fetch('https://api.exchangerate-api.com/v4/latest/USD');
            const data = await response.json();

            // Taux de 1 USD en CDF par exemple
            const tauxCDF = data.rates.CDF;

            // Met à jour le texte dans ton span
            document.getElementById('taux-jour').textContent = tauxCDF.toFixed(2);
        } catch (error) {
            console.error('Erreur lors de la récupération du taux:', error);
        }
    }

    // Mettre à jour toutes les 5 minutes (par exemple)
    setInterval(mettreAJourTaux, 5 * 60 * 1000);

    // Appel immédiat au chargement
    mettreAJourTaux();
    </script>                                                     

</body>


<!-- Mirrored from themesbrand.com/velzon/html/master/index.html by HTTrack Website Copier/3.x [XR&CO'2014], Mon, 12 Aug 2024 07:45:33 GMT -->
</html>