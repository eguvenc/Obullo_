
## Redis Sürücüsü / Veritabanı

Redis sürücüsü sunucunuzda php extension olarak kurulmayı gerektirir. Ubuntu ve benzer linux sistemleri altında redis kurulumu için <a href="https://github.com/obullo/warmup/tree/master/Redis" target="_blank">bu belgeden</a> yararlanabilirsiniz.

<ul>
<li><a href="#service-provider">Servis Sağlayıcısı</a></li>
<li><a href="#service-provider-connections">Servis Sağlayıcısı Bağlantıları</a></li>
<li><a href="#service">Servis</a></li>
<li>
    <a href="#cache-reference">Redis Sürücü Referansı</a>
    <ul>
        <li><a href="#redis-hasItem">$this->cache->hasItem()</a></li>
        <li><a href="#redis-setItem">$this->cache->setItem()</a></li>
        <li><a href="#redis-setItems">$this->cache->setItems()</a></li>
        <li><a href="#redis-getItem">$this->cache->getItem()</a></li>
        <li><a href="#redis-removeItem">$this->cache->removeItem()</a></li>
        <li><a href="#redis-removeItems">$this->cache->removeItems()</a></li>
        <li><a href="#redis-replaceItem">$this->cache->replaceItem()</a></li>
        <li><a href="#redis-replaceItems">$this->cache->replaceItems()</a></li>
        <li><a href="#redis-setSerializer">$this->cache->setSerializer()</a></li>
        <li><a href="#redis-getSerializer">$this->cache->getSerializer()</a></li>
        <li><a href="#redis-setOption">$this->cache->setOption()</a></li>
        <li><a href="#redis-getOption">$this->cache->getOption()</a></li>
        <li><a href="#redis-flushAll">$this->cache->flushAll()</a></li>
    </ul>
</li>
<li>
    <a href="#redis-db-reference">Redis Veritabanı Referansı</a>
</li>
</ul>

<a name="service-provider"></a>

#### Servis Sağlayıcısı

<kbd>app/providers.php</kbd> dosyasında servis sağlayıcıların tanımlı olduğundan emin olun.

```php
$container->addServiceProvider('ServiceProvider\Connector\Redis');
```

CacheManager servis sağlayıcısı önbellekleme için farklı sürücüler ile farklı bağlantıları almanıza yardımcı olur.

```php
$cache = $container->get('cacheManager')->shared(
    [
        'driver' => 'memcached', 
        'connection' => 'default'
    ]
);
$cache->method();
```

<a name="service-provider-connections"></a>

#### Servis Sağlayıcısı Bağlantıları

Servis sağlayıcısı <kbd>connection</kbd> anahtarındaki bağlantı değerlerini bu dosya içerisinden alır.

```php

return array(

    'connections' => 
    [
        'default' => [ 
            'host' => '10.0.0.168',
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
        
    ]
);
```

<a name="service"></a>

#### Servis

Cache servisi uygulamanızda önceden yapılandırılmış cache arayüzüne erişmenizi sağlar.

```php
$this->container->get('cache')->metod();
```

Varsayılan sürücü türü <kbd>app/classes/ServiceProvider/Cache</kbd> servis sağlayıcısından aşağıdaki gibi yapılandırılır.

```php
$cache = $container->get('cacheManager');

$container->share(
    'cache',
    $cache->shared(['driver' => 'redis', 'connection' => 'default'])
);
```

Yukarıda görüldüğü gibi redis servis sağlayıcısı varsayılan cache servisi olarak tanımlanıyor.


<a name="cache-reference"></a>

### Cache Sınıfı Referansı

Bu sınıf içerisinde tanımlı olmayan metotlar __call metodu ile php <kbd>Redis</kbd> sınıfından çağrılırlar. Anahtar içerisinde <kbd>:</kbd> karakterini kullanırsanız anahtarlar gruplanarak gösterilirler.

<a name="redis-hasItem"></a>

##### $this->cache->hasItem(string $key)

Bir anahtarın var olup olmadığını kontrol eder. Anahtar mevcut ise <kbd>true</kbd> değilse <kbd>false</kbd> değerinde döner.

<a name="redis-setItem"></a>

##### $this->cache->setItem(mixed $key, mixed $data, int optional $expiration)

Önbellek deposuna veri kaydeder. Kaydetme işlemlerinde <kbd>string</kbd> ve <kbd>array</kbd> türlerini kullanabilirsiniz Eğer ilk parametreye bir dizi gönderirseniz ikinci parametreyi artık sona erme süresi olarak kullanabilirsiniz.

<a name="redis-setItems"></a>

##### $this->cache->setItems(array $data, $ttl = 60);

Önbellek deposuna girilen dizi türünü ayrıştırarak kaydeder.

<a name="redis-getItem"></a>

##### $this->cache->getItem($key)

Önbellek deposundan veri okur. Okuma işlemlerinde string ve array türlerini kullanabilirsiniz. Anahtar içerisinde ":" karakterini kullanarak gruplanmış verilere ulaşabilirsiniz.

```php
$this->cache->getItem('key');           // Çıktı value
$this->cache->getItem('example:key');   // Çıktı value
```

<a name="redis-replace"></a>

##### $this->cache->replaceItem(string $key, $value, $ttl = 60);

Varsayılan anahtara ait değeri yeni değer ile günceller.

<a name="redis-replaceItems"></a>

##### $this->cache->replaceItems(array $data, $ttl = 60);

Dizi türünde girilen yeni değerleri günceller.

<a name="redis-removeItem"></a>

##### $this->cache->removeItem(string $key);

Girilen anahtarı önbellekten siler.

<a name="redis-removeItems"></a>

##### $this->cache->removeItems(array $keys);

Dizi türünde girilen anahtarların tümünü siler.

<a name="redis-setSerializer"></a>

##### $this->cache->setSerializer(string|constant $serializer);

Encode ve decode işlemleri için serileştirici türünü seçer.

* **none**     : Serileştirici kullanılmaz veriler raw biçiminde kaydedilir.
* **php**      : Php serialize() fonksiyonunu serileştirici olarak seçer.
* **json**     : Serileştiriciyi JSON encoder fonksiyonu olarak seçer.
* **igbinary** : Serileştiriciyi igbinary olarak seçer.

<a name="redis-getSerializer"></a>

##### $this->cache->getSerializer();

Geçerli serileştirici türüne geri döner.

<a name="redis-setOption"></a>

##### $this->cache->setOption(string|constant $option = 'OPT_SERIALIZER', string|constant $value = 'SERIALIZER_NONE')

Bir opsiyon tanımlar. Birer sabit olan opsiyonlar parametrelerden string olarak kabul edilir. Sabitler hakkında daha detaylı bilgi için <a href="https://github.com/phpredis/phpredis#setoption">Redis setOption</a> metoduna bir gözatın.

<a name="redis-getOption"></a>

##### $this->cache->getOption(string|constant $option = 'OPT_SERIALIZER');

Geçerli opsiyon değerine döner. Daha detaylı bilgi için <a href="https://github.com/phpredis/phpredis#getoption">Redis getOption</a> metoduna bir gözatın.

<a name="redis-flushAll"></a>

##### $this->cache->flushAll()

Geçerli veritabanından tüm anahtarları siler. Bu işlemin sonucu daima <kbd>true</kbd> döner.

<a name="redis-db-reference"></a>

### Redis Veritabanı Referansı

Redis sürücüsünü eğer bir veritabanı olarak kullanmak istiyorsanız [Cache-Redis-Database.md](Cache-Redis-Database.md) dökümentasyonuna gözatabilirsiniz.