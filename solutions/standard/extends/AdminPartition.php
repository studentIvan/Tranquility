<?php
abstract class AdminPartition
{
    public function __construct($component = null)
    {
        $data = $this->getUserInterfaceData();
        if ($component and $component == $data['link'])
        {
            Admin::$notFoundBreak = true;
            Process::$context['component'] = $component;
            $page = isset($_GET['page']) ? abs($_GET['page']) : 1;
            $action = isset($_GET['action']) ? $_GET['action'] : false;
            $identify = isset($_GET['identify']) ? abs($_GET['identify']) : false;

            Process::$context['data_action'] = $action;

            if (method_exists($this, $action)) {
                $this->$action($page, $identify);
            } else {
                $this->main($page, $identify);
            }
        }
    }

    public function getUserInterfaceData()
    {
        return array(
            'link' => 'admin_partition',
            'name' => 'Admin Partition',
            'description' => 'Admin partition.',
            'count' => 0,
            'plural' => array(
                'root' => 'админ',
                'first' => '',
                'second' => 'а',
                'third' => 'ов',
            )
        );
    }

    public function main($page, $identify)
    {
        // index page
    }
}
