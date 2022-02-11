<aside class="control-sidebar control-sidebar-dark" style="height: -webkit-fill-available;">
  <div class="container">

    <?php if(hasAccess($role, 'r_withdraw')): ?>
    <div class="row p-2">
      <div class="col" id="payment_gateway_balance">
        Balance 
        <span class="ml-2" data-balance="0"></span> 
        <i class="fas fa-sync"></i>
      </div>
    </div>
    <?php endif; ?>
    <div class="row p-2">
      <div class="col text-warning" id="darkmode">
        <i class="fas fa-moon"></i> Turn off Dark Mode
      </div>
    </div>
    <div class="row p-2">
      <div class="col" id="checkNotificationToken">
        <i class="fas fa-info-circle"></i> Check Web Notification
      </div>
    </div>
    <div class="row p-2">
      <div class="col" id="resetNotificationToken">
        <i class="fas fa-undo-alt"></i> Reset Web Notification
      </div>
    </div>

  </div>
</aside>