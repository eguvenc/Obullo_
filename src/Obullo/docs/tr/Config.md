
## Konfigürasyon Sınıfı 

Uygulama içerisindeki konfigürasyon dosyalarını çevre ortamına duyarlı olarak yükler ve konfigürasyon dosyalarını yönetir.

<ul>

<li>
    <a href="#running">Çalıştırma</a>
    <ul>
        <li><a href="#methods">Metotlara Erişim</a></li>
        <li><a href="#loading-config-files">$this->config->get()</a></li>
        <li><a href="#loading-folders">$this->config->get('folder::')</a></li>
        <li><a href="#writing-config-files">$this->config->write()</a></li>
    </ul>

    <a href="#environments">Ortama Değişkenine Duyarlı Konfigürasyonlar</a>
</li>

</ul>

<a name="running"></a>

### Çalıştırma

Konfigürasyon sınıfı uygulamaya çalışmaya başladığında yüklenir ve her zaman uygulama içerisinde yüklüdür. Varsayılan konfigürasyon dosyası <kbd>app/local/config.php</kbd> dosyasıdır ve bu dosya uygulama çalıştırıldığında uygulamaya kendiliğinden dahil edilir.

<a name="methods"></a>

#### Metotlara Erişim

```php
$this->container->get('config')->method();
```

<a name="loading-config-files"></a>

#### $this->config->get()

Konfigürasyon dosyalarınızı <kbd>app/$env/</kbd> dizininden yükler. Aşağıda verilen örnekte çevre ortamını "local" ayarlandığını varsayarsak <kbd>maintenance.php</kbd> dosyası <kbd>app/local/</kbd> klasöründen çağrılır.

```php
print_r($this->config->get('maintenance')['subdomain']);
```

Çıktı 

```php
Array ( [maintenance] => up [regex] => sub.domain.com )
```

<a name="loading-folders"></a>

#### $this->config->get('folder::')

Eğer ilk kelime önünde <kbd>::</kbd> karakterini kullanırsanız config sınıfı bu kelimeyi klasör olarak algılar.

```php
$database = $this->config->get('providers::database');
```

Yukarıdaki konfigürasyon dosyası <kbd>/providers</kbd> klasöründen çağırılıyor.

```php
echo $database['params']['connections']['default']['host']['dsn'];
```

Çıktı

```php
pdo_mysql:host=localhost;port=;dbname=test
```
<a name="writing-config-files"></a>

#### $this->config->write()

Config sınıfı içerisindeki write metodu <kbd>app/$env/</kbd> klasörü içerisindeki config dosyalarınıza yeni konfigürasyon verileri kaydetmenizi sağlar. Takip eden örnekte <kbd>app/local/maintenance.php</kbd> konfigürasyon dosyasındaki <kbd>maintenance</kbd> değerini güncelliyoruz.

```php
$newArray = $this->config->get('maintenance');
$newArray['root']['maintenance'] = 'down';  // Yeni değeri atayalım

$this->config->write('maintenance', $newArray);
```

Şimdi <kbd>maintenance.php</kbd> dosyanız aşağıdaki gibi güncellenmiş olmalı.

```php
return array(

    'root' => [
        'maintenance' => 'down',
        'regex' => null,
    ],

);

/* Location: ./var/www/framework/app/local/maintenance.php */
```

Yazma işlemlerinde dosya adı da kullanabilirsiniz.


```php
$newArray = $this->config->get('providers::csrf');
$newArray['params']['token']['name'] = 'test_token';  // Yeni değerleri atayalım

$this->config->write('providers::csrf', $newArray);
```

Şimdi <kbd>providers/csrf.php</kbd> dosyasına bir gözatın.

```php
return array(

    'params' => [
        'protection' => true,
        'token' => [
            'salt' => 'create-your-salt-string',
            'name' => 'test_token',
            'refresh' => 30,
        ],
    ],
);
```

<a name="environments"></a>

#### Ortam Değişkenine Duyarlı Konfigürasyonlar

Ortam değişkeni <kbd>app/environments.php</kbd> dosyasından kontrol edilir ve ortam bu dosyaya göre belirlenir. Konfigürasyon dosyaları ortam değişkenine duyarlıdır.
Daha fazla bilgi için [Environments.md ](Environments.md) dökümentasyonunu ziyaret edin.