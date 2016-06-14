
## Konsol Arayüzü

Konsol arayüzü komut satırından yürütülen işlemler için yardımcı paketler içerir. Bu arayüz projenizin ana dizinindeki uzantısız olan <kbd>task</kbd> php dosyası üzerinden çalışır.

<ul>
<li>
    <a href="#flow">İşleyiş</a>
    <ul>
        <li><a href="#cli-uri">Uri Sınıfı</a></li>
        <li><a href="#cli-router">Router Sınıfı</a></li>
        <li>
            <a href="#arguments">Argümanlar</a>
            <ul>
                <li><a href="#shortcuts">Kısayollar</a></li>
            </ul>
        </li>
    </ul>
</li>

<li>
    <a href="#console-commands">Konsol Komutları</a>
    <ul>
        <li><a href="#log-command">Log</a></li>
        <li><a href="#help-commands">Help</a></li>
        <li><a href="#app-command">App</a></li>
        <li><a href="#debugger-command">Debugger</a></li>
        <li><a href="#queue-command">Queue</a></li>
        <li><a href="#run-your-commands">Kendi Komutlarınızı Çalıştırmak</a></li>
        <li><a href="#external-run">Konsol Komutlarını Dışarıdan Çalıştırmak</a></li>
        <li><a href="#internal-run">Konsol Komutlarını İçeriden Çalıştırmak</a></li>
    </ul>
</li>

<li><a href="#method-reference">Konsol Referansı</a></li>
</ul>

<a name="flow"></a>

### İşleyiş

Konsol arayüzü komut satırından yürütülen işlemleri <kbd>modules/tasks</kbd> klasörü içerisinde yaratılmış olan kontrolör dosyalarına istek göndererek yürütür. Projenizin ana dizinindeki <kbd>task</kbd> dosyası üzerinden çalışır. Bu dosyaya gelen konsol istekleri <kbd>Obullo/Application/Cli.php</kbd> dosyasını çalıştırarak çağırılan kontrolör dosyalarını çözümler.

Uygulama konsol dosyaları <kbd>modules/tasks</kbd> klasörü içerisinden, eğer dosyalar bu dizinde mevcut değilse <kbd>Obullo\Cli\Task</kbd> klasöründen yüklenirler. Örnek bir konsol komutu.

```php
php task log
```

Task kontrolör dosyaları http kontrolör dosyaları gibi çalışırlar, tek fark dosya üzerindeki <kbd>namespace</kbd> alanının <kbd>Tasks</kbd> olarak değiştirilmesidir.

<a name="cli-uri"></a>

### Uri Sınıfı

Uri sınıfı <kbd>app/modules/tasks</kbd> dizini içindeki komutlara <kbd>--</kbd> sembolü ile gönderilen konsol argümanlarını çözümlemek için kullanılır. Sınıf task komutu ile gönderilen isteklere ait argümanları çözümleyerek <kbd>$this->uri</kbd> nesnesi ile bu argümanların yönetilmesini kolaylaştırır. Sınıfı daha iyi anlamak için aşağıdaki örneği inceleyelim.

```php
namespace Tasks;

use Obullo\Cli\Console;
use Obullo\Cli\Controller;

class Hello extends Controller {
  
    public function index()
    {
        echo Console::logo("Welcome to Hello Controller");
        echo Console::description("This is my first task controller.");

        $planet = $this->uri->argument('planet');

        echo Console::text("Hello ".$planet, 'yellow');
        echo Console::newline(2);
    }
}

/* Location: .app/modules/tasks/Hello.php */
```

Konsoldan hello komutunu <kbd>planet</kbd> argümanı ile aşağıdaki gibi çalıştırdığınızda bir <kbd>Hello World</kbd> çıktısı almanız gerekir.

```php
php task hello --planet=World
```

Argümanları sayısal olarak da alabilirsiniz.

```php
$planet = $this->uri->segment(0);
```

Aşağıdaki gibi standart parametreler de desteklenmektedir.

```php
namespace Tasks;

use Obullo\Cli\Controller;

class Hello extends Controller {

    public function index($planet = '')
    {
        echo Console::logo("Welcome to Hello Controller");
        echo Console::description("This is my first task controller.");

        echo Console::text("Hello ".$planet, 'yellow');
        echo Console::newline(2);
    }
}
```

Yukarıdaki örneği <kbd>planet</kbd> argümanı ile aşağıdaki gibi çalıştırdığınızda yine bir <kbd>Hello World</kbd> çıktısı almanız gerekir.

```php
php task hello World
```

<a name="cli-router"></a>

### Router Sınıfı

Cli router sınıfı http router ile benzer metotlara sahiptir. Router sınıfı bir task sınıfı içerisinde kullanıldığında çalıştırılan sınıfın adı, metot adı, isim alanı ve host bilgileri gibi bilgileri verir.


```php
namespace Tasks;

use Obullo\Cli\Controller;

class Hello extends Controller {

    public function index()
    {
        echo "Task Controller : " . $this->router->getClass()."\n";
        echo "Method : ". $this->router->getMethod()."\n";
        echo "Namespace : ". $this->router->getNamespace()."\n";
        echo "Uri : ". $this->router->getPath()."\n";
        echo "Host: ". $this->router->getHost()."\n";
    }
}
```

Komutu çalıştırın

```php
php task hello index --host=test
```

Çalıştırdığınızda aşağıdaki gibi bir çıktı almanız gerekir.

```php
Task Controller : Hello
Method : index
Namespace : \Tasks\Hello
Uri : hello/index/--host=test
Host: test
```

<a name="arguments"></a>

### Argümanlar

Argümanlar method çözümlemesinin hemen ardından gönderilirler. Aşağıdaki örnekte bir kuyruğu dinlemek için kullanılan konsol komutu gösteriliyor.

```php
php task queue listen --worker=Workers@Logger --job=logger.1 --memory=128 --sleep=3 --output=1
```

Kısayolları da kullanabilirsiniz.

```php
php task queue listen --w=Workers@Logger --j=logger.1 --m=128 --s=3 --o=1
```

<a name="shortcuts"></a>

#### Kısayollar

<table>
<thead>
<tr>
<th>Kısayol</th>
<th>Argüman</th>
</thead>
<tbody>
<tr>
<td>--w</td>
<td>--worker</td>
</tr>
<tr>
<td>--j</td>
<td>--job</td>
</tr>
<tr>
<td>--d</td>
<td>--delay</td>
</tr>
<tr>
<td>--m</td>
<td>--memory</td>
</tr>
<tr>
<td>--t</td>
<td>--timeout</td>
</tr>
<tr>
<td>--o</td>
<td>--output</td>
</tr>
<tr>
<td>--s</td>
<td>--sleep</td>
</tr>
<tr>
<td>--a</td>
<td>--attempt</td>
</tr>
<tr>
<td>--v</td>
<td>--var</td>
</tr>
<tr>
<td>--h</td>
<td>--host</td>
</tr>
<tr>
<td>--e</td>
<td>--env</td>
</tr>
</tbody>
</table>


<a name="console-commands"></a>

### Konsol Komutları

<a name="log-command"></a>

#### Log

Uygulamanızın log tutma özelliği <kbd>app/$env/config.php</kbd> dosyasında açık ise, uygulamayı gezdiğinizde konsol dan uygulama loglarını eş zamanlı takip edebilirsiniz. Komutun çalışabilmesi log servis sağlayıcınızda log yazıcınızın <kbd>File</kbd> handler olarak tanımlı olması gerekir.

```php
php task log
```

Yukarıdaki komut <kbd>modules/tasks/Log</kbd> sınıfını çalıştırır ve <kbd>.resources/data/logs/http.log</kbd> dosyasını okuyarak uygulamaya ait http isteklerinin loglarını ekrana döker.


```php
php task log ajax
```

Yukarıdaki komut ise <kbd>.resources/data/logs/ajax.log</kbd> dosyasını okuyarak uygulamaya ait ajax isteklerinin loglarını ekrana döker.

```php
php task log clear
```

Clear metodunu çalıştırdığınızda komut <kbd>.resources/data/logs</kbd> dizinindeki tüm log kayıtlarını siler.

<a name="help-commands"></a>

#### Help

Help metodu standart olarak tüm task kontrolör dosyalarında bulunur. Takip eden örnekte log komutuna ait yardım çıktısını görüyorsunuz.

```php
php task log help
```

<a name="app-command"></a>

#### App

App komutu bakıma alma katmanı işlevlerini yönetir. Eğer <kbd>app/$env/maintenance.php</kbd> dosyanızda tanımlı domain adresleriniz varsa uygulamanızın konsoldan bakıma alma işlevlerini yürütebilirsiniz. 

Uygulamanızı bakıma almak için aşağıdaki komutu çalıştırın.

```php
php task app down root
```

Uygulamanızı bakımdan çıkarmak için aşağıdaki komutu çalıştırın.

```php
php task app up root
```

Maintenance http katmanı hakkında bilgi için [Middleware-Maintenance.md](https://github.com/obullo/http-middlewares) dosyasını inceleyebilirsiniz.

<a name="debugger-command"></a>

#### Debugger

Debugger modülü uygulamanın geliştirilmesi esnasında uygulama isteklerinden sonra oluşan ortam bileşenleri ve arka plan log verilerini görselleştirir.

Debugger modülü için örnek bir kurulum.

```php
php task module add debugger
```

Debug sunucusunu çalıştırmak için aşağıdaki komutu kullanın.

```php
php task debugger
```

Debugger konsolonu görüntülemek için <kbd>/debugger</kbd> sayfasını ziyaret edin

```php
http://myproject/debugger
```

Debugger modülü hakkında daha geniş bilgi için Debugger paketi [Debbuger.md](Debugger.md) belgesine gözatın.

<a name="queue-command"></a>

#### Queue

Kuyruğa atılan işleri <kbd>Obullo\Task\QueueController</kbd> sınıfına istek göndererek tüketir.

Örnek bir kuyruk dinleme komutu

```php
php task queue listen --worker=Logger --job=Server1.Logger --memory=128 --sleep=3--tries=0 --output=1
```

Queue komutu hakkında daha geniş bilgi için [Queue.md](Queue.md) dosyasına gözatın.

<a name="run-your-commands"></a>

#### Kendi Komutlarınızı Çalıştırmak

Kendinize ait task dosyalarını <kbd>app/modules/tasks</kbd> klasörū içerisinde yaratabilirsiniz. Bunun bir kontrolör dosyası yaratın ve namespace bölümünü <kbd>Tasks</kbd> olarak değiştirin.

```php
namespace Tasks;

use Obullo\Cli\Controller;

class Hello extends Controller {
  
    public function index()
    {
        echo Console::logo("Welcome to Hello Controller");
        echo Console::description("This is my first task controller.");
    }
}

/* Location: .modules/tasks/Hello.php */
```

Şimdi oluşturduğunuz komutu aşağıdaki gibi çalıştırın.

```php
php task hello
```

<a name="external-run"></a>

#### Konsol Komutlarını Dışarıdan Çalıştırmak

Eğer bir konsol komutu crontab gibi bir uygulama üzerinden dışarıdan çalıştırılmak isteniyorsa aşağıdaki task dosyasının tam dosya yolu girilmelir.

```php
php /var/www/framework/task help
```

<a name="internal-run"></a>

#### Konsol Komutlarını İçeriden Çalıştırmak

Komutları uygulama içerisinden çalıştırmak için task sınıfı kullanılır.

```php
$this->task->run('welcome/index/arg/arg');
```

Asenkron olmayan bir komutun çıktısını almak için ikinci parametreden <kbd>true</kbd> gönderilmelidir.

```php
echo $this->task->run('welcome/index/arg/arg', true);
```

<a name="method-reference"></a>

#### Uri Sınıfı Referansı

##### $this->uri->argument(string $key, string $defalt = '');

Girilen isme göre konsol komutundan gönderilen argümanın değerine geri döner.

##### $this->uri->getArguments();

Çözümlenen argüman listesine "--key=value" olarak bir dizi içerisinde geri döner.

##### $this->uri->segment(integer $n, string $default = '');

Argüman değerini anahtarlar yerine sayılarla alır ve elde edilen argüman değerine geri döner.

##### $this->uri->getSegments();

Çözümlenen argümanların listesine sadece "value" olarak bir dizi içerisinde geri döner.

##### $this->uri->getPath();

Çözümlenen tüm konsol komutuna argümanları ile birlikte string formatında geri döner.

##### $this->uri->getShortcuts();

Argümanlar için tanımlı olan tüm kısayollara bir dizi içerisinde geri döner.

##### $this->uri->clear();

Sınıf içerisindeki tüm değişkenlerin değerlerini başa döndürür.


#### Router Sınıfı Referansı

##### $this->router->getClass();

Konsoldan gönderilan ilk parametre değerini yani sınıf adını verir.

##### $this->router->getMethod();

Konsoldan gönderilan ilk parametre değerini yani metot adını verir.

##### $this->router->getPath();

Tüm konsol girdisine konsol parametreleri ile birlikte geri döner.

##### $this->router->getHost();

Eğer parametre olarak bir host değeri gönderilmişse bu değere aksi durumda null değerine geri döner.

##### $this->router->clear();

Sınıf içerisindeki tüm değişkenlerin değerlerini başa döndürür.