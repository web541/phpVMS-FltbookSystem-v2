<?php
$pilotid        = Auth::$userinfo->pilotid;
$last_location  = FltbookData::getLocation($pilotid);
$last_name      = OperationsData::getAirportInfo($last_location->arricao);
if(!$last_location) {
  FltbookData::updatePilotLocation($pilotid, Auth::$userinfo->hub);
}
?>
<h3><strong>Schedule Search</strong></h3>
<hr />
<form action="<?php echo url('/Fltbook');?>" method="post">
    <table class="balancesheet" align="center">
    	<tr>
    		<td colspan="5"><strong>Schedule Search</strong></td>
    	</tr>
      <tr>
          <td>Current Location:</td>
          <td>
              <?php if($settings['search_from_current_location'] == 1) { ?>
                <div><span class="pull-left"><input id="depicao" name="depicao" type="hidden" value="<?php echo $last_location->arricao; ?>"><font color="red"><?php echo $last_location->arricao; ?> - <?php echo $last_name->name; ?></font></span></div>
              <?php } else { ?>
                <font color="red"><?php echo $last_location->arricao; ?> - <?php echo $last_name->name; ?></font>
              <?php } ?>
          </td>
      </tr>
      <?php if($settings['search_from_current_location'] == 0) { ?>
        <tr>
            <td>Select An Departure Location:</td>
            <td>
                <select class="search" name="depicao">
                    <option value="" selected disabled>Choose Your Departure Location</option>
                    <?php
                      foreach ($airports as $airport) {
                        echo '<option value="'.$airport->icao.'">'.$airport->icao.' - '.$airport->name.'</option>';
                      }
                    ?>
                </select>
            </td>
        </tr>
      <?php } ?>
      <tr>
          <td>Select An Airline:</td>
          <td>
              <select class="search" name="airline">
                  <option value="">Any</option>
                  <?php
                    foreach ($airlines as $airline) {
                      echo '<option value="'.$airline->code.'">'.$airline->name.'</option>';
                    }
                  ?>
              </select>
          </td>
      </tr>
      <tr>
          <td>Select An Aircraft Type:</td>
          <td>
            <select class="search" name="aircraft">
              <option value="" selected>Any</option>
              <?php
              if($settings['search_from_current_location'] == 1) {
		$airc = FltbookData::routeaircraft($last_location->arricao);
		if(!$airc) {
		      echo '<option>No Aircraft Available!</option>';
	        } else {
		      foreach ($airc as $air) {
			$ai = FltbookData::getaircraftbyID($air->aircraft);
			echo '<option value="'.$ai->icao.'">'.$ai->name.'</option>';
		      }
	        }
              } else {
                $airc = FltbookData::routeaircraft_depnothing();
                if(!$airc) {
		  echo '<option>No Aircraft Available!</option>';
	        } else {
                  foreach($airc as $ai) {
                    echo '<option value="'.$ai->icao.'">'.$ai->name.'</option>';
                  }
                }
              }
	      ?>
            </select>
          </td>
      </tr>
      <tr>
          <td>Select Arrival Airfield:</td>
          <td>
              <select class="search" name="arricao">
                  <option value="">Any</option>
                  <?php
                  if($settings['search_from_current_location'] == 1) {
                    $airs = FltbookData::arrivalairport($last_location->arricao);
                    if(!$airs) {
                      echo '<option>No Airports Available!</option>';
                    } else {
                      foreach ($airs as $air) {
                        $nam = OperationsData::getAirportInfo($air->arricao);
                        echo '<option value="'.$air->arricao.'">'.$air->arricao.' - '.$nam->name.'</option>';
                      }
                    }
                  } else {
                    foreach($airports as $airport) {
                      echo '<option value="'.$airport->icao.'">'.$airport->icao.' - '.$airport->name.'</option>';
                    }
                  }
                  ?>
              </select>
          </td>
      </tr>
      <tr>
    	<td align="center" colspan="2">
          <input type="hidden" name="action" value="search" />
          <a href="<?php echo url('/Fltbook/bids'); ?>"><input type="button" value="View/Remove Bids"></a>
          <input border="0" type="submit" name="submit" value="Search">
    	</td>
      </tr>
      <br />
  </table>
</form>
<hr />

<?php if($settings['search_from_current_location'] == 1) { ?>
<h3><strong>Pilot Transfer</strong></h3>
<form action="<?php echo url('/Fltbook/jumpseat');?>" method="post">
  <table class="balancesheet" width="80%" align="center">
    <thead>
    	<tr class="balancesheet_header">
    	   <td colspan="5">Airport Selection</td>
    	</tr>
    	<tr>
	    <td align="center">Transfer To:</td>
            <td align="left">
              <div id="errors"></div>
                <select class="search" name="depicao" onchange="calculate_transfer(this.value)">
                    <option value="" selected disabled>Select Airport</option>
                    <?php
                    foreach($airports as $airport) {
                      if($airport->icao == $last_location->arricao) {
                        continue;
                      }

                      echo '<option value="'.$airport->icao.'">'.$airport->icao.' - '.$airport->name.'</option>';
                    }
                    ?>
                </select>
                <input type="submit" id="purchase_button" value="Purchase Transfer!" disabled="disabled" />
          </td>
       </tr>
       <tr>
         <td align="center">Distance Travelling:</td>
         <td align="left"><div id="distance_travelling"></div></td>
       </tr>
       <tr>
         <td align="center">Cost:</td>
         <td align="left"><div id="jump_purchase_cost"></div></td>
       </tr>
    </table>
   <input type="hidden" name="cost">
   <input type="hidden" name="airport">
  </form>

<script type="text/javascript">
function calculate_transfer(arricao) {
  var distancediv = $('#distance_travelling')[0];
  var costdiv     = $('#jump_purchase_cost')[0];
  var errorsdiv     = $('#errors')[0];

  errorsdiv.innerHTML = '';

  $.ajax({
    url: baseurl + "/action.php/Fltbook/get_jumpseat_cost",
    type: 'POST',
    data: { depicao: "<?php echo $last_location->arricao; ?>", arricao: arricao, pilotid: "<?php echo Auth::$userinfo->pilotid; ?>" },
    success: function(data) {
      data = $.parseJSON(data);
      console.log(data);

      if(data.error) {
        $("#purchase_button").prop('disabled', true);
        errorsdiv.innerHTML = "<font color='red'>Not enough funds for this transfer!</font>";
      } else {
        $("#purchase_button").prop('disabled', false);
        distancediv.innerHTML = data.distance + "nm";
        costdiv.innerHTML = "$" + data.total_cost;
      }

    },
    error: function(e) {
      console.log(e);
    }
  });
}
</script>
<?php } ?>
