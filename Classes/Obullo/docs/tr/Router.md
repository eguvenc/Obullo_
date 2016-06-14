
## Router Sınıfı

Router sınıfı uygulamanızda index.php dosyasına gelen istekleri <kbd>app/routes.php</kbd> dosyanızdaki route tanımlamalarına göre url yönlendirme, http katmanı çalıştırma, http isteklerini filtreleme gibi işlevleri yerine getirir.

<ul>
<li>
    <a href="#configuration">Konfigürasyon</a>
    <ul>
        <li><a href="#domain">Alan Adı</a></li>
        <li><a href="#subfolders">Alt Klasör Limiti</a></li>
        <li><a href="#defaulPage">Açılış Sayfası</a></li>
        <li><a href="#index.php">Index.php</a></li>
        <li><a href="#404-errors">404 Hataları</a></li>
    </ul>
</li>
<li><a href="#accessing-methods">Metotlara Erişim</a></li>
<li>
    <a href="#routing">Route Kuralları</a>
    <ul>
        <li><a href="#route-types">Kural Türleri</a></li>
        <li><a href="#closures">İsimsiz Fonksiyonlar</a></li>
        <li><a href="#parameters">Parametreler</a></li>
        <li><a href="#route-groups">Route Grupları</a></li>
        <li><a href="#sub-domains">Alt Alan Adları</a></li>
        <li><a href="#folders">Dizinler</a></li>
    </ul>
</li>
<li>
    <a href="#middlewares">Http Katmanları</a>
    <ul>
        <li><a href="#route-md-assignment">Bir Kurala Katman Atamak</a></li>
        <li><a href="#group-md-assignment">Bir Gruba Katman Atamak</a></li>
        <li><a href="#regex-md">Düzenli İfadeler</a></li>
    </ul>
</li>
<li><a href="#method-reference">Router Sınıfı Referansı</a></li>
</ul>

<a name="configuration"></a>

### Konfigürasyon

Router sınıfı konfigürasyon değerlerini aldıktan sonra router kurallarınızı çalıştırmaya başlar bu yüzden <kbd>$router->configure()</kbd> metodunun en tepede ilan edilmesi gerekir.

<a name="domain"></a>

#### Alan Adı

Router sınıfı alan adı eşleşmeleri için geçerli <kbd>kök</kbd> adresinizi bilmek zorundadır.Kök adresinizi aşağıdaki gibi tanımlayabilirsiniz,

```php
$router->setDomainRoot('example.com');
```

Kök domain adresinizi başında <kbd>"www."</kbd> öneki olmadan girin.

```php
myproject.com 
```

<a name="subfolders"></a>

#### Alt Klasör Limiti

Proje içerisinde <kbd>app/folders</kbd> klasörü altında alt alta istediğiniz sayıda klasörler yaratabilirsiniz. Bu sayıyı sub folder level komutu belirler.

```php
$router->setSubfolderLevel(3);
```

<a name="defaulPage"></a>

#### Açılış Sayfası
 
Route dosyanızdaki tanımlı olan ilk route kuralı varsayılan açılış sayfasına ait kontrolör dosyasını belirler.

```php
$router->match(['get', 'post'],
    '/', 'welcome',
    function () use ($container) {

    }
);
```

<a name="index.php"></a>

#### Index.php

Bu dosyanın tarayıcıda gözükmemesini istiyorsanız bir <kbd>.htaccess</kbd> dosyası içerisine aşağıdaki kuralları yazmanız yeterli olacaktır.

```php
Options -Indexes
RewriteEngine on

RewriteCond %{REQUEST_FILENAME} !-f
RewriteCond %{REQUEST_FILENAME} !-d
RewriteRule ^(.*)$ index.php/$1 [L]
```

* Dosyadaki ilk kod bloğu güvenlik amacıyla dizin indexlemeyi kapatır.
* Son kod bloğu ise dosya ve dizin haricindeki tüm istekleri index.php dosyasına yönlendirir.

.htaccess dosyanızın çalışabilmesi için sunucunuzda apache <kbd>mod_rewrite</kbd> modülünün etkin olması gerekir.

<a name="404-errors"></a>

#### 404 Hataları

404 hataları çekirdek http katmanı içerisinde 404 şablonu kullanılarak oluşturulur.

```php
$body = $this->getContainer()
    ->get('view')
    ->withStream()
    ->get('templates::404');

return $response->withStatus(404)
    ->withHeader('Content-Type', 'text/html')
    ->withBody($body);
```

<a name="accessing-methods"></a>

### Metotlara Erişim

```php
$container->get('router')->method();
```

Kontrolör içinden,

```php
$this->router->method();
```

<a name="routing"></a>

### Route Kuralları

Tipik olarak bir URL girdisi ile ve ona uyan dizin arasında <kbd>klasör/sınıf/metot/arg1/arg2/..</kbd> gibi birebir bir ilişki vardır.

```php
example.com/klasör/sınıf/metot/id
```

Aşağıdaki adres <kbd>app/folders/shop/</kbd> klasörü içerisindeki <kbd>product</kbd> kontrolör dosyasını çalıştırır.

```php
example.com/index.php/shop/product/index/1
```

Fakat bazı durumlarda bu birebir ilişki yerine farklı <kbd>klasör/sınıf/method</kbd> ilişkisi yeniden kurgulanmak istenebilir.

```php
example.com/shop/product/index/1
```

Yukarıdaki adresimizi aşağıdaki gibi bir URL biçimine dönüştürmek istiyorsak,  

```php
example.com/2/mp3-player
```

<kbd>app/routes.php</kbd> dosyası içerisinde aşağıdaki gibi bir route kuralı tanımlamamız gerekir.

```php
$router->get('[0-9]/.*', 'shop/product/index/$1/$2');
```

Yukarıda tanımlamadan sonra url <kbd>example.com/2</kbd> gibi bir sayısal değer ile çalıştığında bu sayısal değer <kbd>index/$1</kbd> olarak index metodu birinci argümanına, <kbd>example.com/2/herhangi_bir_deger</kbd> gibi sayısal olmayan herhangi bir değerde <kbd>index/$1/$2</kbd> olarak index metodu ikinci argümanına yönlendirilir.

```php
example.com/shop/product/
```

Varsayılan metot her zaman <kbd>index</kbd> metodudur fakat açılış sayfasında bu metodu yazmanıza gerek kalmaz. Eğer argüman göndermek zorundaysanız bu durum da index metodunu yazmanız gerekir.

Route kuralları <kdd>düzenli ifadeler</kdd> (regex) yada <kbd>/wildcards</kbd> kullanılarak tanılanabilir.

<a name="route-types"></a>

#### Kural Türleri

GET- http isteği türünde bir kural oluşturur.

```php
$router->get('welcome.*', 'home/index/$1');
```

POST - http isteği türünde bir kural oluşturur.

```php
$router->post('welcome/.+', 'home/index/$1');
```

GET, POST, DELETE, PUT ve bunun gibi birden fazla http isteğini kabul eden kurallar oluşturur.

```php
$router->match(['get','post'], 'welcome/.+', 'home/index/$1');
```

Eğer girilen kurala uygun olmayan http isteği gönderilirse <kbd>Http Error 405 method not allowed</kbd> hatası ile karşılaşırsınız. Aşağıdaki tablo route kuralları için mevcut http metotlarını gösteriyor.

<table>
  <thead>
    <tr>
    <th>Metot</th>
    <th>Açıklama</th>
    <th>Örnek</th>
    </tr>
  </thead>
  <tbody>
    <tr>
    <td>post</td>
    <td>Bir route kuralının sadece POST isteğinde çalışmasını sağlar.</td>
    <td>$router->post($url, $rewrite)</td>
    </tr>
    <tr>
    <td>get</td>
    <td>Bir route kuralının sadece GET isteğinde çalışmasını sağlar.</td>
    <td>$router->get($url, $rewrite)</td>
    </tr>
    <tr>
    <td>put</td>
    <td>Bir route kuralının sadece PUT isteğinde çalışmasını sağlar.</td>
    <td>$router->put($url, $rewrite)</td>
    </tr>
    <tr>
    <td>delete</td>
    <td>Bir route kuralının sadece DELETE isteğinde çalışmasını sağlar.</td>
    <td>$router->delete($url, $rewrite)</td>
    </tr>
    <tr>
    <td>match</td>
    <td>Bir route kuralının sadece girilen istek tiplerinde çalışmasını sağlar.</td>
    <td>$router->match(['get','post'], $url, $rewrite)</td>
    </tr>
  </tbody>
</table>

<a name="closures"></a>

#### İsimsiz Fonksiyonlar

Route kuralları içerisinde isimsiz fonksiyonlar da kullanabilmek mümkündür.

```php
$router->get(
    'welcome/[0-9]+/[a-z]+', 'home/index/$1/$2', 
    function () use ($container) {
        $container->get('view')->load('views::dummy');
    }
);
```

Bu örnekte, <kbd>example.com/welcome/123/test</kbd> adresine benzer bir URL <kbd>home/</kbd> kontrolör sınıfı index metodu parametresine <kbd>123 - test</kbd> argümanlarını gönderir, eğer url eşleşirse isimsiz fonksiyon çalıştırılır ve <kbd>folders/views/view/</kbd> dizininden dummy.php adlı view dosyası yüklenir.

Eğer rewrite parametresi boş ise bu parametreye aşağıdaki gibi null değeri girmeniz gerekir. 

```php
$router->get(
    'welcome/index', null,
    function () use ($container) {
        $container->get('view')->load('views::dummy');
    }
);
```

<a name="parameters"></a>

#### Parametreler

Eğer girilen bölümleri fonksiyon içerisinden belirli kriterlere göre parametreler ile almak istiyorsanız süslü parentezler { } kullanabilirsiniz.

```php
$router->get(
    'welcome/index/{id}/{name}',
    function ($id, $name) use ($container) {
        $container->get('response')->getBody()->write($id.'-'.$name);
    }
)->where(['id' => '[0-9]+', 'name' => '[a-z]+']);
```

Yukarıdaki örnekte <kbd>/welcome/index/123/test</kbd> adresine benzer bir URL <kbd>where()</kbd> fonksiyonu içerisine girilen kriterlerle uyuştuğunda isimsiz fonksiyon içerisine girilen fonksiyonu çalıştırır.

```php
welcome/index/123/test
```

Yukarıdaki örnek çalıştırıldığında, düzenli ifadeler route kuralı ile uyuşuyor ise sayfanın $id ve $name argümanlarından oluşan hata sayfasını çıktılaması gerekir.

```php
$router->get(
    '{id}/{name}/{any}', 'shop/index/$1/$2/$3',
    function ($id, $name, $any) use ($c) {
        echo $id.'-'.$name.'-'.$any;
    }
)->where(array('id' => '[0-9]+', 'name' => '[a-z]+', 'any' => '.*'));
```

Bu örnekte ise <kbd>{id}/{name}/{any}</kbd> olarak girilen URI şeması <kbd>/123/electronic/mp3_player/</kbd> adresine benzer bir URL ile uyuştuğunda girdiğiniz düzenli ifade ile değiştirilir ve rewrite değerine <kbd>$1/$2/$3</kbd> olarak girdiğiniz URL argümanları isimsiz fonksiyona parametre olarak gönderilir.

Yukarıdaki route kuralının çalışabilmesi için aşağıdaki gibi bir URL çağırılması gerekir.

```php
shop.example.com/123/electronic/mp3_player
```

<a name="route-groups"></a>

#### Route Grupları

Route grupları bir kurallar bütününü topluca yönetmenizi sağlar. Grup kuralları belirli <kbd>alt domainler</kbd> için çalıştırılabildiği gibi belirli <kbd>http katmanlarına</kbd> da tayin edilebilirler. Bunun için <kbd>$this->attach()</kbd> metodu ile katmanı istediğiniz URL adreslerine tuturmanız gerekir.

```php
$router
    ->domain('test.example.com')
    ->group(
        'examples/',
        function () {

            $this->group(
                'forms/', function () {

                    // form dizini kuralları

                }
            );

            // Route kuralları

            $this->match(['get', 'post'], 'home', 'welcome');

        }
    )->add(['Maintenance'])->attach('.*')
->end();
```

Bu tanımlamadan sonra eğer buna benzer bir URL <kbd>/welcome</kbd> çağırırsanız <kbd>MethodNotAllowed</kbd> katmanı çalışır ve aşağıdaki hata ile karşılaşırsınız.

```php
Http Error 405 Get method not allowed.
```

<a name="sub-domains"></a>

### Alt Alan Adları

Eğer bir gurubu belirli bir alt alan adına tayin ederseniz grup içerisindeki route kuralları yalnızca bu alan adı için geçerli olur.

```php
$router
    ->domain('shop.example.com')
    ->group(function () {

            $this->get('welcome/.+', 'home/index');
            $this->get('product/[0-9]', 'product/list/$1');
        }
    )
->end();
```

Tarayıcınızdan bu URL yi çağırdığınızda bu alt alan adı için tanımlanan route kuralları çalışmaya başlar.

```php
http://shop.example.com/product/123
```

Aşağıda <kbd>account.example.com</kbd> adlı bir alt alan adı için kurallar tanımladık.

```php
$router
    ->domain('account.example.com')
    ->group(function () {

        $this->get(
            '{id}/{name}/{any}', 'user/account/$1/$2/$3',
            function ($id, $name, $any) {
                echo $id.'-'.$name.'-'.$any;
            }
        )->where(array('id' => '[0-9]+', 'name' => '[a-z]+', 'any' => '.+'));
    }
)->end();
```

Tarayıcınızdan aşağıdaki gibi bir URL çağırdığınızda bu alt alan adı için yazılan kurallar çalışmış olur.

```php
http://account.example.com/123/john/test
```

Alt alan adlarınız eğer <kbd>sports19.example.com</kbd>, <kbd>sports20.example.com</kbd> gibi dinamik ise alan adı kısmında düzenli ifadeler de kullanabilirsiniz.

```php
$router
    ->domain('sports.*\d.example.com')
    ->group(
        function ($options) {
            echo $options['subname'];  // sports20
        }
    )
    ->add(['Maintenance'])
    ->attach(['.*'])
->end();
```

<a name="folders"></a>

#### Dizinler

Eğer bir grubun URL den çağırılan değer ile eşleşme olduğunda çalışmasını istiyorsanız,

```php
http://example.com/examples/forms
```

Yukarıdaki gibi bir url için aşağıdaki gibi dizinlere göre iç içe route grupları da oluşturabilirsiniz.

```php
$router
    ->group(
        'examples/',
        function () {

            // example dizini kuralları

            $this->group(
                'forms/', function () {
                    
                    // forms dizini kuralları
                }
            );
        }
    )
->end()
```

<a name="middlewares"></a>

### Http Katmanları

Http katmanları tek bir route kuralına atanarak direkt çalıştırılabilecekleri gibi bir route grubuna da tutturulduktan sonra <kbd>attach()</kbd> metodu ile çalıştırılabilirler.

<a name="route-md-assignment"></a>

#### Bir Kurala Katman Atamak

Tek bir route kuralı için katmanlar atayabilmek mümkündür. Aşağıdaki örnekte <kbd>/hello</kbd> sayfasına güvenli olmayan bir get yada post isteği geldiğinde <kbd>welcome/index</kbd> sayfasına yönlendirilir ve <a href="http://github.com/obullo/http-middlewares" target="_blank">Https katmanı</a> çalıştırılarak istek <kbd>https://</kbd> protokolü ile çalışmaya zorlanır.

```php
$router->match(['get', 'post'], 'hello$', 'welcome/index')->middleware(['Https']);
```

Eğer birden fazla katman çalıştırmak isterseniz katmanları bir dizi içerisinde girin.

```php
$router->get('membership/restricted')->middleware(array('auth', 'guest'));
```

<a name="group-md-assignment"></a>

#### Bir Gruba Katman Atamak

Bir grup için oluşturulan katmanı grup fonksiyonu içerisinde çalıştırabilmek için <kbd>attach()</kbd> metodu kullanılır.

```php
$router->group(
    function () {

        $this->get('welcome/.+', 'home/index');
        $this->get('product/{id}', 'product/list/$1');
    },
)
->add(['Https'])
->attach(['.*'])
->end();
```

Attach metodu içerisinde dizi türü kullanarak birden fazla operasyon ekleyebilirsiniz.


<a name="regex-md"></a>
 
#### Düzenli İfadeler

Bir grup içerisinde kullanılan katmanlar, bazen belirli URL segmentleri haricinde çalıştırılmak istenebilir.

```php
http://www.example.com/test/bad_segment
http://www.example.com/test/good_segment1
http://www.example.com/test/good_segment2
```

Yukarıdaki örneğe benzer adreslerimiz olduğunu varsayarsak,

```php
$router->group(
    function () {

        // kurallar
    },
)
->add(['Test'])
->attach(['^(.*test/(?!bad_segment).*)$'])
->end();
```

Yukarıdaki kural gurubu için <kbd>bad_segment</kbd> segmenti dışındaki tüm url adreslerinde <kbd>Test</kbd> katmanı çalışmış olur.

```php
$router->group(
        function () {

            // kurallar
        }
    )
    ->add(['Guest'])
    ->attach('^(?!.*membership/login|.*membership/logout|.*checkout/payment).*$')
    ->end();
```

Yukarıdaki kural grubunda ise parentez içerisinde tanımlı sayfalar hariç tüm sayfalarda <kbd>Guest</kbd> katmanı çalışmış olur.

<a name="method-reference"></a>

#### Set Metotları

------

##### $router->setDomainRoot(string $domain);

Geçerli ve <kbd>değişmez</kbd> kök domain adresini belirler.

##### $router->setSubFolderLimit(int $num);

Alt klasör açma limitini belirler.

##### $router->match(array $methods, string $match, string $rewrite, $closure = null)

Girilen http istek metotlarına göre bir route yaratır, istek metotları get,post,put ve delete metotlarıdır.

##### $router->get(string $match, string $rewrite, $closure = null)

Http GET isteği türünde bir route kuralı oluşturur.

##### $router->post(string $match, string $rewrite, $closure = null)

Http POST isteği türünde bir route kuralı oluşturur.

##### $router->put(string $match, string $rewrite, $closure = null)

Http PUT isteği türünde bir route kuralı oluşturur.

##### $router->delete(string $match, string $rewrite, $closure = null)

Http DELETE isteği türünde bir route kuralı oluşturur.

##### $router->where(array $replace);

Bir route kuralı parameterelerini girilen düzenli ifadeler ile değiştirir.

##### $router->add(string|array $middleware);

Bir route kuralına http katmanı yada katmanlarını ekler.

##### $router->domain($host);

Bir route grubu için domain tayin eder.

##### $router->group(array $options, $closure);

Bir route grubu oluşturur.

##### $router->add(string|array $middleware);

Bir route grubuna attach metodu ile başlatılmak üzere http katmanı yada katmanlarını tayin eder.

##### $router->attach(string|array $route|$regex)

Geçerli gruba add metodu ile tayin edilmiş katmanları route grubuna ekler.

##### $router->end();

Bir route grubunu bir sonraki group değerlerinden ayırt edebilmek için sonlandırır.

#### Get Metotları

------

##### $this->router->getAncestor();

En üst seviyedeki birincil klasör ismine aksi durumda boş bir string '' değerine geri döner.

##### $this->router->getFolder();

Çağırılan klasör yoluna geri döner.

##### $this->router->getClass();

Çağırılan sınıf adına geri döner.

##### $this->router->getMethod();

Çağırılan metot adına geri döner.

##### $this->router->getNamespace();

Çağırılan dizinin string türünde php <kbd>Namespace\Class</kbd> çözümlemesine geri döner.