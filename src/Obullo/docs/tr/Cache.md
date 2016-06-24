
## Cache Servisi

Cache servisi sık kullanılan önbellekleme türleri için basit ve ortak bir arayüz sağlar.

<ul>
<li><a href="#service-provider">Servis Sağlayıcısı</a></li>
<li><a href="#cacheManager">CacheManager</a></li>
<li>
    <a href="#service">Servis</a>
    <ul>
        <li><a href="#cache-drivers">Sürücüler</a></li>
        <li>
            <a href="#interface">Ortak Arayüz Metotları</a>
            <ul>
                <li><a href="#common-hasItem">$this->cache->hasItem()</a></li>
                <li><a href="#common-setItem">$this->cache->setItem()</a></li>
                <li><a href="#common-setItems">$this->cache->setItems()</a></li>
                <li><a href="#common-getItem">$this->cache->getItem()</a></li>
                <li><a href="#common-removeItem">$this->cache->removeItem()</a></li>
                <li><a href="#common-removeItems">$this->cache->removeItems()</a></li>
                <li><a href="#common-replaceItem">$this->cache->replaceItem()</a></li>
                <li><a href="#common-replaceItems">$this->cache->replaceItems()</a></li>
                <li><a href="#common-setSerializer">$this->cache->setSerializer()</a></li>
                <li><a href="#common-getSerializer">$this->cache->getSerializer()</a></li>
                <li><a href="#common-flushAll">$this->cache->flushAll()</a></li>
            </ul>
        </li>
    </ul>
</li>
<li>
    <a href="#drivers">Sürücüler</a>
    <ul>
        <li><a href="#redis">Redis</a></li>
        <li><a href="#memcached">Memcached</a></li>
        <li><a href="#file">File</a></li>
        <li><a href="#file">Apc</a></li>
        <li><a href="#memcache">Memcache</a></li>
    </ul>
</li>
</ul>

<a name="service-provider"></a>

#### Servis Sağlayıcısı

Cache sınıfı konfigürasyonu <kbd>providers/$sürücü.php</kbd> dosyasından konfigüre edilir. Örneğin memcached sürücüsü için <kbd>providers/memcached.php</kbd> dosyasını konfigüre etmeniz gerekir. <kbd>app/providers.php</kbd> dosyasında servis sağlayıcısının tanımlı olduğundan emin olun.

```php
$container->addServiceProvider('ServiceProvider\Cache');
```
<a name="cacheManager"></a>

### CacheManager

Varsayılan sürücü türü <kbd>app/classes/ServiceProvider/Cache</kbd> servis sağlayıcısından aşağıdaki gibi yapılandırılır. Aşağıdaki örnekte <kbd>default</kbd> bağlantısı ile redis cache sürücüsüne bağlanılıyor.

```php
$container->get('cacheManager')->shared(['driver' => 'redis', 'connection' => 'default'])
```
<a name="service"></a>

### Servis

Cache servisi uygulamanızda önceden yapılandırılmış cache arayüzüne erişmenizi sağlar.

```php
$this->container->get('cache')->metod();
```

Varsayılan sürücü türü <kbd>app/classes/ServiceProvider/Cache</kbd> servis sağlayıcısından yapılandırılır.

```php
$cacheManager = $container->get('cacheManager');

$container->share(
    'cache',
    $cacheManager->shared(['driver' => 'redis', 'connection' => 'default'])
);
```

Yukarıda görüldüğü gibi redis cache sürücüsü varsayılan cache arayüzü olarak tanımlanıyor.

<a name="cache-drivers"></a>

#### Sürcüler

Bu sürüm için varolan cache sürücüleri aşağıdaki gibidir:

* Apc
* File
* Memcache
* Memcached
* Redis

Sürücü seçimi yapılırken küçük harfler kullanılmalıdır.

<a name="interface"></a>

#### Ortak Arayüz Metotları

Cache sürücüleri CacheInterface arayüzünü kullanırlar. Bu arayüz size cache servisinde hangi metotların ortak kullanıldığı gösterir ve eğer yeni bir sürücü yazacaksınız sizi bu metotları sınıfınıza dahil etmeye zorlar.

```php
interface CacheInterface
{
    public function hasItem($key);
    public function setItem($key, $data, $ttl = 60);
    public function setItems(array $data, $ttl = 60);
    public function getItem(string $key);
    public function replaceItem($key, $data, $ttl = 60);
    public function replaceItems(array $data, $ttl = 60);
    public function removeItem($key);
    public function removeItems(array $keys);
    public function flushAll();
}
```

<a name="common-hasItem"></a>

##### $this->cache->hasItem(string $key);

Eğer girilen anahtar önbellekte mevcut ise <kbd>true</kbd> değerine aksi durumda <kbd>false</kbd> değerine döner.

<a name="common-setItem"></a>

##### $this->cache->setItem(string $key, $value, $ttl = 60);

Önbellek deposuna veri kaydeder. Birinci parametre anahtar, ikici parametre değer, üçüncü parametre ise anahtara ait verinin yok olma süresidir. Üçüncü parametrenin varsayılan değeri <kbd>60</kbd> saniyedir. Eğer üçüncü parametreyi <kbd>0</kbd> olarak girerseniz önbelleğe kaydettiğiniz anahtar kalıcı olur.

<a name="common-setItems"></a>

##### $this->cache->setItems(array $data, $ttl = 60);

Önbellek deposuna girilen dizi türünü ayrıştırarak kaydeder. 

<a name="common-getItem"></a>

##### $this->cache->getItem(string $key);

Önbellek deposundan veri okur.

<a name="common-removeItem"></a>

##### $this->cache->removeItem(string $key);

Anahtarı ve bu anahtara kaydedilen değeri bütünüyle siler.

<a name="common-removeItems"></a>

##### $this->cache->removeItems(array $keys);

Dizi türünde girilen anahtarların tümünü siler.

<a name="common-replaceItem"></a>

##### $this->cache->replaceItem(string $key, $value, $ttl = 60);

Varsayılan anahtara ait değeri yeni değer ile günceller.

<a name="common-replaceItems"></a>

##### $this->cache->replaceItems(array $data, $ttl = 60);

Dizi türünde girilen yeni değerleri günceller.

<a name="common-setSerializer"></a>

##### $this->cache->setSerializer(string|constant $serializer = null);

Varsa sürücünüzün desteklediği serileştirici türünü seçer.

<a name="common-getSerializer"></a>

##### $this->cache->getSerializer();

Geçerli serileştirici türüne geri döner.

<a name="redis-setOption"></a>

##### $this->cache->setOption(string|constant $option = 'OPT_SERIALIZER', string|constant $value = 'SERIALIZER_NONE')

Redis ve Memcached sürücüleri için bir opsiyon tanımlar. Birer sabit olan opsiyonlar parametrelerden string olarak kabul edilir. Sabitler hakkında daha detaylı bilgi için <a href="https://github.com/phpredis/phpredis#setoption">Redis setOption</a> metoduna bir gözatın.

<a name="redis-getOption"></a>

##### $this->cache->getOption(string|constant $option = 'OPT_SERIALIZER');

Redis ve Memcached sürücüleri için geçerli opsiyon değerine döner. Daha detaylı bilgi için <a href="https://github.com/phpredis/phpredis#getoption">Redis getOption</a> metoduna bir gözatın.

<a name="common-flushAll"></a>

##### $this->cache->flushAll()

Bellek içerisindeki tüm anahtarları ve değerlerini yokeder.


<a name="drivers"></a>

### Sürücüler

Şu anki sürümde aşağıdaki sürücüler desteklenmektedir.

<a name="redis"></a>

#### Redis

Redis sürücüsü kurulum konfigürasyon ve sınıf referansı için [Cache-Redis.md](Cache-Redis.md) dosyasını okuyunuz.

<a name="memcached"></a>

#### Memcached

Memcached sürücüsü kurulum konfigürasyon ve sınıf referansı için [Cache-Memcached.md](Cache-Memcached.md) dosyasını okuyunuz.

<a name="file"></a>

#### File

Varsayılan önbellek sürücüsüdür ortak arayüz metotlarını kullanarak text dosyalarına kayıt yapar.

<a name="apc"></a>

#### Apc

PECL eklentisi ile kurulum gerektirir ortak arayüz metotlarını kullanarak sunucu önbelleğine kayıt yapar. Kurulum ve sunucu gereksinimleri için <a href="http://php.net/manual/tr/book.apc.php">http://php.net/manual/tr/book.apc.php</a> adresini ziyaret ediniz.