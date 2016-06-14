
## Katmanlar

Çok katmanlı programlama tekniği hiyerarşik kontrolör programlama kalıbından türetilmiş uygulamanızı ölçeklenebilir hale getirmek için kullanılan bir tasarım kalıbıdır. ( bknz. <a href="http://www.javaworld.com/article/2076128/design-patterns/hmvc--the-layered-pattern-for-developing-strong-client-tiers.html" target="_blank">Java Hmvc</a> ).

<ul>
    <li><a href="#architecture">Mimari Yapı</a></li>
    <li><a href="#views">Görünümleri Katmanlar İle Oluşturmak</a></li>
    <li><a href="#methods">Metotlara Erişim</a></li>
    <li><a href="#calling-layers">Bir Katmanı Çağırmak</a></li>
    <li><a href="#hello-layers">Merhaba Katmanlar</a></li>
    <li><a href="#caching-layers">Bir Katmanı Önbelleklemek</a></li>
    <li><a href="#flushing-cached-layers">Bir Katmanı Önbellekten Silmek</a></li>
    <li><a href="#caching-with-params">Parametreler İle Önbellekleme</a></li>
    <li><a href="#flushing-with-params">Parametreler İle Önbelleklenmiş Katmanı Silmek</a></li>
    <li><a href="#creating-navigation-bar">Katmanlarla Bir Gezinme Çubuğu Yaratalım</a></li>
    <li><a href="#debugger">Çıktı Görüntüleyici</a></li>
    <li><a href="#reference">Layer Sınıfı Referansı</a></li>
</ul>

<a name="architecture"></a>

### Mimari Yapı

Çok katmanlı mimari MVC katmanlarını bir üst-alt hiyerarşisi içerisinde çözümler. Uygulama içerisinde tekrarlayan bu model yapılandırılmış bir client-tier mimarisi sağlar.

![Katmanlar](images/layers.png?raw=true "Katmanlı Programlama")

Her bir katman basit kontrolör sınıflarıdır. Layer sınıfı tarafından tekralanabilir olarak çağrılabilen katmanlar uygulamayı parçalayarak farklı işlevsel özellikleri bileşen yada web servisleri haline getirir.

Katmanlı mimari uygulamanızda <kbd>önyüz sürecini</kbd> yönetmenizi sağlar. Bu mimariyi kullanmanın faydalarını aşağıdaki gibi sıralayabiliriz.

* <b>Arayüz Tutarlılığı:</b> Katmanlı programlama görünümleri kesin parçalara ayırır ve her bölüm kendisinden sorumlu olan sınıfı çalıştırır ( view kontrolör ) böylece her katman bir layout yada widget hissi verir.
* <b>Bakım Kolaylığı:</b> Parçalara bölünen kullanıcı arayüzü bileşenleri MVC tasarım desenine bağlı kaldıkları için bakım kolaylığı sağlarlar.
* <b>Mantıksal Uygulamalar:</b> Katmanlar birbirleri ile etkişim içerisinde olabilecekleri gibi uygulama üzerinde hakimiyet ve önbelleklenebilme özellikleri ile genişleyebilir mantıksal uygulamalar yaratmayı sağlarlar. Bölümsel olarak birbirinden ayrılan katmanlar bir <kbd>web servis</kbd> gibi de çalışabilirler.

<a name="views"></a>

### Görünümleri Katmanlar İle Oluşturmak

Aşağıdaki figürde görüldüğü gibi katmanlı mimaride görünen varlıklar parçalara ayrılarak bileşen haline getirilir. 

![Katmanlar](images/layer-ui-components.png?raw=true "HMVC")

Katmanlı mimaride oluşturulan bileşenler birbirlerinden bağımsız parçalardır ve birbirleri ile etkileşim içinde olabilirler. Her bir bileşen kendisi için tayin edilen bir kontrolör sınıfı tarafından yönetilir ve layer paketi aracılığı ile çağrılırlar. Ayrıca birbirinden ayrılıp bağımsız hale gelen bileşenlere dışarıda ajax yada http istekleri de gönderilebilir. Yani bir modül yada web servisi haline getirilen bu parçalara dışarıdan bir http isteği ( Curl vb. ) olmadan ulaşılabileceği gibi bir http yada ajax isteği gönderilerek de ulaşılabilir.

Örneğin bir yönetim paneline ait bir gezinme çubuğu (navigation bar) bir katman aracılığı ile yönetilebilir. Gezinme çubuğu bir katman kullanılarak oluşturulduğunda view yapısından bağımsız olarak kontrol edilebilir hale gelir ve oluşturduğunuz gezinme çubuğu eğer bir ajax isteği ile tazelenmek isteniyorsa bu ajax isteği için ikinci bir kontrolör yazma gereksinimi ortadan kaldırılır bunun yerine gezinme çubuğu katmanına bir ajax istek gönderilerek gezinme çubuğu yeniden yaratılır ve uygulamada genel mvc mantığı dışına çıkılmamış olur.

Burada üzerinde durulan önemli nokta katmanlı mimaride oluşturduğunuz katmanı aynı zaman da bir web servis gibi çalıştırabiliyor olmanızdır.

Bu özelliğin yanında katman içerisinde bir kez yapılması gereken veritabanı sorguları önbelleklenebilir. Eğer örneğimizdeki gezinme çubuğunu bir web servis gibi düşünürsek, bu web servise gönderilen istekler girilen parametrelere göre önbelleklenerek uygulama performansı arttrılabilir.

<a name="methods"></a>

### Metotlara Erişim

Katman sınıfı <kbd>app/providers.php</kbd> dosyasında servis sağlayıcı olarak tanımlıdır.

```php
$container->get('layer')->method();
```

Kontrolör içerisinden,

```php
$this->layer->method();
```

yöntemleri ile servis metotlarına ulaşılabilir.

<a name="calling-layers"></a>

### Bir Katmanı Çağırmak

Katmanlar layer sınıfı üzerinden web servis metotlarına benzer şekilde çağırılırlar. Bir katman http get yada post metodları ile yaratılabilir.

```php
echo $this->layer->get('controller/method/args', $data = array());
echo $this->layer->post('controller/method/args', $data = array());
```

Katman istekleri <kbd>folder/controller/method/args</kbd> standart url çağırma yöntemi ile varolan router sınıfı üzerinden oluşturulurlar.

<a name="hello-layers"></a>

### Merhaba Katmanlar

Katmanları daha iyi anlamak için <kbd>folders/views</kbd> klasörü altında aşağıdaki gibi Header.php adında bir view kontrolör yaratın.

```php
namespace Views;

use Obullo\Http\Controller;

class Header extends Controller
{
    public function index()
    {
        echo $this->view->get(
            'header',
            [
                'header' => '<pre>HELLO HEADER LAYER</pre>'
            ]
        );
    }
}

/* Location: .folders/views/header.php */
```

Daha sonra oluşturduğunuz header katmanı için <kbd>folders/views/view</kbd> klasörü altında aşağıdaki gibi bir view dosyası yaratın.

header.php

```php
<div><?php echo $header ?></div>
```

##### Dosya Görünümü

```php
- folders
      - welcome
          + view
            Welcome.php
      - views
          - view
              header.php
            Header.php
```

Görüldüğü gibi header katmanına ait bir view dosyası var ve bu view dosyasını yöneten bir kontrolör dosyası mevcut. Şimdi oluşturduğunuz katmanı welcome klasörü welcome kontrolör dosyası içerisinde çalıştırın.

```php
namespace Welcome;

use Obullo\Http\Controller;

class Welcome extends Controller
{
    public function index()
    {
        echo $this->layer->get('views/header');
    }
}

/* Location: .folders/welcome/welcome.php */
```

Son olarak <kbd>http://myproject/welcome</kbd> sayfasını ziyaret edin. Eğer yukarıdaki işlemleri doğru yaptı iseniz welcome sayfası içerisinde bir <kbd>HELLO HEADER LAYER</kbd> çıktısı almanız gerekir.

<a name="caching-layers"></a>

### Bir Katmanı Önbelleklemek

Katman sınıfı get fonksiyonunu ikinci veya üçüncü parametresine bir tamsayı gönderilirse katman çıktısı gönderilen süre kadar cache sınıfı aracılığı ile önbellekte tutulur.

```php
$this->layer->get('views/header', $expiration = 7200);
```

<a name="flushing-cached-layers"></a>

### Bir Katmanı Önbellekten Silmek

Katman sınıfı flush metodu ile önbelleğe alınmış bir katman önbellekten temizlenir.

```php
$this->layer->flush('views/header');
```

<a name="caching-with-params"></a>

### Parametreler ile Önbellekleme

Katman sınıfı get fonksiyonunu ikinci parametresinden array türünde bir parametre gönderilirse gönderilen her farklı parametre serileştirilerek json raw formatına dönüştürülür ve elde edilen çıktıdan tekil bir katman kimliği ( ID ) üretilir. Eğer önbellekleme süresi üçüncü parametreye bir tamsayı olarak girilirse elde edilen katman kimliği ile ( ID ) her seferinde parametrelere duyarlı veriler önbelleğe kaydedilmiş olur.

```php
$this->layer->get('views/header', array('user_id' => 5), $expiration = 7200);
```
Yukarıdaki örnekte kullanıcı id değerinin sağlanması ile her bir kullanıcı için oluşturulmuş katman çıktısı verilen sürede önbelleğe kaydedilir.

<a name="flushing-with-params"></a>

### Parametreler ile Önbelleklenmiş Katmanı Silmek

Bir katmanı önbellekten temizlemek için katman yolu (url) ve varsa katman parametrelerini flush metoduna göndermek yeterlidir.

```php
$this->layer->flush('views/header', array('user_id' => 5));
```

<a name="creating-navigation-bar"></a>

### Katmanlar İle Bir Gezinme Çubuğu Yaratalım

Gezinme çubuğu yada diğer adıyla navigasyon menüsünü kontrol edecek olan kontrolör dosyasını <kbd>folders/views/</kbd> klasörü altına Header.php adıyla oluşturalım.

```php
namespace Views;

use Obullo\Http\Controller;

class Header extends Controller
{
    public function index()
    {
        $selected = ($this->request->getFirst()->getUri()->segment(0)) ? $this->request->getFirst()->getUri()->segment(0) : 'welcome';

        $li = '';
        $navbar = array(
            'welcome'    => 'Welcome',
            'about'   => 'About', 
            'contact' => 'Contact',
            'membership/login'  => 'Login',
            'membership/signup' => 'Signup',
        );
        foreach ($navbar as $key => $value) {
            $active = ($selected == $key) ? ' id="active" ' : '';
            $li.= '<li>'.$this->url->anchor($key, $value, " $active ").'</li>';
        }
        echo $this->view->get(
            'header',
            [
                'li' => $li
            ]
        );
    }
}

/* Location: .folders/views/header.php */
```

Gezinme çubuğuna ait görünüm dosyasını <kbd>folders/views/view/</kbd> klasörü altında header.php adıyla oluşturun.

```php
<div id="header"> 
  <h1 class="logo"><?php echo $this->url->anchor('/welcome', 'Navigation Demo') ?></h1>
  <div id="menu">
    <ul>
      <?php echo $li ?>
    </ul>
  </div>
</div>
```

Şimdi oluşturduğumuz katmanı <kbd>welcome/</kbd> klasöründen çağıralım.


```php
namespace Welcome;

use Obullo\Http\Controller;

class Welcome extends Controller
{
    public function index()
    {
        echo $this->layer->get('views/header');
    }
}

/* Location: .folders/welcome/welcome.php */
```

Eğer yukarıdaki işlemleri doğru yaptıysak gezinme çubuğuna ait çıktıyı almış olmamız gerekir. Gezinme çubuğunun doğru görüntülenebilmesi için ilgili stil nesnelerini ana css dosyasınızda oluşturmayı unutmayın.

<a name="debugger"></a>

### Çıktı Görüntüleyici

Aşağıdaki resimde görüldüğü üzere yarattığınız gezinme çubuğuna ait katmanı <kbd>http://yourproject/debugger</kbd> adresini ziyaret ederek katmanların bileşenler halinde nasıl çıktılandığını takip edebilirsiniz.

Debugger <kbd>http://myproject/debugger</kbd> modülünü çalıştırdığınızda aşağıdaki gibi bir çıktı almanız gerekir.

![Hata Ayıklama](images/debugger.png?raw=true "Hata Ayıklama")

Debugger modülü kurulu değilse kurmak için [Debugger.md](Debugger.md) dökümentasyonunu inceleyebilirsiniz.

<a name="reference"></a>

### Layer Sınıfı Referansı

##### $this->layer->post(string $uri, $data = array | int, expiration = 0);  

Bir katman adresi ve varsa data değerleri ile tekil bir katman kimliği oluşturarak alt katmana bir $_POST isteği gönderir.

* Birinci parametreye istek adresi girilir.
* İkinci parametreye varsa isteğe ait veri girilir yada veri yoksa ve parametreye sayısal bir değer girilirse cache sınıfı kullanılarak katman yanıtı önbelleğe kaydedilir.
* Üçüncü parametreye 0 dan büyük bir değer girildiğinde cache sınıfı kullanılarak katman yanıtı önbelleğe kaydedilir. ( İkinci parametreye veri yada boş bir array gönderilmelidir. )

##### $this->layer->get(string $uri, $data = array | int, expiration = 0);

Bir katman adresi ve varsa data değerleri ile tekil bir katman kimliği oluşturarak alt katmana bir $_GET isteği gönderir.

* Get metodunun alabileceği parametreler post metodu ile aynıdır.

##### $this->layer->flush(string $uri, $data = array);

Önbelleğe alınmış bir katmanı girilen url değeri ve varsa data değerlerinden oluşturulan veriye göre önbellekten siler.