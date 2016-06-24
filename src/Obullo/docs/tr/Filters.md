
## Filtreler

Bir uygulamada kullanıcı girdilerini filtrelemek uygulama güvenliği açısından büyük önem taşır. Kullanıcı girdilerine güvenerek filtrelemeyi ihmal etmek uygulama içeriğinize, kullanıcı verilerine hatta uygulamanın barındırıldığı sunucu üzerinde izinsiz girişlere neden olabilir.

<ul>
<li>
    <a href="#is">Is Sınıfı</a>  ( Doğrulama )
    <ul>
        <li><a href="#is-int">$this->is->int()</a></li>
        <li><a href="#is-float">$this->is->float()</a></li>
        <li><a href="#is-bool">$this->is->bool()</a></li>
        <li><a href="#is-email">$this->is->email()</a></li>
        <li><a href="#is-ip">$this->is->ip()</a></li>
        <li><a href="#is-url">$this->is->url()</a></li>
    </ul>
</li>

<li>
    <a href="#clean">Clean Sınıfı</a> ( Temizleme )
    <ul>
        <li><a href="#clean-str">$this->clean->str()</a></li>
        <li><a href="#clean-raw">$this->clean->raw()</a></li>
        <li><a href="#clean-int">$this->clean->int()</a></li>
        <li><a href="#clean-float">$this->clean->float()</a></li>
        <li><a href="#clean-email">$this->clean->email()</a></li>
        <li><a href="#clean-quote">$this->clean->quote()</a></li>
        <li><a href="#clean-escape">$this->clean->escape()</a></li>
        <li><a href="#clean-fullEscape">$this->clean->fullEscape()</a></li>
        <li><a href="#clean-url">$this->clean->url()</a></li>
        <li><a href="#clean-urlencode">$this->clean->urlencode()</a></li>
    </ul>
</li>
</ul>

<a name="is"></a>

### Is Sınıfı

Is sınıfı php <kbd>filter_var()</kbd> fonksiyonlarını kullanarak belirli data tipleri üzerinde doğrulama kontrolü sağlar. Filtreleme özellikleri gönderilen çeşitli parametreler ile değiştirilebilir.

#### Metotlara Erişim

```php
$container->get('is')->method();
```

kontrolör içinden,

```php
$this->is->method();
```

<a name="is-int"></a>

##### $this->is->int($value, $default = false, $min = 0, $max = PHP_INT_MAX, $flag = 'octal');

Bir kullanıcı girdisinin bir tamsayı olup olup olmadığını kontrol etmek için kullanılır.

```php
$this->is->int(285); // Çıktı 285
```

İkinci parametre, işlemin başarısız olması durumunda hangi değere dönüleceğini belirler. Varsayılan değer <kbd>false</kbd> değeridir.

```php
$this->is->int(285.8, null); // Çıktı null
```

Gelişmiş seçenekler ile doğrulama

```php
$this->is->int(0285, false, 1, 300, 'octal');  // Çıktı 2
```

Üçüncü parametre girdinin alabileceği en düşük değeri dördüncü parametre ise en yüksek değeri belirler. Beşinci parametreye ise aşağıdaki tabloda belirtilen seçenekler girilebilir.

<table>
    <thead>
        <tr>
            <th>Parametre</th>
            <th>Denk Php Sabiti</th>
            <th>Açıklama</th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td>octal</td>
            <td>FILTER_FLAG_ALLOW_OCTAL</td>
            <td>Octal sayısal değerlerin kullanımına izin verir. ( Varsayılan )</td>
        </tr>
        <tr>
            <td>hex</td>
            <td>FILTER_FLAG_ALLOW_HEX</td>
            <td>Hexadecimal sayısal değerlerin kullanımına izin verir.</td>
        </tr>
    </tbody>
</table>

<a name="is-float"></a>

##### $this->is->float($value, $default = false);

Bir kullanıcı girdisinin float olup olmadığını kontrol etmek için kullanılır.


```php
$this->is->float(10.99); // Çıktı 10.99
```

İkinci parametre, işlemin başarısız olması durumunda hangi değere dönüleceğini belirler. Varsayılan değer <kbd>false</kbd> değeridir.

```php
$this->is->float(10, null);  // Çıktı null
```

###### Request Sınıfı İle Birlikte Kullanmak

```php
$price = $this->request->post('price');

if ($price = $this->is->float($price)) {
	echo $price;
}
```

<a name="is-bool"></a>

##### $this->is->bool($value, $default = false);

Bir kullanıcı girdisinin boolean olup olmadığını kontrol etmek için kullanılır.

```php
$this->is->bool(true); // Çıktı true
```

İkinci parametre, işlemin başarısız olması durumunda hangi değere dönüleceğini belirler. Varsayılan değer <kbd>false</kbd> değeridir.

```php
$this->is->bool(10, 0);  // Çıktı 0
```

###### Request Sınıfı İle Birlikte Kullanmak

```php
$value = $this->request->post('value');

if ($value = $this->is->bool($value)) {
    // ..
}
```

<a name="is-email"></a>

##### $this->is->email($email, $default = false);

Bir kullanıcı girdisinin email olup olmadığını kontrol etmek için kullanılır.

```php
$this->is->email('user@example.com');  // Çıktı user@example.com
```

İkinci parametre, işlemin başarısız olması durumunda hangi değere dönüleceğini belirler. Varsayılan değer <kbd>false</kbd> değeridir.

```php
$this->is->email(user.example.com, null);  // Çıktı null
```

###### Request Sınıfı İle Birlikte Kullanmak

```php
$email = $this->request->post('email');

if ($this->is->email($email)) {

}
```

<a name="is-ip"></a>

##### $this->is->ip($ip, $default = false, $flag = null);

Bir kullanıcı girdisinin ip adresi olup olmadığını kontrol etmek için kullanılır.

```php
$this->is->ip('127.0.0.1');  // Çıktı 127.0.0.1
```

İkinci parametre, işlemin başarısız olması durumunda hangi değere dönüleceğini belirler. Varsayılan değer <kbd>false</kbd> değeridir.

```php
$this->is->ip('0938493', '0.0.0.0');  // Çıktı 0.0.0.0
```

Ip adresinin IPV6 olup olmadığı kendiliğinden algılanır.

```php
$this->is->ip('FE80:0000:0000:0000:0202:B3FF:FE1E:8329');   // Çıktı FE80:0000:0000:0000:..
```

Sadece belirli bir ip standardı doğrulamasına izin verilmek isteniyorsa 


```php
$this->is->ip('127.0.0.1', false, 'v6');  // Çıktı false
```

Özel ip araklıkları hariç tutulmak isteniyorsa

```php
$this->is->ip('10.0.0.1', false, 'no_priv_range');  // Çıktı false
```

Seçenekler

<table>
    <thead>
        <tr>
            <th>Parametre</th>
            <th>Denk Php Sabiti</th>
            <th>Açıklama</th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td>null</td>
            <td></td>
            <td>Ip version 4 ve version 6 türlerinin her ikisi için de doğrulama yapar. ( Varsayılan )</td>
        </tr>
        <tr>
            <td>v4</td>
            <td>FILTER_FLAG_IPV4</td>
            <td>Sadece ip v4 türünde doğrulamaya izin verir.</td>
        </tr>
        <tr>
            <td>v6</td>
            <td>FILTER_FLAG_IPV6</td>
            <td>Sadece ip v6 türünde doğrulamaya izin verir.</td>
        </tr>
        <tr>
            <td>no_priv_range</td>
            <td>FILTER_FLAG_NO_PRIV_RANGE</td>
            <td>Özel IPv4 aralıklarına ( 10.0.0.0/8, 172.16.0.0/12 ve 192.168.0.0/16 ) izin verilmez ve doğrulama başarısız olur. Ayrıca FD ve FC ile başlayan IPv6 adresleri türleri içinde doğrulama başarısız olur.</td>
        </tr>
    </tbody>
</table>


###### Request Sınıfı İle Birlikte Kullanmak

```php
$ip = $this->request->post('ip_address');

if ($ip = $this->is->ip('v4|no_priv_range')) {
    echo $ip;
}
```

<a name="is-url"></a>

##### $this->is->url($url, $default = false, $flag = 'scheme');

Bir kullanıcı girdisinin url adresi olup olmadığını kontrol etmek için kullanılır.

```php
$this->is->url('http://example.com');  // Çıktı http://example.com
```

İkinci parametre, işlemin başarısız olması durumunda hangi değere dönüleceğini belirler. Varsayılan değer <kbd>false</kbd> değeridir.

```php
$this->is->url('//example.com', null); // Çıktı null
```

Üçüncü parametre seçenekler göndererek fonksiyonu farklı özelliklerde kullanmanızı sağlar.

```php
$this->is->url('http://www.example.com', false, 'host');  // Çıktı http://www.example.com
```

Seçenekler

<table>
    <thead>
        <tr>
            <th>Parametre</th>
            <th>Denk Php Sabiti</th>
            <th>Açıklama</th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td>scheme</td>
            <td>FILTER_FLAG_SCHEME_REQUIRED</td>
            <td>URL RFC standartına uyumlu olmak zorundadır. Örnek: http://example.com ( Varsayılan )</td>
        </tr>
        <tr>
            <td>host</td>
            <td>FILTER_FLAG_HOST_REQUIRED</td>
            <td>URL host ismini içermelidir. Örnek: http://www.example.com </td>
        </tr>
        <tr>
            <td>path</td>
            <td>FILTER_FLAG_PATH_REQUIRED</td>
            <td>URL domain isminden sonra path ismini içermelidir. Örnek: www.example.com/example1/ </td>
        </tr>
        <tr>
            <td>query</td>
            <td>FILTER_FLAG_QUERY_REQUIRED</td>
            <td>URL bir query string değeri içermelidir. Örnek: example.php?name=Peter&age=37</td>
        </tr>
    </tbody>
</table>

###### Request Sınıfı İle Birlikte Kullanmak

```php
$url = $this->request->post('url_adress');

if ($url = $this->is->url($url, false, 'host|query')) {
    echo $url;
}
```

<a name="clean"></a>

### Clean Sınıfı

Clean sınıfı php filter_var() fonksiyonlarını kullanarak belirli data tipleri yardımı ile girilen değeri <kbd>arındırır</kbd>. Filtreleme özellikleri gönderilen çeşitli parametreler ile değiştirilebilir.

#### Metotlara Erişim

```php
$container->get('clean')->method();
```

kontrolör içinden,

```php
$this->clean->method();
```

<a name="clean-str"></a>

##### $this->clean->str($value, $flag = 'strip_low');

Bir kullanıcı girdisinden bütün html taglarını siler.

```php
$this->clean->str("<kbd>Hello World</kbd>"); // Çıktı Hello World
```

İkinci parametre, işlem seçeneklerini belirler. Varsayılan değer <kbd>strip_low</kbd> değeridir.

```php
echo $this->clean->str("<h1>Hello WorldÆØÅ!</h1>", 'strip_high|encode_amp'); // Çıktı Hello World!
```

Tek ve çift tırnak karakterlerinin kodlanmasını engellemek istiyorsanız <kbd>no_encode_quotes</kbd> seçeneğini kullanmanız gerekir.

```php
$str1 = $this->clean->str("Welcome <script> alert('Hello World')</script>");   
var_dump($str1);

// Çıktı string(37) "Welcome alert('Hello World')"
```

```php
$str2 = $this->clean->str(
"Welcome <script> alert('Hello World')</script>",
'encode_amp|no_encode_quotes'
);
var_dump($str2);

//Çıktı string(29) "Welcome alert('Hello World')" 
```

###### Request Sınıfı İle Birlikte Kullanmak

```php
$entry = $this->request->post('entry');

if ($entry = $this->clean->str($entry, 'strip_high|encode_amp')) {
    echo $entry;
}
```

Seçenekler

<table>
    <thead>
        <tr>
            <th>Parametre</th>
            <th>Denk Php Sabiti</th>
            <th>Açıklama</th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td>strip_low</td>
            <td>FILTER_FLAG_STRIP_LOW</td>
            <td>ASCII değeri 32 den küçük olan karakterleri siler. ( Varsayılan )</td>
        </tr>
        <tr>
            <td>strip_high</td>
            <td>FILTER_FLAG_STRIP_HIGH</td>
            <td>ASCII değeri 127 den büyük olan karakterleri siler.</td>
        </tr>
        <tr>
            <td>no_encode_quotes</td>
            <td>FILTER_FLAG_NO_ENCODE_QUOTES</td>
            <td>Tek ve Çift tırnak karakterlerinin kodlanmasını engeller.</td>
        </tr>
        <tr>
            <td>encode_low</td>
            <td>FILTER_FLAG_ENCODE_LOW</td>
            <td>ASCII değeri 32 den küçük olan karakterleri kodlar.</td>
        </tr>
        <tr>
            <td>encode_high</td>
            <td>FILTER_FLAG_ENCODE_HIGH</td>
            <td>ASCII değeri 127 den büyük olan karakterleri kodlar.</td>
        </tr>
        <tr>
            <td>encode_amp</td>
            <td>FILTER_FLAG_ENCODE_AMP</td>
            <td>"<kbd>&</kbd>" karakterini <kbd>&</kbd>amp; olarak kodlar.</td>
        </tr>
    </tbody>
</table>


<a name="clean-raw"></a>

##### $this->clean->raw($str);

Bir kullanıcı girdisinden uygulamaya zarar verebilecek potansiyel verileri temizler. Özel karakterleri silmek veya istenmeyen karakterleri kodlamak için kullanılır. Html karakterlerini <kbd>temizlemez</kbd>.

```php
var_dump($this->clean->raw("Is Peter �� & \0\n funny?", 'strip_high'));
```

```php
// Çıktı  string(21) "Is Peter & funny?"
```

& karakterini kodlama için bir örnek

```php
var_dump($this->clean->raw("Is Peter �� & \0\n funny?", 'strip_high|encode_amp'));
```

```
// Çıktı string(25) "Is Peter & funny?" 
```

Seçenekler

<table>
    <thead>
        <tr>
            <th>Parametre</th>
            <th>Denk Php Sabiti</th>
            <th>Açıklama</th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td>strip_low</td>
            <td>FILTER_FLAG_STRIP_LOW</td>
            <td>ASCII değeri 32 den küçük olan karakterleri siler. ( Varsayılan )</td>
        </tr>
        <tr>
            <td>strip_high</td>
            <td>FILTER_FLAG_STRIP_HIGH</td>
            <td>ASCII değeri 32 den büyük olan karakterleri siler.</td>
        </tr>
        <tr>
            <td>encode_low</td>
            <td>FILTER_FLAG_ENCODE_LOW</td>
            <td>ASCII değeri 32 den küçük olan karakterleri kodlar.</td>
        </tr>
        <tr>
            <td>encode_high</td>
            <td>FILTER_FLAG_ENCODE_HIGH</td>
            <td>ASCII değeri 32 den büyük olan karakterleri kodlar.</td>
        </tr>
        <tr>
            <td>encode_amp</td>
            <td>FILTER_FLAG_ENCODE_AMP</td>
            <td>"<kbd>&</kbd>" karakterini <kbd>&</kbd>amp; olarak kodlar.</td>
        </tr>
    </tbody>
</table>


<a name="clean-int"></a>

##### $this->clean->int($value);

Sayısal bir kullanıcı girdisinden tüm usulsüz karakterleri siler.

```php
$this->clean->int("5-2+3pp"); // "5-2+3"
```

###### Request Sınıfı İle Birlikte Kullanmak

```php
$id = $this->request->post('id');

if ($int = $this->clean->int($id)) {
    echo $int;
}
```

<a name="clean-float"></a>

##### $this->clean->float($value, $flag = 'fraction');

Float formatında bir kullanıcı girdisinden tüm usulsüz karakterleri siler.

```php
$this->clean->float("10.2p#a"); // "10.2"
```

İkinci parametreye <kbd>thousand</kbd> değeri girilirse fonksiyon "," karakterlerini silmez.

```php
$this->clean->float("10,2p#a", 'thousand'); // "10,2"
```

Gelen girdi değişken ise her iki paramtere de girilebilir.

```php
$this->clean->float("10.2p,#aE", 'fraction|thousand|scientific'); // "10.2,E"
```

###### Request Sınıfı İle Birlikte Kullanmak

```php
$price = $this->request->post('price');

if ($price = $this->clean->float($price, 'fraction|thousand')) {
    echo $price;
}
```

Seçenekler

<table>
    <thead>
        <tr>
            <th>Parametre</th>
            <th>Denk Php Sabiti</th>
            <th>Açıklama</th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td>fraction</td>
            <td>FILTER_FLAG_ALLOW_FRACTION</td>
            <td>Nokta ile kesir ayrımına izin verir. (.)</td>
        </tr>
        <tr>
            <td>thousand</td>
            <td>FILTER_FLAG_ALLOW_THOUSAND</td>
            <td>Virgül ile kesir ayrımına izin verir. (,)</td>
        </tr>
        <tr>
            <td>scientific</td>
            <td>FILTER_FLAG_ALLOW_SCIENTIFIC</td>
            <td>"E" veya "e" gibi bilimsel notasyona izin verir.</td>
        </tr>
    </tbody>
</table>

<a name="clean-email"></a>

##### $this->clean->email($email);

Email formatında bir kullanıcı girdisinden tüm usulsüz karakterleri siler.

```php
echo $this->clean->email('u(s)er@ex\\ample.com'); // Çıktı user@example.com
```

Bu metodun request sınıfı ile birlikte kullanımı str metoduyla aynıdır.

<a name="clean-quote"></a>

##### $this->clean->quote($str);

Bir kullanıcı girdisindeki tek veya çift tırnak karakterleri başına addslashes() fonksiyonu gibi kaçış karakterleri ( "\" ) ekler. Kaçış yapılan karakterler " (çift tırnak), ' (tek tırnak), \ (backslash) ve NULL karakterleridir.

```php
$this->clean->quote("Ayşe'nin elbisesi");  // Çıktı Ayşe\'nin elbisesi 
```

Bu metodun request sınıfı ile birlikte kullanımı str metoduyla aynıdır.

<a name="clean-escape"></a>

##### $this->clean->escape($str, flag = "strip_low");

Bir kullanıcı girdisindeki özel karakterlerden kaçış için kullanılır. Özel karakterlere kaçış atarak karakterlerin olduğu gibi gösterilmesini sağlar.

```php
echo $this->clean->escape("Is Peter <kbd>smart</kbd>> & funny?");
```

```php
// Çıktı Is Peter <kbd>smart</kbd> & funny? 
```

Seçenekler

<table>
    <thead>
        <tr>
            <th>Parametre</th>
            <th>Denk Php Sabiti</th>
            <th>Açıklama</th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td>strip_low</td>
            <td>FILTER_FLAG_STRIP_LOW</td>
            <td>ASCII değeri 32 den küçük olan karakterleri siler. ( Varsayılan )</td>
        </tr>
        <tr>
            <td>strip_high</td>
            <td>FILTER_FLAG_STRIP_HIGH</td>
            <td>ASCII değeri 32 den büyük olan karakterleri siler.</td>
        </tr>
        <tr>
            <td>encode_low</td>
            <td>FILTER_FLAG_ENCODE_LOW</td>
            <td>ASCII değeri 32 den küçük olan karakterleri kodlar.</td>
        </tr>
        <tr>
            <td>encode_high</td>
            <td>FILTER_FLAG_ENCODE_HIGH</td>
            <td>ASCII değeri 32 den büyük olan karakterleri kodlar.</td>
        </tr>
    </tbody>
</table>

Bu metodun request sınıfı ile birlikte kullanımı str metoduyla aynıdır.

<a name="clean-fullEscape"></a>

##### $this->clean->fullEscape($html, $flag = null);

Bir kullanıcı girdisindeki özel karakterleri kodlar. <kbd>ENT_QUOTES</kbd> ile kullanılmış bir php htmlspecialchars() fonksiyonuna eş değerdir. Bu fonksiyonda tırnaklardan kaçış kodlaması <kbd>FILTER_FLAG_NO_ENCODE_QUOTES</kbd> özelliği ile kapatılabilir. Htmlspecialchars fonksiyonunda olduğu gibi bu filtrede varsayılan karakter setine duyarlıdır.

```php
$str = "Is It Peter's?";

var_dump($this->clean->escape($str));  // Çıktı string(18) "Is It Peter's?" 
var_dump($this->clean->fullEscape($str, 'no_encode_quotes'));  // string(14) "Is It Peter's?" 
```

Seçenekler

<table>
    <thead>
        <tr>
            <th>Parametre</th>
            <th>Denk Php Sabiti</th>
            <th>Açıklama</th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td>no_encode_quotes</td>
            <td>FILTER_FLAG_NO_ENCODE_QUOTES</td>
            <td>Tırnaklardan kaçışı engeller.</td>
        </tr>
    </tbody>
</table>

Bu metodun request sınıfı ile birlikte kullanımı str metoduyla aynıdır.

<a name="clean-url"></a>

##### $this->clean->url($url, $flag = 'scheme');

Bir url adresi girdisinden tüm usulsüz karakterleri siler. Sadece harfler, sayılar ve $-_.+!*'(),{}|\\^~[]`"><#%;/?:@&= karakterlerine izin verilir.

```php
echo $this->clean->url("http://www.example��.co�m", 'host');  // Çıktı http://www.example.com
```

Birden fazla seçenek de kullanılabilir.

```php
echo $this->clean->url("http://mydomain.example\0.com?a=b&c=d", 'host|query');
```

```php
// Çıktı http://mydomain.example.com?a=b&c=d
```

Seçenekler

<table>
    <thead>
        <tr>
            <th>Parametre</th>
            <th>Denk Php Sabiti</th>
            <th>Açıklama</th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td>scheme</td>
            <td>FILTER_FLAG_SCHEME_REQUIRED</td>
            <td>URL RFC standartına uyumlu olmak zorundadır. Örnek: http://example.com ( Varsayılan )</td>
        </tr>
        <tr>
            <td>host</td>
            <td>FILTER_FLAG_HOST_REQUIRED</td>
            <td>URL host ismini içermelidir. Örnek: http://www.example.com </td>
        </tr>
        <tr>
            <td>path</td>
            <td>FILTER_FLAG_PATH_REQUIRED</td>
            <td>URL domain isminden sonra path ismini içermelidir. Örnek: www.example.com/example1/ </td>
        </tr>
        <tr>
            <td>query</td>
            <td>FILTER_FLAG_QUERY_REQUIRED</td>
            <td>URL bir query string değeri içermelidir. Örnek: example.php?name=Peter&age=37</td>
        </tr>
    </tbody>
</table>

Bu metodun request sınıfı ile birlikte kullanımı str metoduyla aynıdır.

<a name="clean-urlencode"></a>

##### $this->clean->urlencode($url, $flag = 'strip_low');

Girilen bir url adresi içerisindeki özel karakterleri kodlar.

```php
echo $this->clean->urlencode("http://example��.com", 'strip_low');
```

```php
// Çıktı http%3A%2F%2Fexample%EF%BF%BD%EF%BF%BD.com 
```

```php
echo $this->clean->urlencode("http://example��.com", 'strip_high');
```

```php
// Çıktı http%3A%2F%2Fexample.com 
```

###### Request Sınıfı İle Birlikte Kullanmak

```php
$url = $this->request->post('url');

if ($url = $this->clean->urlencode($url, 'strip_high')) {
    echo $url;
}
```

Seçenekler

<table>
    <thead>
        <tr>
            <th>Parametre</th>
            <th>Denk Php Sabiti</th>
            <th>Açıklama</th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td>strip_low</td>
            <td>FILTER_FLAG_STRIP_LOW</td>
            <td>ASCII değeri 32 den küçük olan karakterleri siler. ( Varsayılan )</td>
        </tr>
        <tr>
            <td>strip_high</td>
            <td>FILTER_FLAG_STRIP_HIGH</td>
            <td>ASCII değeri 127 den büyük olan karakterleri siler.</td>
        </tr>
        <tr>
            <td>encode_low</td>
            <td>FILTER_FLAG_ENCODE_LOW</td>
            <td>ASCII değeri 32 den küçük olan karakterleri kodlar.</td>
        </tr>
        <tr>
            <td>encode_high</td>
            <td>FILTER_FLAG_ENCODE_HIGH</td>
            <td>ASCII değeri 127 den büyük olan karakterleri kodlar.</td>
        </tr>
    </tbody>
</table>