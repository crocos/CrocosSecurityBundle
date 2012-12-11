<?php

return array(
    'data' => array(
        array(
            'version'        => 1,
            'name'           => 'foo',
            'id'             => '10000001',
            'bookmark_order' => 1,
        ),
        array(
            'version'        => 1,
            'name'           => 'bar',
            'id'             => '10000002',
            'unread'         => 6,
            'bookmark_order' => 999999999,
        ),
    ),
    'paging' => array(
        'next' => 'https://graph.facebook.com/12345/groups?limit=5000&offset=5000&__after_id=123456789',
    ),
);
