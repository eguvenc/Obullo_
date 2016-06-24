
## Çerez Sınıfı

Çerez, herhangi bir internet sitesi tarafından son kullanıcının bilgisayarına bırakılan bir tür tanımlama dosyasıdır. Çerez dosyalarında oturum bilgileri ve benzeri veriler saklanır. Çerez kullanan bir siteyi ziyaret ettiğinizde, bu site tarayıcınıza bir ya da birden fazla çerez bırakma konusunda talep gönderebilir.

<ul>
    <li><a href="#service-provider">Servis Sağlayıcısı</a></li>
    <li>
        <a href="#setcookie">Bir Çereze Veri Kaydetmek</a>
        <ul>
            <li><a href="#method-chaining">Zincirleme Method Yöntemi</a></li>
            <li><a href="#arrays">Array Yöntemi</a></li>
        </ul>
    </li>
    <li><a href="#readcookie">Bir Çerez Verisini Okumak</a></li>
    <li><a href="#removecookie">Bir Çerezi Silmek</a></li>
    <li><a href="#prefix">Çerez Ön Ekleri</a></li>
    <li><a href="#parameters">Parametreler</a></li>
    <li><a href="#method-reference">Cookie Sınıfı Referansı</a></li>
</ul>

Bir çereze kayıt edilebilecek maksimum veri 4KB tır.

<a name="service-provider"></a>

### Servis Sağlayıcısı

<kbd>app/providers.php</kbd> dosyasında servis sağlayıcısının tanımlı olduğundan emin olun.

```php
$container->addServiceProvider('Obullo\Container\ServiceProvider\Cookie');
```

<a name="setcookie"></a>

### Bir Çereze Veri Kaydetmek

Çerez sınıfını kullandığınızda bir çereze iki tür yöntemle veri kaydedebilirsiniz.

<a name="method-chaining"></a>

#### Zincirleme Method Yöntemi

```php
$this->cookie
    ->expire(0)
    ->set('name', 'value'); 
```

Bu yöntemi kullanarak konfigürasyon dosyasından gelen varsayılan değerleri devre dışı bırakarak girilen değerleri çereze kaydedebilirsiniz. Çereze ait domain, path ve diğer girilmeyen bilgiler <kbd>config.php</kbd> konfigürasyon dosyasından okunur.

```php
$set = $this->cookie
    ->name('hello')
    ->value('world')
    ->expire(86400)
    ->domain('')
    ->path('/')
    ->set();

var_dump($set); // true 
```

<a name="arrays"></a>

#### Array Yöntemi

Eğer konfigürasyon dosyasını ezerek bir çerez kaydetmek istiyorak aşağıdaki gibi tüm parametreleri göndermeliyiz.

```php
$cookie = array(
                   'name'   => 'cookieName',
                   'value'  => 'cookieValue',
                   'expire' => 86500,
                   'domain' => '.some-domain.com',
                   'path'   => '/',
                   'secure' => false,
                   'httpOnly' => false,
                   'prefix' => 'myprefix_',
               );

$this->cookie->set($cookie); 
```

<a name="readcookie"></a>

### Bir Çerez Verisini Okumak

Bir çerezi okumak için get metodu kullanılır.

```php
if ($value = $this->cookie->get('name')) {
	echo $value;
}
```

Eğer çereze kayıtlı bir değer yoksa fonksiyon <kbd>false</kbd> değerine döner. 

<a name="prefix"></a>

### Çerez Ön Ekleri

Eğer çerezler için önceden konfigürasyondan bir ön ek belirlenmişse tüm çerez işlemleri bu ön ek gözönüne alınarak yapılır. Eğer konfigürasyonda olmayan özel ön ekler kullanılıyorsa bu durumda ikinci parametereden ön ek girilmelidir.

```php
if ($value = $this->cookie->get('name', 'prefix')) {
	echo $value;
}
```

<a name="removecookie"></a>

### Bir Çerezi Silmek

Bir çerezi silmek için çerez ismi girmeniz yeterlidir.

```php
$isRemoved = $this->cookie->remove("name");

var_dump($isRemoved);  // true
```

İkinci parametre, varsayılan konfigürasyondan farklı bir ön ek kullanıbilmeniz için ayrılmıştır.

```php
$this->cookie->remove($name = "name", $prefix = null)
```

Domain ve path metotları ile bir örnek.

```php
$this->cookie->domain('my.subdomain.com')->path('/')->delete("name");
```

Veya

```php
$this->cookie->name('name')->prefix('prf_')->domain('my.subdomain.com')->path('/')->delete();
```

<a name="parameters"></a>

### Parametreler

<table>
    <thead>
        <tr>
            <th>Parametre</th>
            <th>Açıklama</th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td>name</td>
            <td>Çerezin kaydedileceği isim.</td>
        </tr>
        <tr>
            <td>value</td>
            <td>Çereze kayıt edilecek değer.</td>
        </tr>
        <tr>
            <td>expire</td>
            <td>Son erme süresi saniye türünden girilir ve girilen değer şu anki zaman üzerine eklenir. Eğer sona erme süresi girilmez ise konfigürasyon dosyasındaki değer varsayılan olarak kabul edilir. Eğer sona erme süresi <kbd>0</kbd> dan küçük olarak girilirse çerez tarayıcı kapandığında kendiliğinden yok olur.</td>
        </tr>
        <tr>
            <td>domain</td>
            <td>Çerezin geçerli olacağı alan adıdır. Site-wide çerezler ( tüm alt domainlerde geçerli çerezler ) kaydetmek için domain parametresini <kbd>.your-domain.com</kbd> gibi girmeniz gereklidir.</td>
        </tr>
        <tr>
            <td>path</td>
            <td>Çerezin geçerli olacağı dizin, genel olarak çerezin tüm url adresinlerine ait alt dizinlerde kabul edilmesi istendiğinden bölü işareti "/" ( forward slash ) varsayılan değer olarak kullanılır.</td>
        </tr>
        <tr>
            <td>secure</td>
            <td>Eğer çerez güvenli bir <kbd>https://</kbd> protokolü üzerinden okunuyorsa bu değerin true olması gerekir. Protokol güvenli olmadığında çereze erişilemez.</td>
        </tr>
        <tr>
            <td>httpOnly</td>
            <td>Eğer http only parameteresi true gönderilirse çerez sadece http protokolü üzerinden okunabilir hale gelir javascript gibi diller ile çerezin okunması engellenmiş olur. Çerez güvenliği ile ilgili daha fazla bilgi için <a href="http://resources.infosecinstitute.com/securing-cookies-httponly-secure-flags/" target="_blank">bu makaleden</a> faydalanabilirsiniz.</td>
        </tr>
        <tr>
            <td>prefix</td>
            <td>Sadece çerezlerinizin diğer çerezler ile karışmasını engellemek için kullanılır. Bir değer girilmezse varsayılan değer konfigürasyon dosyasından okunur.</td>
        </tr>
        </tbody>
</table>

<a name="method-reference"></a>

### Cookie Sınıfı Referansı  

##### $this->cookie->name(string $name);

Kaydedilmek üzere olan bir çereze isim atar.

##### $this->cookie->value(mixed $value = '');

Kaydedilmek üzere olan bir çerez ismine değer atar.

##### $this->cookie->expire(int $expire = 0);

Kaydedilmek üzere olan bir çerezin sona erme süresini belirler.

##### $this->cookie->domain(string $domain = '');

Kaydedilmek üzere olan bir çereze ait alanadı parametresini belirler.

##### $this->cookie->path(string $path = '/');

Kaydedilmek üzere olan bir çereze ait path parametresini tanımlar.

##### $this->cookie->secure(boolean $bool = false);

Kaydedilmek üzere olan bir çereze ait secure parametresini tanımlar.

##### $this->cookie->httpOnly(boolean $bool = false);

Kaydedilmek üzere olan bir çereze ait httpOnly parametresini tanımlar.

##### $this->cookie->prefix(string $prefix = '');

Kaydedilmek üzere olan bir çereze ait bir ön ek tanımlar.

##### $this->cookie->set(mixed $name, string $value);

Gönderilen parametrelere göre bir çereze veri kaydeder. En son çalıştırılmalıdır. Kayıt işleminden sonra daha önce kullanılan çereze ait veriler başa döndürülür.

##### $this->cookie->get(string $name, string $prefix = '');

Kayıtlı bir çerezi okur eğer çerez mevcut değilese <kbd>false</kbd> değerine döner. Konfigürasyonda yada parametrede bir ön ek belirtilmişse çerez bu ön ek kullanılarak okunur. Parametreden bir ön ek gönderilirse konfigürasyon dosyasındaki varsayılan değer pas geçilir.

##### $this->cookie->delete(string $name, string $prefix = '');

Gönderilen parametrelere göre bir çerezi tarayıcıdan siler.

##### $this->cookie->remove(string $name, string $prefix = '');

Delete fonksiyonu ile aynı işlevi görür.

##### $this->cookie->getHeaders();

Kuyruğa gönderilmiş çerezlerin raw değerilerine bir dizi içerisinde geri döner.

##### $this->cookie->getId();

Çerez sınıfı tarafından rastgele üretilen geçerli çerezin kimlik değerine geri döner.
