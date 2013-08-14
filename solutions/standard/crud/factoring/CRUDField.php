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

    const PARAM_ONE_TO_MANY_SETTINGS = 'from';
    const PARAM_ONE_TO_MANY_JOIN_TABLE = 'table';
    const PARAM_ONE_TO_MANY_JOIN_CONDITION_JOIN_TABLE_FIELD = 'field';
    const PARAM_ONE_TO_MANY_JOIN_CONDITION_THIS_TABLE_FIELD = 'on';
    const PARAM_ONE_TO_MANY_TARGET_JOIN_TABLE_FIELD = 'as';
    const PARAM_ONE_TO_MANY_JOIN_TYPE = 'join';
    const PARAM_ONE_TO_MANY_JOIN_GROUP = 'group';

    const TYPE_STRING = 'string';
    const TYPE_TEXT = 'text';
    const TYPE_PASSWORD = 'password';
    const TYPE_INTEGER = 'integer';
    const TYPE_DECIMAL = 'decimal';
    const TYPE_SELECT = 'select';
    const TYPE_INFINITY = 'infinity';
    const TYPE_EMAIL = 'email';
    const TYPE_DATE = 'date';
    const TYPE_DATETIME = 'datetime';
    const TYPE_CALCULATED = 'calculated';

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