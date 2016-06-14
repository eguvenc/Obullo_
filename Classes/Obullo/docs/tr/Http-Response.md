
## Http Response Sınıfı

Http response sınıfının ana fonksiyonu finalize edilmiş web çıktısını tarayıcıya göndermektir. Http durum kodu, http sunucu başlıkları göndermek gibi ana fonksiyonlarla beraber json, html ve redirect gibi sayfa başlıklarını oluşturmak da response sınıfının diğer fonksiyonlarındandır. Http paketi <a href="https://github.com/zendframework/zend-diactoros" target="_blank">Zend-Diactoros</a> kütüphanesi bileşenlerinden oluşturulmuştur ve <a href="http://www.php-fig.org/psr/psr-7/" target="_blank">Psr7</a> http standartlarını destekler.

<ul>
    <li><a href="#accessing-methods">Metotlara Erişim</a></li>
    <li><a href="#newInstance">$this->response->newInstance()</a></li>
    <li><a href="#getBody">$this->response->getBody()</a></li>
    <li><a href="#withBody">$this->response->withBody()</a></li>
    <li><a href="#withStatus">$this->response->withStatus()</a></li>
    <li><a href="#getStatusCode">$this->response->getStatusCode()</a></li>
    <li><a href="#getReasonPhrase">$this->response->geReasonPhrase()</a></li>
    <li><a href="#getHeaders">$this->response->getHeaders()</a></li>
    <li><a href="#hasHeader">$this->response->hasHeader()</a></li>
    <li><a href="#getHeader">$this->response->getHeader()</a></li>
    <li><a href="#getHeaderLine">$this->response->getHeaderLine()</a></li>
    <li><a href="#withHeader">$this->response->withHeader()</a></li>
    <li><a href="#withAddedHeader">$this->response->withAddedHeader()</a></li>
    <li><a href="#withoutHeader">$this->response->withoutHeader()</a></li>
    <li>
        <a href="#helper-methods">Kurtarıcı Metotlar</a>
        <ul>
            <li><a href="#json">$this->response->json()</a></li>
            <li><a href="#html">$this->response->html()</a></li>
            <li><a href="#redirect">$this->response->redirect()</a></li>
            <li><a href="#emptyContent">$this->response->emptyContent()</a></li>
        </ul>
    </li>
    <li>
        <a href="#templates">Şablonlar</a>
        <ul>
            <li><a href="#templates_error">$this->view->get('templates::error')</a></li>
            <li><a href="#templates_404">$this->view->get('templates::404')</a></li>
            <li><a href="#templates_maintenance">$this->view->get('templates::maintenance')</a></li>
        </ul>
    </li>
</ul>

<a name="accessing-methods"></a>

### Metotlara Erişim

```php
$container->get('response')->method();
```

Kontrolör içerisinden,

```
$this->response->method();
```

<a name="newInstance"></a>

##### $this->response->newInstance($body = 'php://memory', $status = 200, array $headers = [])

Yeni bir response nesnesi oluşturur.

<a name="getBody"></a>

##### $this->response->getbody()

<kbd>Psr\Http\Message\StreamInterface</kbd> arayüzünü uygulayan Stream nesnesine geri döner. 

```php
$body = $this->response->getBody();
```

Böylece stream nesnesine ait metotlara erişilir. Aşağıdaki örnekte write fonksiyonu ile çıktı gövdesine veriler ekleniyor.

```php
$body->write('<p>example append data</p>');
```
<a name="withBody"></a>

##### $this->response->withBody(StreamInterface $body)

Girilen mesaj gövdesi ile birlikte http response nesnesine geri döner. Gövde StreamInterface arayüzünü uygulayan bir nesne olmak zorundadır.

<a name="withStatus"></a>

##### $this->response->withStatus($code, $reasonPhrase = '')

Tarayıca gönderilen durum kodunu belirler.

```php
$body = $container->get('view')
    ->withStream()
    ->get('templates::404');

return $this->response
    ->withStatus(404)
    ->withHeader('Content-Type', 'text/html')
    ->withBody($body);
```

Birinci parametre durum kodunu, ikinci parametre ise varsa bu duruma yolaçan ifadeyi değiştirir. Http durum kodu ve ifade listesini görmek için [Buraya tıklayın](http://www.w3.org/Protocols/rfc2616/rfc2616-sec10.html).

<a name="getStatusCode"></a>

##### $this->response->getStatusCode()

Mevcut http durum kodu ifadesini verir.

```php
echo $this->reponse->getStatusCode();   // 401
```

<a name="getReasonPhrase"></a>

##### $this->response->getReasonPhrase()

Mevcut http durum kodu ifadesine geri döner.

```php
echo $this->reponse->getReasonPhrase();  // OK
```
Bazı örnek http durum ifadeleri

```php
(200) OK
(201) Created
(202) Accepted
(203) Non-Authoritative Information
(404) Not Found
```

<a name="getHeaders"></a>

##### $this->response->getHeaders()

Http başlığında eklenmiş tüm başlıklara döner.

```php
Array
(
    [content-type] => plain-text
    [pragma] => no-cache
)
```

<a name="hasHeader"></a>

##### $this->response->hasHeader($header)

Girilen http sunucu başlığına ait anahtar http sunucu başlıklarında mevcut ise <kbd>true</kbd> değilse <kbd>false</kbd> değerine döner.

<a name="getHeader"></a>

##### $this->response->getHeader($header)

Http başlığına eklenmiş bir başlığın değerine döner.

```php
echo $this->response->getHeader('pragma');  // no-cache
```

<a name="getHeaderLine"></a>

##### $this->request->getHeaderLine($header)

Birden fazla niteliği olan bir http başlığına ait array türündeki değerleri virgüllerle birleştirerek string türünde tek bir satır olarak almayı sağlar.

<a name="withHeader"></a>

##### $this->response->withHeader(string $header, string $value = null, $replace = true)

Girilen http başlığı ile birlikte http response nesnesine geri döner. Eğer girilen başlık http başlıklarında mevcut ise, değeri yenisi ile günceller.

```php
$this->response->withHeader("content-type", "application/json");
```

Http başlıkları tam listesi için [Buraya tıklayın](https://en.wikipedia.org/wiki/List_of_HTTP_header_fields)

<a name="withAddedHeader"></a>

##### $this->request->withAddedHeader($header, $value)

Girilen http başlığını eski başlıkların üzerine ekleyerek oluşan başlıklar ile birlikte http response nesnesine geri döner.

```php
$this->response->withAddedHeader("HTTP/1.0 200 OK");
$this->response->withAddedHeader("HTTP/1.1 200 OK");
$this->response->withAddedHeader("last-modified", gmdate('D, d M Y H:i:s', time()).' GMT');
$this->response->withAddedHeader("cache-control", "no-store, no-cache, must-revalidate");
$this->response->withAddedHeader("cache-control", "post-check=0, pre-check=0");
$this->response->withAddedHeader("pragma", "no-cache");
```

<a name="withoutHeader"></a>

##### $this->response->withoutHeader($header)

Girilen http başlığını varolan başlıklardan silerek http response nesnesine geri döner.

```php
$this->response->withoutHeader('pragma');
```

<a name="helper-methods"></a>

### Kurtarıcı Metotlar

Aşağıdaki ek metotlar duruma göre uygulamanızın <kbd>json</kbd>, <kbd>html</kbd> veya <kbd>emptyContent</kbd> gibi sık kullanılan http başlıklarını kolayca oluşturmasına yardımcı olur.

<a name="json"></a>

##### $this->response->json(array $data, $status = 200, array $headers = [], $encodingOptions = 15)

Json kodlanmış bir metin ile birlikte Content-Type http başlığını <kbd>application/json</kbd> olarak belirleyerek tarayıcıya gönderir.

```php
return $this->response->json(['test']);  // Çıktı [ "test" ]
```

<a name="html"></a>

##### $this->response->html($html, $status = 200, array $headers = [])

Html kodlanmış bir metin ile birlikte Content-Type http başlığını <kbd>text/html</kbd> olarak belirleyerek tarayıcıya gönderir.

<a name="redirect"></a>

##### $this->response->redirect($uri, $status = 301, array $headers = [])

Varsayılan 301 durum kodu ile tarayıcıya girilen url adresine yönlendirir.

<a name="emptyContent"></a>

##### $this->response->emptyContent($status = 204, array $headers = [])

Varsayılan 204 durum kodu ile tarayıcıya boş bir sayfa (içerik) gönderir.

<a name="templates"></a>

### Şablonlar

Bazı durumlarda özelleştirilebilir şablonlar kullanarak uygulamanızın daha esnek olması sağlanabilir.

<a name="templates_error"></a>

##### $this->view->get('templates::error')

Uygulamanızda oluşturduğunuz genel hatalar için <kbd>error</kbd> şablonunu kullanabilirsiniz.

```php
$body = $this->view
    ->withStream()
    ->get(
        'templates::error',
        [
            'error' => sprintf(
                '%s Method Not Allowed',
                "GET"
            )
        ]
    );
return $this->response->withStatus(405)
    ->withHeader('Content-Type', 'text/html')
    ->withBody($body);
```

<a name="templates_404"></a>

##### $this->view->get('templates::404')

Uygulamanızda oluşturduğunuz sayfa bulunamadı hataları için <kbd>404</kbd> şablonunu kullanabilirsiniz.

```php
$body = $this->view
    ->withStream()
    ->get('templates::404');

return $this->response->withStatus(404)
    ->withHeader('Content-Type', 'text/html')
    ->withBody($body);
```

<a name="templates_maintenance"></a>

##### $this->view->get('templates::maintenance')

Uygulamanızda oluşturduğunuz bakıma alma durumları için <kbd>maintenance</kbd> şablonunu kullanabilirsiniz.

```php
$body = $this->view->get('view')
    ->withStream()
    ->get('templates::maintenance');

return $this->response->withStatus(404)
    ->withHeader('Content-Type', 'text/html')
    ->withBody($body);
```
