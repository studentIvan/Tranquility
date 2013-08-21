<?php
class CRUDField
{
    const PARAM_TYPE = 'type';
    const PARAM_DEFAULT = 'default';
    const PARAM_DESCRIPTION = 'description';
    const PARAM_DISPLAY = 'display';
    const PARAM_MODIFY = 'modify';
    const PARAM_STATIC_VALUES = 'values';
    const PARAM_DISPLAY_FUNCTION = 'function';
    const PARAM_IS_COUNT_OF_FIELD = 'count_of';
    const PARAM_PLACEHOLDER = 'placeholder';
    const PARAM_COALESCE = 'coalesce';

    const PARAM_ONE_TO_ONE_SETTINGS = 'from';
    const ONE_TO_ONE_JOIN_TABLE = 'table';
    const ONE_TO_ONE_JOIN_CONDITION_JOIN_TABLE_FIELD = 'field';
    const ONE_TO_ONE_JOIN_CONDITION_THIS_TABLE_FIELD = 'on';
    const ONE_TO_ONE_TARGET_JOIN_TABLE_FIELD = 'as';
    const ONE_TO_ONE_JOIN_TYPE = 'join';
    const ONE_TO_ONE_JOIN_GROUP = 'group';

    const PARAM_MANY_TO_ONE_SETTINGS = 'from';
    const MANY_TO_ONE_JOIN_TABLE = 'table';
    const MANY_TO_ONE_JOIN_CONDITION_JOIN_TABLE_FIELD = 'field';
    const MANY_TO_ONE_JOIN_CONDITION_THIS_TABLE_FIELD = 'on';
    const MANY_TO_ONE_TARGET_JOIN_TABLE_FIELD = 'as';
    const MANY_TO_ONE_JOIN_TYPE = 'join';
    const MANY_TO_ONE_JOIN_GROUP = 'group';

    /**
     * @todo ONE_TO_MANY support
     */

    const PARAM_MANY_TO_MANY_SETTINGS = 'from_many';
    const MANY_TO_MANY_JOIN_DATA_TABLE = 'table_many_data';
    const MANY_TO_MANY_JOIN_RELATION_TABLE = 'table_many_relation';
    const MANY_TO_MANY_RELATION_TABLE_JOIN_CONDITION_JOIN_TABLE_FIELD = 'field_many_relation';
    const MANY_TO_MANY_RELATION_TABLE_JOIN_CONDITION_THIS_TABLE_FIELD = 'on_many_relation';
    const MANY_TO_MANY_DATA_TABLE_JOIN_CONDITION_JOIN_TABLE_FIELD = 'field_many_data';
    const MANY_TO_MANY_DATA_TABLE_JOIN_CONDITION_RELATION_TABLE_FIELD = 'on_many_data';
    const MANY_TO_MANY_DATA_TABLE_TARGET_JOIN_TABLE_FIELD = 'as_many_data';
    const MANY_TO_MANY_RELATION_TABLE_JOIN_GROUP = 'group_many_relation';
    const MANY_TO_MANY_DATA_TABLE_JOIN_GROUP = 'group_many_data';

    const TYPE_STRING = 'string';
    const TYPE_TEXT = 'text';
    const TYPE_TEXT_VISUAL = 'visual';
    const TYPE_PASSWORD = 'password';
    const TYPE_INTEGER = 'integer';
    const TYPE_DECIMAL = 'decimal';
    const TYPE_SELECT = 'select';
    const TYPE_INFINITY = 'infinity';
    const TYPE_EMAIL = 'email';
    const TYPE_DATE = 'date';
    const TYPE_DATETIME = 'datetime';
    const TYPE_CALCULATED = 'calculated';
    const TYPE_ENUMERATION = 'enum';
    const TYPE_TAGS = 'tags';

    const JOIN_TYPE_INNER = 'inner';
    const JOIN_TYPE_CROSS = 'cross';
    const JOIN_TYPE_RIGHT = 'right';
    const JOIN_TYPE_LEFT = 'left';
    const JOIN_TYPE_FULL = 'full';
    const JOIN_TYPE_RIGHT_OUTER = 'right outer';
    const JOIN_TYPE_LEFT_OUTER = 'left outer';
    const JOIN_TYPE_FULL_OUTER = 'full outer';

    const DEFAULT_NULL = 'null';
    const DEFAULT_NOW = 'now';
}