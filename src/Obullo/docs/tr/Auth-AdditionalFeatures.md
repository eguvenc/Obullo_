
## Ek Özellikler

Auth paketinde yetki doğrulama onayı gibi bazı ek özellikler de kullanılabilir.

<ul>
    <li>
        <a href="#authentication-verify">Yetki Doğrulama Onay Özelliği</a>
        <ul>
            <li><a href="#temporary-identity">Geçici Kimlikler</a></li>
            <li><a href="#permanent-identity">Kalıcı Kimlikler</a></li>
        </ul>
    </li>
</ul>

<a name="authentication-verify"></a>

### Yetki Doğrulama Onay Özelliği

Opsiyonel olarak gümrükten pasaport ile geçiş gibi kimlik onaylama sistemi isteniyorsa yetki doğrulama onayını kullanabilirsiniz. Yetki doğrulama onayı kullanıcının kimliğini sisteme giriş yapmadan önce <b>email</b>, <b>sms</b> yada <b>mobil çağrı</b> gibi yöntemlerle onay işlemi sağlar.

Kullanıcı başarılı olarak giriş yaptıktan sonra kimliği kalıcı olarak ( varsayılan 3600 saniye ) önbelleklenir. Eğer kullanıcı onay adımından geçirilmek isteniyorsa kalıcı kimlikler <kbd>$this->user->identity->makeTemporary()</kbd> metodu ile geçici hale ( varsayılan 300 saniye ) getirilir. Geçici olan bir kimlik 300 saniye içerisinde kendiliğinden yokolur. Belirtilen süreler konfigürasyon dosyasından ayarlanabilir.

![Authentication](images/auth-flowchart.png?raw=true "Authentication")

<a name="temporary-identity"></a>

#### Geçiçi Kimlikler

Kullanıcı sisteme giriş yaptıktan sonra <kbd>$this->user->identity->makeTemporary()</kbd> metodu ile kimliği geçici hale getirilir ve kullanıcı sisteme giriş yapamaz. Kullanıcının geçici kimliğini onaylaması sizin ona <b>email</b>, <b>sms</b> yada <b>mobil çağrı</b> gibi yöntemlerinden herhangi biriyle göndermiş olacağınız onay kodu ile gerçekleşir. Eğer kullanıcı 300 saniye içerisinde kendisine gönderilen onay kodunu onaylayamaz ise geçiçi kimlik kendiliğinden yok olur.

Eğer kullanıcı onay işlemini başarılı bir şekilde gerçekleştirir ise geçici kimliğin <kbd>$this->user->identity->makePermanent()</kbd> metodu ile kalıcı hale getirilmesi gereklidir. Bir kimlik kalıcı yapıldığında kullanıcı sisteme giriş yapmış olur.

```php
$authResult = $this->user->login->attempt(
    [
        'db.identifier' => $this->validator->getValue('email'), 
        'db.password'   => $this->validator->getValue('password'),
    ],
    [
        'rememberMe' => $this->request->post('rememberMe'),
        'regenerateSessionId' => true
    ]
);
```

Oturum bilgileri doğru ise kimliği geçici hale getirebilirsiniz.

```php
if ($authResult->isValid()) {
    
    $this->user->identity->makeTemporary();
    $this->flash->success('Verification code has been sent.');
    $this->url->redirect('membership/confirm');
}
```

Yukarıdaki kod bloğuna login kontrolör içerisine entegre edip çalıştırdığınıza login denemesi başarılı ise geçici kimlik oluşturulur. Sonraki adım için bir <kbd>membership/confirm</kbd> adlı bir sayfa oluşturun ve bu sayfada oluşturacağınız formda kullanıcı onay kodunu doğru girdi ise <kbd>$this->user->identity->makePermanent()</kbd> metodunu kullanarak kimliği kalıcı hale getirin. Ve yetkilendirmeden sonra kullanıcıyı <kbd>membership/restricted</kbd> sayfanıza yönlendirin.

<a name="permanent-identity"></a>

#### Kalıcı Kimlikler

Bir geçici kimliği kalıcı hale dönüştürmek için,

```php
$this->user->identity->makePermanent();
```

metodu kullanılır. Kalıcı kimliğe sahip olan kullanıcı artık sisteme giriş yapabilir. Kalıcı olan kimlikler önbelleklenirler. Böylece önbelleklenen kimlik tekrar oturum açıldığında veritabanı sorgusuna gidilmeden elde edilmiş olur. Kalıcı kimliğin önbelleklenme süresi konfigürasyon dosyasından ayarlanabilir. Eğer geçici kimlik oluşturma fonksiyonu kullanılmamışsa sistem her kimliği varsayılan olarak <kbd>kalıcı</kbd> olarak kaydeder.