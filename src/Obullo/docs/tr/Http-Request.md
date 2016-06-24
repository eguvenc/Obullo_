
## Http Request Sınıfı

Http request sınıfı gelen istek türü, bağlantının güvenli olup olmadığı, ip adresi, ajax istekleri ve buna benzer http istekleri ile ilgili bilgilere ulaşmamızı sağlar. Http paketi <a href="https://github.com/zendframework/zend-diactoros" target="_blank">Zend-Diactoros</a> kütüphanesi bileşenlerinden oluşturulmuştur ve <a href="http://www.php-fig.org/psr/psr-7/" target="_blank">Psr7</a> http standartlarını destekler.

<ul>
    <li><a href="#accessing-methods">Metotlara Erişim</a></li>
    <li>
        <a href="#inputs">Girdileri Almak / Değiştirmek</a>
        <ul>
    		<li><a href="#get">$this->request->get()</a></li>
    		<li><a href="#post">$this->request->post()</a></li>
    		<li><a href="#all">$this->request->all()</a></li>
            <li><a href="#getQueryParams">$this->request->getQueryParams()</a></li>
    		<li><a href="#withQueryParams">$this->request->withQueryParams()</a></li>
            <li><a href="#getParsedBody">$this->request->getParsedBody()</a></li>
            <li><a href="#withParsedBody">$this->request->withParsedBody()</a></li>
            <li><a href="#getServerParams">$this->request->getServerParams()</a></li>
            <li><a href="#getCookieParams">$this->request->getCookieParams()</a></li>
            <li><a href="#withCookieParams">$this->request->withCookieParams()</a></li>
            <li><a href="#getAttributes">$this->request->getAttributes()</a></li>
    		<li><a href="#getAttribute">$this->request->getAttribute()</a></li>
            <li><a href="#withAttribute">$this->request->withAttribute()</a></li>
            <li><a href="#withoutAttribute">$this->request->withoutAttribute()</a></li>
            <li><a href="#getMethod">$this->request->getMethod()</a></li>
            <li><a href="#withMethod">$this->request->withMethod()</a></li>
            <li><a href="#getIpAddress">$this->request->getIpAddress()</a></li>
            <li><a href="#isValidIp">$this->request->isValidIp()</a></li>
            <li><a href="#getBody">$this->request->getBody()</a></li>
            <li><a href="#withBody">$this->request->withBody()</a></li>
            <li><a href="#getUploadedFiles">$this->request->getUploadedFiles()</a></li>
            <li><a href="#withUploadedFiles">$this->request->withUploadedFiles()</a></li>
            <li><a href="#getHeaders">$this->request->getHeaders()</a></li>
            <li><a href="#hasHeader">$this->request->hasHeader()</a></li>
            <li><a href="#getHeader">$this->request->getHeader()</a></li>
            <li><a href="#withHeader">$this->request->withHeader()</a></li>
            <li><a href="#withAddedHeader">$this->request->withAddedHeader()</a></li>
            <li><a href="#withoutHeader">$this->request->withoutHeader()</a></li>
            <li><a href="#getUri">$this->request->getUri()</a></li>
            <li><a href="#withUri">$this->request->withUri()</a></li>
            <li><a href="#getRequestTarget">$this->request->getRequestTarget()</a></li>
            <li><a href="#withRequestTarget">$this->request->withRequestTarget()</a></li>
            <li><a href="#getProtocolVersion">$this->request->getProtocolVersion()</a></li>
            <li><a href="#withProtocolVersion">$this->request->withProtocolVersion()</a></li>
    	</ul>
    </li>
    <li>
    	<a href="#filters">İstekleri Filtrelemek</a>
    	<ul>
    		<li><a href="#isAjax">$this->request->isAjax()</a></li>
    		<li><a href="#isSecure">$this->request->isSecure()</a></li>
    		<li><a href="#isLayer">$this->request->isLayer()</a></li>
            <li><a href="#isPost">$this->request->isPost()</a></li>
            <li><a href="#isGet">$this->request->isGet()</a></li>
            <li><a href="#isPut">$this->request->isPut()</a></li>
            <li><a href="#isDelete">$this->request->isDelete()</a></li>
            <li><a href="#isHead">$this->request->isHead()</a></li>
            <li><a href="#isOption">$this->request->isOption()</a></li>
            <li><a href="#isPatch">$this->request->isPatch()</a></li>
            <li><a href="#isMethod">$this->request->isMethod()</a></li>
    	</ul>
    </li>
</ul>

<a name="accessing-methods"></a>

### Metotlara Erişim

```php
$container->get('request')->method()
```
Kontrolör içerisinden,

```php
$this->request->method();
```

<a name="inputs"></a>

### Girdileri Almak / Değiştirmek

<a name="get"></a>

##### $this->request->get(string $key)

<kbd>$_GET</kbd> süper küresel değişkeninden değerler almanıza yardımcı olur. Değişkenlere direkt olarak $_GET['variable'] şeklinde ulaşmak yerine get fonksiyonu kullanmanın ana avantajı değişkenin varlığını kontrol etme durumunda isset() fonksiyonu kullanımı ortadan kaldırmasıdır. Eğer girdi $_GET küresel değişkeni içerisinde yoksa <kbd>false (boolean)</kbd> değerine var ise kendi değerine geri döner. 

Normal şartlarda <kbd>isset($_GET['variable'])</kbd> bloğunu her seferinde yazmamak için aşağıdaki gibi request sınıfı get metodu kullanmanız tavsiye edilir.

```php
if ($variable = $this->request->get('variable')) {
    echo $variable;
}
```

<a name="post"></a>

##### $this->request->post(string $key)

<kbd>$_POST</kbd> değişkeninden değerler almanıza yardımcı olur.

```php
if ($variable = $this->request->post('variable')) {
    echo $variable;
}
```

<a name="all"></a>

##### $this->request->all()

<kbd>$_REQUEST</kbd> değişkeninden değerler almanıza yardımcı olur.

```php
if ($variable = $this->request->all('variable')) {
    echo $variable;
}
```

Tüm değerlere ulaşmak için parametre girmeyin.

```php
$request = $this->request->all();
```

<a name="getQueryParams"></a>

##### $this->request->getQueryParams()

Eğer varsa query string argümanlarına geri döner.

```php
http://example.com/?a=b&c=d
```

```php
$query = $this->request->getQueryParams();

print_r($query);  // Array ( [a] => b [c] => d )
```

<a name="withQueryParams"></a>

##### $this->request->withQueryParams(array $query)

Girilen sorgu argümanları ile birlikte http request nesnesine geri döner.

<a name="getParsedBody"></a>

##### $this->request->getParsedBody()

Eğer http <kbd>Content-Type</kbd> başlığı <kbd>application/x-www-form-urlencoded</kbd> yada <kbd>multipart/form-data</kbd> ve istek metodu POST ise metot $_POST değişkeni içeriğine geri döner. Aksi durumda bu metot http request gövdesinde gelen deserialize olmuş yada çözümlenmiş gövde içeriğine dönebilir. Potansiyel data türleri <kbd>array</kbd> veya <kbd>object</kbd> türüdür. Eğer <kbd>null</kbd> tipinde bir değer gelirse bu gövde içeriğinin olmadığı anlamına gelir.

<a name="withParsedBody"></a>

##### $this->request->withParsedBody($data)

Girilen veri ile birlikte http request nesnesine geri döner. Verinin $_POST türünden gelmesi gerekmez fakat veri sonuçlarının, deserialize olmuş bir gövde içeriğinden gelmesi gerekir. Deserialize olumuş / çözümlenmiş bu ve bunun gibi bir veriye geri döner, bu metot yalnızca <kbd>array</kbd>, <kbd>object</kbd> veya eğer bir veri gelmediyse <kbd>null</kbd> değerine döner.

<a name="getServerParams"></a>

##### $this->request->getServerParams()

Php $_SERVER türünden değişken değerlerine erişmeyi sağlar. Veri kökeni $_SERVER değişkeninden gelmek zorunda değildir.

<a name="getCookieParams"></a>

##### $this->request->getCookieParams()

İstemci tarafından suncuya gönderilen çerezlere erişmeyi sağlar. Gelen veri $_COOKIE php süper küreseli yapısına uygun olmak zorundadır.

<a name="withCookieParams"></a>

##### $this->request->withCookieParams(array $cookies)

Girilen çerez verisi ile birlikte http request nesnesine geri döner. Veri php $_COOKIE süper küreselinden gelmek zorunda değildir fakat bu yapıya uygun olmak zorundadır.

<a name="getAttributes"></a>

##### $this->request->getAttributes()

Http isteğinden doğan niteliklere geri döner. İstek "nitelikleri" request nesnesine enjekte edilmek istenen herhangi bir veriye ait parametreler olabilir. Örneğin çerez decrypt işlemi sonuçları, deserialize edilmiş http gövde içerikleri ve path eşleşme işlemlerinden doğan parametreler gibi.

<a name="getAttribute"></a>

##### $this->request->getAttribute($attribute, $default = null)

Tek bir http isteği niteliğini elde etmeyi sağlar. İkinci parametre nitelik elde edilemezse işlemin hangi türe döneceğini belirler.

<a name="withAttribute"></a>

##### $this->request->withAttribute($attribute, $value)

Girilen tek nitelik ile birlikte http request nesnesine geri döner. 

<a name="withoutAttribute"></a>

##### $this->request->withoutAttribute($attribute)

Girilen http isteği niteliğini nesne içerisinden silerek http request nesnesine döner.

<a name="getMethod"></a>

##### $this->request->getMethod()

Php <kbd>$_SERVER['REQUEST_METHOD']</kbd> değerine ger döner.

```php
$this->request->getMethod();  // GET
```
<a name="withMethod"></a>

##### $this->request->withMethod(string $method)

Http isteği metodunu belirler. Method isminin büyük harfler ile girilmesi gerekir.

<a name="getIpAddress"></a>

##### $this->request->getIpAddress()

```php
echo $this->request->getIpAddress(); // 88.54.844.15
```

<a name="isValidIp"></a>

##### $this->request->isValidIp($ip)

Girilen ip adresi doğru ise true değerine aksi durumda false değerine geri döner.

```php
if ( ! $this->request->isValidIp($ip)) {
    echo 'Not Valid';
} else {
    echo 'Valid';
}
```

<a name="getBody"></a>

##### $this->request->getBody()

Eğer bir stream türünde bir mesaj sözkonusu stream nesnesine geri döner.

<a name="withBody"></a>

##### $this->request->withBody(StreamInterface $body)

Girilen mesaj gövdesi ile birlikte http request nesnesine geri döber. Gövde StreamInterface arayüzünü uygulayan bir nesne olmak zorundadır.

<a name="getUploadedFiles"></a>

##### $this->request->getUploadedFiles()

Bu metot request nesnesi içerisinde normalize edilmiş ağaç yapısındaki metadaya geri döner. Her bir veri parçası <kbd>Psr\Http\Message\UploadedFileInterface</kbd> sınıfına genişler. Bu metoda gelen değerler $_FILES değişkeninden hazırlanmış, örnekleme sırasında bir mesaj gövdesi ile gelmiş, yada  withUploadedFiles() metodu ile enjekte edilmiş olabilir.

<a name="withUploadedFiles"></a>

##### $this->request->withUploadedFiles(array $uploadedFiles)

Belirlenen yüklü dosyalar ile birlikte http request nesnesine geri döner.

<a name="getHeaders"></a>

##### $this->request->getHeaders()

Tüm http sunucu başlıklarına geri döner.

```php
$headers = $this->request->getHeaders();
print_r($headers);
```

```php
Array
(
    [Host] => localhost
    [User-Agent] => Mozilla/5.0 (X11; Ubuntu; Linux x86_64; rv:38.0) Gecko/20100101 Firefox/38.0
    [Accept] => text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8
    [Accept-Language] => en-US,en;q=0.5
    [Accept-Encoding] => gzip, deflate
    ...
)
```

<a name="hasHeader"></a>

##### $this->request->hasHeader($header)

Girilen http sunucu başlığına ait anahtar http sunucu başlıklarında mevcut ise <kbd>true</kbd> değilse <kbd>false</kbd> değerine döner.

<a name="getHeader"></a>

##### $this->request->getHeader($header)

Seçilen http sunucu başlığına geri döner.

```php
echo $this->request->getHeader('Host'); // localhost
echo $this->request->getHeader('Content-Type'); // gzip, deflate
```

<a name="getHeaderLine"></a>

##### $this->request->getHeaderLine($header)

Birden fazla niteliği olan bir http başlığına ait array türündeki değerleri virgüllerle birleştirerek string türünde tek bir satır olarak almayı sağlar.

<a name="withHeader"></a>

##### $this->request->withHeader($header, $value)

Girilen http başlığı ile birlikte http request nesnesine geri döner. Eğer girilen başlık http başlıklarında mevcut ise, değeri yenisi ile günceller.

<a name="withAddedHeader"></a>

##### $this->request->withAddedHeader($header, $value)

Girilen http başlığını eski başlıkların üzerine ekleyerek oluşan başlıklar ile birlikte http request nesnesine geri döner.

<a name="withoutHeader"></a>

##### $this->request->withoutHeader($header)

Girilen http başlığını varolan başlıklardan silerek http request nesnesine geri döner.

<a name="getUri"></a>

##### $this->request->getUri()

Http URI nesnesine geri döner. Bu metot UriInterface arayüzünü uygulayan bir nesneye dönmek zorundadır.

<a name="withUri"></a>

##### $this->request->withUri($uri, $preserveHost = false)

Bu metot eğer URI bir host bileşeni içeriyorsa, varsayılan request nesnesine dönerek http Host başlığını değiştirir. Eğer URI bir host bileşeni içermiyorsa varsayılan Host başlığı dönen yeni http nesnesi üzerine taşınır.

<a name="getRequestTarget"></a>

##### $this->request->getRequestTarget()

Bu metot aşağıdaki gibi gelen bir http isteğine ait,

```php
http://example.com/
```

eğer yukarıdaki gibi <kbd>getPath()</kbd> değeri yoksa varsayılan path "/" değerine döner,

```php
echo $this->request->getRequestTarget();  // "/"
```

```php
http://example.com/welcome
```

eğer yukarıdaki gibi <kbd>getPath()</kbd> değeri varsa gelen path değerine döner,


```php
echo $this->request->getRequestTarget();  // "/welcome"
```

```php
http://example.com/welcome?a=b&c=d
```

eğer yukarıdaki gibi <kbd>getPath()</kbd> ve <kbd>getQuery()</kbd> değerleri varsa bu değerlerin birleşimine döner.

```php
echo $this->request->getRequestTarget();  // "/welcome?a=b&c=d"
```

<a name="withRequestTarget"></a>

##### $this->request->withRequestTarget($requestTarget)

Belirlenen hedef adres ile birlikte http request nesnesine geri döner.

<a name="getProtocolVersion"></a>

##### $this->request->getProtocolVersion()

HTTP protokol versiyonuna string türünde geri döner.

<a name="withProtocolVersion"></a>

##### $this->request->withProtocolVersion($version)

Girilen HTTP protokol versiyonu ile birlikte http nesnesine geri döner. Versiyon numarası sadece HTTP versiyon numaralarını içermelidir. ( örn., "1.1", "1.0").

<a name="filters"></a>

### İstekleri Filtrelemek

Aşağıdaki yardımcı metotlar http isteklerini filtreleyebilmek amacıyla eklenmiştir.

<a name="isAjax"></a>

##### $this->request->isAjax()

Uygulama gelen istek eğer <kbd>xmlHttpRequest</kbd> ( Ajax ) isteği ise true değerine aksi durumda false değerine geri döner.

<a name="isSecure"></a>

##### $this->request->isSecure()

Uygulamaya gelen istek eğer <kbd>https://</kbd> protokülünden geliyorsa true aksi durumda false değerine geri döner.

<a name="isLayer"></a>

##### $this->request->isLayer()

Uygulama gelen istek eğer katman yani <kbd>hmvc</kbd> isteği ise true değerine aksi durumda false değerine geri döner.

<a name="isPost"></a>

##### $this->request->isPost()

Eğer http metodu <kbd>POST</kbd> değerinde geliyorsa true değerine aksi durumda false değerine döner.

<a name="isGet"></a>

##### $this->request->isGet()

Eğer http metodu <kbd>GET</kbd> değerinde geliyorsa true değerine aksi durumda false değerine döner.

<a name="isPut"></a>

##### $this->request->isPut()

Eğer http metodu <kbd>PUT</kbd> değerinde geliyorsa true değerine aksi durumda false değerine döner.

<a name="isDelete"></a>

##### $this->request->isDelete()

Eğer http metodu <kbd>DELETE</kbd> değerinde geliyorsa true değerine aksi durumda false değerine döner.

<a name="isOptions"></a>

##### $this->request->isOptions()

Eğer http metodu <kbd>OPTIONS</kbd> değerinde geliyorsa true değerine aksi durumda false değerine döner.

<a name="isPatch"></a>

##### $this->request->isPatch()

Eğer http metodu <kbd>PATCH</kbd> değerinde geliyorsa true değerine aksi durumda false değerine döner.

<a name="isHead"></a>

##### $this->request->isHead()

Eğer http metodu <kbd>HEAD</kbd> değerinde geliyorsa true değerine aksi durumda false değerine döner.

<a name="isMethod"></a>

##### $this->request->isMethod($method)

Eğer http metodu sizin belirlediğiniz <kbd>ÖZEL</kbd> metot türüne eşitse true değerine aksi durumda false değerine döner.

```php
if ($this->request->isMethod('COPY')) {

}
```