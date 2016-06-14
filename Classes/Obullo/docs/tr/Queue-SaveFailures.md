
## Başarısız İşleri Kaydetmek

İşciler çalışırken, işin işlendiği durumda herhangi bir istisnai hata sözkonusu olursa, hatalı iş otomatik olarak tekrar kuyruğa atılır fakat gerçekleşen hataları takip edebilmek için işleri bir veritabanına kaydetmek gerekir. Aşağıda başarısız işleri veritabanına kaydedebilmek için sırası ile yapmanız gerekenler anlatılıyor.

<a name="failed-jobs-config"></a>

#### Konfigürasyon

Başarısız işlere ait ayarlar <kbd>providers/queue.php</kbd> konfigürasyon dosyasında tutulur. Başarısız işlere ait mevcut kayıt edici sınıf <kbd>Obullo\Queue\Failed\Storage\Database</kbd> olarak belirlenmiştir.

```php
'job' => 
[
    'saveFailures' => [

        'enabled' => true,
        'storage' => '\Obullo\Queue\Failed\Storage\Database',
        'provider' => [
            'name' => 'database',
            'connection' => 'failed',
        ],
        'table' => 'failures',
    ]
]
```

Başarısız işlerin kaydedilebilmesi için konfigürasyonda <kbd>enabled</kbd> anahtarının true olması gerekir.

#### Kaydetme Arayüzü

Kaydedici sınıflar yada sizin yaratmış olduğunuz yeni bir kaydedici sınıf aşağıdaki arayüzü kullanmak zorundadır.

```php
interface StorageInterface
{
    public function save(array $event);
    public function exists($file, $line);
    public function update($id, array $event);
}

/* Location: .Obullo/Queue/Failed/Storage.php */
```

Mevcut kaydedici sınıf aşağıdaki gibi StorageInterface arayüzünü kullanır.

```php
class Database implements StorageInterface
{
    // 
}

/* Location: .Obullo/Queue/Failed/Storage/Database.php */
```

#### Kurulum ve Test

* <kbd>Obullo/Queue/Failed/Database.sql</kbd> dosyası ile veritabanını oluşturun.
* Test için <kbd>app/classes/Workers/</kbd> içerisinde herhangi bir işçi dosyası fire metodu içerisinde aşağıdaki gibi hatalar oluşturun.

```php
public function fire($job, array $data)
{
    echo $a;  // Error test.
    throw new \Exception("Exception test.");
}
```

* Başarısız işlerin kaydedilebilmesi için <kbd>enabled</kbd> anahtarının true olması gerekir. Queue konfigürasyon dosyasınızdan bu değeri kontrol edin.
* İşçi dosyanızı konsoldan çalıştırın.

```php
php task queue listen --worker=Workers@Logger --job=logger.1
```

Eğer herşey yolunda gittiyse bulunan hatalar veritabanına kaydedilir. Birşeyler ters gittiyse <kbd>--output=1</kbd> değeri ile hata çıktılarını alabilirsiniz.

```php
php task queue listen --worker=Workers@Logger --job=logger.1 --output=1
```