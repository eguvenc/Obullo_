
## Çıktı Görüntüleyici (Debugger)

Debugger paketi uygulamanın geliştirilmesi esnasında uygulama isteklerinden sonra oluşan ortam bileşenleri ve arka plan log verilerini görselleştirir. Debugger modülü aktifken uygulama ziyaret edilir ve uygulama çalışırken bir başka yeni pencerede <kbd>http://yourproject/debugger</kbd> adresine girilerek bu sayfada http, konsol, ajax <kbd>log</kbd> verileri ve ortam bilgileri ( $_POST, $_SERVER, $_GET, $_SESSION, $_COOKIE, http başlıkları, http gövdesi ) websocket bağlantısı ile dinamik olarak görüntülenir.

Logların okunabilmesi için <kbd>File</kbd> sürücüsünün logger servisinizde tanımlı olması gerekir.

```php
'methods' => [
    ['registerHandler' => [5, 'file']],
]
```

### Middleware

Aşağıdaki kaynaktan <kbd>Debugger.php</kbd> dosyasını uygulamanızın <kbd>app/classes/Http/Middlewares/</kbd> klasörüne kopyalayın.

```php
http://github.com/obullo/http-middlewares/
```

### Linux / Mac

#### Http Modülü

Aşağıdaki kaynaktan <kbd>Debugger.php</kbd> dosyasını uygulamanızın <kbd>app/modules/Debugger/</kbd> dizinine kopyalayın.

```php
http://github.com/obullo/http-modules/
```

#### Çalıştırma

Debugger sunucusunu konsoldan çalıştırın. 

```php
php task debugger
```

Şimdi tarayıcınızdan yeni bir pencere açın ve debugger sayfasını ziyaret edin. Debugger sayfası ziyaret edildiğinde tarayıcınız istemci olarak websocket sunucusuna bağlanır. 

```php
http://mylocalproject/debugger
```

Eğer debugger kurulumu doğru gerçekleşti ise aşağıdaki gibi bir sayfa ile karşılaşmanız gerekir.

![Debugger](images/debugger.png?raw=true "Debugger Ekran Görüntüsü")

Websocket bağlantısı bazı tarayıcılarda kendiliğinden kopabilir panel üzerindeki ![Closed](images/socket-closed.png?raw=true "Socket Closed") simgesi debugger sunucusuna ait bağlantının koptuğunu ![Open](images/socket-open.png?raw=true "Socket Open") simgesi ise bağlantının aktif olduğunu gösterir. Eğer bağlantı koparsa verileri sayfa yenilemesi olmadan takip edemezsiniz. Böyle bir durumda debugger sunucunuzu ve tarayıcınızı yeniden başlatmayı deneyin.

### Windows

#### Http Modülü


#### Çalıştırma

Debugger sunucusunu konsoldan çalıştırın. 

```php
C:\xampp\php\php.exe -f "C:\xampp\htdocs\myproject\task" debugger
```

* Bu uzun komutu sürekli yazamayacağınızdan onun için masaüstüne bir <kbd>kısayol</kbd> oluşturup onu debugger adıyla kaydedin.

Şimdi tarayıcınızdan yeni bir pencere açın ve debugger sayfasını ziyaret edin. Debugger sayfası ziyaret edildiğinde tarayıcınız istemci olarak websocket sunucusuna bağlanır. 

```php
http://mylocalproject/debugger
```

Eğer debugger kurulumu doğru gerçekleşti ise aşağıdaki gibi bir sayfa ile karşılaşmanız gerekir.

![Debugger](images/debugger.png?raw=true "Debugger Ekran Görüntüsü")

Websocket bağlantısı bazı tarayıcılarda kendiliğinden kopabilir panel üzerindeki ![Closed](images/socket-closed.png?raw=true "Socket Closed") simgesi debugger sunucusuna ait bağlantının koptuğunu ![Open](images/socket-open.png?raw=true "Socket Open") simgesi ise bağlantının aktif olduğunu gösterir. Eğer bağlantı koparsa verileri sayfa yenilemesi olmadan takip edemezsiniz. Böyle bir durumda debugger sunucunuzu ve tarayıcınızı yeniden başlatmayı deneyin.

### Kapatma

Debugger penceresi açıksa debugger sunucusu kapatıldığında websocket istekleri <kbd>otomatik</kbd> olarak kapatılır. Eğer aşağıdaki gibi bir hata alırsanız,

```php
Debugger seems enabled. Run debug server or disable it from your config.
```

<kbd>app/local/debugger.php</kbd> dosyasından websocket bağlantısını pasif etmeniz gerekir.

```php
return array(

    'enabled' => false,
    'socket' => 'ws://127.0.0.1:9000'
)
```