<?php
class Fltbook extends CodonModule {
    public $title = 'Flight Booking Admin';

    public function HTMLHead() {
      $this->set('sidebar', 'fltbook/sidebar.php');
    }

    public function Navbar() {
      echo '<li><a href="'.SITE_URL.'/admin/index.php/Fltbook">Fltbook Admin</a></li>';
    }

    public function index() {
      echo '<div style="text-align: center;">';
      echo '<br />';
      echo 'Fltbook Scheduling System Admin v2!';
      echo '<br />';
      echo 'Check out the sidebar links for more options.';
      echo '</div>';
    }

    public function transfer() {
      switch(DB::escape($this->post->action)) {
        case 'move_pilot':
        $this->move_pilot_post();
        break;
      }

      $this->set('allpilots', PilotData::getAllPilots());
      $this->set('allairports', OperationsData::getAllAirports());
      $this->show('fltbook/move_pilots');
    }

    public function settings() {
      switch(DB::escape($this->post->action)) {
        case 'edit_settings':
        $this->editSettingsPost();
        break;
      }
      $this->set('allsettings', FltbookData::getAllSettings());
      $this->show('fltbook/edit_settings');
    }

    public function bids() {
      $this->show('fltbook/pilots_viewallbids');
    }

    protected function editSettingsPost() {
      $data = array(
        'disabled_ac_allow_book' 			 => DB::escape($this->post->disabled_ac_allow_book),
        'disabled_ac_sched_show' 			 => DB::escape($this->post->disabled_ac_sched_show),
        'show_ac_if_booked' 		 			 => DB::escape($this->post->show_ac_if_booked),
        'search_from_current_location' => DB::escape($this->post->search_from_current_location),
        'jumpseat_cost'								 => DB::escape($this->post->jumpseat_cost),
        'pagination_enabled'					 => DB::escape($this->post->pagination_enabled),
        'show_details_button'					 => DB::escape($this->post->show_details_button),
      );

      FltbookData::editSettings($data);

      $this->set('message', 'Settings Edited Successfully!');
      $this->show('core_success');
    }

    protected function move_pilot_post() {
      $pilotid = DB::escape($this->post->pilotid);
      $arricao = DB::escape($this->post->arricao);

      FltbookData::updatePilotLocation($pilotid, $arricao, true);

      $this->set('message', 'Pilot Transferred Successfully!');
      $this->show('core_success');
    }

    public function getSettings() {
      return array(
        'disabled_ac_allow_book'       => FltbookData::getSettingByName('disabled_ac_allow_book')->value,
        'disabled_ac_sched_show'       => FltbookData::getSettingByName('disabled_ac_sched_show')->value,
        'show_ac_if_booked' 	       => FltbookData::getSettingByName('show_ac_if_booked')->value,
        'search_from_current_location' => FltbookData::getSettingByName('search_from_current_location')->value,
        'jumpseat_cost'		           => FltbookData::getSettingByName('jumpseat_cost')->value,
        'pagination_enabled'	       => FltbookData::getSettingByName('pagination_enabled')->value,
        'show_details_button'	       => FltbookData::getSettingByName('show_details_button')->value,
      );
    }
}
