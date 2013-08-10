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
            'type' => 'calculated',
            'function' => 'flagField',
            'display' => true,
        ),
        'url_hash' => array(
            'type' => 'string',
        ),
        'url' => array(
            'type' => 'string',
            'modify' => 'переходы по ссылке: <a href="http://anonym.to/?$1" target="_blank">$1</a>',
            'display' => true,
        ),
        'rating' => array(
            'type' => 'integer',
            'modify' => '$1 раз',
            'count_of' => 'url',
            'display' => true,
        ),
    );

    public function flagField($key) {
        $host = parse_url($key['url'], PHP_URL_HOST);
        return '<img src="http://favicon.yandex.net/favicon/' . $host . '">';
    }
}