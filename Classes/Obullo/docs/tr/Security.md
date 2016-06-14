
## Güvenlik

Bu başlık altında çerçeve içerisinde alınan güvenlik önlemlerine ait nesneler listelenmektedir.

<ul>
    <li>
        <a href="#csrf">Cross Site Request Forgery</a>
        <ul>
            <li><a href="#service-provider">Servis Sağlayıcısı</a></li>
            <li><a href="#running">Çalıştırma</a></li>
        </ul>
    </li>
</ul>

<a name="csrf"></a>

### Cross Site Request Forgery ( Csrf )

Cross Site Request dolandırıcılığı hakkında daha detaylı bilgi için <a href="http://shiflett.org/articles/cross-site-request-forgeries">bu makalaye</a> gözatabilirsiniz.

Çerçeve içerisinde bu tehdide karşı uygulanan çözüm aşağıdaki gibidir :

* Uygulamanızdaki tüm formlarda bir güvenlik algoritması oluşturulur ve bu algoritma http POST istekleri geldiğinde sunucu tarafında doğrulanır, doğrulama başarılı olmazsa doğrulama sınıfı içerisinden kullanıcıya bir güvenlik hatası gösterilir.

<a name="service-provider"></a>

#### Servis Sağlayıcısı

<kbd>app/providers.php</kbd> dosyasında servis sağlayıcısının tanımlı olduğundan emin olun.

```php
$container->addServiceProvider('Obullo\Container\ServiceProvider\Csrf');
```

<a name="running"></a>

#### Çalıştırma

<kbd>providers/csrf.php</kbd> dosyasından csrf protection değerini true olarak değiştirin.

```php
return array(

    'params' => [
        'protection' => true,
        'token' => [
            'salt' => 'create-your-salt-string',
            'name' => 'csrf_token',
            'refresh' => 30,
        ],
    ],
);
```

Eğer <kbd>Form/Element</kbd> paketini kullanmıyorsanız uygulamanızda csrf güvenliği gereken formlar içerisinde aşağıdaki gibi güvenlik değeri oluşturmanız gerekir.

```php
<form action="/buy" method="post">
<input type="hidden" name="<?php echo $this->csrf->getTokenName() ?>" 
value="<?php echo $this->csrf->getToken() ?>" />
</form>
``` 

Eğer form element paketini kullanıyorsanız,

```php
$element = $this->form->getElement();
```

form metodu sizin için csrf değerini kendiliğinden oluşturur.

```php
echo $element->form('/buy', array('method' => 'post'));
```

Formunuzun csrf doğrulaması yapabilmesi için form doğrulama kuralları içerisinde <kbd>csrf</kbd> kuralını kullanmanız gerekir.

```php
$this->validator->setRules('csrf_token', 'Csrf Token', 'csrf');
```

Örnek bir doğrulama kodu.

```php
if ($this->request->isPost()) {

    $this->validator->setRules('email', 'Email', 'required|email');
    $this->validator->setRules('csrf_token', 'Csrf Token', 'csrf');

    if ($this->validator->isValid()) {
        $this->form->success('Form validation success.');
    } else {
        $this->form->error('Form validation failed.');
    }
}
```

Demo form için uygulamanızdan <kbd>/examples/forms/csrf</kbd> adresine göz atın.