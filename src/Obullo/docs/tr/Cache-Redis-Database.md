
### Redis Veritabanı Referansı

Cache sınıfı içerisinde tanımlı olmayan metotlar __call metodu ile php <kbd>Redis</kbd> sınıfından çağrılırlar.

<a name="redis-auth"></a>

##### $this->cache->auth(string $password)

Eğer yetkilendirme konfigürasyon dosyasından yapılmıyorsa bu fonksiyon ile manual olarak yetkilendirme yapabilirsiniz.

<a name="redis-setOption"></a>

##### $this->cache->setOption(string|constant $option = 'OPT_SERIALIZER', string|constant $value = 'SERIALIZER_NONE')

Redis için bir opsiyon tanımlar. Birer sabit olan opsiyonlar parametrelerden string olarak kabul edilir. Sabitler hakkında daha detaylı bilgi için <a href="https://github.com/phpredis/phpredis#setoption">Redis setOption</a> metoduna bir gözatın.

<a name="redis-getOption"></a>

##### $this->cache->getOption(string|constant $option = 'OPT_SERIALIZER');

Geçerli opsiyon değerine döner. Daha detaylı bilgi için <a href="https://github.com/phpredis/phpredis#getoption">Redis getOption</a> metoduna bir gözatın.


<a name="redis-append"></a>

##### $this->cache->append(string $key, string or array $data)

Daha önce değer atanmış bir anahtara yeni değer ekler. Yeni atanan değer önceki değer ile string biçiminde birleştirilir.


<a name="redis-setTimeout"></a>

##### $this->cache->setTimeout(string $key, int $ttl)

Önceden set edilmiş bir anahtarın sona erme süresini günceller. Son parametre mili saniye formatında yazılmalıdır.

<a name="redis-getAllKeys"></a>

##### $this->cache->getAllKeys();

Bütün anahtarları dizi olarak döndürür.

<a name="redis-getMultiple"></a>

##### $this->cache->getItems(array $key)

Tüm belirtilen anahtarların değerini dizi olarak döndürür. Bir yada daha fazla anahtar değeri bulunamaz ise bu anahtarların değeri <kbd>false</kbd> olarak dizide var olacaklardır.

```php
$this->cache->setItem('key1', 'value1');
$this->cache->setItem('key2', 'value2');
$this->cache->setItem('key3', 'value3');
$this->cache->getItems(array('key1', 'key2', 'key3')); 
```
<a name="redis-getLastError"></a>

##### $this->cache->getLastError()

En son meydana gelen hataya string biçiminde geri döner.

<a name="redis-type"></a>

##### $this->cache->type(string $key)

Girilen anahtarın redis türünden biçimine döner bu biçimlerden bazıları şunlardır: <kbd>string, set, list, zset, hash</kbd>.

<a name="redis-getSet"></a>

##### $this->cache->getSet(string $key, string $value);

Önbellek deposuna yeni veriyi kaydederken eski veriye geri dönerek eski veriyi elde etmenizi sağlar.

<a name="redis-hSet"></a>

##### $this->cache->hSet(string $key, string $hashKey, mixed $value);

Belirtilen anahtarın alt anahtarına ( hashKey ) bir değer ekler. Metot eğer anahtara ait bir veri yoksa yani insert işleminde <kbd>true</kbd> değerine anahtara ait bir veri varsa yani replace işleminde <kbd>false</kbd> değerine döner.

```php
$this->cache->hSet('h', 'key1', 'merhaba'); // Sonuç true
$this->cache->hGet('h', 'key1'); // Sonuç "merhaba"

$this->cache->hSet('h', 'key1', 'php'); // Sonuç false döner ama değer güncellenir
$this->cache->hGet('h', 'key1');  // Sonuç "php"
```
<a name="redis-hGet"></a>

##### $this->cache->hGet(string $key, string $hashKey);

Hash tablosundan bir değere ulaşmanızı sağlar. Saklanan değere erişmek için belirtilen anahtarı hash tablosunda veya diğer anahtarlar içinde arayacaktır. Bulunamaz ise sonuç <kbd>false</kbd> dönecektir. 

```php
$this->cache->hGet('h', 'key');   // key "h" tablosunda aranır
```
<a name="redis-hGetAll"></a>

##### $this->cache->hGetAll();

Hash tablosundaki tüm değerleri bir dizi içerisinde verir.

```php
$this->cache->removeItem('h');
$this->cache->hSet('h', 'a', 'x');
$this->cache->hSet('h', 'b', 'y');

print_r($this->cache->hGetAll('h'));  // Çıktı array("x", "y");
```
<a name="redis-hLen"></a>

##### $this->cache->hLen();

Hash tablosundaki değerlerin genişliğini rakam olarak döndürür.

```php
$this->cache->removeItem('h');
$this->cache->hSet('h', 'key1', 'php');
$this->cache->hSet('h', 'key2', 'obullo');
print_r($this->cache->hLen('h')); // sonuç 2
```
<a name="redis-hDel"></a>

##### $this->cache->hDel();

Hash tablosundan bir değeri siler. Hash tablosu yada belirtilen anahtar yok ise sonuç <kbd>false</kbd> dönecektir.

```php
$this->cache->hDel('h', 'key');
```

<a name="redis-hKeys"></a>

##### $this->cache->hKeys();

Bir hash deki tüm anahtarları dizi olarak döndürür.

```php
$this->cache->removeItem('h');
$this->cache->hSet('h', 'a', 'x');
$this->cache->hSet('h', 'b', 'y');

print_r($this->cache->hKeys('h'));  // Çıktı  array("a", "b");
```
<a name="redis-hVals"></a>

##### $this->cache->hVals();

Bir hash deki tüm değerleri dizi olarak döndürür.

```php
$this->cache->removeItem('h');
$this->cache->hSet('h', 'a', 'x');
$this->cache->hSet('h', 'b', 'y');

print_r($this->cache->hVals('h'));  // Çıktı array("x", "y");
```
<a name="redis-hIncrBy"></a>

##### $this->cache->hIncrBy();

Bir hash üyesinin değerini belirli bir miktarda artırır, hIncrBy() metodunu kullanabilmek için serileştirme türü <kbd>none</kbd> olmalıdır.

```php
$this->cache->removeItem('h');
$this->cache->hIncrBy('h', 'x', 2);  // Sonuç:  2 / yeni değer: h[x] = 2
$this->cache->hIncrBy('h', 'x', 1);  // h[x] ← 2 + 1. sonuç 3
```

<a name="redis-hIncrByFloat"></a>

##### $this->cache->hIncrByFloat();

Bir hash üyesinin değerini float (ondalıklı) değer olarak artırmayı sağlar, hIncrByFloat() metodunu kullanabilmek için serileştirme türü <kbd>none</kbd> olmalıdır.

```php
$this->cache->removeItem('h');
$this->cache->hIncrByFloat('h','x', 1.5);   // Sonuç 1.5: h[x] = 1.5 now
$this->cache->hIncrByFLoat('h', 'x', 1.5);  // Sonuç 3.0: h[x] = 3.0 now
$this->cache->hIncrByFloat('h', 'x', -3.0); // Sonuç 0.0: h[x] = 0.0 now
```

<a name="redis-hSet"></a>

##### $this->cache->hSet($key, $hashKey, $data, $ttl = 0);

Bir diziye (hash tablosuna) ait anahtarın değerini değiştirir yada diziye yeni değeri ekler.

```php
$this->cache->removeItem('h')
$this->cache->hSet('h', 'key1', 'hello');
$this->cache->hGet('h', 'key1'); /* Çıktı "hello" */

$this->cache->hSet('h', 'key1', 'plop'); /* 0, değer değiştirildi. */
$this->cache->hGet('h', 'key1');  /* Çıktı "plop" */
```

<a name="redis-hGet"></a>

##### $this->cache->hGet(string $key);

Hash tablosunda varolan anahtarın değerine döner anahtar yoksa <kbd>false</kbd> değerine döner.

```php
$this->cache->removeItem('h')
$this->cache->hSet('h', 'key1', 'hello');
$this->cache->hGet('h', 'key1'); /* Çıktı "hello" */
```

<a name="redis-hMSet"></a>

##### $this->cache->hMSet(string $key, array $members);

Tüm hash değerlerini doldurur. String olmayan değerleri string türüne çevirir, bunuda standart string e dökme işlemini kullanarak yapar. Değeri <kbd>null</kbd> olarak saklanmış veriyi boş string olarak saklar.

```php
$this->cache->removeItem('user:1');
$this->cache->hMset('user:1', array('ad' => 'Ali', 'maas' => 2000));
$this->cache->hIncrBy('user:1', 'maas', 100);  // Ali'nin maaşını 100 birim arttırdık.
```

<a name="redis-hMGet"></a>

##### $this->cache->hMGet(string $key, array $members);

Hash de özel tanımlanan alanların değerlerini dizi olarak getirir.

```php
$this->cache->removeItem('h');
$this->cache->hSet('h', 'field1', 'value1');
$this->cache->hSet('h', 'field2', 'value2');
$this->cache->hmGet('h', array('field1', 'field2')); 

// Sonuç: array('field1' => 'value1', 'field2' => 'value2')
```

<a name="redis-sort"></a>

##### $this->cache->sort(string $key, array $sort)

Saklanan değerleri parametreler doğrultusunda sıralar.

Değerler:

```php
$this->cache->removeItem('test');
$this->cache->sAdd('test', 2);
$this->cache->sAdd('test', 1);
$this->cache->sAdd('test', 3);
```

Kullanımı:

```php
print_r($this->cache->sort('test')); // 1,2,3
print_r($this->cache->sort('test', array('sort' => 'desc')));  // 5,4,3,2,1
print_r($this->cache->sort('test', array('sort' => 'desc', 'store' => 'out'))); // (int)5
```
<kbd>sort</kbd> metodunun kullanılabilmesi için serileştirme türünün <kbd>none</kbd> olarak tanımlaması gerekmektedir.

<a name="redis-sSize"></a>

##### $this->cache->sSize(string $key)

Belirtilen anahtara ait değerlerin toplamını döndürür.

```php
$this->cache->sAdd('key1' , 'test1');
$this->cache->sAdd('key1' , 'test2');
$this->cache->sAdd('key1' , 'test3'); // 'key1' => {'test1', 'test2', 'test3'}
```

```php
$this->cache->sSize('key1'); /* 3 */
$this->cache->sSize('keyX'); /* 0 */
```
<a name="redis-sInter"></a>

##### $this->cache->sInter(array $key)

Belirtilen anahtarlara ait değerlerin bir birleriyle kesişenleri döndürür.

```php
$this->cache->sAdd('key1', 'val1');
$this->cache->sAdd('key1', 'val2');
$this->cache->sAdd('key1', 'val3');
$this->cache->sAdd('key1', 'val4');

$this->cache->sAdd('key2', 'val3');
$this->cache->sAdd('key2', 'val4');

$this->cache->sAdd('key3', 'val3');
$this->cache->sAdd('key3', 'val4');
```

```php
print_r($this->cache->sInter('key1', 'key2', 'key3'));  // Çıktı array('val4', 'val3')
```

<a name="redis-sGetMembers"></a>

##### $this->cache->sMembers(string $key)

Belirtilen anahtarın değerini bir dizi olarak döndürür.

```php
$this->cache->removeItem('key');
$this->cache->sAdd('key', 'val1');
$this->cache->sAdd('key', 'val2');
$this->cache->sAdd('key', 'val1');
$this->cache->sAdd('key', 'val3');
```

```php
print_r($this->cache->sMembers('key'));  // Çıktı array('val3', 'val2', 'val1');
```

Php Redis sınıfı hakkında daha detaylı dökümentasyona <a href="https://github.com/phpredis/phpredis" target="_blank">buradan</a> ulaşabilirsiniz.