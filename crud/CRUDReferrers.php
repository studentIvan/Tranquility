<?php
/**
 * Class CRUDReferrers
 * Example extensible class for tranquility standard's solution admin panel
 *
 * ---------------------------------------------
 * protected vars:
 * ---------------------------------------------
 * $menuName - name of menu apart
 * default: Something
 *
 * $menuCreate - string for localize :create new:
 * default: create new
 *
 * $menuIcon - icon of menu link from glyph icons set
 * default: icon-folder-open
 *
 * $tableName - name of target table in database
 *
 * $diffField - field which uses to makes many operations on table (delete for example)
 * default: id
 *
 * $orderByField - field which uses in ORDER BY
 * default: id
 *
 * $fields - array of fields (see below)
 *
 * $driver - experimental settings
 * for this version only MySQL
 *
 * $elementsPerPage - count of elements per page (for pagination)
 * default: 15
 *
 * $displayStyle - style attribute of <ul> tag with elements (see twitter bootstrap 3 nav)
 * default: nav nav-pills nav-stacked
 *
 * $fields contain:
 * field_name => data
 *
 * ---------------------------------------------
 * data contain:
 * ---------------------------------------------
 * type - content type
 * available: string, integer, select, datetime, date, image_uri
 *
 * modify - expression for modify displaying (use $1)
 * example data: Admin
 * example modify: user login - $1
 * result: user login - Admin
 *
 * display - boolean, Is field be displays in listing?
 *
 * count_of - group factor, see CRUDReferrers (below)
 *
 * default - default value for 'create new'
 * available: null, now
 *
 * edit_as - see CRUDUsersData
 *
 * values - values for select data (for localization)
 *
 * ---------------------------------------------
 * from - array for tables coupling, contain:
 * ---------------------------------------------
 * from[table] - joining table
 *
 * from[field] - selecting field
 * SELECT a.something, b.login AS uid
 * FROM main_table AS a
 * INNER JOIN users AS b
 * ON a.id = b.<field>
 *
 * from[as]
 * SELECT a.something, b.<as> AS uid
 * FROM main_table AS a
 * INNER JOIN users AS b
 * ON a.id = b.uid
 *
 * from[join] - join type (left, inner, outer)
 * default: inner
 *
 * from[group] - join group
 * default: b
 *
 * from[on] - parameter for more difficult joining
 * See CRUDUsersData
 *
 */
class CRUDReferrers extends CRUDObject
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