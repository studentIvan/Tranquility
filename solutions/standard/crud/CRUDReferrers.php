<?php
class CRUDReferrers extends CRUDObjectInterface
{
    protected $menuName = 'Реферреры';
    protected $menuCreate = false;
    protected $tableName = 'referrers';
    protected $menuIcon = 'icon-globe';
    protected $diffField = 'url_hash';
    protected $orderByField = 'rating';
	
    protected $fields = array(
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
            CRUDField::PARAM_MODIFY => 'переходы по ссылке: <a href="http://anonym.to/?$1" target="_blank">$1</a>',
            CRUDField::PARAM_DISPLAY => true,
        ),
        'rating' => array(
            CRUDField::PARAM_TYPE => CRUDField::TYPE_INTEGER,
            CRUDField::PARAM_MODIFY => '$1 раз',
            CRUDField::PARAM_IS_COUNT_OF_FIELD => 'url',
            CRUDField::PARAM_DISPLAY => true,
        ),
    );

    public function flagField($key) {
        $host = parse_url($key['url'], PHP_URL_HOST);
        return '<img src="http://favicon.yandex.net/favicon/' . $host . '">';
    }
}