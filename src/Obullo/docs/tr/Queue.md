
## Kuyruklama

Kuyruklama paketi uzun sürmesi beklenen işlemlere ( loglama, email gönderme, sipariş alma gibi. ) ait verileri mesaj gönderim protokolü  ( AMQP ) üzerinden arkaplanda işlem sırasına sokar. Kuyruğa atılan veriler eş zamanlı işlemler (multi threading) ile tüketilerek işler arkaplanda tamamlanır ve kuyruktan silinir, böylece uzun süren işlemler ön yüzde sadece işlem sırasına atıldığından uygulamanıza gelen http istekleri büyük bir yükten kurtulmuş olur.

<ul>
<li><a href="#requirements">Sunucu Gereksinimleri</a></li>
<li><a href="#service-provider">Servis Sağlayıcısı</a></li>
<li><a href="#accessing-methods">Metotlara Erişim</a></li>
<li><a href="#queuing-a-job">$this->queue->push()</a></li>
<li><a href="#delaying-a-job">$this->queue->later()</a></li>
<li>
    <a href="#workers">İşçiler</a>
    <ul>
        <li><a href="#define-worker">$job->fire()</a></li>
        <li><a href="#delete-job">$job->delete()</a></li>
        <li><a href="#release-job">$job->release()</a></li>
        <li><a href="#job-attempt">$job->getAttempts()</a></li>
        <li><a href="#job-id">$job->getId()</a></li>
        <li><a href="#job-name">$job->getName()</a></li>
        <li><a href="#job-body">$job->getRawBody()</a></li>
        <li><a href="#job-isDeleted">$job->isDeleted()</a></li>
    </ul>
</li>
<li>
    <a href="#running-workers">Konsoldan İşçileri Çalıştırmak</a>
    <ul>
        <li><a href="#show">Kuyruğu Listelemek</a></li>
        <li><a href="#listen">Kuyruğu Dinlemek</a></li>
        <li><a href="#worker-parameters">İşçi Parametreleri</a></li>
        <li><a href="#worker-logs">İşçi Logları</a></li>
    </ul>
</li>
<li><a href="#threading">Çoklu İş Parçaları</a> (Multi Threading)</a></li>
<li><a href="#saving-failed-jobs">Başarısız İşleri Kaydetmek</a></li>
<li><a href="#cloud-solutions">Bulut Çözümler</a></li>
<li><a href="#method-reference">Queue Sınıfı Referansı</a></li>
</ul>

<a name="requirements"></a>

### Sunucu Gereksinimleri

Amqp servis sağlayıcısını kullanıyorsanız uygulamanızda php AMQP genişlemesinin kurulu olması gerekir. Php AMQP arayüzü ile çalışan birçok kuyruklama yazılımı mevcuttur. Bunlardan bir tanesi de <a href="https://www.rabbitmq.com/" target="_blank">RabbitMQ</a> yazılımıdır. Aşağıdaki linkten RabbitMQ yazılımı için Ubuntu işletim sistemi altında gerçekleştilen örnek bir kurulum bulabilirsiniz.

<a href="https://github.com/obullo/warmup/tree/master/AMQP/RabbitMQ">RabbitMQ ve Php AMQP Extension Kurulumu </a>

Diğer AMQP Yazılımları ve Servisler

* <a href="http://zeromq.org/bindings:php/" target="_blank">ZeroMQ</a>
* <a href="https://qpid.apache.org/" target="_blank">Apache Qpid</a>
* <a href="https://www.cloudamqp.com/" target="_blank">Cloud AMQP</a>

<a name="service-provider"></a>

### Servis Sağlayıcısı

Queue servisi ve amqp servis sağlayıcısının <kbd>app/providers.php</kbd> dosyasında tanımlı olduğundan emin olun.

```php
$container->addServiceProvider('ServiceProvider\Queue');
$container->addServiceProvider('ServiceProvider\Connector\Amqp');
```

Queue servisi ana konfigürasyonu <kbd>providers/queue.php</kbd> dosyasından konfigüre edilir. Dosya içerisindeki <kbd>connections</kbd> anahtarına AMQP servis sağlayıcısı için gereken bağlantı bilgileri girilir.

<kbd>ServiceProvider\Queue</kbd> servisinde varsayılan olarak <kbd>Amqp</kbd> sürücüsü tanımlıdır. Kuyruklama servisi için sürücü seçenekleri aşağıdaki gibidir.

#### Amqp ( PECL )

```php
$container->share('queue', 'Obullo\Queue\Handler\Amqp')
    ->withArgument($container->get('amqp'))
    ->withArgument($config->getParams());
```

#### AmqpLib ( Composer / php-amqplib )

Eğer AmqpLib alternatifini kullanmak istiyorsanız <kbd>ServiceProvider\Queue</kbd> servisi kuyruklama sürücüsü AmqpLib olarak güncelleyin.

```php
$container->share('queue', 'Obullo\Queue\Handler\AmqpLib')
    ->withArgument($container->get('amqp'))
    ->withArgument($config->getParams());
```

Ayrıca <kbd>ServiceProvider\Connector\Amqp</kbd> dosyasından servis sağlayıcı konnektörünü AmqpLib olarak güncellemeniz gerekir.

```php
$container->share('amqp', 'Obullo\Container\ServiceProvider\Connector\AmqpLib')
    ->withArgument($container)
    ->withArgument($config->getParams());
```

<a name="accessing-methods"></a>

#### Metotlara Erişim

Queue servisi aracılığı ile queue metotlarına aşağıdaki gibi erişilebilir.

```php
$container->get('queue')->method();
```

Kontrolör içerisinden,

```php
$this->queue->method();
```

<a name="queuing-a-job"></a>

#### $this->queue->push()

Bir işi kuyruğa atmak için <kbd>$this->queue->push()</kbd> metodu kullanılır.

```php
$this->queue->push(
    'Workers@Mailer',
    'mailer.1',
    array('mailer' => 'x', 'message' => 'Hello World !')
);
```

Birinci parametreye <kbd>app/classes/Workers/</kbd> klasörü altındaki işçiye ait sınıf adı, ikinci parametreye kuyruk adı, üçüncü parametreye ise kuyruğa gönderilecek veriler girilir. Opsiyonel olan dördüncü parametreye ise varsa amqp sürücüsüne ait gönderim seçenekleri girilebilir.

Aşağıda RabbitMQ AMQP sağlayıcısına ait web panelinden kuyruğa atılmış bir iş örneği görülüyor.

![RabbitMQ](images/rabbitmq.png?raw=true)


<a name="delaying-a-job"></a>

#### $this->queue->later()

```php
$this->queue->later(
    $delay = 60,
    'Workers@Order',
    'orders',
    array('order_id' => 'x', 'order_data' => [])
);
```

Eğer later metodu kullanılarak ilk parametreye integer türünde (unix time) bir zaman değeri girilirse girilen veri kuyruğa belirlenen süre kadar gecikmeli olarak eklenir.

<a name="workers"></a>

### İşçiler

Uygulamada kuyruğu tüketen her işçi <kbd>app/classes/Workers/</kbd> klasörü altında çalışır. Bir işi kuyruğa gönderirken iş parametresine uygulamanızdaki klasör yolunu vererek kuyruğu tüketecek işçi belirlenir.

```php
$this->queue->push(
    'Workers@Mailer',
    'mailer.1',
    array('mailer' => 'x', 'message' => 'Hello World !')
);
```

Yukarıdaki örnekte <kbd>Workers@Mailer</kbd> adlı işe ait girilen veriler <kbd>mailer.1</kbd> adlı kuyruğa gönderilir.

<a name="define-worker"></a>

#### $job->fire()

Kuyruğa gönderme esnasında parametre olarak girilen işçi adı <kbd>app/classes/Workers/</kbd> klasörü altında bir sınıf olarak yaratılmalıdır. Worker sınıfı içerisinde tanımlı olan fire metodu ilk parametresine iş sınıfı ikinci parametresine ise işe ait kuyruk verileri gönderilir.

Aşağıda <kbd>Workers@Mailer</kbd> örneği görülüyor.

```php
namespace Workers;

use Obullo\Queue\Job;
use Obullo\Queue\JobInterface;
use Obullo\Container\ContainerAwareTrait;
use Obullo\Container\ContainerAwareInterface;

class Mailer implements JobInterface, ContainerAwareInterface
{
    use ContainerAwareTrait;

    public function fire($job, array $data)
    {
        switch ($data['mailer']) { 
        case 'x': 

            print_r($data);

            // Send mail message using "x" mail provider

            break;
        }
        if ($job instanceof Job) {
            $job->delete(); 
        }       
    }
}

/* Location: .app/classes/Workers/Mailer.php */
```

<a name="delete-job"></a>

#### $job->delete()

Fire metodu ile elde edilen iş nesnesi iş tamamlandıktan sonra <kbd>$job->delete()</kbd> metodu ile silinmelidir. Aksi durumda tüm işler kuyruklama yazılımında birikir.

```php
public function fire($job, array $data)
{
    imageResize($data);  // Do job

    if ($job instanceof Job) {  // Delete completed job
        $job->delete(); 
    }       
}
```

<a name="release-job"></a>

####  $job->release($int $delay = 0)

Eğer bir iş herhangi bir nedenle tekrar kuyruğa atılmak isteniyorsa <kbd>release</kbd> metodu kullanılır.

```php
public function fire($job, array $data)
{
    // Process the job...

    $job->release();
}
```

İlk parametreye bir sayı girerek işin tekrar kuyruğa atılma zamanını geciktirebilirsiniz.

```php
$job->release(5);
```

<a name="job-attempt"></a>

#### $job->getAttempts()

Eğer iş işlenirken herhangi bir istisnai hata sözkonusu oluğunda, hatalı iş otomatik olarak tekrar kuyruğa atılır. Hatalı bir işin denenme sayısını <kbd>$job->getAttempts()</kbd> metodu ile elde edebilirsiniz.

```php
if ($job->getAttempts() > 3)
{
    //
}
```

<a name="job-id"></a>

#### $job->getId()

İhtiyaç duyulduğunda işin ID değerini almak için <kbd>$job->getId()</kbd> metodunu kullanabilirsiniz.

```php
$job->getId();  // 15
```

<a name="job-name"></a>

#### $job->getName()

İhtiyaç duyulduğunda kuyruk / iş adını almak için <kbd>$job->getName()</kbd> metodunu kullanabilirsiniz.

```php
$job->getName();  // mailer.1
```

<a name="job-body"></a>

#### $job->getRawBody()

İhtiyaç duyulduğunda işe ait veriyi almak için <kbd>$job->getRawBody()</kbd> metodunu kullanabilirsiniz.

```php
$job->getRawBody();

// {"job":"Workers\\Mailer","data":{"files":[{"name":"logs.jpg","type":"image\/jpeg","fileurl
```

<a name="job-isDeleted"></a>

#### $job->isDeleted()

Eğer iş kuyruktan silinmişse <kbd>true</kbd> değerine aksi durumda <kbd>false</kbd> değerine döner.


<a name="running-workers"></a>

### Konsoldan İşçileri Çalıştırmak

<a name="show"></a>

#### Kuyruğu Listelemek

Konsol komutunu çalıştırabilmek için proje ana dizinine girin.

```php
cd /var/www/myproject/
```

Show komutu ile kuyruktaki tüm işleri görebilirsiniz.

```php
php task queue show --worker=Workers@Mailer --job=mailer.1 --output=1
```

```php
Following "mailer.1" queue ... 

------------------------------------------------------------------------------------------
Job ID  | Job Name            | Data 
------------------------------------------------------------------------------------------
1       | Workers@Mailer      | {"files":[{"name":"logs.jpg","type":"image\/jpeg", .. }
2       | Workers@Mailer      | {"files":[{"name":"logs.jpg","type":"image\/jpeg","fileurl":"..}
```

<a name="listen"></a>

#### Kuyruğu Dinlemek

Bir işçiyi çalıştarak bir kuyruğu dinlemek onu tüketmek anlamında gelir. Kuyruğu tüketmek için konsoldan aşağıdaki komut çalıştırılmalıdır.

```php
php task queue listen --worker=Workers@Mailer --job=mailer.1 --output=1
```

##### Gelişmiş parametreler kullanmak

Gelişmiş parametreler kullanarak işçinin kullanabileceği maximum hafıza, gecikme süresi, bekleme süresi gibi özellikleri belirleyebilirsiniz.

```php
php task queue listen --worker=Workers@Logger --job=logger.1 --delay=0 --memory=128 --timeout=0 --sleep=3
```

##### Parametreler için kısayollar

Ayrıca işçi tanımlanırken aşağıdaki gibi kısa yollar da kullanabilir.

```php
php task queue listen --w=Workers@Mailer --j=mailer.1 --o=1
```

##### Hata Ayıklama Modu

Konsoldan output değerini 1 yapmanız durumunda bulunan hatalar ekrana dökülür. Bu parametre <kbd>test</kbd> veya <kbd>local</kbd> çalışma ortamlarında kullanılmalıdır.

```php
php task queue listen --w=Workers@Logger --j=logger.1 --o=1
```

<a name="worker-parameters"></a>

##### İşçi Parametreleri

<a name="worker"></a>
<a name="job"></a>
<a name="delay"></a>
<a name="memory"></a>
<a name="timeout"></a>
<a name="sleep"></a>
<a name="attempt"></a>
<a name="output"></a>
<a name="env"></a>
<a name="var"></a>

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
<td>Kuyrukta bir hatadan dolayı yapılamayan bir iş için en fazla kaç kere daha deneme yapılacağını belirler. Bir sayı verilmez ise iş el ile silinene kadar kuyrukta kalır.</td>
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

<a name="worker-logs"></a>

#### İşçi Logları

Varsayılan olarak konsoldan uygulamaya gelen işçi isteklerine ait log kayıtları tutulmaz. Bu verilere genellikle işçilerin <kbd>ayrı sunucularda</kbd> tutulması gerektiğinde ihtiyaç duyulur.

İşci sunucularınız ayrı ise bir işçi sunucusuna ait iş loglarını açmak için <kbd>Workers\Logger</kbd> sınıfı içerisinden <kbd>return</kbd> komutunu yorum içerisine almanız yeterli olur.

```php
if ($event['request'] == 'worker') {   // Disable worker server logs.
    // return;
}
```

<a name="threading"></a>

### Çoklu İş Parçaları ( Multi Threads )

Çok çekirdekli sunucularda bir kuyruğu eş zamanlı tüketmek için çoklu iş parçalarına ihtiyaç duyulur. Bu konuda ayrıntılı bilgi için [Queue-MultiThreads.md](Queue-MultiThreads.md) dökümentasyonunu inceleyebilirsiniz.

<a name="saving-failed-jobs"></a>

### Başarısız İşleri Kaydetmek

Eğer iş işlenirken herhangi bir istisnai hata sözkonusu oluğunda, hatalı iş otomatik olarak tekrar kuyruğa atılır fakat gerçekleşen hataları takip edebilmek için işleri bir veritabanına kaydetmek gerekir. Bu konuda ayrıntılı bilgi için [Queue-SaveFailures.md](Queue-SaveFailures.md) dökümentasyonunu inceleyebilirsiniz.

<a name="cloud-solutions"></a>

### Bulut Çözümler

Eğer kuyruklama işi için bulut bir çözüm düşüyorsanız aşağıdaki bu servisleri listeledik.

<a name="cloud-amqp"></a>

#### Cloud AMQP Servisi

Eğer RabbitMQ kullanıyor ve dağıtık bir kuyruklama servisi arıyorsanız <a href="https://www.cloudamqp.com/" target="_blank">cloud amqp</a> servisine bir gözatın.

<a name="method-reference"></a>

### Queue Sınıfı Referansı

##### $this->queue->push(string $job, string $route, array $data, array $options = array());

Bir işi kuyruk sağlayıcınıza gönderir. Birinci parametreye iş adı, ikinci parametreye kuyruk adı, üçüncü parametreye kuyruğa ait veri, opsiyonel olan son parametreye ise varsa gönderim opsiyonları girilebilir.

##### $this->queue->later(int $delay, $job, string $route, array, $data, array $options = array());

Bir işi kuyruk sağlayıcınıza gecikmeli olarak gönderir. Push metodundan tek farkı ilk parametrenin gecikme süresi için ayrılmış olmasıdır.

##### $this->queue->pop(string $job, string $queue);

Girilen kuyruk adına göre kuyruktan bir veriyi okur ve sonraki veriye geçer. Exchange metodu ile birlikte kullanılmalıdır.

```php
while (true) {
    $job = $this->queue->pop('Workers@Mailer', 'mailer.1');
    if (! is_null($job)) {
        echo $job->getRawBody()."\n";
    }
}
```

##### $this->queue->delete(string $queue);

Kuyruktaki tüm veriyi kuyruk adı ile birlikte kalıcı olarak siler.
