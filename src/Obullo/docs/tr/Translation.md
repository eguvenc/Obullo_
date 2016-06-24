
## Çeviri Sınıfı

Çeviri sınıfı uygulamanızı birden fazla farklı dilde yayınlamak için dil dosyalarından dil satırları elde etmeyi sağlar. Dil dosyaları uygulamanızın <kbd>resources/translations</kbd> klasöründe tutulurlar. Kendi dil dosyalarınızı da bu klasörde oluşturabilirsiniz. Her bir dil dosyası kendine ait klasör içerisinde kayıtlı olmalıdır. Mesela ispanyolca dosyaları <kbd>app/translations/es</kbd> klasörüne kaydedilmelidir.

<ul>
    <li><a href="#creating-files">Dil Dosyaları Oluşturmak</a></li>
    <li><a href="#load">$this->translator->load()</a></li>
    <li><a href="#get">$this->translator->get()</a></li>
    <li><a href="#has">$this->translator->has()</a></li>
    <li><a href="#translation-middleware">Translation Katmanı</a></li>
    <li><a href="#getLocale">$this->translator->getLocale()</a></li>
    <li><a href="#setLocale">$this->translator->setLocale()</a></li>
    <li><a href="#rewrite">Url Adresi Dil Desteği</a></li>
    <li><a href="#fallback">Bulunamayan Dil</a></li>
    <li><a href="#fallback-lines">Bulunamayan Dil Çevirisi</a></li>
    <li><a href="#method-reference">Translator Sınıfı Referansı</a></li>
</ul>

<a name="service-provider"></a>

### Servis Sağlayıcısı

<kbd>app/providers.php</kbd> dosyasında servis sağlayıcısının tanımlı olduğundan emin olun.

```php
$container->addServiceProvider('Obullo\Container\ServiceProvider\Translator');
```

<a name="creating-files"></a> 

### Dil Dosyaları Oluşturmak

Dil dosyaları <kbd>resources/translations</kbd> klasörü altında oluşturulur. Her bir dil dosyası kendine ait klasör içerisinde kayıtlıdır. Uygulamanızdaki dil dosyalarına ait satırların birbirleriyle karışmaması için en iyi yöntemlerden biri satırları kategorilere göre <kbd>:</kbd> karakteri ile ayırmaktır. Örneğin <kbd>Games</kbd> adında bir klasörümüzün olduğunu varsayarsak bu klasöre ait dil satırlarının aşağıdaki gibi olması önerilir.

```php
return array(

    /**
     * Label
     */
    'GAMES:MANAGEMENT:LABEL:ID'              => 'Id',
    'GAMES:MANAGEMENT:LABEL:NAME'            => 'Game Name',
    'GAMES:MANAGEMENT:LABEL:CATEGORIES'      => 'Categories',
    'GAMES:MANAGEMENT:LABEL:STATUS'          => 'Status',
    'GAMES:MANAGEMENT:LABEL:ORDER'           => 'Order',
    'GAMES:MANAGEMENT:LABEL:GAME_URL'        => 'Game URL',
    'GAMES:MANAGEMENT:LABEL:GAME_IP'         => 'Game IP',
    'GAMES:MANAGEMENT:LABEL:GAME_RESOLUTION' => 'Game Resolution',
    'GAMES:MANAGEMENT:LABEL:IMAGE'           => 'Image',
    'GAMES:MANAGEMENT:LABEL:DESCRIPTION'     => 'Description',
    'GAMES:MANAGEMENT:LABEL:ACTIVE'          => 'Active',
    'GAMES:MANAGEMENT:LABEL:PASSIVE'         => 'Passive',
    'GAMES:MANAGEMENT:LABEL:FILTER_ALL'      => 'All',
    'GAMES:MANAGEMENT:LABEL:EDIT'            => 'Edit',

    /**
     * Link
     */
    'GAMES:MANAGEMENT:LINK:EDIT'    => 'Edit Game',
    'GAMES:MANAGEMENT:LINK:ADD_NEW' => 'Add New Game',

    /**
     * Error
     */
    'GAMES:MANAGEMENT:ERROR:NOTVALIDRESOLUTION' => 'Game resolution is not valid. Ex: 800x600',

    /**
     * Button
     */
    'GAMES:MANAGEMENT:BUTTON:FILTER' => 'Filter',
    'GAMES:MANAGEMENT:BUTTON:SUBMIT' => 'Submit',

    /**
     * Notice
     */
    'GAMES:MANAGEMENT:NOTICE:CREATE' => 'Game successfully added.',
    'GAMES:MANAGEMENT:NOTICE:UPDATE' => 'Game successfully updated.',
);
```

<a name="load"></a> 

### $this->translator->load()

Bir dil dosyası oluşturduktan sonra dosyayı kullanabilmek için ilk önce load() metodu kullanılır.

```php
- resources
    - translations
        - en
            games.php
```

```php
$this->translator->load('games');
```

<a name="get"></a> 

### $this->translator->get()

Bir dil metnine ulaşmak için aşağıdaki yöntem kullanılır.

```php
echo $this->translator['GAMES:MANAGEMENT:LABEL:NAME'];  // Game Name
```

Eğer metin içerisinde <kbd>%s</kbd> yada <kbd>%d</kbd> gibi formatlar kullanılmışsa aşağıdaki gibi get() metodu kullanabilirsiniz.

```php
The %s field must be at least %d characters.
```

```php
echo $this->translator->get('OBULLO:VALIDATOR:MIN', 'Email', 6);  // The Email field must be at least 6 ch..
```

Eğer girilen metne ait dil satırı ilgili dil dosyasında bulunamazsa girilen metin çıktılanır.

```php
echo $this->translator['GAMES:MANAGEMENT:LABEL:NAME'];  // GAMES:MANAGEMENT:LABEL:NAME
```

<a name="has"></a>

### $this->translator->has()

Bir dile ait satırın var olup olmadığını kontröl etmek için <kbd>has()</kbd> metodu kullanılır.

```php
var_dump($this->translator->has('EXAMPLE:UNDEFINED_TEXT'));  // false
```

<a name="translation-middleware"></a>

### Translation Katmanı

Translation katmanı eğer mevcut değilse <a href="https://github.com/obullo/http-middlewares/" target="_blank">https://github.com/obullo/http-middlewares/</a> adresinden Translation.php dosyasını <kbd>app/classes/Http/Middlewares</kbd> klasörünüze kopyalayın.

#### İşleyiş

```php
http://example.com/en/home
```

Kullanıcı siteyi ziyaret ettiğinde http dil katmanı çalışır ve aşağıdaki adımlardan sonra dil <kbd>$_COOKIES[locale]</kbd> değeri içerisinden okunur yada seçilen dil bu çereze kaydedilir.

Http dil katmanınının kullanıcının varsayılan dilini belirleme davranışı sırasıyla aşağıdaki gibidir:

* Eğer ziyaretçi <kbd>http://example.com/en/welcome</kbd> gibi bir URI get isteği ile geldiyse dil <kbd>en</kbd> olarak kaydedilir.
* Eğer ziyaretçi tarayıcısında <kbd>$_COOKIES['locale']</kbd> değeri mevcut ise varsayılan dil bu kabul edilir.
* Eğer ziyaretçinin tarayıcısı <kbd>locale_accept_from_http()</kbd> fonksiyonu ile incelenir ve tarayıcının geçerli dili bulunursa varsayılan dil bu kabul edilir.
* Eğer yukarıdaki tüm seçenekler ile mevcut dil bulunamazsa <kbd>$this->translator->getDefault()</kbd> metodu ile translator.php konfigürasyonunuzdaki <kbd>default => locale</kbd> değeri ile varsayılan dil belirlenir.

<kbd>locale_accept_from_http()</kbd> fonksiyonu <a href="http://php.net/manual/tr/book.intl.php" target="_blank">intl</a> genişlemesi gerektirir. Bu yüzden bu genişlemenin php konfigürasyonunuzda kurulu olması tavsiye edilir.

#### Konfigürasyon

Dil katmanının çalıştırılmadan önce konfigüre edilmesi gerekir.

```php
$middleware->init(
    [
        // 'Maintenance',
        'Translation',
        'View', 
        'Router',
    ]
);
```

Dil konfigürasyonu <kbd>providers/translator.php</kbd> dosyasında varsayılan olarak aşağıdaki gibi tanımlıdır.


```php
'uri' => [
    'segment' => true,
    'segmentNumber' => 0   // Uri segment number e.g. http://example.com/en/home    
],
'cookie' => [
    'name'   =>'locale', 
    'domain' => null,
    'expire' => (365 * 24 * 60 * 60), // 365 day
    'secure' => false,
    'httpOnly' => false,
    'path' => '/',
],
```

<a name="getLocale"></a>

### $this->translator->getLocale()

Bir http isteği ile dil katmanı sayesinde çerezler içerisine kaydedilen varsayılan dili almak için aşağıdaki metot kullanılır.

```php
$this->translator->getLocale();  // en
```

<a name="setLocale"></a>

### $this->translator->setLocale()

Eğer çerezde kayıtlı varsayılan dili değiştirmek istiyorsanız set metodunu kullanmanız gerekir.

```php
$this->translator->setLocale('en');
```

Eğer çereze yazmak istemiyorsanız ikinci parametreyi <kbd>false</kbd> olarak girmeniz gerekir.

```php
$this->translator->setLocale('en', false);
```

<a name="rewrite"></a>

### Url Adresi Dil Desteği

Eğer uygulamanızın <kbd>http://example.com/en/welcome/index</kbd> gibi bir dil desteği ile çalışmasını istiyorsanız aşağıdaki route kurallarını <kbd>app/routes.php</kbd> dosyası içerisine tanımlamanız gerekir.

```php
$router->get('(?:en|de|es|tr)', 'welcome');     // example.com/en
$router->get('(?:en|de|es|tr)(/)', 'welcome');  // example.com/en/
$router->get('(?:en|de|es|tr)/(.*)', '$1');     // example.com/en/examples/helloWorld
```

* İlk kural dil segmentinden sonra kontrolör, metot ve parametre çalıştırmayı sağlar. ( örn. http://example.com/en/examples )
* İkinci kural ise varsayılan açılış sayfası içindir. ( örn. http://example.com/en/ )

Uygulamanızın desteklediği dilleri düzenli ifadelerdeki parentez içlerine girmeniz gerekir. Yukarıda en,es ve de dilleri örnek gösterilmiştir.

Eğer uygulamanızın bütününe değilde sadece belirli url adreslerinize dil desteği eklemek istiyorsanız <a href="https://github.com/obullo/http-middlewares" target="_blank">http-middlewares</a> bağlantısındaki <kbd>RewriteLocale</kbd> katmanını ve bu katmana ait dökümentasyonu inceleyin.


<a name="fallback"></a>

### Bulunamayan Dil

Eğer kullanıcının seçtiği dile ait <kbd>klasör</kbd> uygulamanızda mevcut değilse bunun yerine başka bir dil yüklenir. Bu yönteme fallback denilir ve fallback dilini almak için aşağıdaki metot kullanılır.

```php
$this->translator->getFallback();  // en
```

Fallback değeri <kbd>providers/translator.php</kbd> konfigürasyon dosyanızda tanımlıdır. Fallback dili Translation katmanı içerisindeki setFallback() fonksiyonu ile kontrol edilir.

```php
public function __invoke(Request $request, Response $response, callable $next = null)
{
    $this->request = $request;
    $this->translator = $this->getContainer()->get('translator');
    $this->params = $this->getContainer()->get('translator.params');

    $this->cookieValue = $this->readCookie();
    $this->setLocale();
    $this->setFallback();

    return $next($request, $response);
}
```

eğer konfigürasyonda fallback değeri <kbd>true</kbd> ise,

```php
'fallback' => array(
    'enabled' => true,
),
```

bulunamayan dil özelliği çalışmış olur ve bu fonksiyon eğer dil <kbd>klasörü</kbd> mevcut değilse setLocale() metodu ile fallback değerini mevcut dil olarak belirler.

<a name="fallback-lines"></a>

### Bulunamayan Dil Çevirisi

Mevcut yüklü dil dosyanızda bir çeviri metni bulunamazsa fallback dil dosyanız devreye girer ve fallback dosyası yüklenerek mevcut olmayan çeviri bu dosya içerisinden çağrılır.
Bu özelliği kullanabilmek için <kbd>providers/translator.php</kbd> konfigürasyon dosyanızdaki <kbd>fallback => translation</kbd> değerinin <kbd>true</kbd> olması gerekir.

```php
'fallback' => array(
    'enabled' => true,
    'translation' => true,
),
```

<a name="method-reference"></a>

### Translator Sınıfı Referansı

##### $this->translator->load(string $filename)

Girilen dile ait php dosyasını <kbd>resources/translations</kbd> klasörden yükler.

##### $this->translator->has(string $key)

Girilen dile ait çeviri mevcut ise <kbd>true</kbd> değerine aksi durumda <kbd>false</kbd> değerine geri döner.

##### $this->translator->get(string $key)

Girilen dile anahtarına ait çeviriye geri döner.

##### $this->translator->getLocale()

Mevcut dile geri döner. Örn. es, de, fr, tr.

##### $this->translator->setLocale(string $locale)

Mevcut dil değerini değiştirir.

##### $this->translator->getFallback()

Mevcut dil bulunamadığında yüklenmesi gereken dile geri döner.

##### $this->translator->setFallback(string $locale)

Bulunamayan dil değerini belirler.

##### $this->translator->hasFolder(string $locale)

<kbd>resources/translations</kbd> klasöründe dile ait klasör mevcut ise <kbd>true</kbd> değerine aksi durumda <kbd>false</kbd> değerine geri döner.
