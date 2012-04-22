use LWP::UserAgent

# See table referers after script run

$ua = LWP::UserAgent->new('agent' => 'Mozilla/4.0 (compatible; PERL)');
$ua->default_headers->push_header('Accept' => 'text/xml,application/xml,application/xhtml+xml,text/html;q=0.9,text/plain;q=0.8,image/png,*/*;q=0.5');
$ua->default_headers->push_header('Accept-Language' => 'ru,en-us;q=0.7,en;q=0.3');
$ua->default_headers->push_header('Accept-Encoding' => 'gzip,deflate');
$ua->default_headers->push_header('Accept-Charset' => 'ISO-8859-1,utf-8;q=0.7,*;q=0.7');
$ua->default_headers->push_header('Keep-Alive' => '300');
$ua->default_headers->push_header('Referer' => 'http://google.com/thisisreferertest' ); 
$ua->get('http://turbo.local/admin/');