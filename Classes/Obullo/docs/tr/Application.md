
## Uygulama Sınıfı

Uygulama sınıfı, ortam değişkenini elde etme, çerçeve versiyonu, hata tanımlamaları ve evrensel nesne değerlerine ulaşma gibi temel fonksiyonları içerir.

<ul>
<li>
    <a href="#application-flow">İşleyiş</a>
    <ul>
        <li><a href="#index-file">Index.php dosyası</a></li>
        <li><a href="#http-requests">Http İstekleri</a></li>
        <li><a href="#cli-requests">Konsol İstekleri</a></li>
        <li><a href="#dispatcing-routes">Route Kuralları</a></li>
        <li><a href="#middlewares">Http Katmanları</a></li>
        <li><a href="#class-reference">Application Sınıfı Referansı</a></li>
    </ul>
</li>
</ul>

<a name='application-flow'></a>

### İşleyiş

 Uygulama ortam değişkeni olmadan çalışamaz ve bu nedenle ortam çözümlemesi ilk yüklenme seviyesinde <kbd>app/environments.php</kbd> dosyası okunarak gerçekleşir ve ortam değişkenine,

```php
$container->get('env')->getValue()
```

metodu ile uygulamanın her yerinden ulaşılabilir.

<a name="index-file"></a>

#### Index.php dosyası

Uygulamaya ait tüm isteklerin çözümlendiği dosya <kbd>index.php</kbd> dosyasıdır. Bu dosyanın tarayıcıda gözükmemesini istiyorsanız bir <kbd>.htaccess</kbd> dosyası içerisine aşağıdaki kuralları yazmanız yeterli olacaktır.

```php
RewriteEngine on
RewriteCond %{REQUEST_FILENAME} !-f
RewriteCond %{REQUEST_FILENAME} !-d
RewriteCond $1 !^(index\.php|assets|robots\.txt)
RewriteRule ^(.*)$ ./index.php/$1 [L,QSA]
```
<a name='http-requests'></a>

#### Http  İstekleri

Uygulamaya gelen istekler http ve konsol istekleri olarak ikiye ayrılır. Http istekleri <kbd>public/</kbd> klasöründeki <kbd>index.php</kbd> dosyasından çalışır. Http istekleri çözümlendikten sonra kontrolör dosyalarını <kbd>app/modules/</kbd> klasöründen çağırılırlar.

<a name='cli-requests'></a>

#### Konsol İstekleri

Uygulamada konsol istekleri kök dizindeki <kbd>task</kbd> dosyasından çalışır. Konsol komutları çözümlendikten sonra kontrolör dosyalarını <kbd>app/modules/tasks</kbd> klasöründen çağırırlar. 

<a name="dispatcing-routes"></a>

#### Route Kuralları

<kbd>index.php</kbd> dosyasına gelen bir http isteğinden sonra url çözümlemesi route sınıfı tarafından yönetilir.

Bir http GET isteği çözümlemesi

```php                   
$router->get('product/([0-9])', 'shop/product/$1');                            
```

Bir http POST isteği çözümlemesi

```php
$router->post('product/post', 'shop/product/post');
```

GET ve POST isteklerini içeren bir route çözümlenmesi

```php
$router->match(['get', 'post'], 'product/page', 'shop/product/page');
```
Route çözümlemeleri ilgili daha fazla bilgi için [Router.md](Router.md) dosyasını gözden geçirebilirsiniz.

<a name="middlewares"></a>

#### Http Katmanları

Http katmanları uygulamayı etkilemek, analiz etmek, <kbd>request</kbd> ve <kbd>response</kbd> nesneleri ile uygulamanın çalışmasından sonraki veya önceki aşamayı etkilemek için kullanılırlar. Bir http katmanı <kbd>app/classes/Http/Middlewares</kbd> klasörü içerisinde yeralan basit bir php sınıfıdır. Http katmanları ile ilgili daha fazla bilgi için [App-Middlewares.md](App-Middlewares.md) dökümentasyonunu inceleyebilirsiniz.

<a name="class-reference"></a>

### Application Sınıfı Referansı

##### $this->app->getVersion();

Mevcut çerçeve sürümüne geri döner.

```php
$this->app->getVersion(); // 1.0
```

##### $this->app->getContainer();

Konteyner nesnesini verir.

##### $this->app->x();

Uygulama sınıfında içerisinde çağırılan metot tanımlı değilse Controller sınıfından çağırır.

```php
$this->app->test();  // Controller test metodu çıktılanır
```
##### $this->app->uri->x();

Uygulamada  bir Layer ( Bknz. [Layer](Layer.md) paketi  ) isteği yaratıldığında uri nesnesi istek gönderilen uri değeri ile yeniden oluşturulur ve bu nedenle evrensel uri nesnesi değişime uğrar. Böyle bir durumda bu yöntem ilk durumdaki http isteğinin uri nesnesine ulaşabilmeyi sağlar.

```php
$this->app->uri->getPath();
```
##### $this->app->router->x();

Uygulamada bir Layer isteği ( Bknz. [Layer](Layer.md) paketi  ) yaratıldığında router nesnesi istek gönderilen uri değeri ile yeniden oluşturulur ve bu nedenle evrensel router nesnesi değişime uğrar. Böyle bir durumda bu yöntem ilk durumdaki http isteğinin router nesnesine ulaşabilmeyi sağlar.

```php
$this->app->router->getMethod();
```