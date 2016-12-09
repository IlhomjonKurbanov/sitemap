<h1>SiteMap Расширение для Yii 2</h1>
Создание карты сайта в формате XML.
Карты разбиваются, в каждой sitemap может быть 49000 линков, если у вас их больше, то создатутся дополнительные sitemap-ы.
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
Обратите внимание на $siteMap->addTable   - "news" это таблица в базе данных, array("translit","id") это поля таблицы, данные которых нужно подставить в урл, в урле в конце /%s-%d/ - т.е. скрипт в место %s вставляет данные с поля "translit", а в место %d ставит данные с "id". А "1=1" - это условие sql запроса, т.е. Where (может быть такой пример "active=1 AND status=1 AND ...") 

Запускать генерацию карты сайта можно через консоль: php yii sitemap/init
или поставить в крон
