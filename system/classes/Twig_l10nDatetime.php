<?php
/**
 * Няшный datetime форматинг аля вконтакте
 * > в разработке <
 *
 * n секунд(у|ы|) назад
 * n минут(у|ы|) назад
 * n час(|а|ов) назад
 * вчера в 0:51:45
 * 23 апреля в 0:51:45
 * 23 апреля 2012 в 0:51:45
 */
class Twig_l10nDatetime extends Twig_Extension
{
    public function getName()
    {
        return 'localized_datetime';
    }

    public function getFilters()
    {
        return array(
            'dtl10n' => new Twig_Filter_Method($this, 'dtl10n'),
        );
    }

    public function dtl10n($datetime)
    {
        return $datetime;
    }
}
