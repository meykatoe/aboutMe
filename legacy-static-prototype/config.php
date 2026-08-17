<?php
// 個人資料與導覽連結設定檔，之後要改內容只需編輯這裡

return [
    'profile' => [
        'name'    => '你的名字',
        'title'   => '職稱 / 一句話介紹',
        'avatar'  => '', // 留空則顯示姓名縮寫；也可填圖片網址，例如 'https://example.com/avatar.jpg'
        'bio'     => '在這裡寫一段簡短的自我介紹，說明你是誰、專長是什麼、對什麼感興趣。',
        'email'   => 'you@example.com',
        'socials' => [
            ['name' => 'GitHub',   'url' => 'https://github.com/yourname'],
            ['name' => 'Twitter',  'url' => 'https://twitter.com/yourname'],
            ['name' => 'LinkedIn', 'url' => 'https://linkedin.com/in/yourname'],
        ],
    ],

    // 導覽連結，依分類分組，可自行增減分類與連結
    'nav_groups' => [
        [
            'category' => '常用工具',
            'links' => [
                ['name' => 'Google',    'url' => 'https://www.google.com',    'desc' => '搜尋引擎'],
                ['name' => 'Gmail',     'url' => 'https://mail.google.com',   'desc' => '電子郵件'],
                ['name' => 'GitHub',    'url' => 'https://github.com',        'desc' => '程式碼託管'],
            ],
        ],
        [
            'category' => '學習資源',
            'links' => [
                ['name' => 'MDN',       'url' => 'https://developer.mozilla.org', 'desc' => 'Web 開發文件'],
                ['name' => 'PHP 官方文件', 'url' => 'https://www.php.net/manual/zh/', 'desc' => 'PHP 手冊'],
            ],
        ],
        [
            'category' => '社群媒體',
            'links' => [
                ['name' => 'Twitter',   'url' => 'https://twitter.com',  'desc' => '社群動態'],
                ['name' => 'YouTube',   'url' => 'https://youtube.com',  'desc' => '影音平台'],
            ],
        ],
    ],
];
