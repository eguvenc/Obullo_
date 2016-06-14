
## Url Sınıfı

Url sınıfı uygulamanızda kullandığınız iç ve dış html linklerini oluşturmanıza yardımcı olmayı sağlayan fonksiyonları içerir.

<ul>
    <li><a href="#service-provider">Servis Sağlayıcısı</a></li>
    <li>
        <a href="#methods">Metotlara Erişim</a>
        <ul>
            <li><a href="#anchor">$this->url->anchor()</a></li>
            <li><a href="#asset">$this->url->asset()</a></li>
            <li><a href="#getBaseUrl">$this->url->basePath()</a></li>
            <li><a href="#prep">$this->url->prep()</a></li>
            <li>
                <a href="#chain">Dış Url Adresleri Üretmek</a>
                <ul>
                    <li><a href="#withAnchor">$this->url->withHost()->withAnchor()</a></li>
                    <li><a href="#withAsset">$this->url->withHost()->withAsset()</a></li>
                    <li><a href="#withScheme">$this->url->withHost()->withScheme()</a></li>
                    <li><a href="#withUserInfo">$this->url->withHost()->withUserInfo()</a></li>
                    <li><a href="#withPort">$this->url->withHost()->withPort()</a></li>
                    <li><a href="#withPath">$this->url->withHost()->withPath()</a></li>
                    <li><a href="#withQuery">$this->url->withHost()->withQuery()</a></li>
                    <li><a href="#getUriString">$this->url->withHost()->getUriString()</a></li>
                </ul>
            </li>
        </ul>
    </li>
</ul>

<a name="service-provider"></a>

### Servis Sağlayıcısı

<kbd>app/providers.php</kbd> dosyasında servis sağlayıcısının tanımlı olduğundan emin olun.

```php
$container->addServiceProvider('ServiceProvider\Url');
```

Url sınıfına ait servis parametreleri ise <kbd>providers/url.php</kbd> dosyasından konfigüre edilir.

```php
'params' => [

    'base' => [
        'path' => '/'
    ],
    'assets'   => [
        'path' => '/',
        'folder' => '/assets/',
    ]
]
```

* <b>base.path</b> : Url fonksiyonları kök adresidir, genellikle "/" karakteri yeterli olur.
* <b>assets.path</b> : Kaynaklar kök adresidir, genellikle "/" karakteri yeterli olur. Buraya bir <kbd>cdn</kbd> sağlayıcı adresi de girilebilir.
* <b>assets.folder</b> : Kaynaklar klasörünü belirler varsayılan klasör "/assets/" klasörüdür.

<a name="methods"></a>

### Metotlara Erişim

```php
$this->url->method();
```

Konteyner içerisinden,

```php
$container->get('url')->method();
```

<a name="anchor"></a>

#### $this->url->anchor($uri, $label = '', $attributes = '')

Yerel site adresinize göre standart bir HTML bağlantısı oluşturur.

```php
echo $this->url->anchor('welcome', 'Click Here');
```
Çıktı

```php
<a href="http://example.com/welcome">Click Here</a>
```

Üçüncü parametreden ek nitelikler gönderilebilir.


```php
echo $this->url->anchor('welcome', 'Click Here', ' title="Welcome" class="btn btn-default" ');
```

Çıktı

```php
<a href="/welcome" title="Welcome" class="btn btn-default">Click Here</a>
```

<a name="asset"></a>

#### $this->url->asset($path)

<kbd>public</kbd> dizini içerisinde yer alan bir kaynak dosyasına ait url adresi oluşturmak için kullanılır.

```php
echo $this->url->asset('css/welcome.css');
```
Çıktı

```
/assets/css/welcome.css 
```

Bir resim dosyası için oluşturulan kaynak url.

```php
echo $this->url->asset('images/logo.png');
```

Çıktı

```
/assets/images/logo.png 
```

Eğer konfigürasyon dosyanızda bir dış url tanımlı ise.

```php
'assets'   => [
    'path' => 'static.example.com',
    'folder' => '/assets/',
]
```

O zaman alacağınız çıktı aşağıdaki gibi olur.

```
http://static.example.com/assets/images/logo.png 
```

<a name="getBaseUrl"></a>

#### $this->url->basePath($uri = '')

Konfigürasyonda tanımlı olan kök dizine geri döner.

```php
echo $this->url->basePath();
```

Çıktı

```php
/ 
```

Bir url adresi ile birlikte kök dizin adresi alınabilir.

```php
echo $this->url->basePath('examples/forms');
```

Çıktı

```php
/examples/forms
```

<a name="prep"></a>

#### $this->url->prep($url)

Girilen url adresinin başında <kbd>http://</kbd> protokolü eksik ise tamamlar.

```php
echo $this->url->prep('example.com');
```

Çıktı

```php
http://example.com
```

Protokol mevcut ise herhangi bir değişilik yapılmaz.


```php
echo $this->url->prep('https://example.com');
```

Çıktı

```php
https://example.com
```

<a name="chain"></a>

### Dış Url Adresleri Üretmek

Url sınıfı dış url adresleri oluşturmak için <kbd>Http\Uri</kbd> nesnesi kullanılır.

<a name="withAnchor"></a>

##### $this->url->withHost()->withAnchor()

Dinamik url bağlantıları oluşturmak için withAnchor metodu kullanılır.

```php
echo $this->url->withHost('example.com')
    ->withScheme('https')
    ->withAnchor('Click Here');
```

Çıktı

```php
<a href="https://example.com">Click Here</a>
```

Eğer bir url berlirtilmezse geçerli host adresi host olarak kabul edilir.

```php
echo $this->url->withHost()
    ->withPath('en')
    ->withAnchor('Click Here');
```

Çıktı

```php
<a href="http://mylocalproject/en">Click Here</a>
```

<a name="withAsset"></a>

##### $this->url->withHost()->withAsset()

Dinamik kaynak url adresleri oluşturmak için withAsset metodu kullanılır.

```php
echo $this->url->withHost('static.example.com')
    ->withScheme('http')
    ->withAsset('images/logo.png');
```

Çıktı

```php
http://static.example.com/assets/images/logo.png
```

Eğer konfigürasyonda kaynak url tanımlı ise host adı girmeye gerek kalmaz.

```php
echo $this->url->withHost()
    ->withAsset('images/logo.png');
```

Çıktı

```php
http://static.example.com/assets/images/logo.png
```

Güvenli protokol ile bir kaynak url.

```php
echo $this->url->withHost('static.example.com')
    ->withScheme('https')
    ->withAsset('css/welcome.css');
```

Çıktı

```php
https://static.example.com/assets/css/welcome.css
```

<a name="withScheme"></a>

##### $this->url->withHost()->withScheme()

Eğer geçerli protokol ile bir bağlantı oluşturulmak isteniyorsa withScheme() metodu kullanılır.

```php
echo $this->url->withHost('example.com')
    ->withScheme('https');
```

Çıktı

```php
https://example.com
```

Kesin bir url berlirtilmezse varsayılan olarak baseUrl kullanılır.

<a name="withUserInfo"></a>

##### $this->url->withHost()->withUserInfo()

Geçerli url adresine port ekler.

```php
echo $this->url->withHost('example.com')
    ->withScheme('http')
    ->withUserInfo('username', '123456');
```

Çıktı

```php
http://username:123456@example.com
```

<a name="withPort"></a>

##### $this->url->withHost()->withPort()

Geçerli url adresine port ekler.

```php
echo $this->url->withHost('example.com')
    ->withScheme('https')
    ->withPort(9090);
```

Çıktı

```php
https://example.com:9090
```

<a name="withPath"></a>

##### $this->url->withHost()->withPath()

Geçerli url adresine dizin ekler.

```php
echo $this->url->withHost('example.com')
    ->withScheme('https')
    ->withPath('forum/welcome');
```

Çıktı

```php
https://example.com/forum/welcome
```

<a name="withQuery"></a>

##### $this->url->withHost()->withQuery()

Geçerli url adresine sorgu parçaları ekler.

```php
echo $this->url->withHost('example.com')
    ->withScheme('http')
    ->withPath('en')
    ->withQuery("a=1&b=2");
```

Çıktı

```php
http://example.com/en?a=1&b=2
```

<a name="getUriString"></a>

##### $this->url->withHost()->getUriString();

En son üretilen uri değerini bir değişkene atamak için kullanılır.

```php
$uriString = $this->url->withHost('example.com')->getUriString();

echo $uriString;
```

Çıktı

```php
http://example.com
```