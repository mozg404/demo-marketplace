<?php

use App\Enum\FeatureType;

return [
    'keys' => [
        'name' => 'Ключи активации',
        'title' => 'Ключи активации',
        'slug' => 'keys',
        'children' => [
            'keys/games' => [
                'name' => 'Игры',
                'title' => 'Игры и DLC',
                'slug' => 'games',
            ],
            'keys/software' => [
                'name' => 'Программы',
                'title' => 'Программы',
                'slug' => 'software',
                'children' => [
                    'keys/software/office' => [
                        'name' => 'Офис',
                        'title' => 'Ключи для офисных программ',
                        'slug' => 'office',
                    ],
                    'keys/software/graphics' => [
                        'name' => 'Графика и 3D',
                        'title' => 'Ключи для графических и 3D редакторов',
                        'slug' => 'graphics',
                    ],
                    'keys/software/programming' => [
                        'name' => 'Программирование',
                        'title' => 'Ключи для сред разработки',
                        'slug' => 'programming',
                    ],
                    'keys/software/antivirus' => [
                        'name' => 'Антивирусы',
                        'title' => 'Ключи для антивирусов',
                        'slug' => 'antivirus',
                    ],
                ],
            ],
            'keys/windows' => [
                'name' => 'Windows',
                'title' => 'Ключи активации Windows',
                'slug' => 'windows',
            ],
        ]
    ],

    'certificates' => [
        'name' => 'Сертификаты и карты пополнения',
        'title' => 'Сертификаты, подарочные карты и пополнение счета',
        'slug' => 'certificates',
    ],

    'game-currency' => [
        'name' => 'Игровая валюта',
        'title' => 'Игровая валюта',
        'slug' => 'game-currency',
    ],

    'subscriptions' => [
        'name' => 'Подписки',
        'title' => 'Подписки',
        'slug' => 'subscriptions',
        'children' => [
            'subscriptions/games' => [
                'name' => 'Игровые подписки',
                'title' => 'Игровые подписки',
                'slug' => 'games',
            ],
            'subscriptions/ai' => [
                'name' => 'Нейросети',
                'title' => 'Подписки на нейросети',
                'slug' => 'ai',
            ],
            'subscriptions/streaming' => [
                'name' => 'Стриминговые сервисы',
                'title' => 'Подписки на стриминговые сервисы',
                'slug' => 'streaming',
            ],
            'subscriptions/social' => [
                'name' => 'Общение и соц. сети',
                'title' => 'Подписки на платформы для общения и социальные сети',
                'slug' => 'social',
            ],
            'subscriptions/software' => [
                'name' => 'Программы',
                'title' => 'Программы',
                'slug' => 'software',
                'children' => [
                    'subscriptions/software/bundles' => [
                        'name' => 'Комплекты',
                        'title' => 'Единые подписки на программное обеспечение',
                        'slug' => 'bundles',
                    ],
                    'subscriptions/software/office' => [
                        'name' => 'Офис',
                        'title' => 'Ключи для офисных программ',
                        'slug' => 'office',
                    ],
                    'subscriptions/software/graphics' => [
                        'name' => 'Графика и 3D',
                        'title' => 'Ключи для графических и 3D редакторов',
                        'slug' => 'graphics',
                    ],
                    'subscriptions/software/programming' => [
                        'name' => 'Программирование',
                        'title' => 'Ключи для сред разработки',
                        'slug' => 'programming',
                    ],
                    'subscriptions/software/antivirus' => [
                        'name' => 'Антивирусы',
                        'title' => 'Подписки на антивирусные программы',
                        'slug' => 'antivirus',
                    ],
                ],
            ],
        ],
    ],
];