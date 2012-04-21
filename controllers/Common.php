<?php
class Common extends Services
{
    public static function test()
    {
        $page = intval(isset($_GET['p']) ? $_GET['p'] : 1);

        $elements = array(
            'ADNhisnd nashidnas',
            'ASDJisdnas nasjidnasid',
            'SADNjsiadn nasidnasidn',
            'ansdIANSdin asiDNahisd',
            'asdnIASNd i zxhciac',
            'sand8nc8s nuasdjamsxajs',
            'sandsa8nu8 (ASUDnsd9ansd',
        );

        $pagination = Data::paginate(count($elements), 3, $page);

        echo '<pre>';
        var_dump($pagination);
    }
}
