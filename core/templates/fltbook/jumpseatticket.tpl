<div id="contenttext">
  <h3>Confirm Jumpseat</h3>
  <form action="<?php echo url('/Fltbook/jumpseatPurchase');?>" method="post">
   <table class="balancesheet" align="center">
      <tr>
          <td colspan="1">Jumpseat Confirmation</td>
      </tr>
      <tr>
          <td>Destination: <strong><?php echo $airport->name; ?></strong></td>
      </tr>
      <tr>
          <td>Departure Date: <strong><?php echo date('m/d/Y') ?></strong></td>
      </tr>
      <tr>
          <td>Total Cost: <strong>$<?php echo $cost; ?></strong></td>
      </tr>
    </table>
      <br />
      <div style="text-align: center;">
        <a href="<?php echo url('/Fltbook');?>"><input type="button" class="btn btn-success sharp" value="Cancel Jumpseat"></a>
  	    <input type="submit" value="Confirm Jumpseat">
      </div>
      <input type="hidden" name="arricao" value="<?php echo $airport->icao; ?>" />
  </form>
</div>
