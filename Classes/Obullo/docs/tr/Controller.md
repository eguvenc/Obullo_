
## Kontrolör Sınıfı

Kontrolör sınıfı uygulamanın kalbidir ve uygulamaya gelen HTTP isteklerinin nasıl yürütüleceğini kontrol eder.

<ul>
    <li><a href="#controller">Kontrolör Nedir ?</a></li>
    <li><a href="#folders">Klasörler</a></li>
    <li><a href="#primary-folders">Ata Klasör</a></li>
    <li><a href="#proxy-way">Proxy Yöntemi Nedir ?</a></li>
    <li><a href="#arguments">Argümanlar</a></li>
    <li><a href="#middlewares">Http Katmanları</a></li>
    <li><a href="#welcome-page">İlk Açılış Sayfası</a></li>
    <li><a href="#annotations">Anotasyonlar</a></li>
    <li><a href="#reserved-methods">Rezerve Edilmiş Metotlar</a></li>
</ul>

Kontrolör dosyaları http route çözümlemesinden sonra <kbd>folders/</kbd> klasöründen çağrılarak çalıştırılır.

Bir http GET isteği çözümlemesi

```php
$router->post('product/list', 'shop/product/list'); 
```

Bir http POST isteği çözümlemesi

```php
$router->post('product/post', 'shop/product/post'); 
```

Route çözümlemeleri ilgili daha fazla bilgi için [Router.md](Router.md) dosyasını gözden geçirebilirsiniz.

<a name="controller"></a>

### Kontrolör Nedir ?

Kontrolör dosyaları uygulamada http adres satırından çağrıldığı ismi ile bağlantılı olarak çözümlenebilen php sınıflarıdır. Kontrolör dosyaları uygulamada <kbd>app/folders/</kbd> klasörü altında çalışırlar.

Örnek bir http isteği.

```php
http://example.com/index.php/welcome
```

ve bu isteğe ait kontrolör dosyası.

```php
use Obullo\Http\Controller;

class Welcome extends Controller
{
    public function index()
    {
        $this->view->load('views::welcome');
    }
}
```

<kbd>HelloWorld</kbd> gibi birden fazla kelime içeren bir kontrolör varsa sadece <kbd>ikinci</kbd> kelime büyük yazılmalı,

```php
example.com/index.php/examples/helloWorld
```

yada tüm kelimeler büyük yazılmalıdır.


```php
example.com/index.php/examples/HelloWorld
```

Aksi durumda kontrolör <kbd>helloworld</kbd> olarak çağrılırsa sayfaya ulaşılamaz.

<a name="folders"></a>

### Klasörler

Klasör içerisine konulan dosyalar <kbd>app/folders/klasöradı/</kbd> gibi bir dizin içinde ve php <kbd>namespace</kbd> ler ile çalışırlar. Klasörler kullanarak çalışmak uygulama esnekliğini arttırır ve mantıksal uygulamalar yaratmanızı sağlar. 

```php
example.com/index.php/examples/
```

Yukarıdaki adres satırı <kbd>examples</kbd> adlı dizin altında bulunan <kbd>Examples.php</kbd> isimli kontrolör dosyasını çağırır.

```php
namespace Examples;

use Obullo\Http\Controller;

class Examples extends Controller
{
    public function index()
    {
        $this->view->load('examples');
    }
}
```

Dizin ve kontrolör adı aynı ise uygulama bu kontrolör dosyasını <kbd>index</kbd> kontrolör olarak çözümler.

<a name="primary-folders"></a>

### Ata Klasör

Ata klasör, bir alt klasörü olan klasöre verilen addır.Örnek verirsek, uygulamanızda <kbd>app/folders/examples/captcha/</kbd> adlı bir dizin ve altında <kbd>Ajax.php</kbd> adlı bir kontrolörümüzün olduğunu varsayalım.

Bu dosyayı çözümlemek için ziyaret edeceğimiz adres aşağıdaki gibi olur.

```php
http://example.com/examples/captcha/ajax
```

Bu çözümlemede en dıştaki klasör <kbd>ata</kbd>, sonraki klasör ise alt klasördür.

```php
namespace Examples\Captcha;

use Obullo\Http\Controller;

class Ajax extends Controller
{
    public function index()
    {
        echo $this->uri->segment(0);  // examples
        echo $this->uri->segment(1);  // captcha

        echo $this->router->getAncestor();  // examples
        echo $this->router->getFolder();    // captcha
    }
}
```

Bir birincil klasör altına en fazla <kbd>iki</kbd> alt klasör açılabilir.

```php
http://example.com/tests/authentication/storage/redis
```

Aşağıdaki iki alt klasörü olan <kbd>tests</kbd> adlı ata klasör örneği gösteriliyor.

```php
namespace Tests\Authentication\Storage;

use Obullo\Http\TestController;

class Redis extends TestController
{
    public function index()
    {
        echo $this->uri->segment(0);  // tests
        echo $this->uri->segment(1);  // authentication

        echo $this->router->getAncestor();  // tests
        echo $this->router->getFolder();         // authentication/storage
    }
}
```

<a name="proxy-way"></a>

### Proxy Yöntemi Nedir ?

Proxy yöntemi <kbd>$container->get()</kbd> yazımını kolaylaştırmak tasarlanmıştır ve aşağıdaki gibi <kbd>$this</kbd> yöntemi  ile kullanılır.

```php
namespace Welcome;

class Welcome extends \Controller
{
    public function index()
    {
        echo $this->url->anchor("http://example.com/", "Hello World");
    }

}
```

<kbd>$this</kbd> ile çağırılan sınıflar,

```php
$this->class
```

<kbd>Obullo\Http\Controller</kbd> sınıfı içerisindeki __get() metodu aracılığı ile konteyner içerisinden çağırılmış olurlar.

```php
public function __get($class)
{
    return $this->container->get($class);
}
```

Çağırılan kütüphaneler <kbd>app/providers.php</kbd> dosyası aracılığı ile konteyner içerisine tanımlanmış <kbd>servis</kbd> ler olmalıdırlar.

<a name="arguments"></a>

### Argümanlar

Eğer adres satırında bir metot dan sonra gelen segmentler birden fazla ise bu segmentler metot argümanları olarak çözümlenir. Örneğin aşağıdaki gibi bir url adresimizin olduğunu varsayalım:

```php
example.com/shop/products/computer/index/desktop/123
```

<kbd>shop/products</kbd> klasörü altına <kbd>Computer.php</kbd> adlı bir sınıf oluşturun.

```php
-  app
-  folders
    - shop
        - products
            Computer.php
```

Yukarıdaki url adresi tarayıcıda çalıştırıldığında URI sınıfı tarafından segmentler aşağıdaki gibi çözümlenirler.

* shop (0)
* products (1)
* computer (2)
* index (3)
* desktop (4)
* 123 (5)

```php
namespace Products;

use Obullo\Http\Controller;

class Computer extends Controller
{
    public function index($type, $id)
    {
        echo $type;  // desktop
        echo $id;    // 123
        
        echo $this->uri->segment(0);    // shop
        echo $this->uri->segment(1);    // products
        echo $this->uri->segment(2);    // computer
        echo $this->uri->segment(3);    // index
        echo $this->uri->segment(4);    // desktop
        echo $this->uri->segment(5);    // 123

        echo $this->router->getFolder();  // products
        echo $this->router->getAncestor();  // shop
    }
}

/* Location: .shop/products/computer.php */
```

<a name="middlewares"></a>

### Http Katmanları

Http katmanları http çözümlemesinden önce <kbd>$request</kbd> yada <kbd>$response</kbd> nesnelerini etkilemek için kullanılırlar. Katmanlar <kbd>app/classes/Http/Middlewares</kbd> klasörü içerisinde yeralan php sınıflarıdır. Bir katman route yapısında tutturulabilir yada uygulamada evrensel olarak çalışabilir. Daha fazla bilgi için [App-Middlewares.md](App-Middlewares.md) dökümentasyonunu inceleyebilirsiniz.

<a name="welcome-page"></a>

### İlk Açılış Sayfası

Uygulamanıza gelen bir adrese aşağıdaki gibi herhangi bir segment girilmezse,

```php
http://example.com/
```

router sınıfı ilk açılış sayfası için varsayılan bir kontrolör tanımlamasına ihtiyaç duyar. Bu nedenle <kbd>app/routes.php</kbd> dosyanızı açıp varsayılan kontrolör adresinizi <kbd>defaultPage</kbd> anahtarından konfigüre etmeniz gerekir.

```php
$router->defaultPage('welcome');
```

<a name="annotations"></a>

### Anotasyonlar

Bir anotasyon aslında bir metadata yı (örneğin yorum,  açıklama, tanıtım biçimini) yazıya, resime veya diğer veri türlerine tutturmaktır. Anotasyonlar genellikle orjinal bir veriyi yada işlemi refere ederler. Şu anki sürümde anotasyonlar sadece <kbd>Http Katman</kbd> işlemleri için kullanılıyor.

Anotasyonları aktif etmek için <kbd>config.php</kbd>  dosyasını açın ve <kbd>annotations</kbd> anahtarının değerini <kbd>true</kbd> olarak güncelleyin.

```php
'extra' => [
    'annotations' => true,
],
```

Anotasyonlar hakkında daha fazla bilgiye [Annotations.md](Annotations.md) dökümentasyonundan ulaşabilirsiniz.

<a name="reserved-methods"></a>

### Rezerve Edilmiş Metotlar

Kontrolör sınıfı içerisine tanımlanmış yada tanımlanması olası bazı metotlar çekirdek sınıflar tarafından sık sık kullanılır. Bu metotlara uygulamanın dışından erişmeye çalıştığınızda 404 hataları ile karşılaşırsınız.

<table>
    <thead>
        <tr>
            <th>Metot</th>
            <th>Açıklama</th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td><kbd>__get()</kbd></td>
            <td>Container nesnesinden servislere ulaşmak için kullanılır.</td>
        </tr>
        <tr>
            <td><kbd>__set()</kbd></td>
            <td>Controller sınıfı içerisine nesne değerleri enjekte etmek için kullanılır. </td>
        </tr>
    </tbody>
</table>
