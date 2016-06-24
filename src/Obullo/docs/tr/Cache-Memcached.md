
## Memcached Sürücüsü

Memcached sürücüsü sunucunuzda php extension olarak kurulmayı gerektirir. Ubuntu ve benzer linux sistemleri altında memcached kurulumu için <a href="https://github.com/obullo/warmup/tree/master/Memcached" target="_blank">bu belgeden</a> yararlanabilirsiniz.

<ul>
<li><a href="#service-provider">Servis Sağlayıcısı</a></li>
<li><a href="#service-provider-connections">Servis Sağlayıcısı Bağlantıları</a></li>
<li><a href="#service">Servis</a></li>
<li>
    <a href="#memcached-reference">Memcached Sürücü Referansı</a>
    <ul>
        <li><a href="#memcached-hasItem">$this->cache->hasItem()</a></li>
        <li><a href="#memcached-setItem">$this->cache->setItem()</a></li>
        <li><a href="#memcached-setItems">$this->cache->setItems()</a></li>
        <li><a href="#memcached-getItem">$this->cache->getItem()</a></li>
        <li><a href="#memcached-replaceItem">$this->cache->replaceItem()</a></li>
        <li><a href="#memcached-replaceItems">$this->cache->replaceItems()</a></li>
        <li><a href="#memcached-removeItem">$this->cache->removeItem()</a></li>
        <li><a href="#memcached-removeItems">$this->cache->removeItems()</a></li>
        <li><a href="#memcached-setSerializer">$this->cache->setSerializer()</a></li>
        <li><a href="#memcached-getSerializer">$this->cache->getSerializer()</a></li>
        <li><a href="#memcached-flushAll">$this->cache->flushAll()</a></li>
    </ul>
</li>

<li><a href="#helper-methods">Yardımcı Fonksiyonlar</a></li>
</ul>

<a name="service-provider"></a>

#### Servis Sağlayıcısı

<kbd>app/providers.php</kbd> dosyasında servis sağlayıcısının tanımlı olduğundan emin olun.

```php
$container->addServiceProvider('ServiceProvider\Connector\Memcached');
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

Servis sağlayıcısı <kbd>connection</kbd> anahtarındaki bağlantı değerlerini <kbd>providers/memcached.php</kbd> içerisinden alır.

```php
return array(

    'connections' => 
    [
        'default' => [
            'host' => '127.0.0.1',
            'port' => 11211,
            'weight' => 1,
            'options' => [

            ]
        ]
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
    $cache->shared(['driver' => 'memcached', 'connection' => 'default'])
);
```

Yukarıda görüldüğü gibi memcached servis sağlayıcısı varsayılan cache servisi olarak tanımlanıyor.

<a name="memcached-reference"></a>

#### Memcached Sürücü Referansı

Bu sınıf içerisinde tanımlı olmayan metotlar __call metodu ile php <kbd>Memcached</kbd> sınıfından çağrılırlar.

<a name="memcached-hasItem"></a>

##### $this->cache->hasItem(string $key)

Bir anahtarın var olup olmadığını kontrol eder. Anahtar mevcut ise <kbd>true</kbd> değilse <kbd>false</kbd> değerinde döner.

<a name="memcached-setItem"></a>

##### $this->cache->setItem(string $key, mixed $data, int $ttl = 60);

Girilen anahtara veri kaydeder, son parametre sona erme süresine "0" girilirse veri siz silinceye kadar yok olmaz. Eğer ilk parametreye bir dizi gönderirseniz ikinci parametreyi artık sona erme süresi olarak kullanabilirsiniz.

<a name="memcached-setItems"></a>

##### $this->cache->setItems(array $data, $ttl = 60);

Önbellek deposuna girilen dizi türünü ayrıştırarak kaydeder.

<a name="memcached-getItem"></a>

##### $this->cache->getItem(string $key);

Anahtara atanmış değere geri döner. Anahtar mevcut değilse <kbd>false</kbd> değerine döner. Anahtar bir dizi de olabilir.

<a name="memcached-replaceItem"></a>

##### $this->cache->replaceItem(string $key, $value, $ttl = 60);

Varsayılan anahtara ait değeri yeni değer ile günceller.

<a name="memcached-replaceItems"></a>

##### $this->cache->replaceItems(array $data, $ttl = 60);

Dizi türünde girilen yeni değerleri günceller.

<a name="memcached-removeItem"></a>

##### $this->cache->removeItem(string $key);

Girilen anahtarı önbellekten siler.

<a name="memcached-removeItems"></a>

##### $this->cache->removeItems(array $keys);

Dizi türünde girilen anahtarların tümünü siler.

<a name="memcached-setSerializer"></a>

##### $this->cache->setSerializer(string|constant $serializer = 'php');

Geçerli serileştirici türünü seçer. Serileştirici tipleri : <kbd>php</kbd>, <kbd>igbinary</kbd> ve <kbd>json</kbd> dır.

<a name="memcached-getSerializer"></a>

##### $this->cache->getSerializer();

Geçerli serileştirici türüne geri döner.

<a name="memcached-flushAll"></a>

##### $this->cache->flushAll()

Geçerli veritabanından tüm anahtarları siler.

<a name="helper-methods"></a>

### Yardımcı Fonksiyonlar

<a name="memcached-setOption"></a>

##### $this->cache->setOption(string|constant $option = 'OPT_SERIALIZER', string|constant $value = 'SERIALIZER_PHP');

Memcached için bir opsiyon tanımlar. Birer sabit olan opsiyonlar parametrelerden string olarak kabul edilir. Sabitler hakkında daha detaylı bilgi için <a href="http://www.php.net/
manual/en/memcached.constants.php">bu adrese</a> bir gözatın.

<a name="memcached-getOption"></a>

##### $this->cache->getOption(string|constant $option = 'OPT_SERIALIZER');

Geçerli opsiyon değerine döner. Opsiyon sabitleri hakkında detaylı bilgi için <a href="http://www.php.net/manual/en/memcached.constants.php">bu adrese</a> bir gözatın.

<a name="memcached-getInfo"></a>

##### $this->cache->getInfo();

Sunucu üzerinde memcached yazılımı hakkındaki bigilere geri döner.

<a name="memcached-getAllKeys"></a>

##### $this->cache->getAllKeys();

Kayıtlı tüm anahtarlara geri döner.

<a name="memcached-getAllData"></a>

##### $this->cache->getAllData();

Kayıtı tüm verilere geri döner.

<a name="memcached-getMetaData"></a>

##### $this->cache->getMetaData(string $key);

Girilen anahtarın meta verisine geri döner.