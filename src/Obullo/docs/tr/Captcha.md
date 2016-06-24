
## Captcha Sınıfı

CAPTCHA "Carnegie Mellon School of Computer Science" tarafından geliştirilen bir projedir. Projenin amacı bilgisayar ile insanların davranışlarının ayırt edilmesidir ve daha çok bu ayrımı yapmanın en zor olduğu web ortamında kullanılmaktadır. CAPTCHA projesinin bazı uygulamalarına çoğu web sayfalarında rastlamak mümkündür. Üyelik formlarında rastgele resim gösterilerek formu dolduran kişiden bu resmin üzerinde yazan sözcüğü girmesi istenir.

Buradaki basit mantık o resimde insan tarafından okunabilecek ancak bilgisayar programları tarafından okunması zor olan bir sözcük oluşturmaktır. Eğer forma girilen sözcük resimdeki ile aynı değilse ya formu dolduran kişi yanlış yapmıştır ya da formu dolduran bir programdır denebilir.

<ul>
<li>
    <a href="#setup">Kurulum</a>
    <ul>
        <li><a href="#service-provider">Servis Sağlayıcısı</a></li>
    </ul>
</li>

<li>
    <a href="#running">Çalıştırma</a>
    <ul>
        <li><a href="#module">Captcha Modülü</a></li>
        <li><a href="#loading-service">Servisi Yüklemek</a></li>
    </ul>
</li>

<li>
    <a href="#options">Seçenekler</a>
    <ul>
        <li><a href="#choosing-background">Arkaplan Seçimi</a></li>
        <li><a href="#font-options">Font Seçenekleri</a></li>
        <li><a href="#color-options">Renk Seçenekleri</a></li>
        <li><a href="#foreground-color-options">Arkaplan Renk Seçenekleri</a></li>
        <li><a href="#image-height">Imaj Yüksekliği</a></li>
        <li><a href="#font-width">Font Genişliği</a></li>
        <li><a href="#font-wave">Font Eğimi</a></li>
        <li><a href="#char-pool">Karakter Havuzu</a></li>
        <li><a href="#char-width">Karakter Genişliği</a></li>
    </ul>
</li>
  
<li>
    <a href="#create-operations">Captcha İşlemleri</a>
    <ul>
        <li>
            <a href="#creating-captcha">Captcha Oluşturma</a>
            <ul>
                <li><a href="#create">$this->captcha->create()</a></li>
                <li><a href="#printJs">$this->captcha->printJs()</a></li>
                <li><a href="#printHtml">$this->captcha->printHtml()</a></li>
                <li><a href="#printRefreshButton">$this->captcha->printRefreshButton()</a></li>
            </ul>
        </li>
        <li><a href="#validation">Doğrulama</a></li>
        <li><a href="#validation-with-validator">Validator Sınıfı İle Doğrulama</a></li>
        <li><a href="#results-table">Hata ve Sonuç Kodları Tablosu</a></li>
    </ul>
</li>

<li><a href="#method-reference">Captcha Sınıfı Referansı</a></li>
</ul>

<a name="setup"></a>

### Kurulum

Captcha paketi konfigürasyon dosyası <kbd>providers/</kbd> klasöründen konfigüre edilir.

<a name="removing-module"></a>

#### Http Modülünü Kurmak

Aşağıdaki kaynaktan <kbd>Create.php</kbd> dosyasını uygulamanızın <kbd>app/modules/Captcha/</kbd> dizinine kopyalayın.

```php
http://github.com/obullo/http-modules/
```

<a name="service-provider"></a>

#### Servis Sağlayıcısı

<kbd>app/providers.php</kbd> dosyasında servis sağlayıcısının tanımlı olduğundan emin olun.

```php
$container->addServiceProvider('Obullo\Container\ServiceProvider\Captcha');
```

<a name="running"></a>

### Çalıştırma

Captcha modülünü kurduktan sonra Captcha arayüzüne bağlanmak için captcha servisi kullanılır.

<a name="loading-service"></a>

#### Servisi Yüklemek

Captcha servisi aracılığı ile captcha metotlarına aşağıdaki gibi erişilebilir.

```php
$this->container->get('captcha')->method();
```

<a name="module"></a>

#### Captcha Modülü

Captcha modülü ile ilgili kapsamlı örnekleri incelemek için tarayıcınızdan aşağıdaki adresleri ziyaret edin.

```html
http://myproject/examples/captcha
http://myproject/examples/captcha/ajax
```

<a name="options"></a>

### Seçenekler

<a name="choosing-background"></a>

#### Arkaplan Seçimi

Imaj sürücüsü arka plan için iki tür seçeneğe sahiptir: <kbd>secure</kbd> ve <kbd>none</kbd>. Güvenli arkaplan seçildiğinde imajlar kompleks bir arkaplan seçilerek oluşturulur. none seçeneğinde ise captcha imajı arkaplan kullanılmadan oluşturulur.

```php
$this->captcha->setBackground('none');
```
<a name="font-options"></a>

#### Font Seçenekleri

```php
$this->captcha->setFont(['Arial', 'Tahoma', 'Verdana']);
```

Fontlarınız <kbd>.resources/fonts</kbd> dizininden yüklenirler. Bu dizin konfigürasyon dosyasından değiştirilebilir. Özel fontlar eklemek için font dizinine fontlarınızı ekleyip setFont() metodunu çalıştırmanız yeterli olur.

```php
$this->captcha->setFont([
                  'AlphaSmoke',
                  'Almontew'
				 ]);
```

<a name="color-options"></a>

#### Renk Seçenekleri

Varsayılan renkleri konfigürasyon dosyasından ayarlayabilirsiniz. Mevcut renkler aşağıdaki gibidir.

<kbd>red</kbd> - <kbd>blue</kbd> - <kbd>green</kbd> - <kbd>black</kbd> - <kbd>yellow</kbd> 

```php
$this->captcha->setColor(['red','black']);
```
<a name="foreground-color-options"></a>

#### Arkaplan Desen Renkleri

Varsayılan renkleri konfigürasyon dosyasından ayarlayabilirsiniz. Birden fazla renk seçildiğinde captcha rastgele bir renk seçilerek yaratılır. Mevcut renkler aşağıdaki gibidir.

<kbd>red</kbd> - <kbd>blue</kbd> - <kbd>green</kbd> - <kbd>black</kbd> - <kbd>yellow</kbd> 

```php
$this->captcha->setNoiseColor(['black','cyan']);
```
<a name="image-height"></a>

#### Imaj Yüksekliği

Eğer imaj yüksekliği bir kez ayarlanır ise imaj genişliği, karakter ve font genişliği değerleri otomatik olarak hesaplanır. Varsayılan değer <kbd>40</kbd> px dir.

```php
$this->captcha->setHeight(40);
```

<a name="font-width"></a>

#### Font Genişliği

Font size değerini atar, varsayılan değer <kbd>20</kbd> px dir.

```php
$this->captcha->setFontSize(20);
```

<a name="font-wave"></a>

#### Font Eğimi

Font eğimi özelliği etkin kılar.

```php
$this->captcha->setWave(false);
```

<a name="char-pool"></a>

#### Karakter Havuzu

Karakter havuzu captcha imajında kullanılacak karakterleri belirler, aşağıdaki listedeki değerler örnek olarak verilmiştir. Değerler konfigürasyon dosyanızdan değiştirilebilir.

```php
$this->captcha->setPool('numbers');
```

<table>
<thead>
<tr>
<th>Type</th>
<th>Description</th>
</tr>
</thead>
<tbody>
<tr>
<td>random</td>
<td>23456789ABCDEFGHJKLMNPRSTUVWXYZ</td>
</tr>
<tr>
<td>numbers</td>
<td>23456789</td>
</tr>
<tr>
<td>alpha</td>
<td>ABCDEFGHJKLMNPRSTUVWXYZ</td>
</tr>
<tr>
</tbody>
</table>

Daha fazla okunabilirlik için <kbd>"1 I 0 O"</kbd> karakterlerini kullanmamanız tavsiye edilir. Varsayılan değer <kbd>random</kbd> değeridir.

<a name="char-width"></a>

#### Karakter Genişliği

```php
$this->captcha->setChar(10);
```

<a name="create-operations"></a>

### Captcha İşlemleri

Captcha işlemleri captcha html ve javascript kodunu oluşturma, yenileme tuşu oluşturma ve doğrulama işlemlerini kapsar.

<a name="creating-captcha"></a>

#### Captcha Oluşturma

Captcha oluşturma metotları captcha elemetlerini oluşturur.

<a name="create"></a>

##### $this->captcha->create();

Captcha modülü eklendiğinde captcha modülü altında <kbd>/modules/captcha/Create.php</kbd> adında aşağıdaki gibi bir imaj controller yaratılır.

```php
namespace Captcha;

use Obullo\Http\Controller;

class Create extends Controller
{
    public function index()
    {
        $this->response->newInstance(
            'php://memory',
            200,
            [
                'Cache-Control' => 'no-cache, must-revalidate',
                'Expires' => 'Mon, 26 Jul 1997 05:00:00 GMT',
                'Content-Type' => 'image/png',
            ]
        );
        $this->captcha->create();
    }
}
```

<a name="printJs"></a>

##### $this->captcha->printJs();

Sayfaya captcha eklemek için aşağıdaki gibi <kbd>head</kbd> tagları arasına javascript çıktısını ekrana dökmeniz gerekir.

```php
<!DOCTYPE html>
<html>
<head>
    <title></title>
    <?php echo $this->captcha->printJs() ?>
</head>
<body>

</body>
</html>
```

<a name="printHtml"></a>

##### $this->captcha->printHtml();

Formlarınıza captcha eklemek için aşağıdaki gibi captcha çıktısını ekrana dökmeniz gerekir.

```php
<form method="POST" action="/captcha/examples/form">
    <?php echo $this->captcha->printHtml() ?>
    <input type="submit" value="Send" name="sendForm">
</form>
```

Bu fonksiyon aşağıdaki gibi bir input alanı

```php
<input name="captcha_answer" placeholder="Security Code" type="text">
```

ve image elementi oluşturur.

```php
<img src="/captcha/create/index/" id="captcha_image" class="">
```


<a name="printRefreshButton"></a>

##### $this->captcha->printRefreshButton();

Eğer refresh button özelliğinin etkin olmasını istiyorsanız. Form taglarınız içierisinde bu fonksiyonu kullanın.

```php
<form method="POST" action="/captcha/examples/form">
    <?php echo $this->captcha->printHtml() ?>
    <?php echo $this->captcha->printRefreshButton() ?>
    <input type="submit" value="Send" name="sendForm">
</form>
```

<a name="validation"></a>

#### Doğrulama 

Captcha doğrulama için bütün sürücüler için ortak olarak kullanılan <kbd>CaptchaResult</kbd> sınıfı kullanılır. Bir captcha kodunun doğru olup olmadığı aşağıdaki gibi <kbd>isValid()</kbd> komutu ile anlaşılır.

```php
if ($this->captcha->result()->isValid()) {

	// Doğrulama başarılı
}
```

Bir doğrulamadan dönen mesajlar aşağıdaki gibi alınır.

```php
print_r($this->captcha->result()->getMessages());
```

Bir doğrulamaya ait hata kodu alma örneği


```php
echo $this->captcha->result()->getCode();  // -2  ( Invalid Code )
```

<a name="validation-with-validator"></a>

#### Validator Sınıfı İle Doğrulama 

Eğer varolan formunuz içerisinde <kbd>validator</kbd> sınıfını kullanıyorsanız doğrulama için herhangi bir kod yazmanıza gerek kalmaz ve <kbd>captcha</kbd> doğrulama kuralını kural olarak eklemeniz yeterli olur.

```php
$this->validator->setRules('captcha_answer', 'Captcha', 'required|captcha');
```

<a name="results-table"></a>

#### Hata ve Sonuç Kodları Tablosu

<table>
    <thead>
        <tr>
            <th>Kod</th>    
            <th>Sabit</th>    
            <th>Açıklama</th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td>0</td>
            <td>CaptchaResult::FAILURE</td>
            <td>Genel başarısız doğrulama.</td>
        </tr>
        <tr>
            <td>1</td>
            <td>CaptchaResult::SUCCESS</td>
            <td>Doğrulama başarılıdır.</td>
        </tr>
        <tr>
            <td>-1</td>
            <td>CaptchaResult::FAILURE_EXPIRED</td>
            <td>Girilen captcha kodunun zaman aşımına uğradığını gösterir.</td>
        </tr>
        <tr>
            <td>-2</td>
            <td>CaptchaResult::FAILURE_INVALID_CODE</td>
            <td>Girilen captcha kodunun yanlış olduğunu gösterir.</td>
        </tr>
        <tr>
            <td>-3</td>
            <td>CaptchaResult::FAILURE_CAPTCHA_NOT_FOUND</td>
            <td>Girilen captcha kodunun veriler içerisinde hiç bulunamadığını gösterir.</td>
        </tr>
    </tbody>
</table>

<a name="method-reference"></a>

#### Captcha Sınıfı Referansı

##### $this->captcha->setBackground($bg = 'none');

Captcha arkaplanını seçer <kbd>secure</kbd> veya <kbd>none</kbd> seçilebilir. none seçeneği seçildiğinde arkaplan boşaltılır.

##### $this->captcha->setNoiseColor(mixed $color = ['red']);

Arkaplan desen renklerini belirler.

##### $this->captcha->setColor(mixed $color = ['black']);

Imaj yazı rengini belirler.

##### $this->captcha->setTrueColor(boolean $bool = false);

Image true color seçeneğini etkin kılar. Mevcut renklerin bir siyah versiyonunu yaratır. Bknz. Php <a href="http://php.net/manual/en/function.imagecreatetruecolor.php" target="_blank">imagecreatetruecolor</a>

##### $this->captcha->setFontSize(integer $size);

Font genişliği belirler.

##### $this->captcha->setHeight(integer $height);

Eğer imaj <kbd>yüksekliği</kbd> bir kez ayarlanır ise imaj genişliği, karakter ve font genişliği değerleri otomatik olarak hesaplanır.

##### $this->captcha->setPool(string $pool);

Karakter havuzunu belirler. Değerler: <kbd>numbers</kbd>, <kbd>random</kbd> ve <kbd>alpha</kbd> dır.

##### $this->captcha->setChar(integer $char);

Imaj üzerindeki karakterlerin maximum sayısını belirler.

##### $this->captcha->setWave(true or false);

Yazı eğimi özelliğini açar veya kapatır.

##### $this->captcha->setFont(mixed ['FontName', ..]);

Mevcut fontlardan font yada fontlar seçmenize olanak tanır.

##### $this->captcha->excludeFont(mixed ['FontName', ..]);

Mevcut fontlardan font yada fontlar çıkarmanızı sağlar.

##### $this->captcha->getInputName();

Captcha input alanı adını verir.

##### $this->captcha->getImageUrl();

Captcha http image adresini verir.

##### $this->captcha->getImageId();

Rastgele üretilen captcha imajı id sini verir.

##### $this->captcha->getCode();

Geçerli captcha koduna geri döner.

##### $this->captcha->create();

Captcha imajını yaratır. Http başlıkları ile birlikte kullanılması gerekir.

##### $this->captcha->result(string $code = null);

Parametreden gönderilen captcha kodunu doğrulama işlemini başlatarak <kbd>CaptchaResult</kbd> nesnesine döner. Eğer bir parametre girilmezse otomatik olarak post alanı değerini alınır.

##### $this->captcha->printJs();

Captcha refresh javascript fonksiyonunu sayfaya yazdırır. Html head tagları arasında kullanılması önerilir.

##### $this->captcha->printHtml();

Bir html form için captcha <kbd>input</kbd> alanı ve <kbd>img</kbd> tagını üretir.

##### $this->captcha->printRefreshButton();

Captcha html refresh button tagını yaratır.