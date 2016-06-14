
## Şifre Sınıfı

Şifre sınıfı uygulamanıza kaydettiğiniz kullanıcılar için istenen algoritma ile güvenli şifreler üretir.

<ul>
    <li><a href="#loading-component">Bileşeni Yüklemek</a></li>
    <li><a href="#service-provider">Servis Sağlayıcısı</a></li>
    <li><a href="#accessing-methods">Metotlara Erişim</a></li>
    <li><a href="#password_hash">$this->password->hash()</a></li>
    <li><a href="#password_verify">$this->password->verify()</a></li>
    <li><a href="#password_rehash">$this->password->needsRehash()</a></li>
    <li><a href="#why-bcrypt">Neden Bcrypt ?</a></li>
    <li><a href="#scheme-identifiers">Şema Tanımlayıcıları</a></li>
    <li><a href="#structure">Yapı</a></li>
    <li><a href="#diagram">Diagram</a></li>
</ul>

<a name="loading-component"></a>

### Bileşeni Yüklemek

Php <a href="http://php.net/manual/en/function.password-hash.php" target="_blank">password hash fonksiyonları</a> php <kbd>5.5.0</kbd> ve üzeri versiyonlarda doğal olarak desktelenir. Eğer php sürümünüz 5.5.0 altı bir sürümse password compat kütüphanesini yüklemeniz tavsiye edilir.

```php
composer require ircmaxell/password-compat
```

sonraki adımda <kbd>composer.json</kbd> dosyası <kbd>files</kbd> anahtarı içerisine dosya adını aşağıdaki gibi tanımlayın.

```php
{
    "autoload": {
        "psr-4": {
            "": "app/classes"
        },
        "files": [
            "vendor/ircmaxell/password-compat/lib/password.php"
        ]
    }
}
```

Bu tanımlamadan doğal php password hash metotlarına aşağıdaki gibi erişebilirsiniz.

```php
password_method();
```

<a name="service-provider"></a>

### Servis Sağlayıcısı

<kbd>app/providers.php</kbd> dosyasında servis sağlayıcısının tanımlı olduğundan emin olun.

```php
$container->addServiceProvider('Obullo\Container\ServiceProvider\Password');
```

<a name="accessing-methods"></a>

### Metotlara Erişim

```php
$container->get('password')->method();
```

Kontrolör içerisinden,

```php
$this->password->method();
```

<a name="password_hash"></a>

### $this->password->hash(string $password, int $algo, array $options)

Güvenli şifreler yaratmak için <kbd>hash</kbd> metodu kullanılır.

```php
echo $this->password->hash("obulloFramework", PASSWORD_BCRYPT, array("cost" => 10));
```
Çıktı

```php
$2y$10$g6KqDmd.qZPQMaBnzhOeW.tYq03iqBe/.f3flea2zlzwyHWKBJVnm
```

Eğer işlem başarısız olursa metot <kbd>false</kbd> değerine aksi durumda güveli şifre değerine geri döner. Cost değeri şifre üretilirken oluşan maliyettir. Bu seçenek uygulamanın güvenliği ve donanımınızın kuvvetine göre ayarlanmalıdır. Zira sunucunuzun donanınımı zayıfsa yada şifre doğrulama aşamasında performans sorunları yaşıyorsanız bu değeri 6 olarak ayarlamanız önerilir. 8 veya 10 değerleri orta donanımlı bilgisayarlar için, 12 ve üzeri sayılar ise güçlü donanımlı ( çekirdek sayısı fazla ) olan bilgisayarlar için tavsiye edilir.

<a name="password_verify"></a>

### $this->password->verify($password, $hash)

Kullanıcıya ait bir şifreyi doğrulamak için kayıt edilen şifrenin <kbd>plain-text</kbd> formatındaki gerçek değerine ve <kbd>şifrelenmiş</kbd> değerine ihtiyaç duyulur. Bu iki değer karşılaştırılarak şifre doğruluğu kontrol edilir.

```php
$hash     = '$2y$10$g6KqDmd.qZPQMaBnzhOeW.tYq03iqBe/.f3flea2zlzwyHWKBJVnm';
$password = 'obulloFramework'

if ($this->password->verify($password, $hash)) {
    echo 'Şifre doğru !';
} else {
    echo 'Şifre yanlış.';
}
```

Eğer doğrulama başarılı ise metot <kbd>true</kbd> değerine aksi durumda <kbd>false</kbd> değerine döner.

<a name="password_rehash"></a>

### $this->password->needsRehash($hash, $algo, $options)

Güvenli şifre doğrulandıktan sonra eğer api tarafında güvenlik için şifrenin periyodik yenilenme zamanı gelmişse password_needs_rehash() metodu <kbd>true</kbd> değerine döner. Böylece sisteme giriş yapan kullanıcılara ait şifreler periyodik olarak veritabanında yenilenmiş olurlar.

```php
$hash    = '$2y$10$g6KqDmd.qZPQMaBnzhOeW.tYq03iqBe/.f3flea2zlzwyHWKBJVnm';
$options = ['cost' => 12];
$password  = 'obulloFramework'

if ($this->password->verify($password, $hash)) {

    echo 'Şifre doğru.';

  if ($this->password->needsRehash($hash, PASSWORD_BCRYPT, $options)) {

      $hash = $this->password->hash($password, PASSWORD_BCRYPT, $options);

      // Yenilenen şifreyi veritabanında güncelleyin.
  }

} else {

    echo 'Şifre yanlış.';
}
```

Şifre ilk şifrelenirken verilen opsiyonlar ile yeniden şifreleme işlemi için verilen opsiyonlar aynı olmalıdır.

<a name="why-bcrypt"></a>

### Neden Bcrypt ?

Bcrypt blowfish algoritmasını kullanarak güvenli şifreler üretir. Neden bcrypt kullanılması gerektiğini bir kaç madde ile özetlersek;

- Yüksek güvenlik sağlar bu tipteki şifreleri çözebilmek için saldırganın çok yüksek donanımlı bir bilgisayar kullanması gerekir.
- Bcrypt tek yönlü bir algoritmadır bu da şifrenin tekrar plain-text formatına geri çevrilemeyeceği anlamına gelir.
- Bcrypt sınıfı herbir şifreyi farklı bir tuz (salt) ile şifreler.

<kbd>MD5</kbd> şifreleme yöntemi ise hızlı bir metot olduğundan dolayı belirsiz hassas olmayan verilerde çok sık kullanılır. Bu özelliğine karşın [rainbow tabloları](http://en.wikipedia.org/wiki/Rainbow_table) ' ndan anlaşılabileceği gibi bir güvenli şifre operasyonunda kolayca çözülebilmeleri dezavantajdır.

İşte bu nedenle Bcrypt algoritması önem kazanır. Çalışma faktörü ( cost opsiyonu ) "12" belirlendiğinde Bcrypt bir şifreyi *0.3 saniyede* MD5 ise mikrosaniyeden az bir zamanda şifreler.

Daha fazla bilgi için bu makaleye gözatabilirsiniz. <a href="http://phpmaster.com/why-you-should-use-bcrypt-to-hash-stored-passwords/" target="_blank">Why you should use Bcrypt</a>.

<a name="scheme-identifiers"></a>

### Şema Tanımlayıcıları

Bcrypt şifreleme yönteminde algoritma şema tanımlayıcıları şifrelenmiş güvenli şifre hakkında bilgiler verir.

- `$2a$` - Potansiyel olarak demode (buggy) olmuş bir algorithma ile yaratılmış şifre.
- `$2x$` - Geriye dönük uyumluluk için Bcrypt uyumluluk seçeneği implementasyonu.
- `$2y$` - Varolan en yeni şema ile yaratılmış versiyon *(crypt_blowfish 1.1 ve yeni sürümlerde)*.

Varsayılan şema en yeni şema olan `$2y$` değeridir. Diğer şemalar eski versiyonlar da üretilen şifreler için kullanılır.

<a name="structure"></a>

### Yapı

Şifrelenmiş bir güvenli şifre aşağıdaki yapıda gibi gözükür.

```php
$2a$12$Some22CharacterSaltXXO6NC3ydPIrirIzk1NdnTz0L/aCaHnlBa
```

- `$2a$` php yorumlayıcısına hangi şemayı kullanması gerektiğini anlatır. *(Bcrypt tabanlı)*
- `12$` şifreleme mekanizmasının çalışma faktörü yani "cost" değeridir.
- `Some22CharacterSaltXXO` rastgele oluşturulan bir tuzlama değeridir *(OpenSSL tarafından oluşturulur)*
- `6NC3ydPIrirIzk1NdnTz0L/aCaHnlBa` güvenli şifre değeridir 31 karakterden oluşur.

<a name="diagram"></a>

### Diagram

```php
$2a$12$Some22CharacterSaltXXO6NC3ydPIrirIzk1NdnTz0L/aCaHnlBa
\___________________________/\_____________________________/
  \                            \
   \                            \ Actual Hash (31 chars)
    \
     \  $2a$   12$   Some22CharacterSaltXXO
        \__/    \    \____________________/
          \      \              \
           \      \              \ Salt (22 chars)
            \      \
             \      \ Number of Rounds (work factor)
              \
               \ Hash Header
```

Diagram bu kaynaktan alınmıştır [Andrew Moore's structure](http://stackoverflow.com/a/5343655).