<h1>SiteMap Расширение для Yii 2</h1>
Создание карты сайта в формате XML
<h2>Настройка</h2>
в console\config\main.php добавляем
<pre>
<code>
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
</code>
</pre>

<h2>Использование</h2>

Создаем в console\controllers\  контроллер SitemapController.php и в него вставляем код:
<pre>
<code>
<?php
namespace console\controllers;

use Yii;
use yii\console\Controller;
use yii\helpers\Console;
use borysenko\sitemap\SiteMap;

class SitemapController extends Controller
{
    public function actionInit()
    {
        $siteMap = new SiteMap("@frontend/web/","sitemap");
        $siteMap->addUrl("http://www.example.com/","1.0",date("Y-m-d"),"daily");
        $siteMap->addTable("http://www.example.com/news/%s-%d/","0.9",date("Y-m-d"),"daily", "news",array("translit","id"),"1=1");
        $siteMap->start();
        $siteMap->saveXML("%d_sitemap.xml");
        $siteMap->saveIndexXml("sitemap.xml");

        Console::output('Success!');
    }
}

</code>
</pre>
