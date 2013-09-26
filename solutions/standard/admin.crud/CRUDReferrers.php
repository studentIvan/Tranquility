<?php
class CRUDReferrers extends CRUDObjectInterface
{
    protected function setup()
    {
        $config = new CRUDConfig($this);

        $config->setMenuName('Реферреры');
        $config->setMenuCreate(false);
        $config->setTableName('referrers');
        $config->setDiffField('url_hash');
        $config->setOrderByField('rating');
        $config->setMenuIcon('fighter-jet');
        $config->setElementsPerPage(60);

        $config->setFields(array(
            'flag' => array(
                CRUDField::PARAM_TYPE => CRUDField::TYPE_CALCULATED,
                CRUDField::PARAM_DISPLAY_FUNCTION => 'flagField',
                CRUDField::PARAM_DISPLAY => true,
            ),
            'url_hash' => array(
                CRUDField::PARAM_TYPE => CRUDField::TYPE_STRING,
            ),
            'url' => array(
                CRUDField::PARAM_TYPE => CRUDField::TYPE_STRING,
                CRUDField::PARAM_MODIFY => '<div style="max-width: 150px; overflow:hidden; white-space: nowrap; ' . 
                    'text-overflow: ellipsis;">переходы по ссылке: <a href="http://anonym.to/?$1" target="_blank">$1</a></div>',
                CRUDField::PARAM_DISPLAY => true,
            ),
            'rating' => array(
                CRUDField::PARAM_TYPE => CRUDField::TYPE_INTEGER,
                CRUDField::PARAM_MODIFY => '$1 раз',
                CRUDField::PARAM_IS_COUNT_OF_FIELD => 'url',
                CRUDField::PARAM_DISPLAY => true,
            ),
        ));
    }

    public function flagField($key)
    {
        $host = parse_url($key['url'], PHP_URL_HOST);
        return '<img src="http://favicon.yandex.net/favicon/' . $host . '">';
    }
}