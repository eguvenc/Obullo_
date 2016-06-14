
## Hata Yönetimi

Uygulamada evrensel hata yönetimi <kbd>app/errors.php</kbd> dosyasından kontrol edilir. Hata durumunda ne yapılacağı isimsiz fonksiyonlar tarafından belirlenir. İsimsiz fonksiyon parametresi önüne istisnai hata tipine ait sınıf ismi yazılarak filtreleme yapılmalıdır. Aksi durumda her bir istisnai hata için bütün error fonksiyonları çalışacaktır.

<ul>
<li>
    <a href="#php-errors">Php Hataları</a>
    
    <ul>
        <li>
            <a href="#global-errors">Tüm Hataları Yakalamak</a>
            <ul>
                <li><a href="#php-exception-hierarchy">İstisnai Hatalar Hiyerarşisi</a></li>
                <li><a href="#database-and-runtime-exceptions">Veritabanı ve İstisnai Hatalar</a></li>
                <li><a href="#fatal-errors">Ölümcül Hatalar</a></li>
                <li><a href="#error-middleware">Http Error Katmanı</a></li>
            </ul>
        </li>
    </ul>

</li>

<li>
    <a href="#creating-http-errors">Http Hataları Oluşturmak</a>
    <ul>
        <li><a href="#error-message-customization">Hata Şablonlarını Özelleştirmek</a></li>
    </ul>
</li>

</ul>

<a name="php-errors"></a>

### Php Hataları

 Uygulama içerisinde oluşan tüm <kbd>php hataları</kbd> app http katmanı <kbd>handleError</kbd> metodu tarafından exception nesnesine dönüştürülür.

```php
public function handleError($level, $message, $file = '', $line = 0);
```

<kbd>production</kbd> çevre ortamı haricindeki tüm çevre ortamları için hata mesajları ekrana yazdırılır. Aşağıda Http İstekleri için oluşmuş örnek bir hata çıktısı görüyorsunuz.

```php
NOTICE
Undefined variable: a
Details
Type: ErrorException
Code: 8
File: APP/modules/Welcome.php
Line: 14
debug_backtrace (15)
```

İstisnai hatalar içinse bu durum kontrol edilebilir, bunun için <a href="#error-middleware">Http Error</a> katmanına bakınız.

<a name="global-errors"></a>

#### Tüm Hataları Yakalamak

Error metoları içerisine girilen isimsiz fonksiyoları kendi ihtiyaçlarınıza göre özelleştirebilirsiniz. İsimsiz fonksiyonlar uygulama çalıştığında fonksiyon parametresi önüne yazılan istisnai hata tipine göre filtrelenerek çalıştırılır. Aşağıdaki örnekte <kbd>istisnai hatalara</kbd> dönüştürülmüş <kbd>doğal php hataları</kbd> yakalanıp log olarak kaydediliyor.

```php
/*
|--------------------------------------------------------------------------
| Php Native Errors
|--------------------------------------------------------------------------
*/
$app->error(
    function (ErrorException $e) use ($container) {

        $log = new Obullo\Error\Log($container->get('logger'));
        $log->error($e);

        return ! $continue = false;   // Whether to continue show native errors
    }
);
```

Fonksiyon sonucu ise <kbd>$continue</kbd> değişkenine döner ve bu değişken php hatalarının devam edilerek gösterilip gösterilmeyeceğine karar verir. Değişken değeri <kbd>true</kbd> olması durumunda hatalar gösterilmeye devam eder <kbd>false</kbd> durumda ise <kbd>fatal error</kbd> hataları hariç diğer hatalar gösterilmez.

```php
/*
|--------------------------------------------------------------------------
| Logic Exceptions
|--------------------------------------------------------------------------
*/
$app->error(
    function (LogicException $e) use ($container) {

        $log = new Obullo\Error\Log($container->get('logger'));
        $log->error($e);
    }
);
```

Eğer fonksiyon içerisindeki hatalar yukarıdaki gibi error log sınıfına gönderilirse error log sınıfı tarafından istisnai hata çözümlenerek loglanır.

<a name="php-exception-hierarchy"></a>

#### İstisnai Hatalar Hiyerarşisi

Hataları yakalarken uygulamaya tüm exception isimleri yazmanıza <kbd>gerek yoktur</kbd>. Sadece en üst hiyerarşideki istisnai hata isimlerini girerek aynı kategorideki hataların hepsini yakalayabilirsiniz.

```php
/*
|   - Exception
|       - ErrorException
|       - LogicException
|           - BadFunctionCallException
|               - BadMethodCallException
|           - DomainException
|           - InvalidArgumentException
|           - LengthException
|           - OutOfRangeException
|       - RuntimeException
|           - PDOException
|           - OutOfBoundsException
|           - OverflowException
|           - RangeException
|           - UnderflowException
|           - UnexpectedValueException
*/
```

İstisnai hatalar ile ilgili detaylı bilgi için <a href="http://nitschinger.at/A-primer-on-PHP-exceptions">bu kaynağa</a> gözatabilirsiniz.

<a name="database-and-runtime-exceptions"></a>

#### Veritabanı ve Genel İstisnai Hatalar

Genel bir istisnai hatayı <a href="http://php.net/manual/tr/internals2.opcodes.instanceof.php" target="_blank">instanceof</a> yöntemi ile <kbd>exception</kbd> yani $e nesnesine sınıf kontrolü yaparak hatanın ilgili olduğu sınıfa göre hataları filtreleyebilirsiniz. Örneğin uygulamadan dönen veritabanı hatalarını yönetmek istiyorsanız aşağıdaki kod bloğu size yardımcı olabilir.

```php
$app->error(
    function (RuntimeException $e) use ($container) {

        if ($e instanceof PDOException) {

            $body = $container->get('view')
                ->withStream()
                ->get(
                    'templates::error',
                    [
                        'error' => "Something went wrong. We can't process your request right now."
                    ]
                );
            return $container->get('response')
                ->withStatus(200)
                ->withHeader('Content-Type', 'text/html')
                ->withBody($body);
        }

        $log = new Obullo\Error\Log($container->get('logger'));
        $log->error($e);
    }
);
```

<a name="fatal-errors"></a>

#### Ölümcül Hatalar

Aşağıdaki örnekte ise php fatal error türündeki hatalar kontrol altına alınarak error log sınıfına gönderiliyor.

```php
/*
|--------------------------------------------------------------------------
| Php Fatal Errors
|--------------------------------------------------------------------------
*/
$app->fatal(
    function (ErrorException $e) use ($container) {

        $log = new Obullo\Error\Log($container->get('logger'));
        $log->error($e);
    }
);
```
Bir ölümcül hata oluşması durumunda isimsiz fonksiyon çalışarak fonksiyon içerisindeki görevleri yerine getirir. Fatal error metodu uygulamanın en alt seviyesinde çalışır. İstisnai hatalardan faklı olarak <kbd>$app->fatal()</kbd> metodu errors.php dosyası içerisinde yalnızca <kbd>bir kere</kbd> tanımlanabilir.

<a name="error-middleware"></a>

### Http Error Katmanı

Uygulama katmanlarında oluşan istisnai hatalar ve katman hataları Http Error katmanı tarafından yönetilir. Hataların gösterilip gösterilmemesine yada evrensel hata yakalayıcısına gönderilip gönderilmeyeceğine bu katman ile karar verirlebilir.

```php
class Error implements ErrorMiddlewareInterface, ImmutableContainerAwareInterface
{
    use ImmutableContainerAwareTrait;

    public function __invoke($error, Request $request, Response $response, callable $out = null)
    {
        if (is_string($error)) {
            echo $error;
        }
        if (is_object($error)) {

            // ..
        }
        return $response;
    }
}
```

Http error katmanı doğal php hataları ile ilgili değildir. Sadece uygulama istisnai hatalarından sorumludur. Error katmanı ile ilgili daha fazla bilgi için <a href="https://github.com/obullo/http-middlewares/">http-middlewares</a> bağlantısını ziyaret edin.

<a name="creating-http-errors"></a>

### Http Hataları Oluşturmak

Kimi durumlarda uygulamaya özgü http hataları göstermeniz gerekebilir bu durumda Http paketi içerisindeki response sınıfına ait metotları uygulamanızda kullanabilirsiniz. Opsiyonel metotlar <kbd>withStatus()</kbd> ve <kbd>withHeader()</kbd>, http hata başlığı ve durum kodları göndermenizi sağlar.

```php
$body = $this->view->withStream()
    ->get(
        'templates::error',
        [
            'error' => sprintf(
                '%s Method Not Allowed',
                "POST"
            )
        ]
    );

return $this->response->withStatus(405)
    ->withHeader('Content-Type', 'text/html')
    ->withBody($body);
```

Örnek bir 404 Hatası

```php
$body = $this->view->withStream()->get('templates::404');

return $this->response->withStatus(404)
    ->withHeader('Content-Type', 'text/html')
    ->withBody($body);
```

<a name="error-message-customization"></a>

#### Hata Şablonlarını Özelleştirmek

Uygulama içinde kulanılan hata metotlarına ait hata şablonlarını ihtiyaçlarınıza göre özelleştirebilirsiniz. Bu şablonları düzenlemek için,

```php
resources/templates/errors/
```

klasöründeki dosyalara gözatın.