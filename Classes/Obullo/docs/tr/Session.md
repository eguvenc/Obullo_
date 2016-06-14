
## Oturum Sınıfı

Oturum sınıfı kullanıcılar uygulamayı gezerken kendilerine ait nitelikleri devam ettirmeyi ve onlara ait aktiviteleri takip etmenizi sağlar. Bir oturum verisi içerisine kaydedilmiş kimlik bilgileri, nitelikler ve kullanıcı izinleri gibi özel bilgiler oturum süresi boyunca oturum kimliği vasıtası ile hafıza depoları içerisinde saklı tutulur. Oturum sona erdiğinde ise bu bilgiler belirli bir süre içerisinde yok olur.

<ul>
    <li><a href="#service-provider">Servis Sağlayıcısı</a></li>
    <li><a href="#storages">Depolama Türleri</a></li>
    <li>
        <a href="#methods">Metotlara Erişim</a>
        <ul>
            <li><a href="#set">$this->session->set()</a></li>
            <li><a href="#get">$this->session->get()</a></li>
            <li><a href="#remove">$this->session->remove()</a></li>
            <li><a href="#regenerateId">$this->session->regenerateId()</a></li>
            <li><a href="#exists">$this->session->exists()</a></li>
            <li><a href="#getId">$this->session->getId()</a></li>
            <li><a href="#getAll">$this->session->getAll()</a></li>
            <li><a href="#destroy">$this->session->destroy()</a></li>
        </ul>
    </li>
    <li>
        <a href="#reminder">Hatırlatma Sınıfı</a>
        <ul>
            <li><a href="#rememberMe">$this->session->rememberMe()</a></li>
            <li><a href="#forgetMe">$this->session->forgetMe()</a></li>
        </ul>
    </li>
</ul>

<a name="service-provider"></a>

### Servis Sağlayıcısı

<kbd>app/providers.php</kbd> dosyasında servis sağlayıcısının tanımlı olduğundan emin olun.

```php
$container->addServiceProvider('Obullo\Container\ServiceProvider\Session');
```

Depolama sürücüsü ve diğer ayarlar <kbd>providers/session.php</kbd> dosyasından konfigüre edilir.

```php
'methods' => [
    ['name' => 'registerSaveHandler','argument' => ['Obullo\Session\SaveHandler\Cache']],
    ['name' => 'setName','argument' => ['']],
    ['name' => 'start','argument' => ['']]
]
```

<a name="storages"></a>

### Depolama Türleri

<table>
    <thead>
        <tr>
            <th>Depolama Türü</th>
            <th>Sürücüler</th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td>Cache</td>
            <td>Redis, Memcache, Memcached, Apc, File</td>
        </tr>
    </tbody>
</table>

<a name="methods"></a>

### Metotlara Erişim

```php
$container->get('session')->method();
```

Kontrolör içerisinden,

```php
$this->session->method();
```

<a name="set"></a>

#### $this->session->set(mixed $new = array(), $newval = '', $prefix = '')

Bir oturuma yeni veriler kaydeder.

```php
$this->session->set('foo', 'bar');
```

veri türü bir dizi de olabilir.

```php
$data = [
    'username' => 'test@example.com'
    'user_id' => 14
];
$this->session->set($data);
```

Opsiyonel olarak son parametre den bir önad girebilir.

```php
$this->session->set('order_id', 'QIJNMH', '_order');
```

<a name="get"></a>

#### $this->session->get($key, $prefix = '')

Bir oturumdan veri okur.

```php
echo $this->session->get('foo');  // bar
```
<a name="remove"></a>

#### $this->session->remove(mixed $new = array(), $prefix = '')

Bir oturuma ait verileri siler.

```php
$this->session->remove('foo');
```

veri türü bir dizi de olabilir.

```php
$data = [
    'username'
    'user_id'
];
$this->session->remove($data);
```

Opsiyonel olarak son parametre den bir önad girebilir.

```php
$this->session->remove('order_id', '_order');
```

<a name="regenerateId"></a>

#### $this->session->regenerateId($deleteOldSession = true, $lifetime = null)

Session id değerini yeniden yaratır. Eğer birinci parametre true gönderilirse yeni oturum oluşturulduktan sonra eski oturuma ait veriler silinir. 

```php
$this->session->regenerateId(false);
```

Eğer false değeri gönderilirse eski oturuma ait veriler silinmez. Varsayılan değer <kbd>true</kbd> değeridir.


```php
$this->session->regenerateId(true, 3600);
```

Eğer ikinci parametreden yukarıdaki gibi bir tamsayı gönderilirse oturumun ömrü belirlenebilir.


<a name="exists"></a>

#### $this->session->exists()

Bir oturumun var olup olmadığı hakkında bilgi verir. Eğer oturum id değeri mevcut ve aktif ise true değerine, bu iki durumdan biri geçersiz ise false değerine döner.

```php
if ($this->session->exists()) {
    
    // kullanıcının aktif bir oturumu var
}
```

<a name="getId"></a>

#### $this->session->getId()

Kullanıcının geçerli oturuma ait kimlik değerine geri döner.

```php
echo $this->session->getId()  // bqovdui8ra84tnv9g99vpqpav2
```

<a name="getAll"></a>

#### $this->session->getAll()

Tüm oturum verilerine bir dizi içerisinde geri döner.

<a name="destroy"></a>

#### $this->session->destroy()

Kullanıcı oturumunu sonlandırır. Tüm oturum verilerini kalıcı olarak yok eder.

<a name="reminder"></a>

### Hatırlatma Sınıfı

Hatırlatma nesnesi, oturum çerezine (session cookie) ait sona erme süresini kontrol eder.

<a name="rememberMe"></a>

#### $this->session->rememberMe($ttl = null, $deleteOldSession = true)

Kullanıcı oturumu süresinin kalıcılığını belirler. Bir üyelik sisteminde üye girişinden sonra bu metot kullanılırsa kullanıcının oturum çerezi girilen süreye göre tarayıcıda kalıcı hale getirilir. Kullanıcı beni hatırla yönteminiz ile giriş yapmışsa bir sonraki ziyaretinde oturum açmasına gerek kalmaz.

```php
$this->session->rememberMe(6 * 30 * 24 * 3600);
```

Bu fonksiyon php <kbd>session_set_cookie_params()</kbd> metodu özelliklerini kullanır.

<a name="forgetMe"></a>

#### $this->session->forgetMe()

Beni hatırla çerezinin ömrünü <kbd>0</kbd> olarak günceller. Böylece tarayıcı kapatıldığında oturuma ait veriler yok olur.

```php
$this->session->forgetMe();
```