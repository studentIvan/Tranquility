<?php
/**
 * Class CRUDNews
 */
class CRUDNews extends CRUDObjectInterface
{
    protected function setup()
    {
        $config = new CRUDConfig($this);

        $config->setMenuName('Новости');
        $config->setMenuCreate('создать еще одну новость');
        $config->setTableName('news');
        $config->setOrderByField('created_at');
        $config->setMenuIcon('th-list');

        $config->setFields(array(
            'id' => array(
                CRUDField::PARAM_DEFAULT => CRUDField::DEFAULT_NULL,
                CRUDField::PARAM_DESCRIPTION => 'ID новости',
                CRUDField::PARAM_TYPE => CRUDField::TYPE_INTEGER,
            ),
            'title' => array(
                CRUDField::PARAM_TYPE => CRUDField::TYPE_STRING,
                CRUDField::PARAM_DESCRIPTION => 'Заголовок',
                CRUDField::PARAM_DISPLAY => true,
            ),
            'content' => array(
                CRUDField::PARAM_TYPE => CRUDField::TYPE_TEXT_HTML,
                CRUDField::PARAM_DESCRIPTION => 'Контент',
            ),
            'tags' => array(
                CRUDField::PARAM_TYPE => CRUDField::TYPE_TAGS,
                CRUDField::PARAM_MANY_TO_MANY_SETTINGS => array(
                    CRUDField::MANY_TO_MANY_JOIN_DATA_TABLE => 'tags',
                    CRUDField::MANY_TO_MANY_DATA_TABLE_TARGET_JOIN_TABLE_FIELD => 'name',
                    CRUDField::MANY_TO_MANY_RELATION_TABLE_JOIN_CONDITION_JOIN_TABLE_FIELD => 'news_id',
                    CRUDField::MANY_TO_MANY_DATA_TABLE_JOIN_CONDITION_RELATION_TABLE_FIELD => 'tag_id',
                ),
                CRUDField::PARAM_DESCRIPTION => 'Теги',
            ),
            'created_at' => array(
                CRUDField::PARAM_DEFAULT => CRUDField::DEFAULT_NOW,
                CRUDField::PARAM_DESCRIPTION => 'Время создания',
                CRUDField::PARAM_TYPE => CRUDField::TYPE_DATETIME,
                CRUDField::PARAM_DISPLAY => true,
            ),
            'posted_by' => array(
                CRUDField::PARAM_TYPE => CRUDField::TYPE_INTEGER,
                CRUDField::PARAM_DESCRIPTION => 'Автор',
                CRUDField::PARAM_DEFAULT => array('Session', 'getUID'),
                CRUDField::PARAM_MODIFY => '<a href="#" class="tooltipped" data-toggle="tooltip" title="Автор новости">@$1</a>',
                CRUDField::PARAM_MANY_TO_ONE_SETTINGS => array(
                    CRUDField::MANY_TO_ONE_JOIN_TABLE => 'users',
                    CRUDField::MANY_TO_ONE_JOIN_CONDITION_JOIN_TABLE_FIELD => 'id',
                    CRUDField::MANY_TO_ONE_TARGET_JOIN_TABLE_FIELD => 'login',
                ),
                CRUDField::PARAM_DISPLAY => true,
            ),
        ));

        $config->addFilter(CRUDConfig::FILTER_STRING);
        $config->addFilter(CRUDConfig::FILTER_DATE);

        $config->setPolicyForAction(CRUDConfig::ACTION_CREATE, CRUDConfig::FULL_ACCESS);
        $config->setPolicyForAction(CRUDConfig::ACTION_READ, CRUDConfig::FULL_ACCESS);
        $config->setPolicyForAction(CRUDConfig::ACTION_UPDATE, CRUDConfig::FULL_ACCESS);
        $config->setPolicyForAction(CRUDConfig::ACTION_DELETE, CRUDConfig::FULL_ACCESS);
    }
}