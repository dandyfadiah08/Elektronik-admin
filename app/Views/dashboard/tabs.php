<?= $this->extend('layouts/template') ?>

<?= $this->section('content') ?>
<div class="content-wrapper iframe-mode" data-widget="iframe" data-loading-screen="750" style="height: 608px;">
    <div class="nav navbar navbar-expand navbar-white navbar-light border-bottom p-0">
        <div class="nav-item dropdown">
            <a class="nav-link bg-danger dropdown-toggle" data-toggle="dropdown" href="#" role="button" aria-haspopup="true" aria-expanded="false">Close</a>
            <div class="dropdown-menu mt-0" style="left: 0px; right: inherit;">
                <a class="dropdown-item" href="#" data-widget="iframe-close" data-type="all" title="Click to close all the tabs">Close All</a>
                <a class="dropdown-item" href="#" data-widget="iframe-close" data-type="all-other" title="Click to close all the other tabs except the tab you are opening right now">Close All Other</a>
                <a class="dropdown-item" href="<?= base_url() ?>" title="Click here to leave the 'Tabs' page">Leave Tabs</a>
            </div>
        </div>
        <a class="nav-link bg-light" href="#" data-widget="iframe-scrollleft" title="Click to scroll tab list to the right"><i class="fas fa-angle-double-left"></i></a>
        <ul class="navbar-nav overflow-hidden" role="tablist"></ul>
        <a class="nav-link bg-light" href="#" data-widget="iframe-scrollright" title="Click to scroll tab list to the right"><i class="fas fa-angle-double-right"></i></a>
        <a class="nav-link bg-light" href="#" data-widget="iframe-fullscreen" title="Click to expand the page or make the page fullscreen"><i class="fas fa-expand"></i></a>
    </div>
    <div class="tab-content">
        <div class="tab-empty" style="height: 567px;">
            <div class="row">
                <div class="col">
                    <h2 class="display-4">What is tabs?</h2>
                    <i class="fas fa-window-restore"></i> Tabs let you open multiple pages while in the same page of browsers.
                    <br><b>To begin</b>, simply just click the menu in the left sidebar as you wish.
                    <br>And then you are already using <i class="fas fa-window-restore"></i> Tabs, opening the page but without leaving this page.
                    <br><i class="fas fa-angle-double-left"></i> to scroll tab list to the left.
                    <br><i class="fas fa-angle-double-right"></i> to scroll tab list to the right.
                    <br><i class="fas fa-expand"></i> to expand the page or make the page fullscreen.
                    <br><br><b>To exit this page</b>, please click <b class="text-danger">Close</b> button option on the top left, and then choose <b>Leave Tabs</b>, or just <a href="<?= base_url(); ?>">clik here</a>. 
                </div>
            </div>
        </div>
        <div class="tab-loading" style="height: 567px; display: none;">
            <div>
                <h2 class="display-4">Tab is loading <i class="fa fa-sync fa-spin"></i></h2>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection('content') ?>

<?= $this->section('content_css') ?>
<!-- DataTables -->
<link rel="stylesheet" href="<?= base_url() ?>/assets/adminlte3/plugins/overlayScrollbars/css/OverlayScrollbars.min.css">
<?= $this->endSection('content_css') ?>


<?= $this->section('content_js') ?>
<script src="<?= base_url() ?>/assets/adminlte3/plugins/overlayScrollbars/js/jquery.overlayScrollbars.min.js"></script>
<?= $this->endSection('content_js') ?>
