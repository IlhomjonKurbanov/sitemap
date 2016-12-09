<h1>SiteMap Расширение для Yii 2</h1>
Создание карты сайта в формате XML
<h2>Настройка</h2>
в console\config\main.php добавляем
<?php
    'components' => [
        'urlManager' => [
            'class' => 'yii\web\UrlManager',
            'scriptUrl' => 'http://www.example.com', // Setup your domain
            'baseUrl' => 'http://www.example.com', // Setup your domain
            'enablePrettyUrl' => true,
            'showScriptName' => false,
            // ...
        ],
    ],
?>
