
## Http Uri Sınıfı

Uri sınıfı url adresinden gelen string türündeki verileri almayı sağlar. Eğer URI route yapısı kullanıldıysa yeniden route edilmiş segmentleri de almaya yardımcı olur. Http paketi içinde yer alan uri sınıfı <a href="http://www.php-fig.org/psr/psr-7/" target="_blank">Psr7</a> standartlarını destekler ve <a href="https://github.com/zendframework/zend-diactoros" target="_blank">Zend-Diactoros</a> ailesinin üyelerinden biridir.

<ul>
    <li><a href="#url-and-uri">Url ve Uri Nedir ?</a></li>
    <li><a href="#accessing-methods">Metotlara Erişim</a></li>
    <li><a href="#resolving-url">Url Çözümleme</a></li>
    <li><a href="#helper-methods">Kurtarıcı Metotlar</a></li>
    <li><a href="#set-methods">Set Metotları</a></li>
    <li>
        <a href="#get-methods">Get metotları</a>
        <ul>
            <li><a href="#getScheme">$uri->getScheme()</a></li>
            <li><a href="#getAuthority">$uri->getAuthority()</a></li>
            <li><a href="#getUserInfo">$uri->getUserInfo()</a></li>
            <li><a href="#getHost">$uri->getHost()</a></li>
            <li><a href="#getPort">$uri->getPort()</a></li>
            <li><a href="#getPath">$uri->getPath()</a></li>
            <li><a href="#getQuery">$uri->getQuery()</a></li>
            <li><a href="#getFragment">$uri->getFragment()</a></li>
            <li><a href="#getSegments">$uri->getSegments()</a></li>
            <li><a href="#getRoutedSegments">$uri->getRoutedSegments()</a></li>
            <li><a href="#segment">$uri->segment()</a></li>
            <li><a href="#rsegment">$uri->rsegment()</a></li>
        </ul>
    </li>
</ul>


<a name="url-and-uri"></a>

### Url ve Uri Nedir ? 

URL (Uniform Resource Locator) web üzerindeki bir kaynağın konumu gösterir, URI (Uniform Resource Identifier) ise diğer kaynaklardan ayıran tanımlayıcı ismini belirtir. 
Her URL , URI'dir ancak her URI , URL değildir. Bazı URI'ler bir adres olmasına rağmen gerçek bir kaynağı göstermeyebilir, sadece tanımlayıcıdır. Url bu nedenle daha genel bir terimdir.

<a name="accessing-methods"></a>

### Metotlara Erişim

URI nesnesi metotlarına request sınıfı <kbd>$request->getUri()</kbd> metodu içerisinden ulaşılır.

```php
$request->getUri()->method();
```

<a name="resolving-url"></a>

### Url Çözümleme

Uri sınıfı dışarıdan gelen aşağıdaki gibi bir url adresini,

```php
http://example.com/welcome/index?a=1&y=2
```

takip eden örnekte olduğu gibi çözümler.

```php
echo $uri : http://example.com/welcome/index?a=1&y=2
echo $uri->getScheme() : http
echo $uri->getAuthority() : example.com
echo $uri->getHost() : example.com
echo $uri->getPort() : 
echo $uri->getPath() : /welcome/index
echo $uri->getQuery() : a=1&y=2
echo $uri->getFragment() : 
```

<a name="helper-methods"></a>

### Kurtarıcı Metotlar

Çerçeve içerisindeki bazı metotlar size yardımcı olarak url adresinin tümünü yada belirli parçalarını alabilmenize olanak sağlar. Psr7 standartı <kbd>$uri->getPath()</kbd> metodu protokol,host,port ve sorgu değişkenleri olmadan bir url adresinin son kısmını verir.

```php
echo $uri->getPath() : /welcome/index
```

Parçalanan url bir dizi içerisinde toplanır <kbd>$uri->getSegments()</kbd> yardımcı metodu ile numaranlandırılmış parçaların tümüne ulaşılabilir.

```php
print_r($uri->getSegments()) : Array
(
    [0] => welcome
    [1] => index
)
```

Parçalara tek tek ulaşmak için <kbd>$uri->segment(n)</kbd> metodu kullanılır.

```php
echo ($uri->segment(0)) ? $uri->segment(0) : "welcome";
```

Bir url adresine ait <kbd>path</kbd> ve <kbd>query</kbd> parametreleri olasılıklarının bütününe ise <kbd>$request</kbd> nesnesi değişmez metodu <kbd>getRequestTarget()</kbd> ile ulaşılır.

```php
echo $request->getRequestTarget();   // /welcome/index?a=b&c=d
```

<a name="set-methods"></a>

### Set Metotları

Uri sınıfında <kbd>with</kbd> öneki ile başlayan metotları kullanarak uri nesnesine etki etmek mümkündür. Aşağıdaki gibi bir web url adresimizin olduğunu varsayarsak.

```php
http://example.com/welcome/index
```

Aşağıdaki metotlar ile istediğimiz türde uri adresleri elde edebiliriz.

```php
$uri->withScheme("https") : https://example.com/welcome/index
$uri->withUserInfo("test", "123456") : http://test:123456@example.com/welcome/index
$uri->withHost("example") : http://example.com/welcome/index
$uri->withPort("9898") : http://example.com:9898/welcome/index
$uri->withPath("/example.php") : http://example.com/example.php
$uri->withQuery("a=1&b=2") : http://example.com/welcome/index?a=1&b=2
$uri->withFragment("anchor") : http://example.com/welcome/index#anchor
```

<a name="get-methods"></a>

### Get Metotları

<a name="getScheme"></a> 

##### $uri->getScheme()

Uri protokolüne geri döner. Örneğin <kbd>https, ftp, http</kbd>.

<a name="getAuthority"></a> 

##### $uri->getAuthority()

Adrese ait yetki alanına geri döner. Örneğin <kbd>example.com</kbd>.

<a name="getUserInfo"></a> 

##### $uri->getUserInfo()

Eğer uri <kbd>test:123456@yetkiAlanı</kbd> gibi bir kullanıcı bilgisi içerisiyorsa "username[:password]" biçimini içeren string türüne döner. Eğer bir kullanıcı verisi yoksa metot boş string türüne döner.

<a name="getHost"></a> 

##### $uri->getHost()

Adrese ait host adresine geri döner. Örneğin <kbd>example.com</kbd>.

<a name="getPort"></a> 

##### $uri->getPort()

Eğer url sayısal bir port değeri içerisiyorsa bu değere aksi durumda <kbd>null</kbd> değerine geri döner.

<a name="getPath"></a> 

##### $uri->getPath()

Uri path bileşeni varsa <kbd>/foo/index</kbd> gibi örnek bir değere, aksi durumda <kbd>/</kbd> karakterine döner.

<a name="getQuery"></a> 

##### $uri->getQuery()

Uri içerisinde sorgu değişkenlerine geri döner. Örneğin <kbd>x=1&y=2</kbd>

<a name="getFragment"></a> 

##### $uri->getFragment()

Uri içerisinde <kbd>#</kbd> karakteri önüne gelen değeri verir.

<a name="getSegments"></a> 

##### $uri->getSegments()

Tüm uri segmentlerine geri döner.

Örnek:

```php
http://example.com/welcome/index
```

```php
$segments = $uri->getSegments();

foreach ($segments as $segment)
{
    echo $segment;
    echo '<br />';
}
```

Çıktı

```php
welcome
index
```

<a name="getRoutedSegments"></a> 

##### $uri->getRoutedSegments()

Bu method işlev olarak bir önceki metodun aynısıdır, tek farkı route işlemlerine duyarlı segmentlerin elde edilmesidir.

<a name="segment"></a> 

##### $uri->segment(n, $noResult = null)

Spesifik bir segment değerine geri döner. Metot içerisine elde edilmek istenen segmentin numarası (n) girilir. Segmentler soldan sağa doğru numaralandırılır. Örneğin aşağıdaki gibi bir URL adresimiz varsa:

```php
http://example.com/sports/basketball/nba/score_history
```

Segment numaralandırılması aşağıdaki gibi olur.

* (0) sports
* (1) basketball
* (2) nba
* (3) score_history

Eğer olmayan bir numara girilirse method <kbd>null</kbd> değerine geri döner. İkinci parametre ise opsiyoneldir. Eğer girilen segment numarası mevcut değilse method bu durumda ikinci parametrede belirtilen değere döner.

```php
http://example.com/sports/basketball/team/
```

```php
$id = $uri->segment(3, 0); // 0
```

Yukarıdaki kod aşağıdaki yazımdan kaçmak için kullanılır.

```php
http://example.com/sports/basketball/team/5
```

```php
if ($uri->segment(3) === null)
{
    $id = 0;
}
else
{
    $id = $uri->segment(3);  // 5
}
```

<a name="rsegment"></a> 

##### $uri->rsegment(n)

Bu method işlev olarak bir önceki metodun aynısıdır, tek farkı route işlemlerine duyarlı segmentlerin elde edilmesidir.