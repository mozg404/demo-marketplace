<?php

use App\Enum\FeatureType;

return [

    // ------------------------------------------------------
    // Игры
    // ------------------------------------------------------

    [
        'categories' => [
            'keys/games'
        ],
        'type' => FeatureType::SELECT,
        'name' => 'Жанры',
        'options' => [
            'ekshen' => 'Экшен',
            'priklyucheniya' => 'Приключения',
            'rolevye_igry_rpg' => 'Ролевые игры (RPG)',
            'strategii' => 'Стратегии',
            'shutery' => 'Шутеры',
            'simulyatory' => 'Симуляторы',
            'gonki' => 'Гонки',
            'horror' => 'Хоррор',
            'otkrytyy_mir' => 'Открытый мир',
            'vyzhivanie' => 'Выживание',
            'pesochnitsa' => 'Песочница',
            'moba' => 'MOBA',
            'mmo' => 'MMO',
            'boyevik' => 'Боевик',
            'faytingi' => 'Файтинги',
            'golovolomki' => 'Головоломки',
            'rogalik_roguelike' => 'Рогалик (Roguelike)',
            'tacticheskie_igry' => 'Тактические игры',
            'stels-ekshen' => 'Стелс-экшен',
            'vizualnye_novelly' => 'Визуальные новеллы',
        ]
    ],
    [
        'categories' => [
            'keys/games',
            'game-currency',
        ],
        'type' => FeatureType::SELECT,
        'name' => 'Платформа',
        'options' => [
            'steam' => 'Steam',
            'epic_games' => 'Epic Games',
            'origin' => 'Origin',
            'uplay' => 'Ubisoft Connect',
            'gog' => 'GOG',
            'battle_net' => 'Battle.net',
            'xbox_live' => 'Xbox Live',
            'playstation_network' => 'PlayStation Network',
            'nintendo_eshop' => 'Nintendo eShop',
            'xbox_game_pass' => 'Xbox Game Pass',
            'ea_play' => 'EA Play',
            'ubisoft_plus' => 'Ubisoft+',
            'playstation_plus' => 'PlayStation Plus',
            'nintendo_switch_online' => 'Nintendo Switch Online',
            'vkontakte_games' => 'ВКонтакте Игры',
            'mygames' => 'MY.GAMES',
        ]
    ],
    [
        'categories' => [
            'keys/games'
        ],
        'type' => FeatureType::SELECT,
        'name' => 'Тип издания',
        'options' => [
            'standart' => 'Стандартное издание',
            'deluxe' => 'Deluxe издание',
            'ultimate' => 'Ultimate издание',
            'gold' => 'Gold Edition',
            'premium' => 'Premium Edition',
            'season_pass' => 'Season Pass',
            'bundle' => 'Бандл (набор DLC)',
            'early_access' => 'Early Access',
            'free_to_play' => 'Free-to-play',
            'digital_only' => 'Только цифровая версия',
        ]
    ],
    [
        'categories' => [
            'keys/games'
        ],
        'type' => FeatureType::SELECT,
        'name' => 'Особенности издания',
        'options' => [
            'cloud_saves' => 'Облачные сохранения',
            'exclusive_content' => 'Эксклюзивный контент',
            'discounts' => 'Скидки на игры',
            'multiplayer' => 'Доступ к мультиплееру',
            'free_games' => 'Бесплатные игры'
        ]
    ],



    [
        'categories' => [
            'keys/windows'
        ],
        'type' => FeatureType::SELECT,
        'name' => 'Версия',
        'options' => [
            'home' => 'Домашняя',
            'pro' => 'Профессиональная',
            'enterprise' => 'Корпоративная',
            'education' => 'Для образования',
            'server' => 'Серверная'
        ]
    ],
    [
        'categories' => [
            'keys/windows'
        ],
        'type' => FeatureType::SELECT,
        'name' => 'Разрядность',
        'options' => [
            '32bit' => '32-битная',
            '64bit' => '64-битная',
            'arm' => 'ARM-версия'
        ]
    ],
    [
        'categories' => [
            'keys/windows'
        ],
        'type' => FeatureType::SELECT,
        'name' => 'Дополнительные функции',
        'options' => [
            'activated' => 'Активирована',
            'fresh_install' => 'Чистая установка',
            'with_drivers' => 'С драйверами',
            'multi_language' => 'Многоязычная',
            'oem' => 'OEM-версия'
        ]
    ],

    [
        'categories' => [
            'subscriptions'
        ],
        'type' => FeatureType::SELECT,
        'name' => 'Тип сервиса',
        'options' => [
            'game_pass' => 'Игровой пропуск',
            'cloud_gaming' => 'Облачный гейминг',
            'early_access' => 'Ранний доступ',
            'premium' => 'Премиум-аккаунт',
            'chatbot' => 'Чат-бот',
            'image_generation' => 'Генерация изображений',
            'text_analysis' => 'Анализ текста',
            'voice_assistant' => 'Голосовой ассистент',
            'video_processing' => 'Обработка видео',
            'music' => 'Музыка',
            'video' => 'Видеостриминг',
            'podcasts' => 'Подкасты',
            'audiobooks' => 'Аудиокниги',
            'ebooks' => 'Электронные книги'
        ]
    ],
    [
        'categories' => [
            'game-currency'
        ],
        'type' => FeatureType::SELECT,
        'name' => 'Номинал',
        'options' => [
            '100' => '100',
            '500' => '500',
            '800' => '800',
            '1000' => '1000',
        ],
    ],
    [
        'categories' => [
            'subscriptions'
        ],
        'type' => FeatureType::SELECT,
        'name' => 'Период подписки',
        'options' => [
            '1_month' => '1 месяц',
            '3_months' => '3 месяца',
            '6_months' => '6 месяцев',
            '1_year' => '1 год',
            'lifetime' => 'Пожизненная'
        ]
    ],
    [
        'categories' => [
            'subscriptions'
        ],
        'type' => FeatureType::SELECT,
        'name' => 'Особенности',
        'options' => [
            'cloud_saves' => 'Облачные сохранения',
            'exclusive_content' => 'Эксклюзивный контент',
            'discounts' => 'Скидки на игры',
            'multiplayer' => 'Доступ к мультиплееру',
            'free_games' => 'Бесплатные игры'
        ]
    ],
    [
        'categories' => [
            'subscriptions'
        ],
        'type' => FeatureType::SELECT,
        'name' => 'Тип подписки',
        'options' => [
            'free' => 'Бесплатная',
            'basic' => 'Базовая',
            'pro' => 'Профессиональная',
            'enterprise' => 'Корпоративная',
            'pay_as_you_go' => 'Pay-as-you-go'
        ]
    ],
    [
        'categories' => [
            'subscriptions/streaming'
        ],
        'type' => FeatureType::SELECT,
        'name' => 'Качество контента',
        'options' => [
            'sd' => 'SD',
            'hd' => 'HD',
            'full_hd' => 'Full HD',
            '4k' => '4K',
            'hdr' => 'HDR'
        ]
    ],
    [
        'categories' => [
            'subscriptions'
        ],
        'type' => FeatureType::SELECT,
        'name' => 'Аккаунт',
        'options' => [
            'personal' => 'Личный',
            'business' => 'Бизнес',
            'premium' => 'Премиум',
            'verified' => 'Верифицированный',
            'creator' => 'Для создателей'
        ]
    ],
    [
        'categories' => [
            'keys',
            'certificates',
            'subscriptions',
        ],
        'type' => FeatureType::SELECT,
        'name' => 'Дополнительно',
        'options' => [
            'ads_free' => 'Без рекламы',
            'extra_storage' => 'Доп. хранилище',
            'analytics' => 'Аналитика',
            'multi_device' => 'Мультиустройство',
            'exclusive_content' => 'Эксклюзивный контент'
        ]
    ],
    [
        'categories' => [
            'subscriptions'
        ],
        'type' => FeatureType::SELECT,
        'name' => 'Локации',
        'options' => [
            '50+' => '50+ стран',
            '100+' => '100+ стран',
        ]
    ],

    [
        'categories' => [
            'keys/windows'
        ],
        'type' => FeatureType::SELECT,
        'name' => 'Лицензия',
        'options' => [
            'trial' => 'Пробная',
            'home' => 'Домашняя',
            'pro' => 'Профессиональная',
            'enterprise' => 'Корпоративная',
            'education' => 'Образовательная'
        ]
    ],
    [
        'categories' => [
            'keys/software',
            'keys/windows'
        ],
        'type' => FeatureType::SELECT,
        'name' => 'Версия',
        'options' => [
            'latest' => 'Последняя',
            '2023' => '2023',
            '2022' => '2022',
            '2021' => '2021',
            'legacy' => 'Устаревшая'
        ]
    ],
    [
        'categories' => [
            'software'
        ],
        'type' => FeatureType::SELECT,
        'name' => 'Особенности',
        'options' => [
            'no_logs' => 'No-logs политика',
            'kill_switch' => 'Kill Switch',
            'multi_device' => 'Мультиустройство',
            'ad_blocker' => 'Блокировщик рекламы',
            'tor' => 'Поддержка Tor'
        ]
    ],

    [
        'categories' => [
            'keys',
            'subscriptions/software',
        ],
        'type' => FeatureType::SELECT,
        'name' => 'Локализация на русский',
        'options' => [
            'yes' => 'Да',
            'no' => 'Нет',
        ]
    ],

    [
        'categories' => [
            'keys',
            'certificates',
            'game-currency',
            'subscriptions',
        ],
        'type' => FeatureType::SELECT,
        'name' => 'Регион',
        'options' => [
            'ru' => 'Россия',
            'us' => 'США',
            'eu' => 'Европа',
            'asia' => 'Азия',
            'global' => 'Весь мир'
        ]
    ],

];