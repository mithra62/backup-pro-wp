<?php
/**
 * mithra62 - Backup Pro
 *
 * @copyright	Copyright (c) 2015, mithra62, Eric Lamb.
 * @link		http://mithra62.com/
 * @version		3.0
 * @filesource 	./backup_pro/admin/controllers/BackupProBackupController.php
 */
 
use mithra62\BackupPro\Platforms\Controllers\Wordpress AS WpController;
use mithra62\BackupPro\BackupPro AS BpInterface;

/**
 * Backup Pro - Wordpress Storage Controller
 *
 * Contains the Storage Controller Actions for Wordpress
 *
 * @package 	BackupPro\Wordpress\Controllers
 * @author		Eric Lamb <eric@mithra62.com>
 */
class BackupProStorageController extends WpController implements BpInterface
{   
    /**
     * The default Storage form field values
     * @var unknown
     */
    public $storage_form_data_defaults = array(
        'storage_location_name' => '',
        'storage_location_file_use' => '1',
        'storage_location_status' => '1',
        'storage_location_db_use' => '1',
        'storage_location_include_prune' => '1',
    );
        
    /**
     * View all the storage entries 
     * @return string
     */
    public function viewStorage()
    {
        $variables = array();
        $variables['can_remove'] = true;
        if( count($this->settings['storage_details']) <= 1 )
        {
            $variables['can_remove'] = false;
        }
    
        $variables['errors'] = $this->errors;
        $variables['available_storage_engines'] = $this->services['backup']->getStorage()->getAvailableStorageDrivers();
        $variables['storage_details'] = $this->settings['storage_details'];
        $variables['menu_data'] = $this->backup_lib->getSettingsViewMenu();
        $variables['section'] = 'storage';
        $variables['view_helper'] = $this->view_helper;
        $variables['url_base'] = $this->url_base;
        $variables['theme_folder_url'] = plugin_dir_url(self::name);
        $template = 'admin/views/storage';
        $this->renderTemplate($template, $variables);
    }
    
    /**
     * Add a storage entry
     * @return string
     */
    public function newStorage()
    {
        $engine = $this->getPost('engine', 'local');
        $variables = array();
        $variables['available_storage_engines'] = $this->services['backup']->getStorage()->getAvailableStorageDrivers();
    
        if( !isset($variables['available_storage_engines'][$engine]) )
        {
            $engine = 'local';
        }
        
        $variables['storage_details'] = $this->settings['storage_details'];    
        $variables['storage_engine'] = $variables['available_storage_engines'][$engine];
        $variables['form_data'] = array_merge($this->settings, $variables['storage_engine']['settings'], $this->storage_form_data_defaults);
        $variables['form_errors'] = array_merge($this->returnEmpty($this->settings), $this->returnEmpty($variables['storage_engine']['settings']), $this->storage_form_data_defaults);
    
        if( $_SERVER['REQUEST_METHOD'] == 'POST' )
        {
            $data = array();
            $data = array_map( 'stripslashes_deep', $_POST );
            
            $variables['form_data'] = $data;
            $settings_errors = $this->services['backup']->getStorage()->validateDriver($this->services['validate'], $engine, $data, $this->settings['storage_details']);
            if( $settings_errors )
            {
                $variables['form_errors'] = array_merge($variables['form_errors'], $settings_errors);
            }
        }
    
        $variables['errors'] = $this->errors;
        $variables['_form_template'] = false;
        if( $variables['storage_engine']['obj']->hasSettingsView() )
        {
            $variables['_form_template'] = 'drivers/_'.$engine.'.php';
        }

        $variables['menu_data'] = $this->backup_lib->getSettingsViewMenu();
        $variables['section'] = 'storage';
        $variables['engine'] = $engine;
        $variables['view_helper'] = $this->view_helper;
        $variables['url_base'] = $this->url_base;
        $variables['theme_folder_url'] = plugin_dir_url(self::name);
        
        //ee()->view->cp_page_title = $this->services['lang']->__('storage_bp_settings_menu');
        //return ee()->load->view('storage/new', $variables, true);
        $template = 'admin/views/storage/new';
        $this->renderTemplate($template, $variables);
    }
    
    /**
     * Edit a storage entry
     * @return string
     */    
    public function editStorage()
    {
        $storage_id = $this->getPost('id');
        $storage_details = $this->settings['storage_details'][$storage_id];
        $variables = array();
        $variables['storage_details'] = $storage_details;
        $variables['form_data'] = array_merge($this->storage_form_data_defaults, $storage_details);
        $variables['form_errors'] = $this->returnEmpty($storage_details); //array_merge($storage_details, $this->form_data_defaults);
        $variables['errors'] = $this->errors;
        $variables['available_storage_engines'] = $this->services['backup']->getStorage()->getAvailableStorageOptions();
        $variables['storage_engine'] = $variables['available_storage_engines'][$storage_details['storage_location_driver']];
        $variables['_form_template'] = 'drivers/_'.$storage_details['storage_location_driver'].'.php';
    
        if( $_SERVER['REQUEST_METHOD'] == 'POST' )
        {
            $data = array();
            $data = array_map( 'stripslashes_deep', $_POST );
            
            $variables['form_data'] = $data;
            $data['location_id'] = $storage_id;
            $settings_errors = $this->services['backup']->getStorage()->validateDriver($this->services['validate'], $storage_details['storage_location_driver'], $data, $this->settings['storage_details']);
            if( $settings_errors )
            {
                $variables['form_errors'] = array_merge($variables['form_errors'], $settings_errors);
            }
        }

        $variables['menu_data'] = $this->backup_lib->getSettingsViewMenu();
        $variables['section'] = 'storage';
        $variables['view_helper'] = $this->view_helper;
        $variables['url_base'] = $this->url_base;
        $variables['theme_folder_url'] = plugin_dir_url(self::name);
        $variables['storage_id'] = $storage_id;
        
        $template = 'admin/views/storage/edit';
        $this->renderTemplate($template, $variables);
    }
    
    /**
     * Remove a storage entry
     * @return string
     */    
    public function removeStorage()
    {
        $storage_id = $this->getPost('id');
        $storage_details = $this->settings['storage_details'][$storage_id];
    
        $variables = array();
        $variables['form_data'] = array('remove_remote_files' => '0');
        $variables['form_errors'] = array('remove_remote_files' => false);
        $variables['errors'] = $this->errors;
        $variables['available_storage_engines'] = $this->services['backup']->getStorage()->getAvailableStorageDrivers();
        $variables['storage_engine'] = $variables['available_storage_engines'][$storage_details['storage_location_driver']];
        $variables['storage_details'] = $storage_details;

        if( $_SERVER['REQUEST_METHOD'] == 'POST' )
        {
            $data = array();
            $data = array_map( 'stripslashes_deep', $_POST );
            
            $backups = $this->services['backups']->setBackupPath($this->settings['working_directory'])
                            ->getAllBackups($this->settings['storage_details'], $this->services['backup']->getStorage()->getAvailableStorageDrivers());
    
            if( $this->services['backup']->getStorage()->getLocations()->setSetting($this->services['settings'])->remove($storage_id, $data, $backups) )
            {
                ee()->session->set_flashdata('message_success', $this->services['lang']->__('storage_location_removed'));
                ee()->functions->redirect($this->url_base.'view_storage');
            }
            else
            {
                $variables['form_errors'] = array_merge($variables['form_errors'], $settings_errors);
            }
        }
        
        $variables['menu_data'] = $this->backup_lib->getSettingsViewMenu();
        $variables['section'] = 'storage';
        $variables['view_helper'] = $this->view_helper;
        $variables['url_base'] = $this->url_base;
        $variables['theme_folder_url'] = plugin_dir_url(self::name);
        $variables['storage_id'] = $storage_id;
        
        $template = 'admin/views/storage/remove';
        $this->renderTemplate($template, $variables);
    }
    
}
