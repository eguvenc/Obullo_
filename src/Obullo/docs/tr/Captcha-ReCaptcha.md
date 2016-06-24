
## ReCAPTCHA Sınıfı

ReCAPTCHA google şirketi tarafından geliştirilen popüler bir captcha servisidir. ReCaptcha servisini kurmak için önce <a href="https://www.google.com/recaptcha/intro/index.html" target="_blank">bu sayfayı</a> ziyaret ederek site key ve secret key bilgilerinizi almanız gerekir.

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
        <li><a href="#module">ReCaptcha Modülü</a></li>
        <li><a href="#loading-service">Servisi Yüklemek</a></li>
    </ul>
</li>
    
<li>
    <a href="#create-operations">ReCaptcha İşlemleri</a>
    <ul>
        <li>
            <a href="#creating-captcha">ReCaptcha Oluşturma</a>
            <ul>
                <li><a href="#printJs">$this->captcha->printJs()</a></li>
                <li><a href="#printHtml">$this->captcha->printHtml()</a></li>
            </ul>
        </li>
        <li><a href="#validation">ReCaptcha Doğrulama</a></li>    
        <li><a href="#validation-with-validator">Validator Sınıfı İle Doğrulama</a></li>
    </ul>
</li>

<li><a href="#method-reference">ReCaptcha Sınıfı Referansı</a></li>
</ul>

<a name="setup"></a>

### Kurulum

Eğer <kbd>recaptcha.php</kbd> konfigürasyon dosyası <kbd>providers/</kbd> klasörü altında mevcut ise bu dosyayı konfigüre etmeniz gerekir

<a name="service-provider"></a>

#### Servis Sağlayıcısı

<kbd>app/providers.php</kbd> dosyasında servis sağlayıcısının tanımlı olduğundan emin olun.

```php
$container->addServiceProvider('Obullo\Container\ServiceProvider\ReCaptcha');
```

<a name="running"></a>

### Çalıştırma

ReCaptcha modülünü kurduktan sonra ReCaptcha arayüzüne bağlanmak için recaptcha servisi kullanılır.

<a name="module"></a>

#### ReCaptcha Modülü

ReCaptcha modülü ile ilgili kapsamlı örnekleri incelemek için tarayıcınızdan aşağıdaki adresleri ziyaret edin.

```html
http://myproject/examples/recaptcha
http://myproject/examples/recaptcha/ajax
```

<a name="loading-service"></a>

#### Servisi Yüklemek

ReCaptcha servisi aracılığı ile recaptcha metotlarına aşağıdaki gibi erişilebilir.

```php
$this->container->get('recaptcha')->method();
```

<a name="create-operations"></a>

### ReCaptcha İşlemleri

ReCaptcha işlemleri recaptcha html ve javascript kodunu oluşturma, yenileme tuşu oluşturma ve doğrulama işlemlerini kapsar.

<a name="creating-captcha"></a>

#### ReCaptcha Oluşturma

ReCaptcha oluşturma metotları recaptcha elemetlerini oluşturur.

<a name="printJs"></a>

##### $this->recaptcha->printJs();

Formlarınıza ReCAPTCHA eklemek için aşağıdaki gibi <b>head</b> tagları arasına javascript çıktısını ekrana dökmeniz gerekir.

```php
<!DOCTYPE html>
<html>
<head>
	<title></title>
	<?php echo $this->recaptcha->printJs() ?>
</head>
<body>

</body>
</html>
```

<a name="printHtml"></a>

##### $this->recaptcha->printHtml();

ReCAPTCHA nın görüntülenmesi için aşağıdaki gibi captcha çıktıyı ekrana dökmeniz gerekir.

```php
<form method="POST" action="/captcha/examples/form">
	<?php echo $this->recaptcha->printHtml() ?>
    <input type="submit" value="Send" name="sendForm">
</form>
```

<a name="validation"></a>

#### ReCaptcha Doğrulama 

ReCaptcha doğrulama için bütün sürücüler için ortak olarak kullanılan CaptchaResult sınıfı kullanılır. Bir captcha kodunun doğru olup olmadığı aşağıdaki gibi isValid() komutu ile anlaşılır.

Bir doğrulamadan dönen mesajlar aşağıdaki gibi alınır.

```php
print_r($this->recaptcha->result()->getMessages());
```

Bir doğrulamaya ait hata kodu alma örneği


```php
echo $this->recaptcha->result()->getCode();  // -2  ( Invalid Code )
```

<a name="validation-with-validator"></a>

#### Validator Sınıfı İle Doğrulama 

Eğer varolan formunuz içerisinde <kbd>validator</kbd> sınıfını kullanıyorsanız doğrulama için herhangi bir kod yazmanıza gerek kalmaz ve <kbd>recaptcha</kbd> doğrulama kuralını kural olarak eklemeniz yeterli olur.

```php
$this->validator->setRules('recaptcha', 'Captcha', 'recaptcha');
```

<a name="method-reference"></a>

#### ReCaptcha Sınıfı Referansı

ReCaptcha servisi dökümentasyona <a href="https://developers.google.com/recaptcha/docs/display" target="_blank">bu bağlantıdan</a> ulaşabilirsiniz. 

##### $this->recaptcha->setLang(string $lang);

Servisin hangi dili desteklemesi gerektiğini belirler.

##### $this->recaptcha->setUserIp(string $ip);

Servise son kullanıcının ip adresini gönderilmesini sağlar.

##### $this->recaptcha->printJs();

Servise ait javascript tagını ekrana yazdırır. Html head tagları arasında kullanılması önerilir.

##### $this->recaptcha->printHtml();

Servise ait captcha elementinin html taglarını ekrana yazdırır. Html body tagları arasında kullanılması önerilir.

##### $this->recaptcha->setSiteKey(string $lang);

Varolan site key konfigurasyonunu dinamik olarak değiştirebilmenizi sağlar.

##### $this->recaptcha->setSecretKey(string $lang);

Varolan secret key konfigurasyonunu dinamik olarak değiştirebilmenizi sağlar.

##### $this->recaptcha->getUserIp();

Tanımlanan user ip adresini verir.

##### $this->recaptcha->getSiteKey();

Tanımlanmış site key konfigürasyonunu verir.

##### $this->recaptcha->getSecretKey();

Tanımlanmış secret key konfigürasyonunu verir.

##### $this->recaptcha->getLang();

Tanımlanmış olan dili verir.

##### $this->recaptcha->getInputName();

Validator sınıfının çalışabilmesi oluşturulan recaptcha elemetinin ismini verir.