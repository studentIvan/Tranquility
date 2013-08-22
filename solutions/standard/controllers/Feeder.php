<?php
class Feeder
{
    public static function dispatcher($matches)
    {
        switch ($matches[1])
        {
            case 'rss':
                header("Content-Type: application/rss+xml; charset=utf-8");
                echo self::getRSS();
                break;
            case 'atom':
                header("Content-Type: application/atom+xml; charset=utf-8");
                echo self::getAtom();
                break;
            default:
                throw new NotFoundException();
        }
    }

    public static function getRSS($domain = false)
    {
        $domain = $domain ? $domain : $_SERVER['HTTP_HOST'];
        $document = new DOMDocument('1.0', 'utf-8');
        $document->formatOutput = true;
        $rss = $document->createElement('rss');
        $rss->setAttribute('version', '2.0');
        $document->appendChild($rss);
        $channel = $document->createElement('channel');
        $rss->appendChild($channel);

        $feedTitle = isset(Process::$context['cms']['news']['feed_title']) ?
            Process::$context['cms']['news']['feed_title'] : 'Just another feed channel';

        $feedDescription = isset(Process::$context['cms']['news']['feed_description']) ?
            Process::$context['cms']['news']['feed_description'] : 'No description';

        $feedLanguage = isset(Process::$context['cms']['news']['feed_language']) ?
            Process::$context['cms']['news']['feed_language'] : 'en-us';

        $feedMaxLength = isset(Process::$context['cms']['news']['feed_max_length']) ?
            Process::$context['cms']['news']['feed_max_length'] : 750;

        $news = News::listing(0, 30);
        $pubDate = (count($news) > 0) ? date('r', strtotime($news[0]['created_at'])) : null;

        $channel->appendChild( $document->createElement('title', $feedTitle) );
        $channel->appendChild( $document->createElement('link', 'http://' . $domain) );
        $channel->appendChild( $document->createElement('description', $feedDescription) );
        $channel->appendChild( $document->createElement('language', $feedLanguage) );
        $channel->appendChild( $document->createElement('pubDate', $pubDate) );
        $channel->appendChild( $document->createElement('lastBuildDate', date('r')) );
        $channel->appendChild( $document->createElement('generator', 'Tranquility Feeder') );

        foreach ($news as $newsElement)
        {
            $newsElementLink = 'http://' . $domain . '/' .
                $newsElement['id'] . '-' . Data::titleToLink($newsElement['title']) . '.html';
            $newsElementPubDate = date('r', strtotime($newsElement['created_at']));
            if (mb_strlen($newsElement['content'], 'utf-8') > $feedMaxLength) {
                $newsElement['content'] = trim(mb_substr($newsElement['content'], 0, $feedMaxLength, 'utf-8')) . '...';
            }
            $newsElementContent = htmlspecialchars($newsElement['content']);
            $item = $document->createElement('item');
            $item->appendChild( $document->createElement('title', $newsElement['title']) );
            $item->appendChild( $document->createElement('link', $newsElementLink) );
            $item->appendChild( $document->createElement('description', $newsElementContent) );
            $item->appendChild( $document->createElement('pubDate', $newsElementPubDate) );
            $channel->appendChild($item);
        }

        return $document->saveXML();
    }

    public static function getAtom($domain = false)
    {
        $domain = $domain ? $domain : $_SERVER['HTTP_HOST'];
        $document = new DOMDocument('1.0', 'utf-8');
        $document->formatOutput = true;
        $feed = $document->createElement('feed');
        $feed->setAttribute('xmlns', 'http://www.w3.org/2005/Atom');
        $document->appendChild($feed);

        $feedTitle = isset(Process::$context['cms']['news']['feed_title']) ?
            Process::$context['cms']['news']['feed_title'] : 'Just another feed channel';

        $feedDescription = isset(Process::$context['cms']['news']['feed_description']) ?
            Process::$context['cms']['news']['feed_description'] : 'No description';

        $feedMaxLength = isset(Process::$context['cms']['news']['feed_max_length']) ?
            Process::$context['cms']['news']['feed_max_length'] : 750;

        $news = News::listing(0, 1);
        $pubDate = (count($news) > 0) ? date('Y-m-d\\TH:i:s-03:00', strtotime($news[0]['created_at'])) : null;

        $feed->appendChild( $document->createElement('title', $feedTitle) );
        $feed->appendChild( $document->createElement('subtitle', $feedDescription) );
        $link = $document->createElement('link');
        $link->setAttribute('href', 'http://' . $domain);
        $feed->appendChild( $link );
        $feed->appendChild( $document->createElement('updated', $pubDate) );
        $feed->appendChild( $document->createElement('id', 'urn:uuid:' . Security::getDigest($domain)) );
        $entry = $document->createElement('entry');
        $newsElement = $news[0];
        $newsElementLink = 'http://' . $domain . '/' .
            $newsElement['id'] . '-' . Data::titleToLink($newsElement['title']) . '.html';
        $newsElementPubDate = date('Y-m-d\\TH:i:s-03:00', strtotime($newsElement['created_at']));
        if (mb_strlen($newsElement['content'], 'utf-8') > $feedMaxLength) {
            $newsElement['content'] = trim(mb_substr($newsElement['content'], 0, $feedMaxLength, 'utf-8')) . '...';
        }
        $newsElementContent = htmlspecialchars($newsElement['content']);
        $entry->appendChild( $document->createElement('title', $newsElement['title']) );
        $link = $document->createElement('link');
        $link->setAttribute('href', $newsElementLink);
        $entry->appendChild( $link );
        $entry->appendChild( $document->createElement('id', 'urn:uuid:' . Security::getDigest($newsElement['title'])) );
        $entry->appendChild( $document->createElement('updated', $newsElementPubDate) );
        $entry->appendChild( $document->createElement('summary', $newsElementContent) );
        $feed->appendChild($entry);

        return $document->saveXML();
    }
} 