
## Loglama Sınıfı

Logger sınıfı <kbd>Obullo\Log\Handler</kbd> klasöründeki log sürücülerini kullanarak uygulamaya ait log verilerini <kbd>app/classes/Workers/Logger</kbd> sınıfı yardımı ile senkron yada kuyruk servisi ile asenkron olarak kaydeder. Logger sınıfı log verilerini arasındaki önemliliği destekler ve php SplPriorityQueue sınıfı yardımı ile log verilerini önem seviyelerine göre gruplar.

<ul>
    <li><a href="#flow-chart">Akış Şeması</a></li>
    <li><a href="#configuration">Konfigürasyon</a></li>
    <li><a href="#service-provider">Servis Sağlayıcısı</a></li>
    <li>
        <a href="#writers">Yazıcılar</a>
        <ul>
            <li><a href="#add-handlers">Sürücüler Tanımlamak</a></li>
            <li><a href="#set-writer">Yazıcı Tanımlamak</a></li>
        </ul>
    </li>
    <li>
        <a href="#filters">Filtreler</a>
        <ul>
            <li><a href="#global-filters">Evrensel Filtreler</a></li>
            <li><a href="#page-filters">Sayfa Filtreleri</a></li>
        </ul>
    </li>
    <li>
        <a href="#messages">Mesajlar</a>
        <ul>
            <li><a href="#severities">Log Seviyeleri</a></li>
            <li><a href="#log-messages">Log Mesajları</a></li>
            <li><a href="#loading-handlers">Log Yazıcıları</a></li>
            <li><a href="#log-workers">Log Mesajlarını İşlemek</a></li>
        </ul>
    </li>
    <li>
        <a href="#queuing">Kuyruklama</a>
        <ul>
            <li><a href="#displaying-queue">Kuyruktaki İşleri Görüntülemek</a></li>
            <li><a href="#workers">İşciler İle Kuyruğu Tüketmek</a></li>
            <li><a href="#worker-parameters">İşci Parametreleri</a></li>
            <li><a href="#debug-mode">Hata Ayıklama Modu</a></li>
            <li><a href="#processing-jobs">Kuyruk Verilerini İşlemek</a></li>
            <li><a href="#removing-completed-jobs">İşleri Kuyruktan Silmek</a></li>
            <li><a href="#save-worker-logs">İşci Logları</a></li>
        </ul>
    </li>
    <li><a href="#displaying-logs">Logları Görüntülemek</a></li>
    <li><a href="#method-reference">Log Sınıfı Referansı</a></li>
</ul>

<a name="flow-chart"></a>

### Akış Şeması

Aşağıdaki akış şeması uygulamada bir log mesajının kaydedilirken hangi aşamalardan geçtiği ve loglamanın genel prensipleri hakkında size bir ön bilgi verecektir:

![Akış Şeması](images/log-flowchart.png?raw=true)

Uygulamada loglanmaya başlanan veriler önce bir dizi içerisinde toplanır ve php <a href="http://php.net/manual/tr/class.splpriorityqueue.php" target="_blank">SplPriorityQueue</a> sınıfı yardımı ile toplanan veriler önemlilik derecesine göre dizi içeriside sıralanırlar. Sıralanan log verileri log yazıcılarına gönderilmeden önce aşağıdaki iki olasılık sözkonusu olur.

* Kuyruk Servisinin Kapalı Olduğu Durum ( Varsayılan )

Bu durumda mevcut sayfadaki tüm log verileri önemlilik sırasına göre <kbd>app/classes/Workers/Logger</kbd> sınıfına gönderilirler.

Şemaya göre <kbd>app/classes/Workers/Logger</kbd> sınıfının çalışmasından sonra elde edilen veri çözümlenerek filtrelerden geçirilir ve log servisinden belirlenmiş yazma önceliklerine göre log sürücüleri çalıştırılarak yazma işlemleri gerçekleştirilir.

* Kuyruk Servisinin Açık Olduğu Durum

Bu durumda mevcut sayfadaki tüm log verileri <kbd>queue</kbd> servisi yardımı ile kuyruğa atılırlar. Kuyruğa gönderilme işlemi her sayfa için bir kere yapılır. Kuyruğa atılan log verileri konsoldan <kbd>php task queue listen</kbd> komutu yardımı ile <kbd>app/classes/workers/Logger</kbd> sınıfı üzerinden dinlenerek tüketilir. Konsoldan <kbd>php task queue listen</kbd> komutunun işlemci sayısına göre birden fazla çalıştırılması çoklu iş parçacıkları (multi threading) oluşturarak kuyruğun daha hızlı tüketilmesini sağlar. 

<a name="configuration"></a>

#### Konfigürasyon

Uygulama loglarının çalışabilmesi için <kbd>app/local/config.php</kbd> dosyasından enabled anahtarının aktif edilmesi gerekir.

```php
'log' => [
    'enabled' => true,
],
```

<a name="service-provider"></a>

#### Servis Sağlayıcısı

<kbd>app/providers.php</kbd> dosyasında servis sağlayıcısının tanımlı olduğundan emin olun.

```php
$container->addServiceProvider('Obullo\Container\ServiceProvider\Logger');
```

<a name="accessing-methods"></a>

#### Metotlara Erişim

```php
$container->get('logger')->method();
```

Kontrolör içerisinden,

```php
$this->logger->method();
```
<a name="writers"></a>

### Yazıcılar

Yazıcılar log verilerini işleme yada gönderme işlemlerini gerçekleştiriler.

<a name="add-handlers"></a>

#### Sürücüler Tanımlamak

Sürücüler log verilerini kaydetmek yada transfer etmek gibi işlemleri yürütürler. Önceden aşağıdaki gibi servis dosyası içerisinde tanımlı olması gereken sürücüler genel log yazıcısı 
olarak kullanılabilecekleri gibi belirli bir sayfaya yada gerçekleşmesi çok sık olmayan olaylar için gönderim işleyicisi (push handler) olarak da kullanılabilirler.

Konfigürasyon içerisinden tanımlama ( önerilen ),

```php
['name' => 'registerHandler', 'argument' => [5, 'file']],
['name' => 'registerHandler','argument' => [4, 'mongo']],
```

Servis sağlayıcısı içerisinden tanımlama.

```php
$logger->registerHandler(5, 'file');
$logger->registerHandler(4, 'mongo');
```

Bir sürücünün yazma yada gönderim önceliği ilk parametreden belirlernir. Değeri yüksek olan sürücünün önemliliği de yüksek olur ve önemlilik değeri yüksek olan sürücülere ait veriler diğer sürücülerden önce yazma işlemlerini gerçekleştirirler. İkinci parametreye ise sürücü adı girilir.

<a name="set-writer"></a>

#### Yazıcı Tanımlamak

Log yazıcısı <kbd>setWriter()</kbd> metodu ile tanımlanır. Bir sürücünün bir yazıcı olarak eklenebilmesi için sürücünün yazma işlemine uygun olması gerekir örneğin email sürücüsü genel bir yazıcı olarak eklenemez.

Konfigürasyon içerisinden ekleme,

```php
['name' => 'setWriter','argument' => ['file']],
```

Servis sağlayıcısı içerisinden ekleme.

```php
$logger->setWriter('file');
```

Eğer birden fazla yazıcıya eş zamanlı yazmak isteniyorsa bu işlemin <kbd>Workers/Logger</kbd> sınıfı içerisinde log kayıtlarının kopyalanarak yapılması önerilir.

<a name="filters"></a>

### Filtreler

Log filtreleri array türünde gelen log verilerini filtreden geçirirler. Bir log filtresi tanımlamak için ilk önce <kbd>app/classes/Log/Filters/</kbd> klasörü altında aşağıdaki gibi bir sınıf oluşturulmalıdır.

```php
namespace Obullo\Log\Filters;

use Obullo\Container\ContainerAwareTrait;
use Obullo\Container\ContainerAwareInterface;

class HelloFilter extends implements ContainerAwareInterface
{
    use ContainerAwareTrait;

    public function method(array $record, $params = array())
    {
        /**
         * Filtre operasyonları
         */
         if ($record['level'] == 'debug') {

            // Birşeyler yap ..  
         }
        return $record;
    }
}
```

Oluşturduğunuz filtreyi registerFilter metodu birinci parametresinden filtre ismi ve metod ismi ile, ikinci parametreyede aşağıdaki gibi sınıf yolunu girerek tanımlamanız gerekir.

```php
['name' => 'registerFilter','argument' => ['HelloFilter@method', 'Log\Filters\HelloFilter']],
```

Bir sürücü sayfa içerisinde yüklendikten sonra filtrelenebilir. 

```php
$this->logger->load('mongo')->filter('priority@notIn', array(LOG_DEBUG));

$this->logger->info('Hello World !');

$this->logger->push();
```

Filtre içine ikinci parametreden gönderilen parametreler filtre sınıfı filtre metodu ikinci parametresi <kbd>$params</kbd> değişkenine karşılık gelir.

<a name="global-filters"></a>

#### Evrensel Filtreler

Eğer bir filtre servis içerisinde tanımlandı ise bu türden filtreler uygulamanın her yerinde çalışacağından evrensel filtreler olarak adlandırılırlar.

Konfigürasyon içerisinden tanımlama ( önerilen ),

```php
['name' => 'setWriter', 'argument' => ['file']],
['name' => 'filter', 'argument' => ['priority@notIn', array(LOG_DEBUG, LOG_ALERT)]],
['name' => 'filter', 'argument' => ['anotherFilter@method'],
```

Servis sağlayıcısı içerisinden.

```php
$logger->setWriter('file')
    ->filter('priority@notIn', array(LOG_NOTICE, LOG_ALERT))
    ->filter('anotherFilter@method')
```

"@" işareti ile method ismi tanımlanır, eğer "@" işareti girilmezse varsayılan olarak filter() metodu çalıştırılır.

```php
$logger->setWriter('syslog')->filter('priority@notIn', array(LOG_DEBUG));
```

Prodüksiyon ortamında olan bir uygulama için LOG_DEBUG seviyesinin kapalı diğer log seviyelerinin (LOG_EMERG,LOG_ALERT,LOG_CRIT,LOG_ERR,LOG_WARNING,LOG_NOTICE) açık olması tavsiye edilir. Aksi durumda log veritabanları çok hızlı dolacaktır.

<a name="page-filters"></a>

#### Sayfa Filtreleri

Belirli bir kontrolör içerisinden de geçerli sayfa için filtreleme yapılabilir. Filter metodu load metodundan sonra çalıştırılmalıdır.

```php
$this->logger->load('mongo')->filter('priority@notIn', array(LOG_DEBUG));

$this->logger->info('Hello World !');
$this->logger->notice('Hello Notice !');
$this->logger->alert('Hello alert !');
$this->logger->debug('Hello debug !');

$this->logger->push();
```

Yukarıdaki örneği gibi herhangi bir sayfada kullanabilirsiniz. Örnekte mongo yazıcısı için bir filtre yaratılıyor ve yaratılan filtre geçerli sayfada <kbd>debug</kbd> seviyesindeki log verilerini mongo yazıcısı log kayıtlarından çıkarıyor ve geriye kalan veriyi mongo yazıcısına gönderiyor.

<a name="messages"></a>

### Mesajlar

Bir log mesajı aşağıdaki gibi oluşturulur.

```php
$this->logger->notice(string $message, $context = array(), $priority = 0);
```

<a name="severities"></a>

#### Log Seviyeleri

<table class="span9">
<thead>
<tr>
<th>Seviye</th>
<th>Değer</th>
<th>Sabit</th>
<th>Açıklama</th>
</tr>
</thead>
<tbody>
<tr>
<td>emergency()</td>
<td>0</td>
<td>LOG_EMERG</td>
<td>Emergency: Sistem kullanılamaz.</td>
</tr>

<tr>
<td>alert()</td>
<td>1</td>
<td>LOG_ALERT</td>
<td>Derhal müdahale edilmesi gereken eylemler. Örnek: Tüm web sitesinin düştüğü, veritabanına erişilemediği vb. durumlar. Bu log seviyesinde karşı tarafın SMS ile uyarılması tavsiye edilir.</td>
</tr>

<tr>
<td>critical()</td>
<td>2</td>
<td>LOG_CRIT</td>
<td>Kritik durumlar. Örnek: Uygulama bileşeni ulaşılamaz durumda yada beklenmedik bir istisnai hata.</td>
</tr>

<tr>
<td>error()</td>
<td>3</td>
<td>LOG_ERR</td>
<td>Çalıştırma hataları ani müdahaleler gerektirmez fakat genel olarak loglanıp monitörlenmelidir.</td>
</tr>

<tr>
<td>warning()</td>
<td>4</td>
<td>LOG_WARNING</td>
<td>Hata olmayan istisnai olaylar. Örnek: Modası geçmiş bir web servisi ( API ),  kötü web servisi kullanımı, yanlış olmayan fakat istenmeyen durumlar.</td>
</tr>

<tr>
<td>notice()</td>
<td>4</td>
<td>LOG_NOTICE</td>
<td>Normal fakat önemli olaylar.</td>
</tr>

<tr>
<td>info()</td>
<td>6</td>
<td>LOG_INFO</td>
<td>Bilgi amaçlı istenen yada ilgi çekici olaylar. Örnek: Kullanıcı logları, SQL logları, Uygulama performans/durum bilgileri.</td>
</tr>

<tr>
<td>debug()</td>
<td>7</td>
<td>LOG_DEBUG</td>
<td>Detaylı hata ayıklama bilgileri.</td>
</tr>
</tbody>
</table>

<a name="log-messages"></a>

#### Log Mesajları

Aşağıdaki gibi oluşturulan bir log mesajı tanımlı log yazıcılarına gönderilir. Log mesajından önce bir kanal açmanız tavsiye edilir sonra bir log seviyesi seçip birinci parametreden log mesajını, opsiyonel olarak ikinci parametreden ise mesaja bağlı özel verilerinizi gönderebilirsiniz.

```php
$this->logger->withName('security')->alert('Possible hacking attempt !', array('username' => $username));
```

Diğer bir opsiyonel parametre olan üçüncü parametreden ise log mesajının önem seviyesi ( kaydedilme önceliği ) belirlenebilir. Önem seviyesi büyük olan log mesajı önce kaydedilecektir.

```php
$this->logger->alert('Alert', array('username' => $username), 3);
$this->logger->notice('Notice', array('username' => $username), 2);
$this->logger->notice('Another Notice', array('username' => $username), 1);
```

<a name="loading-handlers"></a>

#### Log Yazıcıları

Bir log yazıcısına log verileri gönderilmek isteniyorsa load ve push metotları kullanılır.

```php
$mongoClient = $container->get('mongo')->shared(['connection' => 'default'])
$mongodb = new Monolog\Handler\MongoDBHandler($mongoClient, "db", "logs");

$this->logger->pushHandler($mongodb);
$this->logger->withName('security')->alert('Possible hacking attempt !', array('username' => $username));
```

Birden fazla log yazıcısı da aynı anda yüklenebilir.

```php
$this->logger->load('email');
$this->logger->load('mongo');  

$this->logger->withName('security')
    ->alert('Something went wrong !', array('username' => $username));

$this->logger->withName('test')
    ->info('User login attempt', array('username' => $username));
```

Log yazıcıları yüklendiğinde load ve push metotları arasında kullanılan log metotlarına ait tüm veriler push metodu aracılığı ile yüklenen yazıcılara gönderilir. Load metodundan önceki ve push metodundan sonraki loglamalar varsayılan log yazıcılarına gönderilir.

<a name="log-workers"></a>

#### Log Mesajlarını İşlemek

Tüm log mesajları <kbd>app/classes/Workers/Logger</kbd> sınıfı aracılığı ile işlenir. Bu sınıfa gelen log verilerini <kbd>fire</kbd> metodu çözümler ve ilgili log yazıcılarını kullanarak yazma işlemlerinin gerçekleşmesini sağlar.

```php
public function fire(array $data, $job = null)
{
    print_r($data);

    $this->job = $job;
    $this->data = $data;
    $this->process();
}
```

Tüm log kayıtları ve log bilgilerine ait olay verisi <kbd>$data</kbd> verisi içerisinde <kbd>fire</kbd> metoduna gönderilir. Servis konfigürasyonunda kuyruklamanın kapalı veya açık olması durumunda herhangi bir değişiklik yapmanıza gerek kalmaz.

<a name="queuing"></a>

### Kuyruklama

Büyük veya orta ölçekli uygulamalarda kuyruklama gerekebilir. Kuyruklamanın doğru çalışabilmesi için queue servisinin doğru kurulduğundan ve çalışıyor olduğundan emin olun. Kurulum doğru ise log servisi konfigürasyonundaki <kbd>pusher</kbd> anahtarına ait değerin <kbd>\Obullo\Log\Pusher\Queue</kbd> ile değiştirdiğinizde log verileri artık queue servisinizde tanımlı olan kuyruk sürücünüze gönderilir.

```php
'pusher' => 'Obullo\Log\Pusher\Queue'  // 'Obullo\Log\Pusher\Local', // Obullo\Log\Pusher\Amqp
```

Queue servisi <kbd>Workers@Logger</kbd> adlı iş sınıfı üzerinden bir kanal açar ve bu kanal üzerinde konfigürasyonunuzdaki <kbd>job</kbd> anahtarı değeri ile bir kuyruk yaratır.

```php
'queue' => 
    'job' => 'logger.1',
    'delay' => 0,
]
```

Log mesajları <kbd>\Obullo\Log\Pusher\Queue</kbd> sınıfı üzerinden queue servisi push metodu ile kuyruğa gönderilirler.

```php
$this->container->get('queue')
    ->push(
        'Workers@Logger',
        $this->params['queue']['job'],
        $data,
        $this->params['queue']['delay']
    );
```

Kuyruklama için gelişmiş seçeneklere ihtiyacınız varsa kendinize ait push sürücünüzü <kbd>Obullo\Log\Push\Amqp</kbd> sürücüsünü model alarak geliştirebilirsiniz.

Kuyruklama hakkında detaylı bilgi için [Queue.md](Queue.md) dosyasına gözatabilirsiniz.

<a name="displaying-queue"></a>

#### Kuyruktaki İşleri Görüntülemek

Konsoldan php task show komutunu yazarak kuyruktaki işleri görüntüleyebilirsiniz.

```php
php task queue show --w=Workers@Logger --j=logger.1
```

```php
Worker : Workers@Logger
Job    : logger.1

------------------------------------------------------------------------------------------
Job ID  | Job Name            | Data 
------------------------------------------------------------------------------------------
1       | Workers@Logger      | {"time":1436249455,"record":[{"channel": .. }
```

<a name="workers"></a>

#### İşçiler İle Kuyruğu Tüketmek

Kuyruğu tüketmek için konsoldan aşağıdaki komut ile bir php işçisi çalıştırmak gerekir.

```php
php task queue listen --worker=Workers@Logger --job=logger.1 --output=0
```

Yukarıdaki komut aynı anda birden fazla konsolda çalıştırıldığında <kbd>Obullo/Cli/Task/Queue</kbd> sınıfı listen metodu ile her seferinde  <kbd>Obullo/Queue/Worker.php</kbd> dosyasını çalıştırarak yeni bir iş parçaçığı oluşturur. Yerel ortamda birden fazla komut penceresi açarak kuyruğun eş zamanlı nasıl tüketildiğini test edebilirsiniz.

```php
php task queue listen --worker=Workers@Logger --job=logger.1 --delay=0 --memory=128 --timeout=0 --output=1
```
Yerel ortamda yada test işlemleri için output parametresini 1 olarak gönderdiğinizde yapılan işlere ait hata çıktılarını konsoldan görebilirsiniz.

Ayrıca UNIX benzeri işletim sistemlerinde prodüksiyon ortamında kuyruk tüketimini otomasyona geçirmek ve çoklu iş parçaları (multithreading) ile çalışmak için Supervisor adlı programdan yararlanabilirsiniz. <a href="http://supervisord.org/" target="_blank">http://supervisord.org/</a>.

Bir işlemci için açılması gereken optimum işçi sayısı 1 olmalıdır. Örneğin 16 çekirdekli bir sunucuya sahipseniz işçi sayısının 16 olması önerilir. İlgili makaleye bu bağlantıdan gözatabilirsiniz. <a href="http://stackoverflow.com/questions/1718465/optimal-number-of-threads-per-core">Optimal Number of Threads Per Core</a>.

<a name="worker-parameters"></a>

##### İşçi Parametreleri

<table>
<thead>
<tr>
<th>Parametre</th>
<th>Kısayol</th>
<th>Açıklama</th>
<th>Varsayılan</th>
</thead>
<tbody>
<tr>
<td>--worker</td>
<td>--w</td>
<td>Kuyruğun açılacağı kanalı (exchange) ve işe ait sınıf ismini belirler .</td>
<td>null</td>
</tr>
<tr>
<td>--job</td>
<td>--j</td>
<td>Kuyruğa ait iş ismini (route) belirler.</td>
<td>null</td>
</tr>
<tr>
<td>--delay</td>
<td>--d</td>
<td>Tamamlanmamış işler için gecikme zamanını belirler.</td>
<td>0</td>
</tr>
<tr>
<td>--memory</td>
<td>--m</td>
<td>Geçerli iş için kullanılabilecek maksimum belleği belirler. Değer MB cinsinden sayı olarak girilir.</td>
<td>128</td>
</tr>
<tr>
<td>--timeout</td>
<td>--t</td>
<td>Geçerli iş için maksimum çalışma süresini belirler.</td>
<td>0</td>
</tr>
<tr>
<td>--sleep</td>
<td>--s</td>
<td>Eğer kuyrukta iş yoksa girilen saniye kadar çalışma duraklatılır. En az 3 olarak girilmesi önerilir aksi durumda işlemci tüketimi artabilir.</td>
<td>3</td>
</tr>
<tr>
<td>--attempt</td>
<td>--a</td>
<td>Kuyruktaki işin en fazla kaç kere yapılma denemesine ait sayıyı belirler.</td>
<td>0</td>
</tr>
<tr>
<td>--output</td>
<td>--o</td>
<td>Output değeri 1 olması durumunda bulunan hatalar ekrana dökülür.</td>
<td>0</td>
</tr>
<tr>
<td>--env</td>
<td>--e</td>
<td>Ortam değişkenini worker uygulamasına gönderir.</td>
<td>null</td>
</tr>
<tr>
<td>--var</td>
<td>--v</td>
<td>Bu parametre göndermek istediğiniz özel parametreler için ayrılmıştır.</td>
<td>null</td>
</tr>
</tbody>
</table>

<a name="debug-mode"></a>

#### Hata Ayıklama Modu

Output değeri 1 olması durumunda bulunan hatalar ekrana dökülür.

```php
php task queue listen --w=Workers@Logger --j=logger.1 --o=1
```

<a name="processing-jobs"></a>

#### Kuyruk Verilerini İşlemek

<kbd>app/classes/Workers/Logger</kbd> sınfı fire metoduna gönderilen log verileri, istek tipi, yazıcı tipi, gönderilme süresi, log kayıtları gibi verileri aşağıdaki gibi bir array içerisinde gruplar. Php task listen komutu çalıştığında işçi veya işciler bu gruplanmış veriyi Workers/Logger sınıfı içerisinde arka planda çözümleyerek log yazıcılarına gönderirler.

```php
public function fire(array $data, $job = null)
{
    print_r($data);

    $this->job = $job;
    $this->data = $data;
    $this->process();
}
```

Eğer yukarıda görülen <kbd>app/classes/Workers/Logger</kbd> sınıfı fire metodu ikinci parametresi olan $data verisini ekrana dökmeniz halinde aşağıdaki gibi bir çıktı alırsınız.


```php
/*
Array
(
    meta =>  Array(

        [request] => http
        [time] => 1445593189
        [host] => example.com
        [uri] => /welcome/index
    ),

    writers => Array(

        [5] => Array
            (
                [handler] => file
                [type] => writer
                [filters] => Array
                            (
                                [0] => Array
                                    (
                                        [class] => Obullo\Log\Filter\PriorityFilter
                                        [method] => notIn
                                        [params] => Array
                                            (
                                            )

                                    )

                            )
                [records] => Array
                    (
                        [0] => Array
                            (
                                [channel] => system
                                [level] => debug
                                [message] => Uri Class Initialized
                                [context] => Array
                                    (
                                        [uri] => /welcome/index
                                    )

                            )
        [6] => Array(

            // handler data ..
        )
    )
*/
```

Çözümlenenen log verileri process metodu içerisinde log yazıcılarına gönderilir ve yazma işlemleri tamamlanır.

<a name="removing-completed-jobs"></a>

#### İşleri Kuyruktan Silmek

Log verileri kuyruğa gönderilirken ilk parametre <kbd>iş</kbd> sınıfının yolu yani <kbd>Workers@Logger</kbd> girildiğinden kuyruk tüketilmeye başlandığında <kbd>Obullo\Queue\Job</kbd> sınıfına genişleyen <kbd>Obullo\Queue\Job\JobHandler\AMQPJob</kbd> sınıfı <kbd>app/classes/Workers/Logger</kbd> sınıfı fire metodu ilk parametresine gönderilir.

<kbd>app/classes/Workers/Logger</kbd> sınıfı fire metodu ilk parametresine gönderilen iş sınıfı ile kuyruktan alınan işler tamamlandığında delete metodu ile kuyruktan silinirler.

```php
if ($this->job instanceof Job) {
    $this->job->delete();  // Delete job from queue
}
```

<a name="save-worker-logs"></a>

#### İşçi Logları

Varsayılan olarak konsoldan uygulamaya gelen işçi isteklerine ait log kayıtları tutulmaz. Bu verilere genellikle işçilerin <kbd>ayrı sunucularda</kbd> tutulması gerektiğinde ihtiyaç duyulur.

İşci sunucularınız ayrı ise bir işçi sunucusuna ait iş loglarını açmak için <kbd>Workers\Logger</kbd> sınıfı içerisinden <kbd>return</kbd> komutunu yorum içerisine almanız yeterli olur.

```php
if ($data['meta']['request'] == 'worker') {   // Disable worker server logs.
    // return;
}
```

<a name="displaying-logs"></a>

### Logları Görüntülemek

Uygulama logları konsoldan ve web arayüzünden görütülenebilir. Web arayüzünden görüntüleme daha detaylı log görüntüleme ve hata ayıklamalar için önerilir. Web arayüzü web socket teknolojisi kullanır.

<a name="from-console"></a>

#### Konsol

Loglamaya ait verileri konsolonuzdan takip edebilirsiniz. Bunu için proje ana dizinine girin.

```php
cd /var/www/myproject
```

Ardından <kbd>log</kbd> komutun çalıştırın.

```php
php task log
```

Log komutu varsayılan olarak file yazıcısına tanımlıdır. Eğer yerel veya test ortamında loglama için file yazıcısı kullanıyorsanız log verilerini aşağıdaki gibi konsolunuzdan takip edebilirsiniz.

![Logları Konsoldan Takip Etmek](images/log-console.jpg?raw=true "Logları Konsoldan Görüntüleme")

Log dosyasını temizlemek için <kbd>clear</kbd> komutunu kullanın.

```php
php task log clear
```

<a name="from-debugger"></a>

#### Web Arayüzü

Web arayüzü Debugger paketi ile oluşturulur, daha fazla bilgi için [Debugger.md](Debugger.md) dökümentasyonunu inceleyiniz.

<a name="method-reference"></a>

## Log Sınıfı Referansı

##### $this->logger->withName($name);

Bir log kanalı belirleyerek yeni bir log nesnesine geri döner.

##### $this->logger->pushHandler($handler);



##### $this->logger->$seviye(string $message = '', $context = array(), integer $priority = 0);

<a href="#messages">Seviye tablosunda</a> gösterilen loglama seviyelerine göre bir log mesajı oluşturur.
