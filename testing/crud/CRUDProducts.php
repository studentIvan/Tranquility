<?php
class CRUDProducts extends CRUDObjectInterface
{
    protected $menuName = 'Продукты';
    protected $menuCreate = 'добавить продукт';
    protected $tableName = 'products';
    protected $menuIcon = 'icon-shopping-cart';
    protected $orderByField = 'added';

    protected $filterOptions = array(
        'filter_string' => true,
        'filter_date' => true,
        'filter_less_or_more' => true,
    );

    /*public function init()
    {
        $config = new CRUDObjectConfig();

        $id = new CRUDField('id', CRUDField::TYPE_INTEGER);
        $id->setParam(CRUDField::PARAM_DEFAULT, CRUDField::DEFAULT_NULL);
        $id->setParam(CRUDField::PARAM_DESCRIPTION, 'ID продукта');

        $title = new CRUDField('title', CRUDField::TYPE_STRING);
        $title->setParam(CRUDField::PARAM_DESCRIPTION, 'Название');
        $title->setParam(CRUDField::PARAM_DISPLAY, true);

        $price = new CRUDField('price', CRUDField::TYPE_DECIMAL);
        $price->setParam(CRUDField::PARAM_MODIFY, '$1 руб/кг');
        $price->setParam(CRUDField::PARAM_DESCRIPTION, 'Цена руб/кг');
        $price->setParam(CRUDField::PARAM_DISPLAY, true);

        $added = new CRUDField('added', CRUDField::TYPE_DATETIME);
        $added->setParam(CRUDField::PARAM_DEFAULT, CRUDField::DEFAULT_NOW);
        $added->setParam(CRUDField::PARAM_DESCRIPTION, 'Дата добавления');
        $added->setParam(CRUDField::PARAM_DISPLAY, true);

        $config
            ->setFields($id, $title, $price, $added)
            ->setMenuName('Продукты')
            ->setMenuCreate('добавить продукт')
            ->setTableName('products')
            ->setMenuIcon('icon-shopping-cart')
            ->setOrderByField($added)
            ->setFilters(
                CRUDObjectConfig::FILTER_STRING,
                CRUDObjectConfig::FILTER_DATE,
                CRUDObjectConfig::FILTER_LESS_OR_MORE
            );

        $this->setConfig($config);
    }*/

    protected $fields = array(
        'id' => array(
            CRUDField::PARAM_DEFAULT => CRUDField::DEFAULT_NULL,
            CRUDField::PARAM_DESCRIPTION => 'ID продукта',
            CRUDField::PARAM_TYPE => CRUDField::TYPE_INTEGER,
        ),
        'title' => array(
            CRUDField::PARAM_TYPE => CRUDField::TYPE_STRING,
            CRUDField::PARAM_DESCRIPTION => 'Название',
            CRUDField::PARAM_DISPLAY => true,
        ),
        'price' => array(
            CRUDField::PARAM_TYPE => CRUDField::TYPE_DECIMAL,
            CRUDField::PARAM_MODIFY => '$1 руб/кг',
            CRUDField::PARAM_DESCRIPTION => 'Цена руб/кг',
            CRUDField::PARAM_DISPLAY => true,
        ),
        'added' => array(
            CRUDField::PARAM_TYPE => CRUDField::TYPE_DATETIME,
            CRUDField::PARAM_DEFAULT => CRUDField::DEFAULT_NOW,
            CRUDField::PARAM_DESCRIPTION => 'Дата добавления',
            CRUDField::PARAM_DISPLAY => true,
        ),
    );
}