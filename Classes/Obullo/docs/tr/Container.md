
## Konteyner Sınıfı

Bir konteyner, uygulamanızda servisler ve servis sağlayıcıları oluşturabilmeyi sağlar ve bağımlılık enjeksiyonunu yönetir. Çerçeve içerisinde konteyner paketi harici olarak kullanılır ve <a href="http://thephpleague.com" target="blank">The Php League</a> php grubu tarafından geliştirilen <a href="http://container.thephpleague.com/" target="_blank">League Container</a> sınıfı tercih edilmiştir.

<ul>
<li>
    <a href="#services">Servisler</a>
    <ul>
        <li><a href="#container-add">$container->add()</a></li>
        <li><a href="#container-share">$container->shared()</a></li>
        <li><a href="#container-get">$container->get()</a></li>
        <li><a href="#container-has">$container->has()</a></li>
        <li><a href="#container-hasShared">$container->hasShared()</a></li>
        <li><a href="#container-addServiceProvider">$container->addServiceProvider()</a></li>
    </ul>
</li>

<li>
    <a href="#dependency-injection">Bağımlılık Enjeksiyonu</a>
    <ul>
        <li><a href="#container-withArgument">$container->withArgument()</a></li>
        <li><a href="#container-withMethodCall">$container->withMethodCall()</a></li>
    </ul>
</li>

<li>
    <a href="#service-providers">Servis Sağlayıcıları</a>
    <ul>
        <li><a href="#service-configuration">Servis Konfigurasyonu</a></li>
        <li><a href="#service-parameters">Servis Parametreleri</a></li>
    </ul>
</li>

<li>
    <a href="#connectors">Konnektörler</a>
    <ul>
        <li><a href="#load-connector">Konnektörü Yüklemek</a></li>
        <li><a href="#default-connectors">Konnektör Listesi</a></li>
    </ul>
</li>

</ul>

<a name="services"></a>

### Servisler

Konteyner içerisine bir servis <kbd>add</kbd> yada <kbd>share</kbd> metotları ile kaydedilir.

<a name="container-add"></a>

#### $container->add()

Eğer servis her bir defa çağırıldığında yeni değişken değerleri ile yaratılmak isteniyorsa <kbd>add</kbd> metodu kullanılır.

```php
$container->add('class', 'Namespace/MyClass');
```

Servis her çağırıldığında yeni nesneye döner.

```php
$container->get('class');  // yeni nesne
$container->get('class');  // yeni nesne
$container->get('class');  // yeni nesne
```

<a name="container-share"></a>

#### $container->share()

Eğer servis nesnesi paylaşılmak isteniyorsa <kbd>share</kbd> metodu kullanılır.

```php
$container->share('class', 'Namespace/MyClass');
```

Share metodu ile eklenen bir servis ilk çağırımdan sonra her bir defa çağırıldığında ilk yaratılan nesneye döner.

```php
$container->get('class');  // yeni nesne
$container->get('class');  // eski nesne
$container->get('class');  // eski nesne
```

<a name="container-get"></a>

#### $container->get()

Konteyner içerisine kaydedilen bir değere ulaşmayı sağlar. 

```php
var_dummp($container->get('class'));  // object
```

Bu değer eğer <kbd>string</kbd> türünde bir değer ise <kbd>RawArgument()</kbd> sınıfı kullanılmalıdır.

```php
$container->add('foo', new League\Container\Argument\RawArgument('bar'));
```

Foo adlı değeri aldığınızda <kbd>RawArgument()</kbd> nesnesine geri döner. Nesne içerisinden getValue() metodu ile gerçek foo değerine ulaşılmış olur.

```php
$container->get('foo')->getValue();  // bar
```

<a name="container-has"></a>

#### $container->has()

Girilen servis konteyner içerisinde kayıtlı ise <kbd>true</kbd> aksi durumda <kbd>false</kbd> değerine geri döner.

```php
if ($container->has('cookie')) {
    
    // ..
}
```

<a name="container-hasShared"></a>

#### $container->hasShared()

Girilen servis adı uygulama içerisinde bir kez çağırılmışsa <kbd>true</kbd> aksi durumda <kbd>false</kbd> değerine geri döner.

```php
var_dump($container->hasShared('cookie'));   // false

$container->get('cookie');

var_dump($container->hasShared('cookie'));   // true
```

<a name="container-addServiceProvider"></a>

#### $container->addServiceProvider()

Uygulamanıza <kbd>app/providers.php</kbd> dosyasından servis sağlayıcısı ekler.

```php
$container->addServiceProvider('Obullo\Container\ServiceProvider\Logger');
```

<a name="dependency-injection"></a>

### Bağımlılık Enjeksiyonu

Bir servis sınıfına ait bağımlılıklar ( bağımlı diğer nesneler ) harici metotlar yardımı ile enjekte edilir. Bu metotlar aşağıda listelenmiştir.

<a name="container-withArgument"></a>

#### $container->withArgument()

Bir servise ait bağımlılık argümanları bu metot ile enjekte edilir. Her bir bağımlılık için metodun tekrar kullanılması gerekir.

```php
$container->share('view', 'Obullo\View\View')
    ->withArgument($container)
    ->withArgument($container->get('logger'));
    ->withArgument(
        [
            'options' => array()
        ]
    );
```

Bağımlı sınıf aşağıdaki gibi construct metodu içerisinden enjekte edilir.

```php
Class View {
    
    public function __construct(Container $container, Logger $logger, array $params)
    {
         $this->container = $container;
         $this->logger = $logger;
         $this->params = $params;
    }
}
```

<a name="container-withMethodCall"></a>

#### $container->withMethodCall()

Bir servise ait metot çalıştırma bağımlılığı bu metot ile çözülür. Aşağıdaki örnekte view servisi içerisindeki <kbd>addFolder</kbd> metodu çalıştırılarak view nesnesine yeni klasörler ekleniyor.

```php
$container->share('view', 'Obullo\View\View')
    ->withArgument($container)
    ->withArgument($container->get('logger'))
    ->withMethodCall(
        'addFolder',
        [
            'templates',
            RESOURCES.'/templates/'
        ]
    );
```


Daha fazla bilgi için <a href="http://container.thephpleague.com/">League Container</a> dökümentasyonuna gözatabilirsiniz.

<a name="service-providers"></a>

### Servis Sağlayıcıları

Bir servis sağlayıcısı yazımlıcılara yinelenen parçaları uygulamanın farklı bölümlerinde güvenli bir şekilde tekrar kullanabilmelerine olanak tanır. Her bir servisin çalışabilmesi için <kbd>app/providers.php</kbd> dosyasında servis sağlayıcı olarak tanımlanması gerekir.

```php
$container->addServiceProvider('ServiceProvider\Url');
```

Böylece servisler servis sağlayıcılar sayesinde uygulamaya dahil edilirler.

```php
namespace ServiceProvider;

use Obullo\Container\ServiceProvider\AbstractServiceProvider;

class Url extends AbstractServiceProvider
{
    protected $provides = [
        'url'
    ];

    public function register()
    {
        $container = $this->getContainer();

        $container->share('url', 'Obullo\Url\Url')
            ->withArgument($container)
            ->withArgument($container->get('logger'))
            ->withArgument(
                [
                    'baseurl'  => '/',
                    'assets'   => [
                        'url' => '/',
                        'folder' => '/assets/',
                    ]
                ]
            );
    }
}
```

<a name="service-configuration"></a>

#### Servis Konfigürasyonu

<kbd>AbstractServiceProvider</kbd> sınıfına genişleyen bir servis sağlayıcısı içerisinden <kbd>$this->getConfiguration()</kbd> metodu ile servise ait konfigürasyon dosyası <kbd>app/$env/providers</kbd> klasöründen yüklenir.

```php
$config = $this->getConfiguration('logger');
```

Elde edilen <kbd>$config</kbd> değişkeni <kbd>Obullo/Container/ServiceProvider/Configuration</kbd> nesnesine geri döner. Bu nesneye ait <kbd>$config->getParams()</kbd> metodu konfigürasyon parametrelerini verir.

```php
print_r($config->getParams());

Array
(
    [default] => Array
        (
            [channel] => system
        )

    [priorities] => Array
        (
            [emergency] => 7
            [alert] => 6
            .
            .
        )
)
```

<kbd>$config->getMethods()</kbd> metodu ise servise ait konfigürasyondan çalıştırılması gereken metot ve argümanlara geri döner.

```php
print_r($config->getMethods());

Array
(
    [0] => Array
        (
            [name] => registerFilter
            [argument] => Array()

        )
)
```

<a name="service-parameters"></a>

#### Servis Parametreleri

Servis konfigürasyonları <kbd>app/$env/providers</kbd> klasörü içerisinde tanımlanırlar ve çevre ortamı değiştiğinde ( local, test, production ) farklı davranışlar sergileyebilirler. <kbd>AbstractServiceProvider</kbd> sınıfı içerisinde <kbd>$this->getConfiguration()</kbd> metodu ile alınan bir servis konfigürasyon otomatik olarak <kbd>$container</kbd> nesnesi içerisinede kopyalanır. Böylece, 


```php
$container->get('servisAdı.params')
```
 
yöntemi ile bir servise ait parametrelere her yerde ulaşılmış olur.


<a name="connectors"></a>

### Konnektörler

Konnektörler bağlantı yönetimi ile ilgili servis sağlayıcılarıdır. Bir konnektör kendisine farklı parametreler gönderilerek açılan bağlantıları yönetir ve her yazılımcının aynı parametreler ile uygulamada birden fazla bağlantı açmasının önüne geçer.

Kullanmak istediğiniz konnektöre ait servis sağlayıcılarının <kbd>app/providers.php</kbd> dosyasında tanımlı olmaları gerekir.

```php
/*
|--------------------------------------------------------------------------
| Connectors
|--------------------------------------------------------------------------
*/
$container->addServiceProvider('ServiceProvider\Redis');
$container->addServiceProvider('ServiceProvider\Memcached');
```

<a name="load-connector"></a>

#### Konnektörü Yüklemek

Bir servis sağlayıcısı nesnelerde olduğu gibi konteyner içerisinden çağrılarak yüklenir. Aşağıdaki örnekte redis servis sağlayıcısından konfigürasyonda varolan <kbd>default</kbd> bağlantı tanımlamasını kullanarak <kbd>shared()</kbd> metodu ile paylaşılan bir bağlantı getirmesi talep ediliyor.

```php
$redis = $container->get('redis')->get(
    [
        'connection' => 'default'
    ]
);
```

Servis sağlayıcıları varolan bağlantıları yönetebilmek için aşağıdaki gibi <kbd>connections</kbd> anahtarına sahip bir konfigürasyon dosyasına ihtiyaç duyarlar. Aşağıda redis için <kbd>default</kbd> bağlantısına ait bir konfigürasyon örneği gösteriliyor.

```php
return array(

    'connections' => 
    [
        'default' => [
            'host' => '127.0.0.1',
            'port' => 6379,
            'options' => [
                'persistent' => false,
                'auth' => '123456',
                'timeout' => 30,
                'attempt' => 100,
                'serializer' => 'none',
                'database' => null,
                'prefix' => null,
            ]
        ],
        
        'second' => [

        ],
    ],
);

/* Location: .app/local/providers/redis.php */
```

Eğer <kbd>second</kbd> bağlantısına ait bir bağlantı isteseydik o zaman servis sağlayıcımızı aşağıdaki gibi çağırmalıydık.

```php
$redis2 = $container->get('redis')->shared(
    [
        'connection' => 'second'
    ]
);
```

Eğer redis servis sağlayıcısından konfigürasyonda olmayan bir bağlantı talep etseydik aşağıdaki gibi <kbd>factory()</kbd> fonksiyonunu kullanmalıydık.

```php
$redis = $container->get('redis')->factory(
    [
        'driver' => 'redis',
        'options' => array(
        	'host' => '127.0.0.1',
	        'port' => 6379,
	        'options' => array(
	            'persistent' => false,
	            'auth' => '123456',
	            'timeout' => 30,
	            'attempt' => 100,
	            'serializer' => 'igbinary',
	            'database' => null,
	            'prefix' => null,
	        )
       )
    ]
);
```

Servis sağlayıcısı bir kez yüklendikten sonra artık redis metotlarına erişebilirsiniz.

```php
$redis->method();
```

<a name="default-connectors"></a>

### Konnektör Listesi

Aşağıdaki tablo varolan konnektörlerin bir listesini gösteriyor.

<table>
    <thead>
        <tr>
            <th>Konnektör</th>
            <th>Konfigürasyon</th>
            <th>Açıklama</th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td><a href="Container-Connectors.md#amqp">amqp</a></td>
            <td>queue.php</td>
            <td>Uygulamanızdaki <a href="http://php.net/manual/pl/book.amqp.php" target="_blank">AMQP</a> bağlantılarını yönetir.</td>
        </tr>
        <tr>
            <td><a href="Container-Connectors.md#amqp">amqpLib</a></td>
            <td>queue.php</td>
            <td>Uygulamanızdaki <a href="https://github.com/php-amqplib/php-amqplib" target="_blank">AMQPLib</a> (php-amqplib/php-amqplib) bağlantılarını yönetir.</td>
        </tr>
        <tr>
            <td><a href="Container-Connectors.md#cacheFactory">cacheFactory</a></td>
            <td>$sürücü.php</td>
            <td>Uygulamanızdaki sürücülere göre cache bağlantılarını yönetir.</td>
        </tr>
        <tr>
            <td><a href="Container-Connectors.md#database">database</a></td>
            <td>database.php</td>
            <td>Uygulamanızda seçilen database sürücüsüne göre <a href="http://php.net/manual/en/book.pdo.php" target="_blank">PDO</a> veritabanı bağlantılarını yönetir.</td>
        </tr>
        <tr>
            <td><a href="Container-Connectors.md#doctrineDbal">doctrineDBAL</a></td>
            <td>database.php</td>
            <td>Uygulamanızda seçilen database sürücüsüne göre <a href="http://www.doctrine-project.org/projects/dbal.html" target="_blank">DoctrineDBAL</a> PDO veritabanı bağlantılarını yönetir.</td>
        </tr>
        <tr>
            <td><a href="Container-Connectors.md#qb">qb</a></td>
            <td>database.php</td>
            <td>Uygulamanızdaki database servis sağlayıcısını kullanarak Doctrine QueryBuilder nesnesini oluşturur.</td>
        </tr>
        <tr>
            <td><a href="Container-Connectors.md#memcached">memcached</a></td>
            <td>memcached.php</td>
            <td>Uygulamanızdaki <a href="http://php.net/manual/en/book.memcached.php" target="_blank">Memcached</a>  bağlantılarını yönetmenize yardımcı olur.</td>
        </tr>
        <tr>
            <td><a href="Container-Connectors.md#memcache">memcache</a></td>
            <td>memcache.php</td>
            <td>Uygulamanızdaki <a href="http://php.net/manual/en/book.memcache.php" target="_blank">Memcache</a> bağlantılarını yönetmenize yardımcı olur.</td>
        </tr>
        <tr>
            <td><a href="Container-Connectors.md#mongo">mongo</a></td>
            <td>mongo.php</td>
            <td>Uygulamanızdaki <a href="http://php.net/manual/en/book.mongo.php" target="_blank">MongoDb</a> veritabanı bağlantılarını yönetir.</td>
        </tr>
        <tr>
            <td><a href="Container-Connectors.md#redis">redis</a></td>
            <td>redis.php</td>
            <td>Uygulamanızdaki <a href="http://redis.io/" target="_blank">Redis</a> veritabanı bağlantılarını yönetir.</td>
        </tr>
    </tbody>
</table>

 Konnektörler hakkındaki detaylı dökümentasyon için [Container-Connectors.md](Container-Connectors.md) dosyasına gözatabilirsiniz.