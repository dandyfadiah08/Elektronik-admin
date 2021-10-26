<<<<<<< HEAD
<nav class="main-header navbar navbar-expand navbar-dark navbar-light">
    <!-- Left navbar links -->
    <ul class="navbar-nav">
        <li class="nav-item">
            <a class="nav-link" data-widget="pushmenu" href="#" role="button"><i class="fas fa-bars"></i></a>
        </li>
        <!-- <li class="nav-item d-none d-sm-inline-block">
            <a href="index3.html" class="nav-link">Home</a>
        </li> -->
        <li class="nav-item d-none d-sm-inline-block">
            <a href="<?= base_url() ?>/device_check/" class="nav-link">Unreviewed <span class="badge badge-warning unreviewed_count"><?= $unreviewed_count ?></span></a>
        </li>
        <li class="nav-item d-none d-sm-inline-block">
            <a href="<?= base_url() ?>/transaction/" class="nav-link">Transaction <span class="badge badge-primary transaction_count"><?= $transaction_count ?></span></a>
        </li>
        <li class="nav-item d-none d-sm-inline-block">
            <a href="<?= base_url() ?>/withdraw/" class="nav-link">Withdraw Req. <span class="badge badge-success withdraw_count"><?= $withdraw_count ?></span></a>
        </li>
        <li class="nav-item d-none d-sm-inline-block">
            <a href="<?= base_url() ?>/users/" class="nav-link">Submission <span class="badge badge-danger submission_count"><?= $submission_count ?></span></a>
        </li>
    </ul>

    <!-- Right navbar links -->
    <ul class="navbar-nav ml-auto">
        <!-- Navbar Search -->
        <!-- <li class="nav-item">
            <a class="nav-link" data-widget="navbar-search" href="#" role="button">
                <i class="fas fa-search"></i>
            </a>
            <div class="navbar-search-block">
                <form class="form-inline">
                    <div class="input-group input-group-sm">
                        <input class="form-control form-control-navbar" type="search" placeholder="Search" aria-label="Search">
                        <div class="input-group-append">
                            <button class="btn btn-navbar" type="submit">
                                <i class="fas fa-search"></i>
                            </button>
                            <button class="btn btn-navbar" type="button" data-widget="navbar-search">
                                <i class="fas fa-times"></i>
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </li> -->

        <li class="nav-item dropdown">
            <a class="nav-link" data-toggle="dropdown" href="#">
                <i class="fas fa-user"></i> <?= $admin->name ?? session()->username ?>
            </a>
            <div class="dropdown-menu dropdown-menu-right">
                <a href="<?= base_url('dashboard/logout') ?>" class="dropdown-item dropdown-header">Logout</a>
            </div>
        </li>
        <li class="nav-item">
            <a class="nav-link d-none" href="#" role="button" id="isLoading">
                <i class="fas fa-sync-alt fa-spin text-primary"></i>
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link" data-widget="fullscreen" href="#" role="button">
                <i class="fas fa-expand-arrows-alt"></i>
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link" data-widget="control-sidebar" data-slide="true" href="#" role="button">
                <i class="fas fa-th-large"></i>
            </a>
        </li>
    </ul>
=======
<nav class="main-header navbar navbar-expand navbar-dark navbar-light">
    <!-- Left navbar links -->
    <ul class="navbar-nav">
        <li class="nav-item">
            <a class="nav-link" data-widget="pushmenu" href="#" role="button"><i class="fas fa-bars"></i></a>
        </li>
        <!-- <li class="nav-item d-none d-sm-inline-block">
            <a href="index3.html" class="nav-link">Home</a>
        </li> -->
        <li class="nav-item d-none d-sm-inline-block">
            <a href="<?= base_url() ?>/device_check/" class="nav-link">Unreviewed <span class="badge badge-warning unreviewed_count"><?= $unreviewed_count ?></span></a>
        </li>
        <li class="nav-item d-none d-sm-inline-block">
            <a href="<?= base_url() ?>/transaction/" class="nav-link">Transaction <span class="badge badge-primary transaction_count"><?= $transaction_count ?></span></a>
        </li>
        <li class="nav-item d-none d-sm-inline-block">
            <a href="<?= base_url() ?>/withdraw/" class="nav-link">Withdraw Req. <span class="badge badge-success withdraw_count"><?= $withdraw_count ?></span></a>
        </li>
        <li class="nav-item d-none d-sm-inline-block">
            <a href="<?= base_url() ?>/users/" class="nav-link">Submission <span class="badge badge-danger submission_count"><?= $submission_count ?></span></a>
        </li>
    </ul>

    <!-- Right navbar links -->
    <ul class="navbar-nav ml-auto">
        <!-- Navbar Search -->
        <!-- <li class="nav-item">
            <a class="nav-link" data-widget="navbar-search" href="#" role="button">
                <i class="fas fa-search"></i>
            </a>
            <div class="navbar-search-block">
                <form class="form-inline">
                    <div class="input-group input-group-sm">
                        <input class="form-control form-control-navbar" type="search" placeholder="Search" aria-label="Search">
                        <div class="input-group-append">
                            <button class="btn btn-navbar" type="submit">
                                <i class="fas fa-search"></i>
                            </button>
                            <button class="btn btn-navbar" type="button" data-widget="navbar-search">
                                <i class="fas fa-times"></i>
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </li> -->

        <li class="nav-item dropdown">
            <a class="nav-link" data-toggle="dropdown" href="#">
                <i class="fas fa-user"></i> <?= $admin->name ?? session()->username ?>
            </a>
            <div class="dropdown-menu dropdown-menu-right">
                <a href="<?= base_url('dashboard/logout') ?>" class="dropdown-item dropdown-header">Logout</a>
            </div>
        </li>
        <li class="nav-item">
            <a class="nav-link d-none" href="#" role="button" id="isLoading">
                <i class="fas fa-sync-alt fa-spin text-primary"></i>
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link" data-widget="fullscreen" href="#" role="button">
                <i class="fas fa-expand-arrows-alt"></i>
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link" data-widget="control-sidebar" data-slide="true" href="#" role="button">
                <i class="fas fa-th-large"></i>
            </a>
        </li>
    </ul>
>>>>>>> 4ceb680f190ba5888faff33d0231bebcaea1154d
</nav>