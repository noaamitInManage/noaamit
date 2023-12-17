<?PHP
    $method = basename($_SERVER[REQUEST_URI]);
    switch($method) {
        case 'getHostUrl':
            $reply = [ 'status' => 1,
                       'data' => [
                           'get_methods' => ['generalDeclaration'],
                           'get_url' => 'salat.inmanage.com',
                           'media_url' => 'salat.inmanage.com',
                           'host' => 'salat.inmanage.com'
                       ],
                       'err' => ''];
            break;
        case 'generalDeclaration':
            $reply = [ 'status' => 1,
                'data' => [
                    'translationsArr' => [
                        'home_header' => 'עמוד הבית'
                    ],
                ],
                'err' => ''];
            break;
        case 'getMetaTags':
            $reply = [ 'status' => 1,
                'data' => [
                    'tags' => [
                        'keywords' => 'titla, meta, nextjs',
                        'viewport' => 'width=device-width, initial-scale=1.0',
                        'title' => 'ssr meta tags test',
                    ]
                ],
                'err' => ''];
            break;
        default:
        $reply = [ 'status' => 0, 'err' => ['content' => 'unknown method'] ];
    }

    echo json_encode($reply);
?>