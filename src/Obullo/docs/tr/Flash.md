
## Flaş Sınıfı

Uygulama içerisinde son kullanıcıya gösterilen onay, hata veya bilgi mesajlarını yönetir. Bir işlemden sonra flaş sınıfı aracılığı ile <kbd>session</kbd> nesnesine kaydedilen mesaj veya mesajlar bir sonraki http isteğinde mevcut olurlar ve bir kez görüntülendikten sonra mevcut session verisinden silinirler. Flaş sınıfına ait mesajlar olası bir karışıklığı önlemek için session anahtarına <kbd>"flash_"</kbd> öneki ile kaydedilirler.

### Metotlara Erişim

```php
$container->get('flash')->method();
```

kontrolör içerisinden,

```php
$this->flash->method();
```

### Servis Sağlayıcısı

<kbd>app/providers.php</kbd> dosyasında servis sağlayıcısının tanımlı olduğundan emin olun.

```php
$container->addServiceProvider('Obullo\Container\ServiceProvider\Flash');
```

Konfigürasyon dosyası <kbd>providers/flash.php</kbd> dosyasından yönetilir ve flaş mesajlarına ait html şablonu ve niteliklerini belirler. Varsayılan css şablonu bootstrap çerçevesi için konfigüre edilmiştir. <a href="http://getbootstrap.com" target="_blank">http://getbootstrap.com</a>

### Flaş Mesajı

Bir flaş mesajı göstermek oldukça kolaydır bir durum metodu seçin ve içine mesajınızı girin.

```php
$this->flash->success('Form saved successfully.');
```

Ve aşağıdaki kodu view sayfanıza yerleştirin.


```php
$this->flash->getOutput();  // Form saved successfully.
```

Durum Metotları

<table>
    <thead>
        <tr>
            <th>Durum</th>
            <th>Açıklama</th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td>success</td>
            <td>Başarılı işlemlerde kullanılır.</td>
        </tr>
        <tr>
            <td>error</td>
            <td>İşlemlerde bir hata olduğunda kullanılır.</td>
        </tr>
        <tr>
            <td>warning</td>
            <td>Uyarı amaçlı mesajları göstermek amacıyla kullanılır.</td>
        </tr>
        <tr>
            <td>info</td>
            <td>Bilgi amaçlı mesajları göstermek amacıyla kullanılır.</td>
        </tr>
    </tbody>
</table>

Birden fazla flaş mesajı göstermek için birden fazla metot kullanmanız gerekir.

```php
$this->flash->success('Form saved successfully.');
$this->flash->error('Error.');
$this->flash->warning('Something went wrong.');
$this->flash->info('Email has been sent to your mail address.');

$this->flash->getOutput();
```

```php
/*
Çıktı

Form saved successfully.
Error.
Email has been sent to your mail address.
Something went wrong.
*/
```

### Gerçek Bir Örnek

Uygulama içerisinde mesajlar göstermek için <kbd>if .. else</kbd> komutlarından yararlanabilirsiniz.

```php
$delete = $this->db->transactional(
    function () use () {
    	return $this->db->exec("DELETE FROM users WHERE id = 1");
    }
);
if ($delete) {
	$this->flash->success('User successfully deleted');
} else {
	$this->flash->error('Delete error');
}
```

### Mesajın Kalıcılığını Korumak

Eğer bir flaş mesajının bir sonraki http isteğinde kalıcı olmasını istiyorsanız keep metodu kullanmanız gerekir.

```php
$this->flash->keep('notice:warning');
$this->flash->keep('notice:success');
```

### Durum Değerini Almak

Bir flaş mesajına ait durum değerini <kbd>status</kbd> anahtarı ile alabilirsiniz.

```php
$this->flash->get('notice:status');  // Çıktı success
```

### Özel Durum Mesajları

Mevcut durum metotları dışında kendinize ait flaş mesajları da ekleyebilirsiniz. Bunun için set fonksiyonunu kullanmanız gerekir.

```php
$this->flash->set('anahtar', 'değer');
```

Mesajları okumak için ise get fonksiyonu kullanılır.

```php
echo $this->flash->get('anahtar', '<p class="example">', '</p>');
```

Eğer flaş mesajı boş ise get() fonksiyonu boş bir string değerine döner aksi durumda mesaja döncektir. 

```php
// Çıktı  <p class="example">değer</p>
```

Eğer $prefix ve $suffix değerleri boş değilse mesaj konfigüre edilmiş şablon ile birlikte görüntülenir.

### Flaş Sınıfı Referansı

##### $this->flash->success(string $message);

Bir flaş mesajını başarılı durum verisi ile kaydeder.

##### $this->flash->error(string $message);

Bir flaş mesajını hata durum verisi ile kaydeder.

##### $this->flash->warning(string $message);

Bir flaş mesajını uyarı durum verisi ile kaydeder.

##### $this->flash->info(string $message);

Bir flaş mesajını bilgi durum verisi ile kaydeder.

##### $this->flash->getOutput();

Tüm flaş mesajlarını <kbd>string</kbd> türünde alır ve flaş verilerinin sonraki istekte silinmesi için verileri <kbd>old</kbd> değeri ile kaydeder.

##### $this->flash->getOutputArray();

Tüm flaş mesajlarını <kbd>array</kbd> türünde alır ve flaş verilerinin sonraki istekte silinmesi için verileri <kbd>old</kbd> değeri ile kaydeder.

##### $this->flash->keep(string $key)

Bir sonraki http isteğinde mevcut olması için girilen anahtara ait flaş verisini saklar.

##### $this->flash->set(string|array $data = '', $newval = '')

Durum metotlarında tanımlı olmayan yeni bir flaş verisi kaydeder.

##### $this->flash->get(string $key)

Girilen anahtara ait flaş mesajlarını <kbd>string</kbd> türünde alır ve flaş verisinin sonraki istekte silinmesi için veriyi <kbd>old</kbd> değeri ile kaydeder.