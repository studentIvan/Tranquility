<?php
class DemoPart extends AdminPartition
{
    public function getUserInterfaceData()
    {
        return array(
            'link' => 'demo',
            'name' => 'Демо',
            'description' => 'Демо раздел админ-панели.',
            'count' => false,
            'plural' => false
        );
    }
}
