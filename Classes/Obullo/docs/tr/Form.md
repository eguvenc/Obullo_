
## Form Sınıfı

Form sınıfı özel form mesajlarını, validator sınıfı çıktılarını, işlem sonuçlarını, ve form hatalarını yönetir.

<ul>
    <li><a href="#service-providers">Servis Sağlayıcısı</a></li>
    <li><a href="#accessing-methods">Metotlara Erişim</a></li>
    <li><a href="#form-message">Form Mesajı</a></li>
    <li><a href="#using-with-validator">Doğrulama Hataları</a></li>
    <li>
        <a href="#customization">Özelleştirme</a>
        <ul>
            <li><a href="#adding-custom-data">$this->form->setItem()</a></li>
            <li><a href="#adding-custom-errors">$this->form->setErrors()</a></li>
            <li><a href="#adding-custom-results">$this->form->setResults()</a></li>
        </ul>
    </li>
    <li>
        <a href="#get-methods">Get Metotları</a>
        <ul>
            <li><a href="#getMessageString">$this->form->getMessageString()</a></li>
            <li><a href="#getValidationErrors">$this->form->getValidationErrors()</a></li>
            <li><a href="#getError">$this->form->getError()</a></li>
            <li><a href="#isError">$this->form->isError()</a></li>
            <li><a href="#getErrorLabel">$this->form->getErrorLabel()</a></li>
            <li><a href="#getErrorClass">$this->form->getErrorClass()</a></li>
            <li><a href="#getValue">$this->form->getValue()</a></li>
            <li><a href="#getOutputArray">$this->form->getOutputArray()</a></li>
            <li><a href="#getResultArray">$this->form->getResults()</a></li>
            <li><a href="#getElement">$this->form->getElement()</a></li>
        </ul>
    </li>
    <li>
        <a href="#set-methods">Set Metotları</a>
        <ul>
            <li><a href="#success">$this->form->success()</a></li>
            <li><a href="#error">$this->form->error()</a></li>
            <li><a href="#warning">$this->form->warning()</a></li>
            <li><a href="#info">$this->form->info()</a></li>
            <li><a href="#setCode">$this->form->setCode()</a></li>
            <li><a href="#setStatus">$this->form->setStatus()</a></li>
            <li><a href="#setErrors">$this->form->setErrors()</a></li>
            <li><a href="#setMessage">$this->form->setMessage()</a></li>
            <li><a href="#setItem">$this->form->setItem()</a></li>
            <li><a href="#setResults">$this->form->setResults()</a></li>
            <li><a href="#setValue">$this->form->setValue()</a></li>
            <li><a href="#setSelect">$this->form->setSelect()</a></li>
            <li><a href="#setCheckbox">$this->form->setCheckbox()</a></li>
            <li><a href="#setRadio">$this->form->setRadio()</a></li>
        </ul>
    </li>
</ul>

<a name="service-provider"></a>

### Servis Sağlayıcısı

<kbd>app/providers.php</kbd> dosyasında servis sağlayıcısının tanımlı olduğundan emin olun.

```php
$container->addServiceProvider('Obullo\Container\ServiceProvider\Form');
```

Servis konfigürasyonu <kbd>providers/form.php</kbd> dosyası form mesajlarına ait html şablonu ve niteliklerini belirler. Varsayılan css şablonu bootstrap çerçevesi için konfigüre edilmiştir. Bu adresten <a href="http://getbootstrap.com" target="_blank">http://getbootstrap.com</a> bootstrap projesine gözatabilirsiniz.

<a name="accessing-methods"></a>

### Metotlara Erişim

```php
$form = $container->get('form')
$form->method();
```

Konrolör içinden,

```php
$this->form->method();
```

<a name="form-message"></a>

### Form Mesajı

Form şablonunuz ile ilişkili olan form mesajları aşağıdaki gibi üretilir.

```php
$this->form->success('Form saved successfully.');
$this->form->error('Form validation failed.');
$this->form->warning('Something went wrong.');
```

Görünüm dosyasında olması önerilen form fonksiyonu.

```php
echo $this->form->getMessages();
```

Çıktı

```php
<div class="alert alert-success">
<span class="glyphicon glyphicon-ok-sign">Form saved successfully.</span>
<div class="alert alert-danger">
<span class="glyphicon glyphicon-remove-sign">Form validation failed.</span>
<div class="alert alert-warning">
<span class="glyphicon glyphicon-exclamation-sign">Something went wrong.</span>
```

Durum Metotları Tablosu

<table>
    <thead>
        <tr>
            <th>Durum</th>
            <th>Kod</th>
            <th>Açıklama</th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td>error</td>
            <td>0</td>
            <td>İşlemlerde bir hata olduğunda kullanılır.</td>
        </tr>
        <tr>
            <td>success</td>
            <td>1</td>
            <td>Başarılı işlemlerde kullanılır.</td>
        </tr>
        <tr>
            <td>warning</td>
            <td>2</td>
            <td>Uyarı amaçlı mesajları göstermek amacıyla kullanılır.</td>
        </tr>
        <tr>
            <td>info</td>
            <td>3</td>
            <td>Bilgi amaçlı mesajları göstermek amacıyla kullanılır.</td>
        </tr>
    </tbody>
</table>

<a name="using-with-validator"></a>

### Doğrulama Hataları

Bir form doğrulamasından dönen hataları görünüm sayfasında elde etmek için error() fonksiyonu ile hataların form sınıfına aktarılması gerekir. 

<a name="http-requests"></a>

##### Http İstekleri

Bir http post türü form doğrulama işleminde form sınıfı aşağıdaki gibi kullanılır.

```php
if ($this->request->isPost()) {

    $this->validator->setRules('name', 'Name', 'required|trim');
    $this->validator->setRules('email', 'Email', 'required|trim|email');
    $this->validator->isValid();

    if (! $this->validator->isValid()) {
        
        $this->form->error('Validation failed.');

    } else {

        $this->form->success('Validation success.');
    }

}
```

Eğer <kbd>$this->form->error()</kbd> kullanılmıyorsa form hataları için setErrors() fonksiyonunu ayrıca kullanmak durumundasınız.

```php
$this->form->setErrors();
```

Görünüm dosyanıza aşağıdaki kodları yerleştirin.

```php
<form action="/form/post" method="POST">
    <table width="100%">
        <tr>
            <td>Name</td>
            <td><?php echo $this->form->getError('name'); ?>
            <input type="text" name="name" value="<?php echo $this->form->getValue('name') ?>" /></td>
        </tr>
        <tr>
            <td style="width:20%;">Email</td>
            <td><?php echo $this->form->getError('email'); ?>
            <input type="text" name="email" value="<?php echo $this->form->getValue('email') ?>" />
            </td>
        </tr>
    </table>
</form>
```

Yukarıdaki işlemleri yaptıysanız form alanlarını boş girdikten sonra formu çalıştırdığınızda isim ve email alanlarına ait hatalar almanız gerekir.

<a name="ajax-requests"></a>

##### Ajax İstekleri

Bir ajax türü form doğrulama işlemi aşağıdaki gibi yapılabilir.

```php
if ($this->request->isPost()) {

    $this->validator->setRules('name', 'Name', 'required|trim');
    $this->validator->setRules('email', 'Email', 'required|trim|email');

    if ( ! $this->validator->isValid()) {

        $this->form->error('Form validation failed');

        print_r($this->form->getOutputArray());
    }   
}
```

Form sınıfı http ve ajax istekleri için standart bir çıktı üretir.


```php
/*
Çıktı

Array
(
    [success] => 0
    [code] => 0
    [message] => Form validation failed
    [errors] => Array
        (
            [email] => The Email field is required.
            [name] => The Name field is required.
        )
)
*/
```

Son aşamada form çıktıları ajax işlemleri için http resonse sınıfı yardımıyla json formatında kodlanması gereklidir.

```php
return $this->response->json($this->form->getOutputArray());
```

<a name="customization"></a>

### Özelleştirme

<a name="adding-custom-data"></a>

##### $this->form->setItem(string $key, mixed $data)

SetItem fonksiyonu ile mevcut form sınıfı çıktıları içerisine özel veriler eklenebilir.

```php
$this->form->setStatus(1);
$this->form->setCode(2);
$this->form->setItem('message', "Özel durum mesajı");

print_r($this->form->getOutputArray());
```

```php
Çıktı

Array
(
    [success] => 1
    [code] => 2
    [message] => Özel durum mesajı
    [errors] => Array
        (
            [email] => The Email field is required.
            [name] => The Name field is required.
        )
)
```

<a name="adding-custom-errors"></a>

##### $this->form->setErrors()

Bir servis için oluşmuş hataları da form sınıfına gönderebilmek mümkündür.

```php
$errors = array (
    'success' => 0,
    'code' => 99,
    'message' => 'İşlem Başarısız',
    'errors' => [
        'email' => 'The Email field is required.',
        'name' => 'The Name field is required.''
    ]
)
$this->form->setErrors($errors);
```

Ayrıca doğrulama sınıfı hataları da istenirse harici olarak gönderilebilir.

```php
$this->form->setErrors($this->validator->getErrors());
```

<a name="adding-custom-results"></a>

##### $this->form->setResults()

Bir servis yada işlem için oluşmuş hataları da form sınıfına gönderebilmek mümkündür bunun için setResults fonksiyonu kullanılır. Gönderilen veriler form çıktısında <kbd>results</kbd> anahtarına kaydedilir.

```php
$result = $exampleApi->exec();
$this->form->setResults($result->getArray());
```

Örnek Çıktı

```php
print_r($this->form->getResultArray());
```

```php
/*
Array
(
    [success] => 0
    [code] => 0
    [results] => Array
        (
            [messages] => Array
                (
                    [0] => Supplied credential is invalid.
                )

            [identifier] => user@example.com
        )
)
*/
```

Results anahtarına kaydedilen verilere view sayfasından nesne olarak ulaşılabilir. Aşağıdaki örnekte sonuçlardan alınan mesajlar form sınıfı html şablonuna aktarılıyor.

```php
if ($results = $this->form->getResultArray()) {
    foreach ($results['messages'] as $message) {
        echo $this->form->getMessageString($message);  // Mesajlar form sınıfı html şablonuna aktarılıyor.
    }
}
```

<a name="get-methods"></a>

### Get Metotları

Form get metotları bir http form post işleminden sonra doğrulama sınıfı ile filtrelenen değerleri elde etmek veya form elementlerine atamak için kullanılırlar.

<a name="getMessageString"></a>

##### $this->form->getMessageString($msg = '')

Form doğrulama hatalı ise forma ait genel hata mesajlarına bir string biçiminde geri döner. Eğer ekstra bir mesaj gönderilmek isteniyorsa bu mesaj ilk parametreye girilir ve metot bu mesaj ile birlikte hata mesajlarına döner.

<a name="getMessageArray"></a>

##### $this->form->getMessageArray()

Form doğrulama hatalı ise forma ait genel hata mesajlarına bir dizi içerisinde geri döner.

<a name="getValidationErrors"></a>

##### $this->form->getValidationErrors()

Eğer validator sınıfı mevcutsa form post işleminden sonra girilen input alanlarına ait hatalara string formatında geri döner.

<a name="getError"></a>

##### $this->form->getError(string $field, $prefix = '', $suffix = '')

Eğer validator sınıfı mevcutsa form post işleminden sonra girilen input alanına ait hataya geri döner.

```php
<form action="/user/post/index" method="POST">
    <?php echo $this->form->getError('email'); ?>
    <input type="text" name="email" value="<?php echo $this->form->getValue('email') ?>" />
</form>
```

<a name="getErrorClass"></a>

##### $this->form->getErrorClass($field)

Eğer girilen alana ait hata dönerse <kbd>providers/form.php</kbd> dosyasından girilen konfigürasyon çıktılanır.

```php
echo $this->form->getErrorClass('email')
```

Çıktı

```php
has-error has-feedback    
```

<a name="getErrorLabel"></a>

#### $this->form->getErrorLabel($field)

Eğer girilen alana ait hata dönerse <kbd>providers/form.php</kbd> dosyasından girilen konfigürasyon çıktılanır.

```php
echo $this->form->getErrorLabel('email')
```

Çıktı

```php
<label class="control-label" for="field">Label</label>
```

<a name="isError"></a>

##### $this->form->isError($field)

Eğer girilen alana ait bir hata varsa true aksi durumda false değerine döner.


<a name="getValue"></a>

##### $this->form->getValue(string $field)

Eğer validator sınıfı mevcutsa form post işleminden sonra filtrelenen input alanına ait değere geri döner.

```php
<input type="text" name="price" value="<?php echo $this->form->getValue('price') ?>" size="20" />
```

<a name="getOutputArray"></a>

##### $this->form->getOutputArray()

Bir form doğrulamasından sonra oluşan çıktıları array formatında getirir.

<a name="getResultArray"></a>

##### $this->form->getResultArray()

Bir form doğrulamasından sonra <kbd>results</kbd> anahtarına gönderilen sonuçlara geri döner.

<a name="getElement"></a>

##### $this->form->getElement()

Form elementlerini kontrol eden form element nesnesine geri döner.

```php
$element = $this->form->getElement();
echo $element->input('username', 'johndoe', ' maxlength="100" size="50" style="width:50%"');
```
Çıktı

```html
<input type="text" name="username" id="username" 
value="johndoe" maxlength="100" size="50" style="width:50%" />
```

Bu sınıfa ait dökümentasyona [Form-Element.md](Form-Element.md) dosyasından ulaşılabilirsiniz.

<a name="set-methods"></a>

### Set Metotları

Form set metotları http form post işleminden sonra form mesajlarını göstermek yada checbox, menü, radio gibi elemenlere ait elementlere ait opsiyonların güvenli bir şekilde gösterimi için kullanılırlar.

<a name="success"></a>

##### $this->form->success(string $message)

Bir form doğrulamasından sonra <kbd>messages</kbd> anahtarına mesaj, <kbd>success</kbd> anahtarına (1), <kbd>code</kbd> anahtarına (1) değerini ekler.

<a name="error"></a>

##### $this->form->error(string $message)

Bir form doğrulamasından sonra <kbd>messages</kbd> anahtarına mesaj, <kbd>success</kbd> anahtarına (0), <kbd>code</kbd> anahtarına ise (0) değerini ekler.

<a name="warning"></a>

##### $this->form->warning(string $message)

Bir form doğrulamasından sonra <kbd>messages</kbd> anahtarına mesaj, <kbd>code</kbd> anahtarına (2) ekler, <kbd>success</kbd> anahtarı değeri varsayılan (0) dır.

<a name="info"></a>

##### $this->form->info(string $message)

Bir form doğrulamasından sonra <kbd>messages</kbd> anahtarı mesaj, <kbd>code</kbd> anahtarına (2) ekler, <kbd>success</kbd> anahtarı varsayılan (0) dır.

<a name="setCode"></a>

##### $this->form->setCode(int $code)

Bir form doğrulama çıktısı mevcut <kbd>code</kbd> anahtarının sayısal değerini günceller. Mevut kod değeri (0) dır.

<a name="setStatus"></a>

##### $this->form->setStatus(int $status = 0)

Bir form doğrulama çıktısı mevcut <kbd>success</kbd> anahtarının sayısal değerini günceller. Mevcut durum değeri (0) dır. Bu fonsiyonu verilebilecek değerler <b>0</b> yada <b>1</b> olmalıdır. 

<a name="setErrors"></a>

##### $this->form->setErrors(array $errors | object $validator)

Başarısız bir form doğrulamasından sonra <kbd>errors</kbd> anahtarına hatalar ekler. İlk parametre array olarak gönderilirse hatalar olduğu gibi kaydedilir.

<a name="setMessage"></a>

##### $this->form->setMessage(string $message = '', integer $status = 0)

Bir form doğrulaması çıktısı <kbd>message</kbd> anahtarına bir mesaj değeri atar. İkinci parametere girilirse eğer form success anahtarı <b>0</b> yada <b>1</b> olarak değiştirir. Birden fazla mesaj eklenebilir.

<a name="setItem"></a>

##### $this->form->setItem(string $key, mixed $val)

Bir form doğrulamasından sonra oluşan çıktı dizisindeki anahtarlara değeri ile birlikte yeni bir anahtar ekler yada mevcut anahtarı yeni değeriyle günceller.

<a name="setResults"></a>

##### $this->form->setResults(array $results)

Bir servis yada işlem için oluşmuş özel hataları form sınıfına gönderebilmek için kullanılır. Gönderilen veriler form çıktısında <kbd>results</kbd> anahtarına kaydedilir.

<a name="setValue"></a>

##### $this->form->setValue(string $field, $default = '')

Eğer validator sınıfı mevcutsa form post işleminden sonra filtrelenen input alanına ait değere geri döner. İkinci parametre eğer geri dönülecek varsayılan değeri belirler.

```php
<input type="text" name="price" value="<?php echo $this->form->setValue('price', '0.00') ?>" size="20" />
```

<a name="setSelect"></a>

##### $this->form->setSelect(string $field, $value = '', $default = false)

Eğer bir <b>select</b> menü kullanıyorsanız bu fonksiyon seçilen menü değerine ait opsiyonu seçili olarak göstermenize olanak sağlar.

```php
<select name="myselect">
    <option value="one" <?php echo $this->form->setSelect('myselect', 'one', true) ?> >One</option>
    <option value="two" <?php echo $this->form->setSelect('myselect', 'two') ?> >Two</option>
    <option value="three" <?php echo $this->form->setSelect('myselect', 'three') ?> >Three</option>
</select>
```

<a name="setCheckbox"></a>

##### $this->form->setCheckbox(string $field, $value = '', $default = false)

Eğer bir <b>checbox</b> elementi kullanıyorsanız bu fonksiyon seçilen değere ait opsiyonu seçili olarak göstermenize olanak sağlar.

```php
<input type="checkbox" name="mycheck" value="1" <?php echo $this->form->setCheckbox('mycheck', '1') ?> />
<input type="checkbox" name="mycheck" value="2" <?php echo $this->form->setCheckbox('mycheck', '2') ?> />
```

<a name="setRadio"></a>

##### $this->form->setRadio(string $field, $value = '', $default = false)

Eğer bir <b>radio</b> elementi kullanıyorsanız bu fonksiyon seçilen değere ait opsiyonu seçili olarak göstermenize olanak sağlar.

```php
<input type="radio" name="myradio" value="1" <?php echo $this->form->setRadio('myradio', '1', true) ?> />
<input type="radio" name="myradio" value="2" <?php echo $this->form->setRadio('myradio', '2') ?> />
```